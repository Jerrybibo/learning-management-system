<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <title>User Homepage</title>
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
    <h2>Academic Transcript</h2>
    <?php
    include 'helpers.php';
    # Connect to DB
    $sql_conn = connect('jerry.games', 'cs377', 'ma9BcF@Y', 'canvas_db');
    # Fetch the user id from _SESSION
    $id = $_SESSION['id'];
    $name_query = "SELECT u.fname, u.lname, u.net_id FROM user u WHERE u.id = '$id'";
    $result = query($sql_conn, $name_query);
    $full_name = mysqli_fetch_all($result)[0];
    # Get courses that the user is taking (as a student)
    $taking_courses_query = "SELECT c.course_no, c.course_name, t.letter_grade, c.semester, c.year FROM user u, takes t, class c
        WHERE u.id = t.user_id AND t.class_id = c.id AND u.id = '$id' ORDER BY c.year, c.semester DESC;";
    $result = query($sql_conn, $taking_courses_query);
    $taking_courses = mysqli_fetch_all($result);
    $semester_organized_courses = array();
    foreach ($taking_courses as $i => $course) {
        $semester_organized_courses[$course[3] . ' ' . $course[4]][] = array($course[0], $course[1], $course[2]);
    }
    echo '<table class="table table-bordered" style="text-align:center; width:auto; float:left; margin:20px">';
    echo "<thead><tr><th colspan='3'>Student $full_name[0] $full_name[1] - NetID $full_name[2]</th></tr></thead>";
    foreach ($semester_organized_courses as $semester => $semester_courses) {
        echo "<thead><tr><th colspan='3'>$semester</th></tr></thead><tbody>";
        foreach ($semester_courses as $j => $course) {
            echo "<tr><td>$course[0]</td><td>$course[1]</td><td>$course[2]</td></tr>";
        }
    }
    echo '</tbody></table>';
    ?>
    <!--Redirect to home page-->
    <p><input type="button" value="Back to Home" id="home_button" onClick="document.location.href='home.php'" /></p>
</div>

</body>
</html>