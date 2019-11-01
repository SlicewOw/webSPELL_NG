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
$_language->readModule('login');
$login_lang = $_language->module;
$_language->readModule('loginoverview');

if ($userID && !isset($_GET[ 'userID' ]) && !isset($_POST[ 'userID' ])) {
    $title_loginoverview = $GLOBALS["_template"]->replaceTemplate("title_loginoverview", array());
    echo $title_loginoverview;

    $ds = mysqli_fetch_array(safe_query("SELECT registerdate FROM `" . PREFIX . "user` WHERE userID='" . $userID . "'"));
    $username = '<a href="index.php?site=profile&amp;id=' . $userID . '">' . getnickname($userID) . '</a>';
    $lastlogin = getformatdatetime($_SESSION[ 'ws_lastlogin' ]);
    $registerdate = getformatdatetime($ds[ 'registerdate' ]);

    //messages?
    $newmessages = getnewmessages($userID);
    if ($newmessages == 1) {
        $newmessages = $_language->module[ 'one_new_message' ];
    } else if ($newmessages > 1) {
        $newmessages = str_replace('%new_messages%', $newmessages, $_language->module[ 'x_new_message' ]);
    } else {
        $newmessages = $_language->module[ 'no_new_messages' ];
    }

    //boardposts?

    $posts = safe_query(
        "SELECT
            p.topicID,
            p.date,
            p.message,
            p.boardID,
            t.topic,
            t.readgrps
        FROM
            `" . PREFIX . "forum_posts` AS p,
            `" . PREFIX . "forum_topics` AS t
        WHERE
            p.date>" . $_SESSION[ 'ws_lastlogin' ] . "
        AND
            p.topicID = t.topicID
        LIMIT
            0, 10"
    );
    $topics = safe_query(
        "SELECT
            *
        FROM
            " . PREFIX . "forum_topics
        WHERE
            date > " . $_SESSION[ 'ws_lastlogin' ] . " LIMIT 0, 10"
    );

    $new_posts = mysqli_num_rows(
        safe_query(
            "SELECT
                p.postID
            FROM
                `" . PREFIX . "forum_posts` AS p, `" . PREFIX . "forum_topics` AS t
            WHERE
                p.date>" . $_SESSION[ 'ws_lastlogin' ] . "
            AND
                p.topicID = t.topicID"
        )
    );
    $new_topics = mysqli_num_rows(
        safe_query(
            "SELECT
                *
            FROM
                " . PREFIX . "forum_topics
            WHERE
                date > " . $_SESSION[ 'ws_lastlogin' ] .""
        )
    );

    //new topics

    $topiclist = "";
    if (mysqli_num_rows($topics)) {
        $n = 1;
        while ($db = mysqli_fetch_array($topics)) {
            if ($db[ 'readgrps' ] != "") {
                $usergrps = explode(";", $db[ 'readgrps' ]);
                $usergrp = 0;
                foreach ($usergrps as $value) {
                    if (isinusergrp($value, $userID)) {
                        $usergrp = 1;
                        break;
                    }
                }
                if (!$usergrp && !ismoderator($userID, $db[ 'boardID' ])) {
                    continue;
                }
            }
            $posttime = getformatdatetime($db[ 'date' ]);

            $topiclist .= '<tr>
                <td>' . $posttime . '</td>
                <td>
                    <a href="index.php?site=forum_topic&amp;topic=' . $db[ 'topicID' ] . '">' .
                        str_break(getinput($db[ 'topic' ]), 34) . '</a>
                </td>
                <td>' . $db[ 'views' ] . ' ' . $_language->module[ 'views' ] . '</td>
                <td>' . $db[ 'replys' ] . ' ' . $_language->module[ 'replys' ] . '</td>
            </tr>';

            $n++;
        }
    } else {
        $topiclist = '<tr><td colspan="4">' . $_language->module[ 'no_new_topics' ] . '</td></tr>';
    }

    //new posts

    $postlist = "";
    if (mysqli_num_rows($posts)) {
        $n = 1;
        while ($db = mysqli_fetch_array($posts)) {
            if ($db[ 'readgrps' ] != "") {
                $usergrps = explode(";", $db[ 'readgrps' ]);
                $usergrp = 0;
                foreach ($usergrps as $value) {
                    if (isinusergrp($value, $userID)) {
                        $usergrp = 1;
                        break;
                    }
                }
                if (!$usergrp && !ismoderator($userID, $db[ 'boardID' ])) {
                    continue;
                }
            }
            $posttime = getformatdatetime($db[ 'date' ]);
            if (mb_strlen($db[ 'message' ]) > 100) {
                $message = mb_substr(
                    $db[ 'message' ],
                    0,
                    90 + mb_strpos(
                        mb_substr(
                            $db[ 'message' ],
                            90,
                            mb_strlen($db[ 'message' ])
                        ),
                        " "
                    )
                )
                . "...";
            } else {
                $message = $db[ 'message' ];
            }

            $postlist .= '<tr>
                <td><a href="index.php?site=forum_topic&amp;topic=' . $db[ 'topicID' ] . '">' .
                    str_break(getinput($db[ 'topic' ]), 34) . '</a>
                </td>
                <td>' . $posttime . '</td>
                <td>' . str_break(clearfromtags($message), 34) . '</td>
            </tr>';

            $n++;
        }
    } else {
        $postlist = '<tr><td colspan="3">' . $_language->module[ 'no_new_posts' ] . '</td></tr>';
    }

    //clanmember/admin/referer

    if (isclanmember($userID)) {
        $cashboxpic =
            '<a class="thumbnail text-center" href="index.php?site=cashbox"><span class="fa fa-money fa-4x" alt="Cashbox"></span><br>'.$login_lang[ 'cash-box' ].'</a>';
    } else {
        $cashboxpic = '';
    }

    if (isanyadmin($userID)) {
        $admincenterpic =
            '<a class="thumbnail text-center" href="admin/admincenter.php" target="_blank">
                <span class="fa fa-cogs fa-4x" alt="Admincenter"></span><br>
                '.$login_lang[ getConstNameAdmin() ].'
            </a>';
    } else {
        $admincenterpic = '';
    }

    if (isset($_SESSION[ 'referer' ])) {
        $referer_uri = '<a class="btn" href="' . $_SESSION[ 'referer' ] . '">
            <span class="fa fa-chevron-left"></span> ' .
            $_language->module[ 'back_last_page' ] . '</a>';
        unset($_SESSION[ 'referer' ]);
    } else {
        $referer_uri = '';
    }

    //upcoming
    $clanwars = '';
    if (isclanmember($userID)) {
        $clanwars .= "<h4>" . $_language->module[ 'upcoming_clanwars' ] . "</h4>";

        $squads = safe_query("SELECT squadID FROM `" . PREFIX . "squads_members` WHERE userID='" . $userID . "'");
        while ($squad = mysqli_fetch_array($squads)) {
            if (isgamesquad($squad[ 'squadID' ])) {
                $dn = mysqli_fetch_array(
                    safe_query(
                        "SELECT
                            name
                        FROM
                            `" . PREFIX . "squads`
                        WHERE
                            squadID='" . $squad[ 'squadID' ] . "'
                        AND
                            gamesquad='1'"
                    )
                );
                $clanwars .= '<h5>' . $_language->module[ 'squad' ] . ': ' . $dn[ 'name' ] . '</h5>';
                $n = 1;
                $ergebnis = safe_query(
                    "SELECT
                        *
                    FROM
                        `" . PREFIX . "upcoming`
                    WHERE
                        type='c'
                    AND
                        squad='" . $squad[ 'squadID' ] . "'
                    AND
                        date>" . time() . "
                    ORDER BY
                        date"
                );
                $anz = mysqli_num_rows($ergebnis);

                if ($anz) {
                    $clanwars .= '<table class="table table-hover">
                    <thead>
                        <tr>
                            <th>' . $_language->module[ 'date' ] . '</th>
                            <th>' . $_language->module[ 'against' ] . '</th>
                            <th>' . $_language->module[ 'announcement' ] . '</th>
                            <th>' . $_language->module[ 'announce' ] . '</th>
                        </tr>
                    </thead><tbody>';

                    while ($ds = mysqli_fetch_array($ergebnis)) {

                        $date = getformatdate($ds[ 'date' ]);

                        $anmeldung =
                            safe_query(
                                "SELECT
                                    *
                                FROM
                                    " . PREFIX . "upcoming_announce
                                WHERE
                                    upID='" . $ds[ 'upID' ] . "'"
                            );
                        if (mysqli_num_rows($anmeldung)) {
                            $i = 1;
                            $players = "";
                            while ($da = mysqli_fetch_array($anmeldung)) {
                                if ($da[ 'status' ] == "y") {
                                    $fontcolor = "label-success";
                                } else if ($da[ 'status' ] == "n") {
                                    $fontcolor = "label-important";
                                } else {
                                    $fontcolor = "label-warning";
                                }

                                if ($i > 1) {
                                    $players .= ', <a href="index.php?site=profile&amp;id=' . $da[ 'userID' ] .
                                        '"><span class="label ' . $fontcolor . '">' .
                                        strip_tags(stripslashes(getnickname($da[ 'userID' ]))) . '</span></a>';
                                } else {
                                    $players .= '<a href="index.php?site=profile&amp;id=' . $da[ 'userID' ] .
                                        '"><span class="label ' . $fontcolor . '">' .
                                        strip_tags(stripslashes(getnickname($da[ 'userID' ]))) . '</span></a>';
                                }
                                $i++;
                            }
                        } else {
                            $players = $_language->module[ 'no_players_announced' ];
                        }

                        $tag = date("d", $ds[ 'date' ]);
                        $monat = date("m", $ds[ 'date' ]);
                        $yahr = date("Y", $ds[ 'date' ]);

                        $clanwars .= '<tr>
                            <td>' . $date . '</td>
                            <td><a href="' . $ds[ 'opphp' ] . '" target="_blank">' . $ds[ 'opptag' ] . ' / ' .
                            $ds[ 'opponent' ] . '</a></td>
                            <td>' . $players . '</td>
                            <td><a href="index.php?site=calendar&amp;action=announce&amp;upID=' . $ds[ 'upID' ] .
                            '&amp;tag=' . $tag . '&amp;month=' . $monat . '&amp;year=' . $yahr . '#event">' .
                            $_language->module[ 'click' ] . '</a></td>
                        </tr>';
                        $n++;
                    }
                    $clanwars .= '</tbody></table>';
                } else {
                    $clanwars .= $_language->module[ 'no_entries' ];
                }
            }
        }
    }
    unset($events);

    $events = '';
    $ergebnis =
        safe_query("SELECT * FROM `" . PREFIX . "upcoming` WHERE type='d' AND date>" . time() . " ORDER by date");
    $anz = mysqli_num_rows($ergebnis);
    if ($anz) {
        $n = 1;
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $events .= '<tr>
                <td>' . $ds[ 'title' ] . '</td>
                <td>' . date('d.m.y, H:i', $ds[ 'date' ]) . '</td>
                <td>' . date('d.m.y, H:i', $ds[ 'enddate' ]) . '</td>
                <td>' . $ds[ 'location' ] . '</td>
                <td><a href="index.php?site=calendar&amp;tag=' . date('d', $ds[ 'date' ]) . '&amp;month=' .
                date('m', $ds[ 'date' ]) . '&amp;year=' . date('Y', $ds[ 'date' ]) . '#event">' .
                $_language->module[ 'click' ] . '</a></td>
            </tr>';
            $n++;
        }
    } else {
        $events = '<tr>
        <td colspan="5"><span>' . $_language->module[ 'no_events' ] . '</span></td>
    </tr>';
    }

    $data_array = array();
    $data_array['$username'] = $username;
    $data_array['$lastlogin'] = $lastlogin;
    $data_array['$registerdate'] = $registerdate;
    $data_array['$newmessages'] = $newmessages;
    $data_array['$new_topics'] = $new_topics;
    $data_array['$new_posts'] = $new_posts;
    $data_array['$referer_uri'] = $referer_uri;
    $data_array['$clanwars'] = $clanwars;
    $data_array['$events'] = $events;
    $data_array['$cashboxpic'] = $cashboxpic;
    $data_array['$admincenterpic'] = $admincenterpic;
    $data_array['$topiclist'] = $topiclist;
    $data_array['$postlist'] = $postlist;
    $data_array['%buddy_list%'] = $login_lang[ 'buddy_list' ];
    $data_array['%messenger%'] = $login_lang[ 'messenger' ];
    $data_array['%edit_account%'] = $login_lang[ 'edit_account' ];
    $data_array['%logout%'] = $login_lang[ 'logout' ];
    $loginoverview = $GLOBALS["_template"]->replaceTemplate("loginoverview", $data_array);
    echo $loginoverview;
} else {
    echo $_language->module[ 'you_have_to_be_logged_in' ];
}
