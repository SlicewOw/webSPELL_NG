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

$_language->readModule('squads');

$title_squads = $GLOBALS["_template"]->replaceTemplate("title_squads", array());
echo $title_squads;
if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = "";
}
if ($action == "show") {
    if ($_GET[ 'squadID' ]) {
        $getsquad = 'WHERE squadID="' . (int)$_GET[ 'squadID' ] . '"';
    } else {
        $getsquad = '';
    }

    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "squads " . $getsquad . " ORDER BY sort");
    while ($ds = mysqli_fetch_array($ergebnis)) {
        $anzmembers = mysqli_num_rows(
            safe_query(
                "SELECT sqmID FROM " . PREFIX . "squads_members WHERE squadID='" . (int)$ds[ 'squadID' ] . "'"
            )
        );
        if ($anzmembers == 1) {
            $anzmembers = $anzmembers . ' ' . $_language->module[ 'member' ];
        } else {
            $anzmembers = $anzmembers . ' ' . $_language->module[ 'members' ];
        }
        $name = $ds[ 'name' ];
        $squadID = $ds[ 'squadID' ];
        $backlink = '<a href="index.php?site=squads"><strong>' . $_language->module[ 'back_squad_overview' ] .
            '</strong></a>';
        $results = '';
        $awards = '';
        $challenge = '';
        $games = '';

        #$border = BORDER;

        if ($ds[ 'gamesquad' ]) {
            $results = '<a href="index.php?site=clanwars&amp;action=showonly&amp;id=' . $squadID .
                '&amp;sort=date&amp;only=squad" class="btn btn-primary">' . $_language->module[ 'results' ] . '</a>';
            $awards = '<a href="index.php?site=awards&amp;action=showsquad&amp;squadID=' . $squadID .
                '&amp;page=1" class="btn btn-primary">' . $_language->module[ 'awards' ] . '</a>';
            $challenge =
                '<a href="index.php?site=challenge" class="btn btn-primary">' . $_language->module[ 'challenge' ] .
                '</a>';
            $games = $ds[ 'games' ];
            if ($games) {
                $games = str_replace(";", ", ", $games);
                $games = generateAlert( $_language->module[ 'squad_plays' ] . ": " . $games, 'alert-info');
            }
        }

        $member = safe_query(
            "SELECT
                *
            FROM
                " . PREFIX . "squads_members s,
                " . PREFIX . "user u
            WHERE
                s.squadID='" . $ds[ 'squadID' ] . "' AND
                s.userID = u.userID
            ORDER BY
                sort"
        );
        $data_array = array();
        $data_array['$name'] = $name;
        $data_array['$anzmembers'] = $anzmembers;
        $data_array['$results'] = $results;
        $data_array['$awards'] = $awards;
        $data_array['$challenge'] = $challenge;
        $data_array['$games'] = $games;
        $squads_head = $GLOBALS["_template"]->replaceTemplate("squads_head", $data_array);
        echo $squads_head;

        while ($dm = mysqli_fetch_array($member)) {

            $country = '[flag]' . $dm[ 'country' ] . '[/flag]';
            $country = flags($country);
            $nickname = '<a href="index.php?site=profile&amp;id=' . $dm[ 'userID' ] . '"><strong>' .
                strip_tags(stripslashes($dm[ 'nickname' ])) . '</strong></a>';
            $nicknamee = strip_tags(stripslashes($dm[ 'nickname' ]));
            $profilid = $dm[ 'userID' ];

            if ($dm[ 'userdescription' ]) {
                $userdescription = htmloutput($dm[ 'userdescription' ]);
            } else {
                $userdescription = $_language->module[ 'no_description' ];
            }

            if ($dm[ 'userpic' ] != "" && file_exists("images/userpics/" . $dm[ 'userpic' ])) {
                $userpic = $dm[ 'userpic' ];
                $pic_info = $dm[ 'nickname' ] . ' ' . $_language->module[ 'userpicture' ];
            } else {
                $userpic = "nouserpic.gif";
                $pic_info = $_language->module[ 'no_userpic' ];
            }

            $icq = $dm[ 'icq' ];
            if (getemailhide($dm[ 'userID' ])) {
                $email = '';
            } else {
                $email =
                    '<a href="mailto:' . mail_protect($dm[ 'email' ]) . '"><span class="fa fa-envelope"
                    title="' . $_language->module[ 'email' ] . '"></span></a>';
            }

            $pm = '';
            $buddy = '';
            if ($loggedin && $dm[ 'userID' ] != $userID) {
                $pm = '<a href="index.php?site=messenger&amp;action=touser&amp;touser=' . $dm[ 'userID' ] .
                    '"><span class="fa fa-envelope"></span></a>';

                if (isignored($userID, $dm[ 'userID' ])) {
                    $buddy = '<a href="buddies.php?action=readd&amp;id=' . $dm[ 'userID' ] . '&amp;userID=' . $userID .
                        '"><span class="fa fa-user-plus"></span></a>';
                } else if (isbuddy($userID, $dm[ 'userID' ])) {
                    $buddy = '<a href="buddies.php?action=ignore&amp;id=' . $dm[ 'userID' ] . '&amp;userID=' . $userID .
                        '"><span class="fa fa-user-times"></span></a>';
                } else {
                    $buddy = '<a href="buddies.php?action=add&amp;id=' . $dm[ 'userID' ] . '&amp;userID=' . $userID .
                        '"><span class="fa fa-user-plus"></span></a>';
                }
            }

            if (isonline($dm[ 'userID' ]) == "offline") {
                $statuspic = '<span class="fa fa-circle text-danger" aria-hidden="true"></span>';
            } else {
                $statuspic = '<span class="fa fa-circle text-success" aria-hidden="true"></span>';
            }

            $position = $dm[ 'position' ];
            $firstname = strip_tags($dm[ 'firstname' ]);
            $lastname = strip_tags($dm[ 'lastname' ]);
            $town = strip_tags($dm[ 'town' ]);
            if ($dm[ 'activity' ]) {
                $activity = '<span class="label label-success">' . $_language->module[ 'active' ] . '</span>';
            } else {
                $activity = '<span class="label label-warning">' . $_language->module[ 'inactive' ] . '</span>';
            }

            $data_array = array();
            $data_array['$country'] = $country;
            $data_array['$firstname'] = $firstname;
            $data_array['$nickname'] = $nickname;
            $data_array['$lastname'] = $lastname;
            $data_array['$statuspic'] = $statuspic;
            $data_array['$position'] = $position;
            $data_array['$activity'] = $activity;
            $data_array['$email'] = $email;
            $data_array['$pm'] = $pm;
            $data_array['$buddy'] = $buddy;
            $data_array['$town'] = $town;
            $data_array['$memberID'] = $dm['userID'];
            $data_array['$userpic'] = $userpic;
            $data_array['$nicknamee'] = $nicknamee;
            $data_array['$userdescription'] = $userdescription;
            $squads_content = $GLOBALS["_template"]->replaceTemplate("squads_content", $data_array);
            echo $squads_content;

        }
        $squads_foot = $GLOBALS["_template"]->replaceTemplate("squads_foot", array());
        echo $squads_foot;
    }
} else {
    $getsquad = "";
    if (isset($_GET[ 'squadID' ])) {
        $getsquad = 'WHERE squadID="' . (int)$_GET[ 'squadID' ] . '"';
    }

    $ergebnis = safe_query("SELECT * FROM `" . PREFIX . "squads` " . $getsquad . " ORDER BY sort");

    if (mysqli_num_rows($ergebnis)) {
        $i = 1;
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $anzmembers = mysqli_num_rows(
                safe_query(
                    "SELECT
                        sqmID
                    FROM
                        " . PREFIX . "squads_members
                    WHERE
                        squadID='" . (int)$ds[ 'squadID' ] . "'"
                )
            );
            if ($anzmembers == 1) {
                $anzmembers = $anzmembers . ' ' . $_language->module[ 'member' ];
            } else {
                $anzmembers = $anzmembers . ' ' . $_language->module[ 'members' ];
            }
            $name =
                '<a href="index.php?site=squads&amp;action=show&amp;squadID=' . $ds[ 'squadID' ] . '"><strong>' .
                $ds[ 'name' ] . '</strong></a>';
            if ($ds[ 'icon' ]) {
                $icon = '<a href="index.php?site=squads&amp;action=show&amp;squadID=' . $ds[ 'squadID' ] .
                    '"><img class="img-responsive" src="images/squadicons/' . $ds[ 'icon' ] . '" alt="' . htmlspecialchars($ds[ 'name' ]) .
                    '"></a>';
            } else {
                $icon = '';
            }
            $info = htmloutput($ds[ 'info' ]);
            $details = '<a href="index.php?site=squads&amp;action=show&amp;squadID=' . $ds[ 'squadID' ] . '"><strong>' .
                $_language->module[ 'show_details' ] . '</strong></a>';
            $squadID = $ds[ 'squadID' ];
            $results = '';
            $awards = '';
            $challenge = '';

            if ($ds[ 'gamesquad' ]) {
                $results = '<a href="index.php?site=clanwars&amp;action=showonly&amp;id=' . $squadID .
                    '&amp;sort=date&amp;only=squad" class="btn btn-primary">' .
                    $_language->module[ 'results' ] . '</a>';
                $awards = '<a href="index.php?site=awards&amp;action=showsquad&amp;squadID=' . $squadID .
                    '&amp;page=1" class="btn btn-primary">' . $_language->module[ 'awards' ] . '</a>';
                $challenge =
                    '<a href="index.php?site=challenge" class="btn btn-primary">' . $_language->module[ 'challenge' ] .
                    '</a>';
            }

            $data_array = array();
            $data_array['$icon'] = $icon;
            $data_array['$name'] = $name;
            $data_array['$anzmembers'] = $anzmembers;
            $data_array['$results'] = $results;
            $data_array['$awards'] = $awards;
            $data_array['$challenge'] = $challenge;
            $data_array['$info'] = $info;
            $data_array['$details'] = $details;
            $squads = $GLOBALS["_template"]->replaceTemplate("squads", $data_array);
            echo $squads;

            $i++;
        }
    } else {
        echo generateAlert($_language->module['no_entries'], 'alert-info');
    }

}
