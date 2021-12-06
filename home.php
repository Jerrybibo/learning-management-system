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
<div class="container m-4 justify-content-center text-center">
    <h2>Welcome back,
    <?php
        if (!empty($_POST)) {
            if (isset($_POST['qa_course'])) {
                $_SESSION['qa_course'] = $_POST['qa_course'];
                unset($_POST);
                header("Location: qa_posts.php");
                exit();
            } else if (isset($_POST['selected_course'])) {
                $_SESSION['selected_course'] = $_POST['selected_course'];
                $_SESSION['role'] = $_POST['role'];
                unset($_POST);
                header("Location: view_course.php");
                exit();
            }
        }
        include 'helpers.php';
        # Connect to DB
        $sql_conn = connect('jerry.games', 'cs377', 'ma9BcF@Y', 'canvas_db');
        # Fetch the user id from _SESSION
        $id = $_SESSION['id'];
        # Get the full name
        $name_query = "SELECT fname, lname FROM user WHERE id = '$id';";
        $result = query($sql_conn, $name_query);
        $result_arr = mysqli_fetch_all($result);
        $name = $result_arr[0];
        # Reset sessions courses
        $_SESSION['courses'] = [];
        # Get courses that the user is taking (as a student)
        $taking_courses_query = "SELECT c.id, c.course_no, c.course_name, c.year, c.semester FROM user u, takes t, class c
        WHERE u.id = t.user_id AND t.class_id = c.id AND u.id = '$id';";
        $result = query($sql_conn, $taking_courses_query);
        $taking_courses = mysqli_fetch_all($result);
        # ... Teaching as professor
        $teaching_courses_query = "SELECT c.id, c.course_no, c.course_name, c.year, c.semester FROM user u, class c WHERE u.id = c.lecturer_id AND u.id = '$id';";
        $result = query($sql_conn, $teaching_courses_query);
        $teaching_courses = mysqli_fetch_all($result);
        # ... Assisting as TA
        $assisting_courses_query = "SELECT c.id, c.course_no, c.course_name, c.year, c.semester FROM user u, assists a, class c
        WHERE u.id = a.user_id AND a.class_id = c.id AND u.id = '$id';";
        $result = query($sql_conn, $assisting_courses_query);
        $assisting_courses = mysqli_fetch_all($result);
        # Free result and terminate SQL connection because we don't need more information
        mysqli_free_result($result);
        disconnect($sql_conn);
        # Finish up the header
        echo $name[0] . '!</h2><br>';
        $total_courses_count = count($taking_courses) + count($teaching_courses) + count($assisting_courses);
        # If there are classes that the user is taking, then display
        if (count($taking_courses) > 0) {
            echo '<table class="table table-bordered" style="text-align:center; width:auto; float:left; margin:20px">
            <thead><tr><th colspan="7">Classes You\'re Taking</th></tr></thead>
            <tbody>';
            foreach ($taking_courses as $i => $course) {
                $course_id = $course[0];
                $course = array_slice($course, 1);
                echo "<tr><td><form action='' method='post'>
                          <input type='hidden' name='selected_course' value='$course_id'/>
                          <input type='hidden' name='role' value='student'/>
                          <input type='submit' value='Visit Class as Student'/>
                      </form></td>";
                foreach ($course as $j => $v) {
                    echo "<td>$v</td>";
                }
                echo "<td>Student</td><td><form action='' method='post'><input type='hidden' name='qa_course' value='$course_id'/><input type='submit' value='Q&A Corner'></form></td></tr>";
            }
            echo '</tbody></table>';
        }
        # If there are classes that the user is teaching, then display
        if (count($teaching_courses) + count($assisting_courses) > 0) {
            echo '<table class="table table-bordered" style="text-align:center; width:auto; float:left; margin:20px">
            <thead><tr><th colspan="7">Classes You\'re Teaching</th></tr></thead>
            <tbody>';
            foreach ($teaching_courses as $i => $course) {
                $course_id = $course[0];
                $course = array_slice($course, 1);
                echo "<tr><td><form action='' method='post'>
                          <input type='hidden' name='selected_course' value='$course_id'/>
                          <input type='hidden' name='role' value='instructor'/>
                          <input type='submit' value='Visit Class as Instructor'/>
                      </form></td>";
                foreach ($course as $j => $v) {
                    echo "<td>$v</td>";
                }
                echo "<td>Professor</td><td><form action='' method='post'><input type='hidden' name='qa_course' value='$course_id'/><input type='submit' value='Q&A Corner'></form></td></tr>";
            }
            foreach ($assisting_courses as $i => $course) {
                $course_id = $course[0];
                $course = array_slice($course, 1);
                echo "<tr><td><form action='' method='post'>
                          <input type='hidden' name='selected_course' value='$course_id'/>
                          <input type='hidden' name='role' value='instructor'/>
                          <input type='submit' value='Visit Class as Instructor'/>
                      </form></td>";
                foreach ($course as $j => $v) {
                    echo "<td>$v</td>";
                }
                echo "<td>TA</td><td><form action='' method='post'><input type='hidden' name='qa_course' value='$course_id'/><input type='submit' value='Q&A Corner'></form></td></tr>";
            }
            echo '</tbody></table><br>';
        }
        # If we encounter someone who just doesn't like school
        if ($total_courses_count == 0) {
            echo '<p>You\'re not enrolled in any classes! If this is unexpected, contact an administrator.</p>';
        }
        if (count($taking_courses) > 0) echo "<p><input type='button' value='View Student Transcript' id='transcript_button' onClick=" . '"document.location.href='. "'transcript.php'" . '"/></p>';
    ?>
        <!--Log out-->
        <p><input type="button" value="Log Out" id="logout_button" onClick="document.location.href='logout.php'" /></p>
</div>
</body>
</html>