<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <title>Q&A Corner</title>
    <!-- meta tags -->
    <meta charset="utf=8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Include jQuery -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <!-- Bootstrap CSS + JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
            crossorigin="anonymous"></script>
</head>
<body class="d-flex justify-content-center">
<div class="container m-4">
    <h2>Q&A Corner</h2>
    <?php
        date_default_timezone_set('EST');
        include "helpers.php";
        $post_id = $_SESSION['post_id'];
        $id = $_SESSION['id'];
        if (!empty($_POST)) {
            $_POST['thread_text'] = str_replace("'", "''", $_POST['thread_text']);
            $sql_conn = connect('jerry.games', 'cs377', 'ma9BcF@Y', 'canvas_db');
            $id_validate = 1;
            $new_thread_post_id = '';
            # In case by miracle a random ID matches one in the database
            while ($id_validate != 0) {
                $new_thread_post_id = gen_id();
                $id_validate_query = "SELECT * FROM thread t WHERE t.id = '" . $new_thread_post_id . "';";
                $result = query($sql_conn, $id_validate_query);
                $id_validate = count(mysqli_fetch_all($result));
            }
            $current_time = date('y/m/d H:i:s');
            $thread_update_query = "INSERT INTO thread(id, post_date, text, poster_id, parent_id)
                                    VALUES('$new_thread_post_id', '$current_time', '$_POST[thread_text]', '$id', '$post_id')";
            query($sql_conn, $thread_update_query);
            unset($_POST);
            # Free result and terminate SQL connection because we don't need more information
            disconnect($sql_conn);
        }
        # Connect to DB
        $sql_conn = connect('jerry.games', 'cs377', 'ma9BcF@Y', 'canvas_db');
        $name_query = "SELECT u.fname, u.lname FROM user u WHERE u.id = '$id'";
        $result = query($sql_conn, $name_query);
        $full_name = mysqli_fetch_all($result)[0];
        $post_query = "SELECT q.title, q.text, q.post_date, u.fname, u.lname FROM qapost q, user u WHERE q.poster_id = u.id AND q.id = '$post_id'";
        $result = query($sql_conn, $post_query);
        $post_result = mysqli_fetch_all($result)[0];
        $thread_query = "SELECT t.text, t.post_date, u.fname, u.lname FROM thread t, user u WHERE t.poster_id = u.id AND t.parent_id = '$post_id' ORDER BY t.post_date;";
        $result = query($sql_conn, $thread_query);
        $thread_result = mysqli_fetch_all($result);
        # Free result and terminate SQL connection because we don't need more information
        mysqli_free_result($result);
        disconnect($sql_conn);
        echo "<table class='table table-bordered'><thead>";
        echo "<tr><th colspan='2' style='text-align: center; width:auto;'>$post_result[0]</th></tr>";
        echo "</thead><tbody><tr><td colspan='2'>$post_result[1]</td></tr><tr><td>Posted on $post_result[2]</td><td style='text-align: right'>Posted by $post_result[3] $post_result[4]</td></tr>";
        foreach ($thread_result as $i => $thread) {
            echo "<tr><td colspan='2'>$thread[0]</td></tr><tr><td>Replied on $thread[1]</td><td style='text-align: right'>Posted by $thread[2] $thread[3]</td></tr>";
        }
        echo "</tbody></table>";
        echo "<h3>Reply to Thread</h3><form action='' method='post'><textarea name='thread_text' rows='4' cols='60'></textarea><br><input type='submit' value='Reply'> as $full_name[0] $full_name[1]</form><br>";
    ?>
    <!--Redirect to home page-->
    <p><input type="button" value="Back to Q&A Corner" id="qa_button" onClick="document.location.href='qa_posts.php'" /></p>
</div>
</body>
</html>