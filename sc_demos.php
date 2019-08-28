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

$_language->readModule('demos');

$getlist = safe_query("SELECT sc_demos FROM " . PREFIX . "settings");
$ds = mysqli_fetch_array($getlist);

if ($ds[ 'sc_demos' ] == 1) {
    $list = "rating";
} else {
    $list = "date";
}

$ergebnis = safe_query("SELECT * FROM " . PREFIX . "demos ORDER BY $list DESC LIMIT 0,5");
$n = 1;
if (mysqli_num_rows($ergebnis)) {
    echo '<ul class="list-group">';
    while ($ds = mysqli_fetch_array($ergebnis)) {
        $demoID = $ds[ 'demoID' ];
        $count = $ds[ 'downloads' ];
        $clan1 = $ds[ 'clan1' ];
        $tag1 = $ds[ 'clantag1' ];
        $tag2 = $ds[ 'clantag2' ];
        $clan2 = $ds[ 'clan2' ];
        $number = $n;

        #if ($n % 2) {
        #    $bg1 = BG_1;
        #    $bg2 = BG_2;
        #} else {
        #    $bg1 = BG_3;
        #    $bg2 = BG_4;
        #}

        $data_array = array();
        $data_array['$count'] = $count;
        $data_array['$demoID'] = $demoID;
        $data_array['$tag1'] = $tag1;
        $data_array['$tag2'] = $tag2;
        $sc_demos = $GLOBALS["_template"]->replaceTemplate("sc_demos", $data_array);
        echo $sc_demos;

        $n++;
    }
    echo '</ul>';
}
