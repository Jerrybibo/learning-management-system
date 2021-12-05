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
    <h2 class="d-flex justify-content-center">Canvas Login</h2>
    <form action="" method="post">
        <table class="table table-borderless" style="text-align:center">
            <thead>
                <tr><th>Please enter your credentials.</th></tr>
            </thead>
            <tbody>
                <tr><td><label for="login_id">Username:&nbsp;&nbsp;</label><input class="login_id" id="login_id" type="text"></td></tr>
                <tr><td><label for="net_id">NetID:&nbsp;&nbsp;</label><input class="net_id" id="net_id" type="text"></td></tr>
                <tr><td><input type="submit" value="Login"></td><tr>
            </tbody>
        </table>
    </form>
    <?php ?>
</div>
</body>
</html>
