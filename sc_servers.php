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
$result = safe_query("SELECT * FROM " . PREFIX . "servers ORDER BY sort");
$n = 1;
while ($row = mysqli_fetch_array($result)) {
    echo '<ul class="list-group">';

    $servername = htmloutput($row[ 'name' ]);
    $serverip = $row[ 'ip' ];
    $servergame = 'images/games/'.is_gamefilexist('images/games/', $row[ 'game' ]);


    $data_array = array();
    $data_array['$servergame'] = $servergame;
    $data_array['$serverip'] = $serverip;
    $data_array['$servername'] = $servername;
    $sc_servers = $GLOBALS["_template"]->replaceTemplate("sc_servers", $data_array);
    echo $sc_servers;
    $n++;

    echo '</ul>';
}
