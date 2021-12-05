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
        $sql_conn = mysqli_connect('jerry.games', 'cs377','ma9BcF@Y', 'canvas_db');
        if (!mysqli_select_db($sql_conn, 'canvas_db')) {
            printf('Error occurred during MySQL connection!\nDetails: %s\n', mysqli_error($sql_conn));
            exit(1);
        }

    ?>
</div>
</body>
</html>