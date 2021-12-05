<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>CS 377 - Database Systems</title>
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
        include 'helpers.php';
        echo '
        <h2 class="d-flex justify-content-center">Canvas Login</h2>
        <form action="" method="post">
            <table class="table table-borderless" style="text-align:center">
                <thead>
                    <tr><th>Please enter your credentials.</th></tr>
                </thead>
                <tbody>
                    <tr><td><label for="login_id">Personal ID:&nbsp;&nbsp;</label><input name="login_id" id="login_id" type="text"></td></tr>
                    <tr><td><label for="net_id">NetID:&nbsp;&nbsp;</label><input name="net_id" id="net_id" type="text"></td></tr>
                    <tr><td><input type="submit" value="Login" /></td></tr>';
        $_SESSION['id'] = '';
        $_SESSION['authenticated'] = false;
        if (!empty($_POST)) {
            $sql_conn = connect('jerry.games', 'cs377', 'ma9BcF@Y', 'canvas_db');
            $id = $_POST['login_id'];
            $net_id = $_POST['net_id'];
            $query = "SELECT * FROM user WHERE id = '$id' AND net_id = '$net_id';";
            $result = query($sql_conn, $query);
            $result_arr = mysqli_fetch_all($result);
            # If there exists an entry, then we keep the $id and toss the result
            if (count($result_arr) != 0) {
                mysqli_free_result($result);
                disconnect($sql_conn);
                $_SESSION['id'] = $id;
                $_SESSION['authenticated'] = true;
                echo '<td><tr>Authenticated. Welcome.</tr></td>';
                header("Location: home.php");
                exit();
            } else {
                echo '<tr><td>This ID-NetID combo does not exist in our database.</td></tr>';
            }
        }
        echo '  </tbody>
            </table>
        </form>';
    ?>

</div>
</body>
</html>
