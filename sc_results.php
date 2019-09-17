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

$ergebnis = safe_query("SELECT * FROM " . PREFIX . "clanwars ORDER BY date DESC LIMIT 0, " . $maxresults);
if (mysqli_num_rows($ergebnis)) {
    echo '<ul class="list-group">';
    $n = 1;
    while ($ds = mysqli_fetch_array($ergebnis)) {
        $date = getformatdate($ds[ 'date' ]);
        $homescr = array_sum(unserialize($ds[ 'homescore' ]));
        $oppscr = array_sum(unserialize($ds[ 'oppscore' ]));

        if ($homescr > $oppscr) {
            $result = '<span style="color: ' . $wincolor . '">' . $homescr . ':' . $oppscr . '</span>';
        } elseif ($homescr < $oppscr) {
            $result = '<span style="color: ' . $loosecolor . '">' . $homescr . ':' . $oppscr . '</span>';
        } else {
            $result = '<span style="color: ' . $drawcolor . '">' . $homescr . ':' . $oppscr . '</span>';
        }

        $resultID = $ds[ 'cwID' ];
        $gameicon = 'images/games/'.is_gamefilexist('images/games/', $ds[ 'game' ]);

        $data_array = array();
        $data_array['$result'] = $result;
        $data_array['$gameicon'] = $gameicon;
        $data_array['$game'] = $ds['game'];
        $data_array['$opptag'] = $ds['opptag'];
        $data_array['$resultID'] = $resultID;
        $results = $GLOBALS["_template"]->replaceTemplate("results", $data_array);
        echo $results;
        $n++;
    }
    echo '</ul>';
}
