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
    $course_id = $_SESSION['selected_course'];
    $create_failure = false;
    if (!empty($_POST)) {
        if (!empty($_POST['name']) && !empty($_POST['due_date']) && !empty($_POST['max_points'])) {
            $_POST['details'] = str_replace("'", "''", $_POST['details']);
            $sql_conn = connect('jerry.games', 'cs377', 'ma9BcF@Y', 'canvas_db');
            $id_validate = 1;
            $new_assignment_id = '';
            # In case by miracle a random ID matches one in the database
            while ($id_validate != 0) {
                $new_assignment_id = gen_id();
                $id_validate_query = "SELECT * FROM assignment a WHERE a.id = '" . $new_assignment_id . "';";
                $result = query($sql_conn, $id_validate_query);
                $id_validate = count(mysqli_fetch_all($result));
            }
            $create_assignment_query = "INSERT INTO assignment(id, name, due_date, description, points, class_id)
                                        VALUES('$new_assignment_id', '$_POST[name]', '$_POST[due_date]', '$_POST[details]', '$_POST[max_points]', '$course_id');";
            query($sql_conn, $create_assignment_query);
            # Get user_id from class then Update completes with empty strings as values
            foreach ($_SESSION['students_id'] as $k => $v) {
                $update_completes_query = "INSERT INTO completes(user_id, assignment_id, grade)
                                           VALUES('$v', '$new_assignment_id', ' ')";
                query($sql_conn, $update_completes_query);
            }
            disconnect($sql_conn);
            unset($_POST);
            header("Location: view_course.php");
            exit();
        } else {
            $create_failure = true;
        }
    }
    # Connect to DB
    $sql_conn = connect('jerry.games', 'cs377', 'ma9BcF@Y', 'canvas_db');
    $course_info_query = "SELECT c.course_no, c.course_name, c.semester, c.year FROM class c WHERE c.id = '$course_id';";
    $result = query($sql_conn, $course_info_query);
    $course_info = mysqli_fetch_all($result)[0];
    echo "<h2>$course_info[0] $course_info[1]<br>
          <small class='text-muted'>$course_info[2] $course_info[3]</small></h2>";
    if ($create_failure) echo "<p>Failed to create new assignment - Assignment name, due date, and max points cannot be empty. Please try again.</p>";
    echo "<form action='' method='post'><div class='table-responsive'>
          <table class='table table-bordered' style='width:auto;'><thead style='text-align: center'><th colspan='2'>Create New Assignment</th></tr></thead><tbody>";
    echo "<tr><td style='text-align:right;'>Assignment Name</td><td><input name='name' type='text'/></td></tr>";
    echo "<tr><td style='text-align:right;'>Assignment Due Date</td><td><input name='due_date' type='datetime-local'/></td>";
    echo "<tr><td style='text-align:right;'>Maximum Points</td><td><input type='number' name='max_points' min='0' max='1000' value='0'></td>";
    echo "<tr><td style='text-align:right;'>Assignment Description</td><td><textarea name='details' rows='4' cols='40'></textarea></td>";
    echo "</tbody></table></div><input type='submit' value='Create'></form><br>";

    # Free result and terminate SQL connection because we don't need more information
    mysqli_free_result($result);
    disconnect($sql_conn);
?>
    <p><input type="button" value="Back to Course View" id="cv_button" onClick="document.location.href='view_course.php'" /></p>
</div>
</body>
</html>