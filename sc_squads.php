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

$ergebnis = safe_query("SELECT * FROM " . PREFIX . "squads WHERE gamesquad = '1' ORDER BY sort");
if (mysqli_num_rows($ergebnis)) {
    echo '<ul class="list-group">';
    while ($db = mysqli_fetch_array($ergebnis)) {
        if (!empty($db[ 'icon_small' ])) {
            $squadicon = '<img src="images/squadicons/' . $db[ 'icon_small' ] . '" alt="' .
                getinput($db[ 'name' ]) . '" title="' . getinput($db[ 'name' ]) . '" class="img-responsive">';
        } else {
            $squadicon = '';
        }
        $squadname = getinput($db[ 'name' ]);
        $data_array = array();
        $data_array['$squadicon'] = $squadicon;
        $data_array['$squadID'] = $db['squadID'];
        $data_array['$squadname'] = $squadname;
        $sc_squads = $GLOBALS["_template"]->replaceTemplate("sc_squads", $data_array);
        echo $sc_squads;
    }
    echo '</ul>';
}
