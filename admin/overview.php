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

$_language->readModule('overview', false, true);

if (!isanyadmin($userID) || mb_substr(basename($_SERVER[ getConstNameRequestUri() ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

include '../version.php';

$username = '' . getnickname($userID) . ':';
$lastlogin = getformatdatetime($_SESSION[ 'ws_lastlogin' ]);

$phpversion = phpversion() < '4.3' ? '<span style="color: #FF0000">' . phpversion() . '</span>' :
    '<span style="color: #008000">' . phpversion() . '</span>';
$zendversion = zend_version() < '1.3' ? '<span style="color: #FF0000">' . zend_version() . '</span>' :
    '<span style="color: #008000">' . zend_version() . '</span>';
$mysqlversion = mysqli_get_server_version($_database) < '40000' ?
    '<span style="color: #FF0000">' . mysqli_get_server_info($_database) . '</span>' :
    '<span style="color: #008000">' . mysqli_get_server_info($_database) . '</span>';
$get_phpini_path = get_cfg_var('cfg_file_path');
$get_allow_url_fopen =
    get_cfg_var('allow_url_fopen') ? '<span style="color: #008000">' . $_language->module[ 'on' ] . '</span>' :
        '<span style="color: #FF0000">' . $_language->module[ 'off' ] . '</span>';
$get_allow_url_include =
    get_cfg_var('allow_url_include') ? '<span style="color: #FF0000">' . $_language->module[ 'on' ] . '</span>' :
        '<span style="color: #008000">' . $_language->module[ 'off' ] . '</span>';
$get_display_errors =
    get_cfg_var('display_errors') ? '<span style="color: #FFA500">' . $_language->module[ 'on' ] . '</span>' :
        '<span style="color: #008000">' . $_language->module[ 'off' ] . '</span>';
$get_file_uploads = get_cfg_var('file_uploads') ? '<span style="color: #008000">' . $_language->module[ 'on' ] . '</span>' :
    '<span style="color: #FF0000">' . $_language->module[ 'off' ] . '</span>';
$get_log_errors = get_cfg_var('log_errors') ? '<span style="color: #008000">' . $_language->module[ 'on' ] . '</span>' :
    '<span style="color: #FF0000">' . $_language->module[ 'off' ] . '</span>';
$get_magic_quotes =
    get_cfg_var('magic_quotes_gpc') ? '<span style="color: #008000">' . $_language->module[ 'on' ] . '</span>' :
        '<span style="color: #FFA500">' . $_language->module[ 'off' ] . '</span>';
$get_max_execution_time = get_cfg_var('max_execution_time') < 30 ?
    '<span style="color: #FF0000">' . get_cfg_var('max_execution_time') . '</span> <small>(min. > 30)</small>' :
    '<span style="color: #008000">' . get_cfg_var('max_execution_time') . '</span>';
$get_memory_limit =
    get_cfg_var('memory_limit') > 128 ? '<span style="color: #FFA500">' . get_cfg_var('memory_limit') . '</span>' :
        '<span style="color: #008000">' . get_cfg_var('memory_limit') . '</span>';
$get_open_basedir = get_cfg_var('open_basedir') ? '<span style="color: #008000">' . $_language->module[ 'on' ] . '</span>' :
    '<span style="color: #FFA500">' . $_language->module[ 'off' ] . '</span>';
$get_post_max_size =
    get_cfg_var('post_max_size') > 8 ? '<span style="color: #FFA500">' . get_cfg_var('post_max_size') . '</span>' :
        '<span style="color: #008000">' . get_cfg_var('post_max_size') . '</span>';
$get_register_globals =
    get_cfg_var('register_globals') ? '<span style="color: #FF0000">' . $_language->module[ 'on' ] . '</span>' :
        '<span style="color: #008000">' . $_language->module[ 'off' ] . '</span>';
$get_safe_mode = get_cfg_var('safe_mode') ? '<span style="color: #008000">' . $_language->module[ 'on' ] . '</span>' :
    '<span style="color: #FF0000">' . $_language->module[ 'off' ] . '</span>';
$get_short_open_tag =
    get_cfg_var('short_open_tag') ? '<span style="color: #008000">' . $_language->module[ 'on' ] . '</span>' :
        '<span style="color: #FFA500">' . $_language->module[ 'off' ] . '</span>';
$get_upload_max_filesize = get_cfg_var('upload_max_filesize') > 16 ?
    '<span style="color: #FFA500">' . get_cfg_var('upload_max_filesize') . '</span>' :
    '<span style="color: #008000">' . get_cfg_var('upload_max_filesize') . '</span>';
$info_na = '<span style="color: #8F8F8F">' . $_language->module[ 'na' ] . '</span>';
if (function_exists("gd_info")) {
    $gdinfo = gd_info();
    $get_gd_info = '<span style="color: #008000">' . $_language->module[ 'enable' ] . '</span>';
    $get_gdtypes = array();
    if (isset($gdinfo[ 'FreeType Support' ]) && $gdinfo[ 'FreeType Support' ] === true) {
        $get_gdtypes[ ] = "FreeType";
    }
    if (isset($gdinfo[ 'T1Lib Support' ]) && $gdinfo[ 'T1Lib Support' ] === true) {
        $get_gdtypes[ ] = "T1Lib";
    }
    if (isset($gdinfo[ 'GIF Read Support' ]) && $gdinfo[ 'GIF Read Support' ] === true) {
        $get_gdtypes[ ] = "*.gif " . $_language->module[ 'read' ];
    }
    if (isset($gdinfo[ 'GIF Create Support' ]) && $gdinfo[ 'GIF Create Support' ] === true) {
        $get_gdtypes[ ] = "*.gif " . $_language->module[ 'create' ];
    }
    if ((isset($gdinfo[ 'JPG Support' ]) && $gdinfo[ 'JPG Support' ] === true) || (isset($gdinfo[ 'JPEG Support' ]) && $gdinfo[ 'JPEG Support' ] === true)) {
        $get_gdtypes[ ] = "*.jpg";
    }
    if (isset($gdinfo[ 'PNG Support' ]) && $gdinfo[ 'PNG Support' ] === true) {
        $get_gdtypes[ ] = "*.png";
    }
    if (isset($gdinfo[ 'WBMP Support' ]) && $gdinfo[ 'WBMP Support' ] === true) {
        $get_gdtypes[ ] = "*.wbmp";
    }
    if (isset($gdinfo[ 'XBM Support' ]) && $gdinfo[ 'XBM Support' ] === true) {
        $get_gdtypes[ ] = "*.xbm";
    }
    if (isset($gdinfo[ 'XPM Support' ]) && $gdinfo[ 'XPM Support' ] === true) {
        $get_gdtypes[ ] = "*.xpm";
    }
    $get_gdtypes = implode(", ", $get_gdtypes);
} else {
    $get_gd_info = '<span style="color: #FF0000">' . $_language->module[ 'disable' ] . '</span>';
    $gdinfo[ 'GD Version' ] = '---';
    $get_gdtypes = '---';
}

if (function_exists("apache_get_modules")) {
    $apache_modules = implode(", ", apache_get_modules());
} else {
    $apache_modules = $_language->module[ 'na' ];
}

$get = safe_query("SELECT DATABASE()");
$ret = mysqli_fetch_array($get);
$db = $ret[ 0 ];

echo '<div class="panel panel-default">
<div class="panel-heading"><span class="fa fa-smile-o"></span> '.$_language->module['welcome'].'</div>
<div class="panel-body">';

echo $_language->module['hello'].'&nbsp;'.$username.',&nbsp;'.$_language->module['last_login'].'&nbsp;'.$lastlogin.'.<br /><br />';
echo $_language->module['welcome_message'];
 ?>
</div>

</div>

<div class="row">
<div class="col-md-6">

<div class="panel panel-default">
<div class="panel-heading">
                            <span class="fa fa-database"></span> <?php echo $_language->module['serverinfo']; ?>
</div>

<div class="panel-body">


	<div class="row bt"><div class="col-md-6"><?php echo $_language->module['webspell_version']; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><span style="color: #008000"><?php echo $version; ?></span></em></span></div></div>
	<div class="row bt"><div class="col-md-6"><?php echo $_language->module['php_version']; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $phpversion; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6"><?php echo $_language->module['zend_version']; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $zendversion; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6"><?php echo $_language->module['mysql_version']; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $mysqlversion; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6"><?php echo $_language->module['databasename']; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $db; ?></em></span></div></div>

	<div class="row bt"><div class="col-md-6"><?php echo $_language->module['server_os']; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo ($php_s = @php_uname('s')) ? $php_s : $info_na; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6"><?php echo $_language->module['server_host']; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo ($php_n = @php_uname('n')) ? $php_n : $info_na; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6"><?php echo $_language->module['server_release']; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo ($php_r = @php_uname('r')) ? $php_r : $info_na; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6"><?php echo $_language->module['server_version']; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo ($php_v = @php_uname('v')) ? $php_v : $info_na; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6"><?php echo $_language->module['server_machine']; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo ($php_m = @php_uname('m')) ? $php_m : $info_na; ?></em></span></div></div>

</div>
</div>

</div>

<div class="col-md-6">

<div class="panel panel-default">
<div class="panel-heading">
                            <span class="fa fa-file-image-o"></span> GD Graphics Library
</div>

<div class="panel-body">


    <div class="row bt"><div class="col-md-4">GD Graphics Library:</div><div class="col-md-8"><span class="pull-right text-muted small"><em><?php echo $get_gd_info; ?></em></span></div></div>
    <div class="row bt"><div class="col-md-4"><?php echo $_language->module['supported_types']; ?>:</div><div class="col-md-8"><span class="pull-right text-muted small"><em><?php echo $get_gdtypes; ?></em></span></div></div>

    <div class="row bt"><div class="col-md-4">GD Lib <?php echo $_language->module['version']; ?>:</div><div class="col-md-8"><span class="pull-right text-muted small"><em><?php echo $gdinfo['GD Version']; ?></em></span></div></div>

</div>
</div>

</div>

<div class="col-md-6">

<div class="panel panel-default">
<div class="panel-heading">
                            <span class="fa fa-database"></span> <?php echo $_language->module['interface']; ?>
</div>

<div class="panel-body">

<div class="row">
<div class="col-md-12">
	<div class="row bt"><div class="col-md-6"><?php echo $_language->module['server_api']; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo php_sapi_name(); ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6"><?php echo $_language->module['apache']; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php if (function_exists("apache_get_version")) echo apache_get_version(); else echo $_language->module['na']; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6"><?php echo $_language->module['apache_modules']; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php if (function_exists("apache_get_modules")){if (count(apache_get_modules()) > 1) $get_apache_modules = implode(", ",apache_get_modules()); echo $get_apache_modules;} else { echo $_language->module['na'];} ?></em></span></div></div>
</div>
</div>
</div>
</div>

</div>


</div>

<div class="panel panel-default">
<div class="panel-heading">
                            <span class="fa fa-th-list"></span> <?php echo $_language->module['php_settings']; ?>
</div>
<div class="panel-body">
<div class="row bt">
<div class="col-md-12"><?php echo $_language->module['legend']; ?>::&nbsp; &nbsp;<span style="color: #008000"><?php echo $_language->module['green']; ?>:</span> <?php echo $_language->module['setting_ok']; ?>&nbsp; - &nbsp;<span style="color: #FFA500"><?php echo $_language->module['orange']; ?>:</span> <?php echo $_language->module['setting_notice']; ?>&nbsp; - &nbsp;<span style="color: #FF0000"><?php echo $_language->module['red']; ?>:</span> <?php echo $_language->module['setting_error']; ?></div>
</div><div class="row bt"></div>
<div class="row">
<div class="col-md-6">
	<div class="row bt"><div class="col-md-6">php.ini <?php echo $_language->module['path']; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $get_phpini_path; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6">Allow URL fopen:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $get_allow_url_fopen; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6">Allow URL Include:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $get_allow_url_include; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6">Display Errors:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $get_display_errors; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6">Error Log:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $get_log_errors; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6">File Uploads:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $get_file_uploads; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6">Magic Quotes:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $get_magic_quotes; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6">max. Execution Time:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $get_max_execution_time; ?></em></span></div></div>
</div>

<div class="col-md-6">
	<div class="row bt"><div class="col-md-6">Open Basedir:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $get_open_basedir; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6">max. Upload (Filesize):</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $get_upload_max_filesize; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6">Memory Limit:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $get_memory_limit; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6">Post max Size:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $get_post_max_size; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6">Register Globals:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $get_register_globals; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6">Safe Mode:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $get_safe_mode; ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6">Short Open Tag:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $get_short_open_tag; ?></em></span></div></div>
</div>
</div>
</div>
</div>

