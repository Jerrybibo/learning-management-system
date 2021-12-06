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
<div class="container">
    <?php
        ini_set("log_errors", 1);
        $user_id = $_SESSION['id'];
        $course_id = $_SESSION['selected_course'];
        $role = $_SESSION['role'];
        include "helpers.php";
        if (!empty($_POST)) {
            if (strcmp($_POST['operation'], "create_assignment") == 0) {
                unset($_POST);
                header("Location: create_assignment.php");
                exit();
            }
            $sql_conn = connect('jerry.games', 'cs377', 'ma9BcF@Y', 'canvas_db');
            if (strcmp($_POST['operation'], "update_assignment_grade") == 0) {
                $update_a_grade_query = "INSERT INTO completes(user_id, assignment_id, grade) VALUES('$_POST[selected_student]', '$_POST[selected_assignment]', '$_POST[updated_value] ')
                                         ON DUPLICATE KEY UPDATE grade = '$_POST[updated_value] ', user_id = '$_POST[selected_student]', assignment_id = '$_POST[selected_assignment]';";
                $result = query($sql_conn, $update_a_grade_query);
            } else if (strcmp($_POST['operation'], "update_final_grade") == 0) {
                $update_f_grade_query = "INSERT INTO takes(user_id, class_id, letter_grade) VALUES('$_POST[selected_student]', '$course_id', '$_POST[updated_value] ')
                                         ON DUPLICATE KEY UPDATE letter_grade = '$_POST[updated_value] ', user_id = '$_POST[selected_student]', class_id = '$course_id';";
                $result = query($sql_conn, $update_f_grade_query);
            }
            disconnect($sql_conn);
            unset($_POST);
        }
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
            echo '<table class="table table-bordered" style="width:auto; float:left;"><thead style="text-align:center;"><tr><th colspan="3">Assignments</th></tr></thead><tbody>';
            foreach ($assignments_list as $i => $assignment) {
                if (strlen($assignment[2]) == 1) $assignment[2] = "N/A";
                echo "<tr><td style='text-align:right;'>$assignment[0]</td><td>Due $assignment[1]</td><td>$assignment[2] / $assignment[3]</td></tr>";
                echo "<tr><td></td><td colspan='2' style='text-align: left;'>$assignment[4]</td></tr>";
            }
            echo "</tbody></table>";
        } else if (strcmp($role, 'instructor') == 0) {
            $grade_options = array("A", "A-", "B+", "B", "B-", "C+", "C", "C-", "D+", "D", "D-", "F");
            # Connect to DB
            $sql_conn = connect('jerry.games', 'cs377', 'ma9BcF@Y', 'canvas_db');
            $course_info_query = "SELECT c.course_no, c.course_name, c.semester, c.year FROM class c WHERE c.id = '$course_id';";
            $result = query($sql_conn, $course_info_query);
            $course_info = mysqli_fetch_all($result)[0];
            $assignments_query = "SELECT a.id, a.name FROM assignment a WHERE a.class_id = '$course_id' ORDER BY a.due_date;";
            $result = query($sql_conn, $assignments_query);
            $assignments = mysqli_fetch_all($result);
            $students_query = "SELECT u.id, u.fname, u.lname, u.net_id FROM user u, takes t WHERE u.id = t.user_id AND t.class_id = '$course_id';";
            $result = query($sql_conn, $students_query);
            $students = mysqli_fetch_all($result);
            $assignments_id = array_map('take_first', $assignments);
            echo "<h2>$course_info[0] $course_info[1]<br>
                  <small class='text-muted'>$course_info[2] $course_info[3]</small></h2>";
            echo "<table class='table table-bordered' style='text-align:center;'><thead><tr><th>Student Name</th><th>Student NetID</th>";
            foreach ($assignments as $index => $assignment) {
                echo "<th>$assignment[1]</th>";
            }
            echo "<th>Final Grade</th></tr></thead><tbody>";
            foreach ($students as $i => $student) {
                echo "<tr><td>$student[1] $student[2]</td><td>$student[3]</td>";
                foreach ($assignments_id as $j => $assignment_id) {
                    $student_assignment_scores_query = "SELECT c.grade, a.points FROM completes c, assignment a WHERE c.user_id = '$student[0]' AND c.assignment_id = a.id AND c.assignment_id = '$assignment_id'";
                    $result = query($sql_conn, $student_assignment_scores_query);
                    $points = mysqli_fetch_all($result);
                    if (count($points) == 0 || strlen($points[0][0]) == 1) $score = "N/A"; else $score = $points[0][0];
                    echo "<td>$score";
                    if (count($points) > 0) {
                        $max_points = $points[0][1];
                        echo " / $max_points</td>";
                    } else echo "</td>";
                }
                $letter_grade_query = "SELECT t.letter_grade FROM takes t WHERE t.class_id = '$course_id' AND t.user_id = '$student[0]';";
                $result = query($sql_conn, $letter_grade_query);
                $letter_grade = mysqli_fetch_all($result)[0][0];
                if (strlen($letter_grade) == 1) echo "<td>N/A</td>"; else echo "<td>$letter_grade</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
            echo "<h2>Actions<br><small class='text-muted'>I'd like to...</small></h2><ul>";
            # Update grade on assignment
            echo "<li><form action='' method='post'><input type='hidden' name='operation' value='update_assignment_grade'/><input type='submit' value='Update'> student <select name='selected_student'>";
            foreach ($students as $index => $student) {
                echo "<option value='$student[0]'>$student[3]</option>";
            }
            echo "</select>'s grades on assignment <select name='selected_assignment'>";
            foreach ($assignments as $index => $assignment) {
                echo "<option value='$assignment[0]'>$assignment[1]</option>";
            }
            echo "</select> to <input type='number' name='updated_value' min='0' max='1000' value='0'>.</li></form>";
            # Update final grade
            echo "<li><form action='' method='post'><input type='hidden' name='operation' value='update_final_grade'/><input type='submit' value='Update'> student <select name='selected_student'>";
            foreach ($students as $index => $student) {
                echo "<option value='$student[0]'>$student[3]</option>";
            }
            echo "</select>'s final grade to <select name='updated_value'>";
            foreach ($grade_options as $index => $grade_option) {
                echo "<option value='$grade_option'>$grade_option</option>";
            }
            echo "</select>.</li></form>";
            # Create new assignment (Redirects to create_assignment.php)
            echo "<li><form action='' method='post'><input type='hidden' name='operation' value='create_assignment'/><input type='submit' value='Create'> a new assignment.</form>";
            echo "</ul>";
            # Free result and terminate SQL connection because we don't need more information
            mysqli_free_result($result);
            disconnect($sql_conn);
        } else {
            echo "<p>An unexpected error occurred: Incorrect role when attempting to access a class. Role was $role</p>";
        }

    ?>
    <!--Redirect to home page-->
    <p><input type="button" value="Back to Home" id="home_button" onClick="document.location.href='home.php'" /></p>
</div>
</body>
</html>