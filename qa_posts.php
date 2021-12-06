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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</head>
<body class="d-flex justify-content-center">
<div class="container m-4">
    <?php
        date_default_timezone_set('EST');
        include "helpers.php";
        $tag_filter = '';
        $failed_to_post = false;
        # Fetch the user id from _SESSION
        $id = $_SESSION['id'];
        # Fetch the course id from _SESSION
        $applicable_course = $_SESSION['qa_course'];
        if (!empty($_POST)) {
            if (isset($_POST['selected_post'])) {
                $_SESSION['post_id'] = $_POST['selected_post'];
                unset($_POST);
                header("Location: view_thread.php");
                exit();
            }
            if (isset($_POST['tag_filter'])) {
                $tag_filter = $_POST['tag_filter'];
                unset($_POST);
            }
            if (isset($_POST['post_text'])) {
                if (!empty($_POST['post_text']) && !empty($_POST['post_title'])) {
                    $_POST['post_title'] = str_replace("'", "''", $_POST['post_title']);
                    $_POST['post_text'] = str_replace("'", "''", $_POST['post_text']);
                    $set_tags = $_POST['post_tags'];
                    $tags = array();
                    foreach (explode(',', $set_tags) as $k => $v) {
                        $tags[] = trim($v);
                    }
                    $sql_conn = connect('jerry.games', 'cs377', 'ma9BcF@Y', 'canvas_db');
                    $id_validate = 1;
                    $new_post_id = '';
                    # In case by miracle a random ID matches one in the database
                    while ($id_validate != 0) {
                        $new_post_id = gen_id();
                        $id_validate_query = "SELECT * FROM thread t WHERE t.id = '" . $new_post_id . "';";
                        $result = query($sql_conn, $id_validate_query);
                        $id_validate = count(mysqli_fetch_all($result));
                    }
                    $current_time = date('y/m/d H:i:s');
                    $new_post_query = "INSERT INTO qapost(id, title, post_date, text, poster_id, class_id)
                                    VALUES('$new_post_id', '$_POST[post_title]', '$current_time', '$_POST[post_text]', '$id', '$applicable_course');";
                    query($sql_conn, $new_post_query);
                    foreach ($tags as $i => $tag) {
                        $add_tag_query = "INSERT INTO tags(post_id, tag) VALUES('$new_post_id', '$tag')";
                        query($sql_conn, $add_tag_query);
                    }
                    unset($_POST);
                    # Free result and terminate SQL connection because we don't need more information
                    disconnect($sql_conn);
                } else $failed_to_post = true;
            }
        }
        # Connect to DB
        $sql_conn = connect('jerry.games', 'cs377', 'ma9BcF@Y', 'canvas_db');
        $name_query = "SELECT u.fname, u.lname FROM user u WHERE u.id = '$id'";
        $result = query($sql_conn, $name_query);
        $full_name = mysqli_fetch_all($result)[0];
        $course_info_query = "SELECT c.course_no, c.semester, c.year FROM class c WHERE c.id = '$applicable_course';";
        $result = query($sql_conn, $course_info_query);
        $course_info = mysqli_fetch_all($result)[0];
        echo "<h2>Q&A Corner<br><small class='text-muted'>$course_info[0] $course_info[1] $course_info[2]</small></h2>";
        if ($failed_to_post) {
            echo "<p>Failed to create new post! Neither the title nor the body of the post may be blank. Please try again.</p>";
        }
        # List all the post titles, timestamps, and posters
        $posts_list_query = "SELECT p.id, p.title, u.fname, u.lname, p.post_date
                             FROM qapost p, user u WHERE p.poster_id = u.id AND p.class_id = '$applicable_course' ORDER BY p.post_date DESC;";
        $posts_list_result = query($sql_conn, $posts_list_query);
        $all_posts_list = mysqli_fetch_all($posts_list_result);
        if (!empty($tag_filter)) echo "<p>Currently filtering using tag \"$tag_filter\"<br>To stop filtering, click the Filter button with a blank filter text field</p>";
        echo "<form action='' method='post'>Enter a tag to filter by: <input type='text' name='tag_filter'/><input type='submit' value='Filter'></form>";
        echo '<table class="table table-bordered" style="text-align:center; width:auto; margin:20px">
                  <thead><th>Link</th><th>Post Title</th><th>Posted By</th><th>Posted On</th><th>Tags</th></thead><tbody>';
        foreach ($all_posts_list as $i => $post) {
            $tags_query = "SELECT t.tag FROM tags t WHERE t.post_id = '$post[0]'";
            $tags_list_raw = mysqli_fetch_all(query($sql_conn, $tags_query));
            $tags_list = array();
            if (count($tags_list_raw) != 0) {
                foreach ($tags_list_raw as $j => $k) {
                    $tags_list[] = $k[0];
                }
            }
            $skip_row = false;
            if (!empty($tag_filter)) {
                $skip_row = true;
                foreach ($tags_list as $j => $k) {
                    if (strcmp(trim($tag_filter), trim($k)) == 0) $skip_row = false;
                }
            }
            if ($skip_row) continue;
            echo "<tr><td><form action='' method='post'><input type='hidden' name='selected_post' value='$post[0]'><input type='submit' value='View Post'/></form></td><td>$post[1]</td><td>$post[2] " . "$post[3]</td><td>$post[4]</td><td>";
            if (count($tags_list) == 0) echo "N/A"; else {
                foreach ($tags_list as $j => $k) {
                    echo "$k ";
                }
            }
            echo "</td></tr>";
        }
        echo "</tbody></table>";
        echo "<h3>Create a new post</h3><ul><li>Enter tags separated by commas.</li></ul><form action='' method='post'><table class='table table-bordered' style='width:auto;'><tbody>
              <tr><td style='text-align: right;'>Post Title</td><td><input type='text' name='post_title' size='57'/></td></tr>
              <tr><td style='text-align: right;'>Post Tags</td><td><input type='text' name='post_tags' size='57'/></td></tr>              
              <tr><td style='text-align: right;'>Post Text</td><td><textarea name='post_text' rows='4' cols='60'></textarea></td></tr>
              <tr><td colspan='2' style='text-align: center;'><input type='submit' value='Post'> as $full_name[0] $full_name[1]</td></tbody></table></form><br>";

    disconnect($sql_conn);
    ?>
        <!--Redirect to home page-->
        <p><input type="button" value="Back to Home" id="home_button" onClick="document.location.href='home.php'" /></p>
</div>
</body>
</html>