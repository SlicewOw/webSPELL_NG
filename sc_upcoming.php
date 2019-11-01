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

if (isset($site)) {
    $_language->readModule('sc_upcoming');
}
$now = time();
$ergebnis = safe_query("SELECT * FROM " . PREFIX . "upcoming WHERE date>= $now ORDER BY date LIMIT 0, " . $maxupcoming);

while ($ds = mysqli_fetch_array($ergebnis)) {
    echo '<ul class="list-group">';
    if ($ds[ 'type' ] == "c") {
        $date = getformatdate($ds[ 'date' ]);
        $upsquad = getsquadname($ds[ 'squad' ]);

        $upurl =
            'index.php?site=calendar&amp;tag=' . date("d", $ds[ 'date' ]) . '&amp;month=' . date("m", $ds[ 'date' ]) .
            '&amp;year=' . date("Y", $ds[ 'date' ]);

        $opponent = $ds[ 'opponent' ];

        $data_array = array();
        $data_array['$date'] = $date;
        $data_array['$upurl'] = $upurl;
        $data_array['$opponent'] = $opponent;
        $upcomingactions = $GLOBALS["_template"]->replaceTemplate("upcomingactions", $data_array);
        echo $upcomingactions;
    } else {
        $date = getformatdate($ds[ 'date' ]);
        $country = "[flag]" . $ds[ 'country' ] . "[/flag]";
        $country = flags($country);

        $upurl =
            'index.php?site=calendar&amp;tag=' . date("d", $ds[ 'date' ]) . '&amp;month=' . date("m", $ds[ 'date' ]) .
            '&amp;year=' . date("Y", $ds[ 'date' ]);

        $eventtitle = $ds[ 'title' ];

        $data_array = array();
        $data_array['$date'] = $date;
        $data_array['$country'] = $country;
        $data_array['$upurl'] = $upurl;
        $data_array['$eventtitle'] = $eventtitle;
        $upcomingevent = $GLOBALS["_template"]->replaceTemplate("upcomingevent", $data_array);
        echo $upcomingevent;
    }

    echo '</ul>';
}
$anzahl = '';
