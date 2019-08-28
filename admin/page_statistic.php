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

$_language->readModule('page_statistic', false, true);

if (!isanyadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}



$count_array = array();
$tables_array = array(
    PREFIX . "articles",
    PREFIX . "banner",
    PREFIX . "awards",
    PREFIX . "bannerrotation",
    PREFIX . "challenge",
    PREFIX . "clanwars",
    PREFIX . "comments",
    PREFIX . "contact",
    PREFIX . "countries",
    PREFIX . "demos",
    PREFIX . "faq",
    PREFIX . "faq_categories",
    PREFIX . "files",
    PREFIX . "files_categorys",
    PREFIX . "forum_announcements",
    PREFIX . "forum_boards",
    PREFIX . "forum_categories",
    PREFIX . "forum_groups",
    PREFIX . "forum_moderators",
    PREFIX . "forum_posts",
    PREFIX . "forum_ranks",
    PREFIX . "forum_topics",
    PREFIX . "gallery",
    PREFIX . "gallery_groups",
    PREFIX . "gallery_pictures",
    PREFIX . "games",
    PREFIX . "guestbook",
    PREFIX . "links",
    PREFIX . "links_categorys",
    PREFIX . "linkus",
    PREFIX . "messenger",
    PREFIX . "news",
    PREFIX . "news_languages",
    PREFIX . "news_rubrics",
    PREFIX . "partners",
    PREFIX . "poll",
    PREFIX . "servers",
    PREFIX . "shoutbox",
    PREFIX . "smileys",
    PREFIX . "sponsors",
    PREFIX . "squads",
    PREFIX . "static",
    PREFIX . "user",
    PREFIX . "user_gbook"
);
$db_size = 0;
$db_size_op = 0;
if (!isset($db)) {
    $get = safe_query("SELECT DATABASE()");
    $ret = mysqli_fetch_array($get);
    $db = $ret[ 0 ];
}
$query = safe_query("SHOW TABLES");

$count_tables = mysqli_num_rows($query);
foreach ($tables_array as $table) {
    $table_name = $table;
    $sql = safe_query("SHOW TABLE STATUS FROM `" . $db . "` LIKE '" . $table_name . "'");
    $data = mysqli_fetch_array($sql);
    $db_size += ($data[ 'Data_length' ] + $data[ 'Index_length' ]);
    if (strtolower($data[ 'Engine' ]) == "myisam") {
        $db_size_op += $data[ 'Data_free' ];
    }

    $table_base_name = str_replace(PREFIX, "", $table_name);
    if (isset($_language->module[ $table_base_name ])) {
        $table_name = $_language->module[ $table_base_name ];
    } else {
        $table_name = ucfirst(str_replace("_", " ", $table_name));
    }
    $count_array[ ] = array($table_name, $data[ 'Rows' ]);
}
?>


<div class="panel panel-default">

<div class="panel-heading">
                            <i class="fa fa-database"></i> <?php echo $_language->module['database']; ?>
                        </div>

<div class="panel-body">

<div class="row">
<div class="col-md-6">

	<div class="row bt"><div class="col-md-6"><?php echo $_language->module['mysql_version']; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo mysqli_get_server_info($_database); ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6"><?php echo $_language->module['size']; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $db_size; ?> Bytes (<?php echo round($db_size / 1024 / 1024, 2); ?> MB)</em></span></div></div>

</div>



<div class="col-md-6">
	<div class="row bt"><div class="col-md-6"><?php echo $_language->module['overhead']; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $db_size_op; ?> Bytes
    <?php
    if($db_size_op != 0) {
    	echo'<a href="admincenter.php?site=database&amp;action=optimize&amp;back=page_statistic"><font color="red"><b>'.$_language->module['optimize'].'</b></font></a>';
    }
    ?></em></span></div></div>
	<div class="row bt"><div class="col-md-6"><?php echo $_language->module['tables']; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $count_tables; ?></em></span></div></div>
</div>
</div>
</div>
</div>











<div class="panel panel-default">

<div class="panel-heading">
                            <i class="fa fa-pie-chart"></i> <?php echo $_language->module['page_stats']; ?>
                        </div>

<div class="panel-body">

<div class="row">

<?php
  for($i = 0; $i < count($count_array); $i += 1) {
    if($i%4) { $td='td1'; }
    else { $td='td2'; }
  ?>
<div class="col-md-6">
<div class="row bte"><div class="col-md-6"><?php echo $count_array[$i][0]; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $count_array[$i][1]; ?></em></span></div></div>
</div>




    <?php if(isset($count_array[$i + 1])) { ?>
	<div class="col-md-6">
<div class="row bte"><div class="col-md-6"><?php echo $count_array[$i + 1][0]; ?>:</div><div class="col-md-6"><span class="pull-right text-muted small"><em><?php echo $count_array[$i + 1][1]; ?></em></span></div></div>
</div>
    <?php 
		} 
  	else { ?>

    <?php } ?>

<?php
$i++;
}
?>

</div>
</div>
</div>

