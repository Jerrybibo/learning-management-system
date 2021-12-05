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
    <h2>Q&A Corner</h2>
    <?php
        include "helpers.php";
        # Connect to DB
        $sql_conn = connect('jerry.games', 'cs377', 'ma9BcF@Y', 'canvas_db');
        # Fetch the user id from _SESSION
        $id = $_SESSION['id'];
        # Fetch the courses list from _SESSION
        $applicable_courses = $_SESSION['courses'];
        $all_posts_list = [];
        # List all the post titles, timestamps, and posters
        $posts_list_query = "SELECT p.id, p.title, u.fname, u.lname, p.post_date FROM qapost p, user u WHERE p.poster_id = u.id AND p.class_id IN ('" . implode("','", $applicable_courses) . "');";
        $posts_list_result = query($sql_conn, $posts_list_query);
        $all_posts_list = mysqli_fetch_all($posts_list_result);
        disconnect($sql_conn);
        echo '<table class="table table-bordered" style="text-align:center; width:auto; float:left; margin:20px">
              <thead><th>Post ID</th><th>Post Title</th><th>Posted By</th><th>Posted On</th></thead><tbody>';
        foreach ($all_posts_list as $i => $post) {
            echo "<tr><td>$post[0]</td><td>$post[1]</td><td>$post[2] " . "$post[3]</td><td>$post[4]</td></tr>";
        }
        echo "</tbody></table>";
    ?>
        <!--Redirect to home page-->
        <p><input type="button" value="Back to Home" id="home_button" onClick="document.location.href='home.php'" /></p>
</div>
</body>
</html>