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

chdir('../');
include("_mysql.php");
include("_settings.php");
include("_functions.php");
include("_plugin.php");
chdir(getConstNameAdmin());

$load = new PluginManager();
$_language->readModule('admincenter', false, true);

if (isset($_GET['site'])) {
    $site = $_GET['site'];
} else if (isset($site)) {
    unset($site);
}

if (!$loggedin) {
    die($_language->module['not_logged_in']);
}

$admin = isanyadmin($userID);
if (!$admin) {
    die($_language->module['access_denied']);
}

if (!isset($_SERVER[getConstNameRequestUri()])) {
    $arr = explode("/", $_SERVER['PHP_SELF']);
    $_SERVER[getConstNameRequestUri()] = "/" . $arr[count($arr)-1];
    if ($_SERVER['argv'][0]!="") {
        $_SERVER[getConstNameRequestUri()] .= "?" . $_SERVER['argv'][0];
    }
}

function admincenternav($catID)
{
    global $userID;
    $links = '';
    $ergebnis = safe_query("SELECT * FROM ".PREFIX."dashnavi_links WHERE catID='$catID' ORDER BY sort");
    while ($ds=mysqli_fetch_array($ergebnis)) {
        $accesslevel = 'is'.$ds['accesslevel'].getConstNameAdmin();
        if ($accesslevel($userID)) {
            $links .= '<li><a href="'.$ds['url'].'">'.$ds['name'].'</a></li>';
        }
    }
    return $links;
}

function addonnav()
{
    global $userID;

    $links = '';
    $ergebnis = safe_query("SELECT * FROM ".PREFIX."dashnavi_categories WHERE sort>'9' ORDER BY sort");
    while ($ds=mysqli_fetch_array($ergebnis)) {
        $links .= '<li>
        <a href="#"><span class="bi bi-plus-circle-fill"></span> '.$ds['name'].'<span class="fa arrow"></span></a>';
        $catlinks = safe_query("SELECT * FROM ".PREFIX."dashnavi_links WHERE catID='".$ds['catID']."' ORDER BY sort");
        while ($db=mysqli_fetch_array($catlinks)) {
            $accesslevel = 'is'.$db['accesslevel'].getConstNameAdmin();
            if ($accesslevel($userID)) {
                $links .= '<ul class="nav nav-second-level">
                                    <li><a href="'.$db['url'].'">'.$ds['name'].'</a></li>
                        </ul>';
            }
        }
        $links .= '';
    }
    return $links;
}

if ($userID && !isset($_GET[ 'userID' ]) && !isset($_POST[ 'userID' ])) {

    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT
                    `registerdate`
                FROM `" . PREFIX . "user`
                WHERE `userID` = " . $userID
        )
    );

    $username = '<a href="../index.php?site=profile&amp;id=' . $userID . '">' . getnickname($userID) . '</a>';
    $lastlogin = getformatdatetime($_SESSION[ 'ws_lastlogin' ]);
    $registerdate = getformatdatetime($ds[ 'registerdate' ]);

    $data_array = array();
    $data_array['$username'] = $username;
    $data_array['$lastlogin'] = $lastlogin;
    $data_array['$registerdate'] = $registerdate;

}

if ($getavatar = getavatar($userID)) {
    $l_avatar = '<img src="../images/avatars/' . $getavatar . '" alt="Avatar" class="img-circle profile_img">';
} else {
    $l_avatar = $_language->module[ 'n_a' ];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Webspell NOR - Bootstrap Admin Theme</title>

    <!-- Bootstrap Core CSS -->
    <link href="../components/bootstrap/bootstrap.min.css" rel="stylesheet">


    <!-- Custom CSS -->
    <link href="../components/admin/css/page.css" rel="stylesheet">

    <!-- Menu CSS -->
    <link href="../components/admin/css/menu.min.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../components/font-awesome/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- Style CSS -->
    <link href="../components/admin/css/style.css" rel="stylesheet">
    <link href="../css/button.css.php" rel="styleSheet" type="text/css">
    <link href="../components/admin/css/bootstrap-switch.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head>

<body>

    <div id="wrapper">

        <?php include(__DIR__ . '/_navigation.php'); ?>

        <div id="page-wrapper">
            <div class="row">

                <!-- /.col-lg-12 -->
                <div class="col-lg-12">
                <br>
                <?php
    if (isset($site) && $site!="news") {
        $invalide = array('\\','/','//',':','.');
        $site = str_replace($invalide, ' ', $site);
        if (file_exists($site.'.php')) {
            include($site.'.php');
        } else {
			// Load Plugins-Admin-File (if exists)
			chdir("../");
			$plugin = $load->plugin_data($site,0,true);
			$plugin_path = $plugin['path'];
			if (file_exists($plugin_path."admin/".$plugin['admin_file'].".php")) {
				include($plugin_path."admin/".$plugin['admin_file'].".php");
			} else {
				chdir("admin");
			echo "<strong>Modul [or] Plugin Not found</strong><br /><br />";
				include('overview.php');
			}
        }
    } else {
        include('overview.php');
    }


    ?>

            </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->

 <!-- jQuery -->
    <script src="../components/jquery/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" media="all" href="../components/admin/css/style-nav.css">
    <link href="../components/admin/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <script src="../components/admin/js/bootstrap-colorpicker.js"></script>
    <script>
        jQuery(function($) {
            $('#cp1').colorpicker();
            $('#cp2').colorpicker();
            $('#cp3').colorpicker();
            $('#cp4').colorpicker();
            $('#cp5').colorpicker();
            $('#cp6').colorpicker();
            $('#cp7').colorpicker();
            $('#cp8').colorpicker();
            $('#cp9').colorpicker();
            $('#cp10').colorpicker();
            $('#cp11').colorpicker();
            $('#cp12').colorpicker();
            $('#cp13').colorpicker();
            $('#cp14').colorpicker();
            $('#cp15').colorpicker();
            $('#cp16').colorpicker();
            $('#cp17').colorpicker();
            $('#cp18').colorpicker();
            $('#cp19').colorpicker();
            $('#cp20').colorpicker();
            $('#cp21').colorpicker();
            $('#cp22').colorpicker();
            $('#cp23').colorpicker();
            $('#cp24').colorpicker();
            $(document).ready(function(){
                $('[data-toggle="tooltip"]').tooltip();
            });
        });
    </script>
    <!-- Bootstrap Core JavaScript -->
    <script src="../components/bootstrap/bootstrap.min.js"></script>

    <!-- Menu Plugin JavaScript -->
    <script src="../components/admin/js/menu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../components/admin/js/page.js"></script>

<script src="../components/admin/js/index.js"></script>
<script>
        var calledfrom='admin';
    </script>
    <script src="../js/bbcode.js"></script>
<script src="../components/admin/js/bootstrap-switch.js"></script>

</body>
</html>
