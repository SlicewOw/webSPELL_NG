<?php
/*
##########################################################################
#                                                                        #
#           Version 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Free Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyright 2005-2015 by webspell.org                                  #
#                                                                        #
#   visit webSPELL.org, webspell.info to get webSPELL for free           #
#   - Script runs under the GNU GENERAL PUBLIC LICENSE                   #
#   - It's NOT allowed to remove this copyright-tag                      #
#   -- http://www.fsf.org/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),   #
#   Far Development by Development Team - webspell.org                   #
#                                                                        #
#   visit webspell.org                                                   #
#                                                                        #
##########################################################################
*/

session_name("ws_session");
session_start();

header('content-type: text/html; charset=utf-8');

include("../src/func/language.php");
include("../src/func/user.php");
include("../version.php");
if (version_compare(PHP_VERSION, '5.3.7', '>') && version_compare(PHP_VERSION, '5.5.0', '<')) {
    include('../src/func/password.php');
}

$_language = new \webspell\Language();

if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = "en";
}

if (isset($_GET['lang'])) {
    if ($_language->setLanguage($_GET['lang'])) {
        $_SESSION['language'] = $_GET['lang'];
    }
    header("Location: index.php");
    exit();
}

$_language->setLanguage($_SESSION['language']);
$_language->readModule('index', false, false, false, true);

$step = isset($_GET['step']) ? (int)$_GET['step'] : 0;
if ($step < 1) {
    $step = 0;
}

$_language->readModule('step' . $step, true, false, false, true);

?>

<!DOCTYPE html>
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="description" content="Clanpage using webSPELL 4 CMS">
    <meta name="author" content="webspell.org">
    <meta name="copyright" content="Copyright 2005-2014 by webspell.org">
    <meta name="generator" content="webSPELL">

    <title>webSPELL NG Installation</title>

    <link href="../vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/twbs/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" type="text/css">
    <link href="../_stylesheet.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">

    <script src="../vendor/jquery/jquery/jquery-3.6.0.min.js"></script>
    <script src="install.js"></script>

</head>
<body>
   <div class="container">

        <div class="header clearfix">
            <nav>
                <ul class="nav nav-pills pull-right">
                    <li class="nav-item">
                        <a class="nav-link" href="http://webspell-ng.de/">Support</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://www.webspell.org/index.php?site=license">License</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://webspell-ng.de/index.php?site=about">About</a>
                    </li>
                </ul>
            </nav>
            <h3 class="text-muted">webSPELL Installation</h3>
        </div>

        <div class="jumbotron bg">
            <h1>webSPELL NG</h1>
                <p>super powerful, responsive features, easy to adjust one of the easiest content management systems on earth wonderful bootstrap or photoshop templates lots of Add-ons and modifications for all types of websites a community behind you for all issues and problems
            </p>
        </div>

        <div class="container">

            <?php
                echo '<form action="index.php?step=' . ($step + 1) . '" method="post" name="ws_install">';
                include('step0' . $step . '.php');
            ?>

        </div>

        <footer class="footer">
            <hr />
            <p class="text-muted"><small>&copy; <?php echo date("Y"); ?> by <a href="https://webspell-ng.de/" target="_blank">webSPELL NG</a> &amp; <a href="http://www.webspell.org" target="_blank">webspell.org</a></small></p>
        </footer>

    </div>

    <script src="../node_modules/popper.js/dist/umd/popper.js"></script>
    <script src="../vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
    <script>
        $("body").tooltip({
        selector: "[data-toggle='tooltip']",
        container: "body"
    })
    </script>

</body>
</html>