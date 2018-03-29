<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sportsnight Teilnehmererfassung">
    <meta name="author" content="jerome roethlisberger">
    <link rel="icon" href="assets/icon/favicon.ico">

    <title>GISPO SCAN</title>

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/sticky-footer.css" rel="stylesheet">
    <link href="assets/css/app.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="assets/slick/slick.css"/>

    <link rel="stylesheet" type="text/css" href="assets/slick/slick-theme.css"/>



</head>

<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">GISPO SCAN</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="" ><a href="check_in.php">Check IN</a></li>
                <li class="" ><a href="check_out.php">Check OUT</a></li>
                <li class="" ><a href="export.php">Data Export</a></li>

            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>
</nav>
<div class="container">
    <?php require_once $viewFile; ?>
</div>
<footer class="footer">
    <div class="container">
        <p class="text-muted">
            Steampilot™ |
            Jérôme Röthlisberger |
            ©2015 GIBM - Gewerblich-industrielle Berufsfachschule Muttenz
        </p>
    </div>
</footer>
<script src="assets/js/jquery-1.11.2.min.js" type="text/javascript"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/slick/slick.min.js"></script>
<script src="assets/js/gisposcan.js" type="text/javascript"></script>

</body>

</html>