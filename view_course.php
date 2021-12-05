<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <title>Course View</title>
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
        include "helpers.php";
        $user_id = $_SESSION['id'];
        $course_id = $_SESSION['selected_course'];
        $role = $_SESSION['role'];
        if (strcmp($role, 'student') == 0) {
            # Connect to DB
            $sql_conn = connect('jerry.games', 'cs377', 'ma9BcF@Y', 'canvas_db');
            $course_info_query = "SELECT c.course_no, c.course_name, c.semester, c.year FROM class c WHERE c.id = '$course_id';";
            $result = query($sql_conn, $course_info_query);
            $course_info = mysqli_fetch_all($result)[0];
            $letter_grade_query = "SELECT t.letter_grade FROM takes t WHERE t.class_id = '$course_id' AND t.user_id = '$user_id';";
            $result = query($sql_conn, $letter_grade_query);
            $letter_grade = mysqli_fetch_all($result)[0][0];
            $assignments_list_query = "SELECT a.name, a.due_date, co.grade, a.points, a.description FROM completes co, assignment a
                                   WHERE co.assignment_id = a.id AND co.user_id = '$user_id' AND a.class_id = '$course_id' ORDER BY a.due_date;";
            $result = query($sql_conn, $assignments_list_query);
            $assignments_list = mysqli_fetch_all($result);
            # Free result and terminate SQL connection because we don't need more information
            mysqli_free_result($result);
            disconnect($sql_conn);
            echo "<h2>$course_info[0] $course_info[1]<br>
                  <small class='text-muted'>$course_info[2] $course_info[3]</small></h2>";
            if (strlen($letter_grade) == 1) {
                echo "<p>Your final grade has not been posted yet.</p>";
            } else {
                echo "<p>Your posted grade is: $letter_grade</p>";
            }
            echo '<table class="table table-bordered"><thead style="text-align:center;"><tr><th colspan="3">Assignments</th></tr></thead>';
            foreach ($assignments_list as $i => $assignment) {
                if (strlen($assignment[2]) == 1) $assignment[2] = "N/A";
                echo "<tr><td>$assignment[0]</td><td>Due $assignment[1]</td><td>$assignment[2] / $assignment[3]</td></tr>";
                echo "<tr><td></td><td colspan='2' style='text-align: left;'>$assignment[4]</td></tr>";
            }
        } else if (strcmp($role, 'instructor') == 0) {
            # Connect to DB
            $sql_conn = connect('jerry.games', 'cs377', 'ma9BcF@Y', 'canvas_db');
            echo "$course_id";
            $course_info_query = "SELECT c.course_no, c.course_name, c.semester, c.year FROM class c WHERE c.id = '$course_id';";
            $result = query($sql_conn, $course_info_query);
            $course_info = mysqli_fetch_all($result)[0];
            $assignments_id_query = "SELECT a.id, a.name FROM assignment a WHERE a.class_id = '$course_id' ORDER BY a.due_date;";
            $result = query($sql_conn, $assignments_id_query);
            $assignments_id = mysqli_fetch_all($result);
            # Free result and terminate SQL connection because we don't need more information
            mysqli_free_result($result);
            disconnect($sql_conn);
            echo "<h2>$course_info[0] $course_info[1]<br>
                  <small class='text-muted'>$course_info[2] $course_info[3]</small></h2>";
            foreach ($assignments_id as $index => $assignment) {
                echo "$assignment[0] => $assignment[1]<br>";
            }
        } else {
            echo "<p>An unexpected error occurred: Incorrect role when attempting to access a class. Role was $role</p>";
        }

    ?>
    <!--Redirect to home page-->
    <p><input type="button" value="Back to Home" id="home_button" onClick="document.location.href='home.php'" /></p>
</div>
</body>
</html>