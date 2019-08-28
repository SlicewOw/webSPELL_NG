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

if (isset($_POST[ 'board' ])) {
    $board = (int)$_POST[ 'board' ];
} elseif (isset($_GET[ 'board' ])) {
    $board = (int)$_GET[ 'board' ];
} else {
    $board = null;
}

if (!isset($_GET[ 'page' ])) {
    $page = '';
} else {
    $page = (int)$_GET[ 'page' ];
}
if (!isset($_GET[ 'action' ])) {
    $action = '';
} else {
    $action = $_GET[ 'action' ];
}

function forum_stats()
{
    #$pagebg = PAGEBG;
    #$border = BORDER;
    #$bghead = BGHEAD;
    #$bgcat = BGCAT;
    #$bg1 = BG_1;
    global $wincolor;
    global $loosecolor;
    global $drawcolor;
    global $_language;

    $_language->readModule('forum');

    // TODAY birthdays
    $ergebnis = safe_query(
        "SELECT
            nickname, userID, YEAR(CURRENT_DATE()) -YEAR(birthday) 'age'
        FROM
            " . PREFIX . "user
        WHERE
            DATE_FORMAT(`birthday`, '%m%d') = DATE_FORMAT(NOW(), '%m%d')"
    );
    $n = 0;
    $birthdays = '';
    while ($db = mysqli_fetch_array($ergebnis)) {
        $n++;
        $years = $db[ 'age' ];
        if ($n > 1) {
            $birthdays .= ', <a href="index.php?site=profile&amp;id=' . $db[ 'userID' ] . '"><b>' . $db[ 'nickname' ] .
                '</b></a> (' . $years . ')';
        } else {
            $birthdays = '<a href="index.php?site=profile&amp;id=' . $db[ 'userID' ] . '"><b>' . $db[ 'nickname' ] .
                '</b></a> (' . $years . ')';
        }
    }
    if (!$n) {
        $birthdays = $_language->module[ 'n_a' ];
    }

    // WEEK birthdays
    $ergebnis =
        safe_query(
            "SELECT
                nickname, userID, DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(birthday)), '%y') + 1 AS age
            FROM
                " . PREFIX . "user
            WHERE
                IF(DAYOFYEAR(NOW())<=358,((DAYOFYEAR(birthday)>DAYOFYEAR(NOW()))
            AND
                (DAYOFYEAR(birthday)<=DAYOFYEAR(DATE_ADD(NOW(), INTERVAL 7 DAY)))),(DAYOFYEAR(BIRTHDAY)>DAYOFYEAR(NOW())
            OR
                DAYOFYEAR(birthday)<=DAYOFYEAR(DATE_ADD(NOW(), INTERVAL 7 DAY))))
            AND
                birthday !='0000-00-00 00:00:00'
            ORDER BY
                `birthday` ASC"
        );
    $n = 0;
    $birthweek = '';
    while ($db = mysqli_fetch_array($ergebnis)) {
        $n++;
        $years = $db[ 'age' ];
        if ($n > 1) {
            $birthweek .= ', <a href="index.php?site=profile&amp;id=' . $db[ 'userID' ] . '"><b>' . $db[ 'nickname' ] .
                '</b></a> (' . $years . ')';
        } else {
            $birthweek = '<a href="index.php?site=profile&amp;id=' . $db[ 'userID' ] . '"><b>' . $db[ 'nickname' ] .
                '</b></a> (' . $years . ')';
        }
    }
    if (!$n) {
        $birthweek = $_language->module[ 'n_a' ];
    }

    // WHOISONLINE
    $guests = mysqli_num_rows(safe_query("SELECT ip FROM " . PREFIX . "whoisonline WHERE userID=''"));
    $user = mysqli_num_rows(safe_query("SELECT userID FROM " . PREFIX . "whoisonline WHERE ip=''"));
    $useronline = $guests + $user;

    if ($user == 1) {
        $user_on = $_language->module[ 'registered_user' ];
    } else {
        $user_on = $user . ' ' . $_language->module[ 'registered_users' ];
    }

    if ($guests == 1) {
        $guests_on = $_language->module[ 'guest' ];
    } else {
        $guests_on = $guests . ' ' . $_language->module[ 'guests' ];
    }

    $ergebnis = safe_query(
        "SELECT
            w.*, u.nickname
        FROM
            " . PREFIX . "whoisonline w
        LEFT JOIN
            " . PREFIX . "user u
        ON
            u.userID = w.userID
        WHERE
            w.ip=''
        ORDER BY
            u.nickname"
    );
    $user_names = "";
    if ($user) {
        $n = 1;
        while ($ds = mysqli_fetch_array($ergebnis)) {
            if (isforumadmin($ds[ 'userID' ])) {
                $nickname = '<span class="label label-danger">' . $ds[ 'nickname' ] . '</span>';
            } elseif (isanymoderator($ds[ 'userID' ])) {
                $nickname = '<span class="label label-warning">' . $ds[ 'nickname' ] . '</span>';
            } elseif (isclanmember($ds[ 'userID' ])) {
                $nickname = '<span class="label label-success">' . $ds[ 'nickname' ] . '</span>';
            } else {
                $nickname = $ds[ 'nickname' ];
            }
            if ($n > 1) {
                $user_names .= ', <a href="index.php?site=profile&amp;id=' . $ds[ 'userID' ] . '"><b>' . $nickname .
                    '</b></a>';
            } else {
                $user_names =
                    '<a href="index.php?site=profile&amp;id=' . $ds[ 'userID' ] . '"><strong>' .
                    $nickname . '</strong></a>';
            }
            $n++;
        }
    }

    $dt = mysqli_fetch_array(safe_query("SELECT sum(topics), sum(posts) FROM " . PREFIX . "forum_boards"));
    $topics = $dt[ 0 ];
    $posts = $dt[ 1 ];
    $dt = mysqli_fetch_array(safe_query("SELECT count(userID) FROM " . PREFIX . "user WHERE activated='1'"));
    $registered = $dt[ 0 ];
    $newestuser = safe_query(
        "SELECT userID, nickname FROM " . PREFIX .
        "user WHERE activated='1' ORDER BY registerdate DESC LIMIT 0,1"
    );
    $dn = mysqli_fetch_array($newestuser);
    $dm = mysqli_fetch_array(safe_query("SELECT maxonline FROM " . PREFIX . "counter"));
    $maxonline = $dm[ 'maxonline' ];

    $newestmember =
        '<a href="index.php?site=profile&amp;id=' . $dn[ 'userID' ] . '"><strong>' .
        $dn[ 'nickname' ] . '</strong></a>';
    $data_array = array();
    $data_array['$birthdays'] = $birthdays;
    $data_array['$birthweek'] = $birthweek;
    $data_array['$user_on'] = $user_on;
    $data_array['$guests_on'] = $guests_on;
    $data_array['$maxonline'] = $maxonline;
    $data_array['$user_names'] = $user_names;
    $data_array['$posts'] = $posts;
    $data_array['$topics'] = $topics;
    $data_array['$registered'] = $registered;
    $data_array['$newestmember'] = $newestmember;
    $forum_stats = $GLOBALS["_template"]->replaceTemplate("forum_stats", $data_array);
    echo $forum_stats;
}

function boardmain()
{
    global $maxposts;
    global $userID;
    global $action;
    global $loggedin;
    global $_language;
    global $maxtopics;

    $_language->readModule('forum');

    #$pagebg = PAGEBG;
    #$border = BORDER;
    #$bghead = BGHEAD;
    #$bgcat = BGCAT;

    $title_messageboard = $GLOBALS["_template"]->replaceTemplate("title_messageboard", array());
    echo $title_messageboard;

    if ($action == "markall") {
        safe_query("UPDATE " . PREFIX . "user SET topics='|' WHERE userID='$userID'");
    }

    $forum_main_head = $GLOBALS["_template"]->replaceTemplate("forum_main_head", array());
    echo $forum_main_head;

    // KATEGORIEN
    $sql_where = '';
    if (isset($_GET[ 'cat' ]) && is_numeric($_GET[ 'cat' ])) {
        $sql_where = " WHERE catID='" . (int)$_GET[ 'cat' ] . "'";
    }
    $kath = safe_query(
        "SELECT catID, name, info, readgrps FROM " . PREFIX . "forum_categories" . $sql_where .
        " ORDER BY sort"
    );
    while ($dk = mysqli_fetch_array($kath)) {
        $kathname = "<a href='index.php?site=forum&amp;cat=" . $dk[ 'catID' ] . "'>" . $dk[ 'name' ] . "</a>";
        if ($dk[ 'info' ]) {
            $info = $dk[ 'info' ];
        } else {
            $info = '';
        }

        if ($dk[ 'readgrps' ] != "") {
            $usergrp = 0;
            $readgrps = explode(";", $dk[ 'readgrps' ]);
            foreach ($readgrps as $value) {
                if (isinusergrp($value, $userID)) {
                    $usergrp = 1;
                    break;
                }
            }

            if (!$usergrp) {
                continue;
            }
        }
        $data_array = array();
        $data_array['$kathname'] = $kathname;
        $data_array['$info'] = $info;
        $forum_main_kath = $GLOBALS["_template"]->replaceTemplate("forum_main_kath", $data_array);
        echo $forum_main_kath;

        // BOARDS MIT KATEGORIE
        $boards = safe_query(
            "SELECT * FROM " . PREFIX . "forum_boards WHERE category='" . $dk[ 'catID' ] .
            "' ORDER BY sort"
        );
        $i = 1;

        while ($db = mysqli_fetch_array($boards)) {
            #if ($i % 2) {
            #    $bg1 = BG_1;
            #    $bg2 = BG_2;
            #} else {
            #    $bg1 = BG_3;
            #    $bg2 = BG_4;
            #}

            $ismod = ismoderator($userID, $db[ 'boardID' ]);
            $usergrp = 0;
            $writer = 'ro-';
            if ($db[ 'writegrps' ] != "" && !$ismod) {
                $writegrps = explode(";", $db[ 'writegrps' ]);
                foreach ($writegrps as $value) {
                    if (isinusergrp($value, $userID)) {
                        $usergrp = 1;
                        $writer = '';
                        break;
                    }
                }
            } else {
                $writer = '';
            }
            if ($db[ 'readgrps' ] != "" && !$usergrp && !$ismod) {
                $readgrps = explode(";", $db[ 'readgrps' ]);
                foreach ($readgrps as $value) {
                    if (isinusergrp($value, $userID)) {
                        $usergrp = 1;
                        break;
                    }
                }
                if (!$usergrp) {
                    continue;
                }
            }

            $board = $db[ 'boardID' ];
            $anztopics = $db[ 'topics' ];
            $anzposts = $db[ 'posts' ];
            $boardname = '<a href="index.php?site=forum&amp;board=' . $board . '"><strong>' .
                $db[ 'name' ] . '</strong></a>';

            if ($db[ 'info' ]) {
                $boardinfo = $db[ 'info' ];
            } else {
                $boardinfo = '';
            }
            $moderators = getmoderators($db[ 'boardID' ]);
            if ($moderators) {
                $moderators = $_language->module[ 'moderated_by' ] . ': ' . $moderators;
            }
			
			

            $postlink = '';
            $date = '';
            $time = '';
            $poster = '';
            $member = '';

            $q = safe_query(
                "SELECT topicID, lastdate, lastposter, replys FROM " . PREFIX .
                "forum_topics WHERE boardID='" . $db[ 'boardID' ] . "' AND moveID='0' ORDER BY lastdate DESC LIMIT 0," .
                $maxtopics
            );
            $n = 1;
            $board_topics = array();
            while ($lp = mysqli_fetch_assoc($q)) {
                if ($n == 1) {
                    $date = getformatdate($lp[ 'lastdate' ]);
                    $today = getformatdate(time());
                    $yesterday = getformatdate(time() - 3600 * 24);

                    if ($date == $today) {
                        $date = $_language->module[ 'today' ];
                    } elseif ($date == $yesterday && $date < $today) {
                        $date = $_language->module[ 'yesterday' ];
                    }

                    $time = getformattime($lp[ 'lastdate' ]);
                    $poster = '<a href="index.php?site=profile&amp;id=' . $lp[ 'lastposter' ] . '">' .
                        getnickname($lp[ 'lastposter' ]) . '</a>';
                    if (isclanmember($lp[ 'lastposter' ])) {
                        $member =
                            '<i class="fa fa-user" aria-hidden="true" title="Clanmember"></i>';
                    } else {
                        $member = '';
                    }
                    $topic = $lp[ 'topicID' ];
                    $postlink = 'index.php?site=forum_topic&amp;topic=' . $topic . '&amp;type=ASC&amp;page=' .
                        ceil(($lp[ 'replys' ] + 1) / $maxposts);
                }
                if ($userID) {
                    $board_topics[ ] = $lp[ 'topicID' ];
                } else {
                    break;
                }
                $n++;
            }

            // get unviewed topics

            $found = false;

            if ($userID) {
                $gv = mysqli_fetch_array(safe_query("SELECT topics FROM " . PREFIX . "user WHERE userID='$userID'"));
                $array = explode("|", $gv[ 'topics' ]);

                foreach ($array as $split) {
                    if ($split != "" && in_array($split, $board_topics)) {
                        $found = true;
                        break;
                    }
                }
            }

            if ($found) {
                $icon = '<button type="button" class="btn btn-info"><i class="fa fa-comment"></i></button>';
            } else {
                $icon = '<button type="button" class="btn btn-default"><i class="fa fa-comment"></i></button>';
			}


            $data_array = array();
            $data_array['$icon'] = $icon;
            $data_array['$boardname'] = $boardname;
            $data_array['$boardinfo'] = $boardinfo;
            $data_array['$moderators'] = $moderators;
            $data_array['$anztopics'] = $anztopics;
            $data_array['$anzposts'] = $anzposts;
            $data_array['$postlink'] = $postlink;
            $data_array['$date'] = $date;
            $data_array['$time'] = $time;
            $data_array['$poster'] = $poster;
            $data_array['$member'] = $member;
            $forum_main_board = $GLOBALS["_template"]->replaceTemplate("forum_main_board", $data_array);
            echo $forum_main_board;

            $i++;
        }
    }

    // BOARDS OHNE KATEGORIE
    $boards = safe_query("SELECT * FROM " . PREFIX . "forum_boards WHERE category='0' ORDER BY sort");
    $i = 1;
    while ($db = mysqli_fetch_array($boards)) {
        #if ($i % 2) {
        #    $bg1 = BG_1;
        #    $bg2 = BG_2;
        #} else {
        #    $bg1 = BG_3;
        #    $bg2 = BG_4;
        #}

        $usergrp = 0;
        $writer = 'ro-';
        $ismod = ismoderator($userID, $db[ 'boardID' ]);
        if ($db[ 'writegrps' ] != "" && !$ismod) {
            $writegrps = explode(";", $db[ 'writegrps' ]);
            foreach ($writegrps as $value) {
                if (isinusergrp($value, $userID)) {
                    $usergrp = 1;
                    $writer = '';
                    break;
                }
            }
        } else {
            $writer = '';
        }
        if ($db[ 'readgrps' ] != "" && !$usergrp && !$ismod) {
            $readgrps = explode(";", $db[ 'readgrps' ]);
            foreach ($readgrps as $value) {
                if (isinusergrp($value, $userID)) {
                    $usergrp = 1;
                    break;
                }
            }
            if (!$usergrp) {
                continue;
            }
        }

        $board = $db[ 'boardID' ];
        $anztopics = $db[ 'topics' ];
        $anzposts = $db[ 'posts' ];

        $boardname = $db[ 'name' ];
        $boardname = '<a href="index.php?site=forum&amp;board=' . $db[ 'boardID' ] . '"><strong>' .
            $boardname . '</strong></a>';

        $boardinfo = '';
        if ($db[ 'info' ]) {
            $boardinfo = $db[ 'info' ];
        }
        $moderators = getmoderators($db[ 'boardID' ]);
        if ($moderators) {
            $moderators = $_language->module[ 'moderated_by' ] . ': ' . $moderators;
        }

        $postlink = '';
        $date = '';
        $time = '';
        $poster = '';
        $member = '';

        $q = safe_query(
            "SELECT topicID, lastdate, lastposter, replys FROM " . PREFIX . "forum_topics WHERE boardID='" .
            $db[ 'boardID' ] . "' AND moveID='0' ORDER BY lastdate DESC LIMIT 0," . $maxtopics
        );
        $n = 1;
        $board_topics = array();
        while ($lp = mysqli_fetch_assoc($q)) {
            if ($n == 1) {
                $date = getformatdate($lp[ 'lastdate' ]);
                $today = getformatdate(time());
                $yesterday = getformatdate(time() - 3600 * 24);

                if ($date == $today) {
                    $date = $_language->module[ 'today' ];
                } elseif ($date == $yesterday && $date < $today) {
                    $date = $_language->module[ 'yesterday' ];
                }

                $time = getformattime($lp[ 'lastdate' ]);
                $poster = '<a href="index.php?site=profile&amp;id=' . $lp[ 'lastposter' ] . '">' .
                    getnickname($lp[ 'lastposter' ]) . '</a>';
                if (isclanmember($lp[ 'lastposter' ])) {
                    $member = ' <i class="fa fa-user" aria-hidden="true" title="Clanmember"></i>';
                } else {
                    $member = '';
                }
                $topic = $lp[ 'topicID' ];
                $postlink = 'index.php?site=forum_topic&amp;topic=' . $topic . '&amp;type=ASC&amp;page=' .
                    ceil(($lp[ 'replys' ] + 1) / $maxposts);
            }
            if ($userID) {
                $board_topics[ ] = $lp[ 'topicID' ];
            } else {
                break;
            }
            $n++;
        }

        // get unviewed topics

        $found = false;

        if ($userID) {
            $gv = mysqli_fetch_array(safe_query("SELECT topics FROM " . PREFIX . "user WHERE userID='$userID'"));
            $array = explode("|", $gv[ 'topics' ]);

            foreach ($array as $split) {
                if ($split != "" && in_array($split, $board_topics)) {
                    $found = true;
                    break;
                }
            }
        }

        if ($found) {
            $icon =
                '<img src="images/icons/boardicons/' . $writer . 'on.gif" alt="' . $_language->module[ 'new_posts' ] .
                '">';
        } else {
            $icon = '<img src="images/icons/boardicons/' . $writer . 'off.gif" alt="' .
                $_language->module[ 'no_new_posts' ] . '">';
        }

        $data_array = array();
        $data_array['$icon'] = $icon;
        $data_array['$boardname'] = $boardname;
        $data_array['$boardinfo'] = $boardinfo;
        $data_array['$moderators'] = $moderators;
        $data_array['$anztopics'] = $anztopics;
        $data_array['$anzposts'] = $anzposts;
        $data_array['$postlink'] = $postlink;
        $data_array['$date'] = $date;
        $data_array['$time'] = $time;
        $data_array['$poster'] = $poster;
        $data_array['$member'] = $member;
        $forum_main_board = $GLOBALS["_template"]->replaceTemplate("forum_main_board", $data_array);
        echo $forum_main_board;

        $i++;
    }

    $forum_main_foot = $GLOBALS["_template"]->replaceTemplate("forum_main_foot", array());
    echo $forum_main_foot;

    if ($loggedin) {
        $forum_main_legend = $GLOBALS["_template"]->replaceTemplate("forum_main_legend", array());
        echo $forum_main_legend;
    }

    forum_stats();
}

function showboard($board)
{
    global $userID;
    global $loggedin;
    global $maxtopics;
    global $maxposts;
    global $page;
    global $action;
    global $_language;

    $_language->readModule('forum');

    #$pagebg = PAGEBG;
    #$border = BORDER;
    #$bghead = BGHEAD;
    #$bgcat = BGCAT;

    $title_messageboard = $GLOBALS["_template"]->replaceTemplate("title_messageboard", array());
    echo $title_messageboard;

    $alle = safe_query("SELECT topicID FROM " . PREFIX . "forum_topics WHERE boardID='$board'");
    $gesamt = mysqli_num_rows($alle);

    if ($action == "markall" && $userID) {
        $gv = mysqli_fetch_array(safe_query("SELECT topics FROM " . PREFIX . "user WHERE userID='$userID'"));

        $board_topics = array();
        while ($ds = mysqli_fetch_array($alle)) {
            $board_topics[ ] = $ds[ 'topicID' ];
        }

        $array = explode("|", $gv[ 'topics' ]);
        $new = '|';

        foreach ($array as $split) {
            if ($split != "" && !in_array($split, $board_topics)) {
                $new .= $split . '|';
            }
        }

        safe_query("UPDATE " . PREFIX . "user SET topics='" . $new . "' WHERE userID='$userID'");
    }

    if (!isset($page) || $page == '') {
        $page = 1;
    }
    $max = $maxtopics;
    $pages = ceil($gesamt / $max);

    $page_link = '';
    if ($pages > 1) {
        $page_link = makepagelink("index.php?site=forum&amp;board=$board", $page, $pages);
    }

    if ($page <= 1) {
        $start = 0;
    } else {
        $start = $page * $max - $max;
    }

    $db = mysqli_fetch_array(safe_query("SELECT * FROM " . PREFIX . "forum_boards WHERE boardID='" . $board . "' "));
    $boardname = $db[ 'name' ];

    $usergrp = 0;
    $writer = 0;

    $ismod = false;
    if (ismoderator($userID, $board) || isforumadmin($userID)) {
        $ismod = true;
    }

    if ($db[ 'writegrps' ] != "" && !$ismod) {
        $writegrps = explode(";", $db[ 'writegrps' ]);
        foreach ($writegrps as $value) {
            if (isinusergrp($value, $userID)) {
                $usergrp = 1;
                $writer = 1;
                break;
            }
        }
    } else {
        $writer = 1;
    }
    if ($db[ 'readgrps' ] != "" && !$usergrp && !$ismod) {
        $readgrps = explode(";", $db[ 'readgrps' ]);
        foreach ($readgrps as $value) {
            if (isinusergrp($value, $userID)) {
                $usergrp = 1;
                break;
            }
        }
        if (!$usergrp) {
            echo $_language->module[ 'no_permission' ];
            redirect('index.php?site=forum', '', 2);
            return;
        }
    }

    $moderators = getmoderators($board);
    if ($moderators) {
        $moderators = '(' . $_language->module[ 'moderated_by' ] . ': ' . $moderators . ')';
    }

    $actions = '<a href="index.php?site=search" class="btn btn-default"><i class="fa fa-search"></i> Search</a>';
    if ($loggedin) {
        $mark = '<a href="index.php?site=forum&amp;board=' . $board . '&amp;action=markall">' .
            $_language->module[ 'mark_topics_read' ] . '</a>';
        if ($writer) {
            $actions .= ' <a href="index.php?site=forum&amp;addtopic=true&amp;board=' .$board . '" class="btn btn-primary"><i class="fa fa-comment"></i> ' .$_language->module[ 'new_topic' ] . '</a>';
        }
    } else {
        $mark = '';
    }

    $cat = $db[ 'category' ];
    $kathname = getcategoryname($cat);
    $data_array = array();
    $data_array['$cat'] = $cat;
    $data_array['$kathname'] = $kathname;
    $data_array['$boardname'] = $boardname;
    $data_array['$moderators'] = $moderators;
    $forum_head = $GLOBALS["_template"]->replaceTemplate("forum_head", $data_array);
    echo $forum_head;

    // TOPICS

    $topics = safe_query(
        "SELECT * FROM " . PREFIX .
        "forum_topics WHERE boardID='$board' ORDER BY sticky DESC, lastdate DESC LIMIT $start,$max"
    );
    $anztopics = mysqli_num_rows(safe_query("SELECT boardID FROM " . PREFIX . "forum_topics WHERE boardID='$board'"));

    $i = 1;
    unset($link);
    if ($anztopics) {
        $data_array = array();
        $data_array['$page_link'] = $page_link;
        $data_array['$actions'] = $actions;
        $forum_topics_head = $GLOBALS["_template"]->replaceTemplate("forum_topics_head", $data_array);
        echo $forum_topics_head;
        while ($dt = mysqli_fetch_array($topics)) {
            #if ($i % 2) {
            #    $bg1 = BG_1;
            #    $bg2 = BG_2;
            #} else {
            #    $bg1 = BG_3;
            #    $bg2 = BG_4;
            #}

            if ($dt[ 'moveID' ]) {
                $gesamt = 0;
            } else {
                $gesamt = $dt[ 'replys' ] + 1;
            }

            $topicpages = 1;
            $topicpages = ceil($gesamt / $maxposts);

            $topicpage_link = '';
            if ($topicpages > 1) {
                $topicpage_link =
                    makepagelink("index.php?site=forum_topic&amp;topic=" . $dt[ 'topicID' ], 1, $topicpages);
            }

            if ($dt[ 'icon' ]) {
                $icon = '<img src="images/icons/topicicons/' . $dt[ 'icon' ] . '" alt="">';
            } else {
                $icon = '';
            }

            // viewed topics

            if ($dt[ 'sticky' ]) {
                $onicon =
                    '<button type="button" class="btn btn-info"><i class="fa fa-thumb-tack"></i></button>';
                $officon =
                    '<button type="button" class="btn btn-warning"><i class="fa fa-thumb-tack"></i></button>';
                $onhoticon =
                    '<button type="button" class="btn btn-info"><i class="fa fa-thumb-tack"></i>></button>';
                $offhoticon =
                    '<button type="button" class="btn btn-warning"><i class="fa fa-thumb-tack"></i></button>';
            } else {
                $onicon =
                    '<button type="button" class="btn btn-info"><i class="fa fa-comment"></i></button>';
                $officon =
                    '<button type="button" class="btn btn-default"><i class="fa fa-comment"></i></button>';
                $onhoticon =
                    '<button type="button" class="btn btn-info"><i class="fa fa-comment"></i></button>';
                $offhoticon =
                    '<button type="button" class="btn btn-default"><i class="fa fa-comment"></i></button>';
            }

            if ($dt[ 'closed' ]) {
                $folder =
                    '<button type="button" class="btn btn-danger"><i class="fa fa-lock"></i></button>';
            } elseif ($dt[ 'moveID' ]) {
                $folder = '<button type="button" class="btn btn-default"><i class="fa fa-arrow-right"></i></button>';
            } elseif ($userID) {
                $is_unread = mysqli_num_rows(
                    safe_query(
                        "SELECT userID FROM " . PREFIX . "user WHERE topics LIKE '%|" .
                        $dt[ 'topicID' ] . "|%' AND userID='" . $userID . "'"
                    )
                );

                if ($is_unread) {
                    if ($dt[ 'replys' ] > 15 || $dt[ 'views' ] > 150) {
                        $folder = $onhoticon;
                    } else {
                        $folder = $onicon;
                    }
                } else {
                    if ($dt[ 'replys' ] > 15 || $dt[ 'views' ] > 150) {
                        $folder = $offhoticon;
                    } else {
                        $folder = $officon;
                    }
                }
            } else {
                if ($gesamt > 15) {
                    $folder = $offhoticon;
                } else {
                    $folder = $officon;
                }
            }
            // end viewed topics

            $topictitle = getinput($dt[ 'topic' ]);
            $topictitle = str_break($topictitle, 40);

            $poster =
                '<a href="index.php?site=profile&amp;id=' . $dt[ 'userID' ] . '">' . getnickname($dt[ 'userID' ]) .
                '</a>';
            if (isset($posterID) && isclanmember($posterID)) {
                $member1 = ' <i class="fa fa-user" aria-hidden="true" title="Clanmember"></i>';
            } else {
                $member1 = '';
            }

            $replys = '0';
            $views = '0';

            if ($dt[ 'moveID' ]) {
                // MOVED TOPIC
                $move = safe_query("SELECT * FROM " . PREFIX . "forum_topics WHERE topicID='" . $dt[ 'moveID' ] . "'");
                $dm = mysqli_fetch_array($move);

                if ($dm[ 'replys' ]) {
                    $replys = $dm[ 'replys' ];
                }
                if ($dm[ 'views' ]) {
                    $views = $dm[ 'views' ];
                }

                $date = getformatdate($dm[ 'lastdate' ]);
                $time = getformattime($dm[ 'lastdate' ]);
                $today = getformatdate(time());
                $yesterday = getformatdate(time() - 3600 * 24);
                if ($date == $today) {
                    $date = $_language->module[ 'today' ] . ", " . $time;
                } elseif ($date == $yesterday && $date < $today) {
                    $date = $_language->module[ 'yesterday' ] . ", " . $time;
                } else {
                    $date = $date . ", " . $time;
                }
                $lastposter = '<a href="index.php?site=profile&amp;id=' . $dm[ 'lastposter' ] . '">' .
                    getnickname($dm[ 'lastposter' ]) . '</a>';
                if (isclanmember($dm[ 'lastposter' ])) {
                    $member = ' <i class="fa fa-user" aria-hidden="true" title="Clanmember"></i>';
                } else {
                    $member = '';
                }
                $link = '<a href="index.php?site=forum_topic&amp;topic=' . $dt[ 'moveID' ] . '"><b>' .
                    $_language->module[ 'moved' ] . ': ' . $topictitle . '</b></a>';
            } else {
                // NO MOVED TOPIC
                if ($dt[ 'replys' ]) {
                    $replys = $dt[ 'replys' ];
                }
                if ($dt[ 'views' ]) {
                    $views = $dt[ 'views' ];
                }

                $date = getformatdate($dt[ 'lastdate' ]);
                $time = getformattime($dt[ 'lastdate' ]);
                $today = getformatdate(time());
                $yesterday = getformatdate(time() - 3600 * 24);
                if ($date == $today) {
                    $date = $_language->module[ 'today' ] . ", " . $time;
                } elseif ($date == $yesterday && $date < $today) {
                    $date = $_language->module[ 'yesterday' ] . ", " . $time;
                } else {
                    $date = $date . ", " . $time;
                }
                $lastposter = '<a href="index.php?site=profile&amp;id=' . $dt[ 'lastposter' ] . '">' .
                    getnickname($dt[ 'lastposter' ]) . '</a>';
                if (isclanmember($dt[ 'lastposter' ])) {
                    $member = ' <i class="fa fa-user" aria-hidden="true" title="Clanmember"></i>';
                } else {
                    $member = '';
                }
                $link = '<a href="index.php?site=forum_topic&amp;topic=' . $dt[ 'topicID' ] . '"><b>' . $topictitle .
                    '</b></a>';
            }

            $data_array = array();
            $data_array['$folder'] = $folder;
            $data_array['$icon'] = $icon;
            $data_array['$link'] = $link;
            $data_array['$topicpage_link'] = $topicpage_link;
            $data_array['$poster'] = $poster;
            $data_array['$member1'] = $member1;
            $data_array['$replys'] = $replys;
            $data_array['$views'] = $views;
            $data_array['$date'] = $date;
            $data_array['$lastposter'] = $lastposter;
            $data_array['$member'] = $member;
            $forum_topics_content = $GLOBALS["_template"]->replaceTemplate("forum_topics_content", $data_array);
            echo $forum_topics_content;
            $i++;
            unset($topicpage_link);
            unset($lastposter);
            unset($member);
            unset($member1);
            unset($date);
            unset($time);
            unset($link);
        }
        $forum_topics_foot = $GLOBALS["_template"]->replaceTemplate("forum_topics_foot", array());
        echo $forum_topics_foot;
    }

    $data_array = array();
    $data_array['$page_link'] = $page_link;
    $data_array['$mark'] = $mark;
    $data_array['$actions'] = $actions;
    $forum_actions = $GLOBALS["_template"]->replaceTemplate("forum_actions", $data_array);
    echo $forum_actions;

    if ($loggedin) {
        $forum_topics_legend = $GLOBALS["_template"]->replaceTemplate("forum_topics_legend", array());
        echo $forum_topics_legend;
    }

    if (!$loggedin) {
        echo $_language->module[ 'not_logged_msg' ];
    }

    unset($page_link);
}

if (isset($_POST[ 'submit' ]) || isset($_POST[ 'movetopic' ]) || isset($_GET[ 'addtopic' ])
    || isset($_POST[ 'addtopic' ]) || (isset($_GET[ 'action' ]) && $_GET[ 'action' ] == "admin-action")
    || isset($_POST[ 'admaction' ])
) {
    if (!isset($_POST[ 'admaction' ])) {
        $_POST[ 'admaction' ] = '';
    }

    if ($_POST[ 'admaction' ] == "closetopic") {
        include("_mysql.php");
        include("_settings.php");
        include('_functions.php');
        $_language->readModule('forum');

        $topicID = (int)$_POST[ 'topicID' ];
        $board = (int)$_POST[ 'board' ];

        if (!isforumadmin($userID) && !ismoderator($userID, $board)) {
            die($_language->module[ 'no_access' ]);
        }

        safe_query("UPDATE " . PREFIX . "forum_topics SET closed='1' WHERE topicID='$topicID' ");
        header("Location: index.php?site=forum&board=$board");
    } elseif ($_POST[ 'admaction' ] == "opentopic") {
        include("_mysql.php");
        include("_settings.php");
        include('_functions.php');
        $_language->readModule('forum');

        $topicID = (int)$_POST[ 'topicID' ];
        $board = (int)$_POST[ 'board' ];

        if (!isforumadmin($userID) && !ismoderator($userID, $board)) {
            die($_language->module[ 'no_access' ]);
        }

        safe_query("UPDATE " . PREFIX . "forum_topics SET closed='0' WHERE topicID='$topicID' ");
        header("Location: index.php?site=forum&board=$board");
    } elseif ($_POST[ 'admaction' ] == "deletetopic") {
        include("_mysql.php");
        include("_settings.php");
        include('_functions.php');
        $_language->readModule('forum');

        $topicID = (int)$_POST[ 'topicID' ];
        $board = (int)$_POST[ 'board' ];

        if (!isforumadmin($userID) && !ismoderator($userID, $board)) {
            die($_language->module[ 'no_access' ]);
        }

        $numposts =
            mysqli_num_rows(
                safe_query(
                    "SELECT postID FROM " . PREFIX . "forum_posts WHERE topicID='" . $topicID .
                    "'"
                )
            );
        $numposts--;

        safe_query(
            "UPDATE " . PREFIX . "forum_boards SET topics=topics-1, posts=posts-" . $numposts .
            " WHERE boardID='" . $board . "' "
        );
        safe_query("DELETE FROM " . PREFIX . "forum_topics WHERE topicID='$topicID' ");
        safe_query("DELETE FROM " . PREFIX . "forum_topics WHERE moveID='$topicID' ");
        safe_query("DELETE FROM " . PREFIX . "forum_posts WHERE topicID='$topicID' ");
        header("Location: index.php?site=forum&board=$board");
    } elseif ($_POST[ 'admaction' ] == "stickytopic") {
        include("_mysql.php");
        include("_settings.php");
        include('_functions.php');
        $_language->readModule('forum');

        $topicID = (int)$_POST[ 'topicID' ];
        $board = (int)$_POST[ 'board' ];

        if (!isforumadmin($userID) && !ismoderator($userID, $board)) {
            die($_language->module[ 'no_access' ]);
        }

        safe_query("UPDATE " . PREFIX . "forum_topics SET sticky='1' WHERE topicID='$topicID' ");
        header("Location: index.php?site=forum&board=$board");
    } elseif ($_POST[ 'admaction' ] == "unstickytopic") {
        include("_mysql.php");
        include("_settings.php");
        include('_functions.php');
        $_language->readModule('forum');

        $topicID = (int)$_POST[ 'topicID' ];
        $board = (int)$_POST[ 'board' ];

        if (!isforumadmin($userID) && !ismoderator($userID, $board)) {
            die($_language->module[ 'no_access' ]);
        }

        safe_query("UPDATE " . PREFIX . "forum_topics SET sticky='0' WHERE topicID='$topicID' ");
        header("Location: index.php?site=forum&board=$board");
    } elseif ($_POST[ 'admaction' ] == "delposts") {
        include("_mysql.php");
        include("_settings.php");
        include('_functions.php');
        $_language->readModule('forum');

        $topicID = (int)$_POST[ 'topicID' ];
        if (isset($_POST[ 'postID' ]) && is_array($_POST[ 'postID' ])) {
            $postID = $_POST[ 'postID' ];
        } else {
            $postID = array();
        }
        $board = (int)$_POST[ 'board' ];

        if (!isforumadmin($userID) && !ismoderator($userID, $board)) {
            die($_language->module[ 'no_access' ]);
        }
        $last = safe_query("SELECT * FROM " . PREFIX . "forum_posts WHERE topicID = '$topicID' ");
        $anz = mysqli_num_rows($last);
        $deleted = false;
        foreach ($postID as $id) {
            if ($anz > 1) {
                safe_query("DELETE FROM " . PREFIX . "forum_posts WHERE postID='" . (int)$id . "' ");
                safe_query("UPDATE " . PREFIX . "forum_boards SET posts=posts-1 WHERE boardID='" . $board . "' ");
                $last = safe_query(
                    "SELECT * FROM " . PREFIX .
                    "forum_posts WHERE topicID = '$topicID' ORDER BY date DESC LIMIT 0,1 "
                );
                $dl = mysqli_fetch_array($last);
                safe_query(
                    "UPDATE " . PREFIX . "forum_topics SET lastdate='" . $dl[ 'date' ] . "', lastposter='" .
                    $dl[ 'poster' ] . "', lastpostID='" . $dl[ 'postID' ] .
                    "', replys=replys-1 WHERE topicID='$topicID' "
                );
                $deleted = false;
            } else {
                safe_query("DELETE FROM " . PREFIX . "forum_posts WHERE postID='" . (int)$id . "' ");
                safe_query("DELETE FROM " . PREFIX . "forum_topics WHERE topicID='$topicID' OR moveID='$topicID'");
                safe_query("UPDATE " . PREFIX . "forum_boards SET topics=topics-1 WHERE boardID='" . $board . "' ");
                $deleted = true;
            }
        }
        if ($deleted) {
            header("Location: index.php?site=forum&board=$board");
        } else {
            header("Location: index.php?site=forum_topic&topic=$topicID");
        }
    } elseif (isset($_POST[ 'movetopic' ])) {
        include("_mysql.php");
        include("_settings.php");
        include('_functions.php');
        $_language->readModule('forum');

        $toboard = (int)$_POST[ 'toboard' ];
        $topicID = (int)$_POST[ 'topicID' ];

        if (!isanyadmin($userID) && !ismoderator($userID, getboardid($topicID))) {
            die($_language->module[ 'no_access' ]);
        }

        $di = mysqli_fetch_array(
            safe_query(
                "SELECT writegrps, readgrps FROM " . PREFIX .
                "forum_boards WHERE boardID='$toboard'"
            )
        );

        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "forum_topics WHERE topicID='$topicID'");
        $ds = mysqli_fetch_array($ergebnis);

        if (isset($_POST[ 'movelink' ]) && $ds[ 'boardID' ] != $toboard) {
            safe_query(
                "INSERT INTO " . PREFIX .
                "forum_topics (boardID, icon, userID, date, topic, lastdate, lastposter, replys, views, closed, moveID)
                values ('" . $ds[ 'boardID' ] . "',
                '', '" . $ds[ 'userID' ] . "',
                '" . $ds[ 'date' ] . "',
                '" . addslashes($ds[ 'topic' ]) . "',
                '" . $ds[ 'lastdate' ] . "',
                '',
                '',
                '',
                '',
                '$topicID') "
            );
        }

        safe_query(
            "UPDATE " . PREFIX . "forum_topics SET boardID='$toboard', readgrps='" . $di[ 'readgrps' ] .
            "', writegrps='" . $di[ 'writegrps' ] . "' WHERE topicID='$topicID'"
        );
        safe_query("UPDATE " . PREFIX . "forum_posts SET boardID='$toboard' WHERE topicID='$topicID'");
        $post_num = mysqli_affected_rows($_database) - 1;
        safe_query("UPDATE " . PREFIX . "forum_boards SET topics=topics+1 WHERE boardID='$toboard'");
        safe_query("UPDATE " . PREFIX . "forum_boards SET topics=topics-1 WHERE boardID='" . $ds[ 'boardID' ] . "'");
        safe_query(
            "UPDATE " . PREFIX . "forum_boards SET posts=posts+" . $post_num . " WHERE boardID='" . $toboard .
            "'"
        );
        safe_query(
            "UPDATE " . PREFIX . "forum_boards SET posts=posts-" . $post_num . " WHERE boardID='" .
            $ds[ 'boardID' ] . "'"
        );

        header("Location: index.php?site=forum&board=$toboard");
    } elseif ($_POST[ 'admaction' ] == "movetopic") {
        include("_mysql.php");
        include("_settings.php");
        include('_functions.php');
        $_language->readModule('forum');
        if (!isanyadmin($userID) && !ismoderator($userID, getboardid($_POST[ 'topicID' ]))) {
            die($_language->module[ 'no_access' ]);
        }

        $boards = '';
        $kath = safe_query("SELECT * FROM " . PREFIX . "forum_categories ORDER BY sort");
        while ($dk = mysqli_fetch_array($kath)) {
            $ergebnis =
                safe_query("SELECT * FROM " . PREFIX . "forum_boards WHERE category='$dk[catID]' ORDER BY sort");
            while ($db = mysqli_fetch_array($ergebnis)) {
                $boards .= '<option value="' . $db[ 'boardID' ] . '">' . $dk[ 'name' ] . ' - ' . $db[ 'name' ] .
                    '</option>';
            }
        }

        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "forum_boards WHERE category='0' ORDER BY sort");
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $boards .= '<option value="' . $ds[ 'boardID' ] . '">' . $ds[ 'name' ] . '</option>';
        }

        $pagetitle = PAGETITLE;
        #$pagebg = PAGEBG;
        #$border = BORDER;
        #$bghead = BGHEAD;
        #$bg1 = BG_1;

        $data_array = array();
        $data_array['$pagetitle'] = $pagetitle;
        $data_array['$rewriteBase'] = $rewriteBase;
        $data_array['$boards'] = $boards;
        $data_array['$board'] = (int)$_POST['board'];
        $data_array['$topic'] = (int)$_POST['topicID'];
        $forum_move_topic = $GLOBALS["_template"]->replaceTemplate("forum_move_topic", $data_array);
        echo $forum_move_topic;
    } elseif (isset($_POST[ 'newtopic' ]) && !isset($_POST[ 'preview' ])) {
        include("_mysql.php");
        include("_settings.php");
        include('_functions.php');
        $_language->readModule('forum');
        $_language->readModule('bbcode', true);

        if (!$userID) {
            die($_language->module[ 'not_logged' ]);
        }

        $board = (int)$_POST[ 'board' ];
        if (boardexists($board)) {
            if (isset($_POST[ 'icon' ])) {
                $icon = $_POST[ 'icon' ];
                if (!file_exists("images/icons/topicicons/" . $icon)) {
                    $icon = "";
                }
            } else {
                $icon = '';
            }
            $topicname = $_POST[ 'topicname' ];
            if (!$topicname) {
                $topicname = $_language->module[ 'default_topic_title' ];
            }
            $message = $_POST[ 'message' ];
            $topic_sticky = (isset($_POST[ 'sticky' ])) ? '1' : '0';
            $notify = (isset($_POST[ 'notify' ])) ? '1' : '0';

            $ds = mysqli_fetch_array(
                safe_query(
                    "SELECT readgrps, writegrps FROM " . PREFIX .
                    "forum_boards WHERE boardID='$board'"
                )
            );

            $writer = 0;
            if ($ds[ 'writegrps' ] != "") {
                $writegrps = explode(";", $ds[ 'writegrps' ]);
                foreach ($writegrps as $value) {
                    if (isinusergrp($value, $userID)) {
                        $writer = 1;
                        break;
                    }
                }
                if (ismoderator($userID, $board)) {
                    $writer = 1;
                }
            } else {
                $writer = 1;
            }
            if (!$writer) {
                die($_language->module[ 'no_access_write' ]);
            }

            $spamApi = \webspell\SpamApi::getInstance();
            $validation = $spamApi->validate($message);

            $date = time();
            if ($validation == \webspell\SpamApi::NOSPAM) {
                safe_query(
                    "INSERT INTO " . PREFIX .
                    "forum_topics ( boardID,
                    readgrps,
                    writegrps,
                    userID,
                    date,
                    icon,
                    topic,
                    lastdate,
                    lastposter,
                    replys,
                    views,
                    closed,
                    sticky )
                    values ( '$board',
                    '" . $ds[ 'readgrps' ] . "',
                    '" . $ds[ 'writegrps' ] . "',
                    '$userID',
                    '$date',
                    '" . $icon . "',
                    '" . $topicname . "',
                    '$date',
                    '$userID',
                    '0',
                    '0',
                    '0',
                    '$topic_sticky' ) "
                );
                $id = mysqli_insert_id($_database);
                safe_query("UPDATE " . PREFIX . "forum_boards SET topics=topics+1 WHERE boardID='" . $board . "'");
                safe_query(
                    "INSERT INTO " . PREFIX .
                    "forum_posts ( boardID, topicID, date, poster, message )
                    values( '$board',
                    '$id',
                    '$date',
                    '$userID',
                    '" . $message . "' ) "
                );

                // check if there are more than 1000 unread topics => delete oldest one
                $dv = mysqli_fetch_array(
                    safe_query(
                        "SELECT topics FROM " . PREFIX . "user WHERE userID='" . $userID .
                        "'"
                    )
                );
                $array = explode('|', $dv[ 'topics' ]);
                if (count($array) >= 1000) {
                    safe_query(
                        "UPDATE " . PREFIX . "user SET topics='|" . implode('|', array_slice($array, 2)) .
                        "' WHERE userID='" . $userID . "'"
                    );
                }
                unset($array);

                safe_query(
                    "UPDATE " . PREFIX . "user SET topics=CONCAT(topics, '" . $id .
                    "|')"
                ); // update unread topics, format: |oldstring| => |oldstring|topicID|

                if ($notify) {
                    safe_query("INSERT INTO " . PREFIX . "forum_notify (topicID, userID) VALUES ('$id', '$userID') ");
                }
            } else {
                safe_query(
                    "INSERT INTO " . PREFIX .
                    "forum_topics_spam ( boardID, userID, date, icon, topic, sticky, message, rating)
                    values ( '$board',
                    '$userID',
                    '$date',
                    '" . $icon . "',
                    '" . $topicname . "',
                    '$topic_sticky',
                    '" . $message . "',
                    '" . $rating . "') "
                );
            }
            header("Location: index.php?site=forum&board=" . $board . "");
        } else {
            header("Location: index.php?site=forum");
        }
    } elseif (isset($_REQUEST[ 'addtopic' ])) {
        $_language->readModule('forum');
        $_language->readModule('bbcode', true);

        $title_messageboard = $GLOBALS["_template"]->replaceTemplate("title_messageboard", array());
        echo $title_messageboard;

        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "forum_boards WHERE boardID='$board' ");
        $db = mysqli_fetch_array($ergebnis);
        $boardname = $db[ 'name' ];

        $writer = 0;
        if ($db[ 'writegrps' ] != "") {
            $writegrps = explode(";", $db[ 'writegrps' ]);
            foreach ($writegrps as $value) {
                if (isinusergrp($value, $userID)) {
                    $writer = 1;
                    break;
                }
            }
            if (ismoderator($userID, $board)) {
                $writer = 1;
            }
        } else {
            $writer = 1;
        }
        if (!$writer) {
            die($_language->module[ 'no_access_write' ]);
        }

        $moderators = '';
        $cat = $db[ 'category' ];
        $kathname = getcategoryname($cat);

        $data_array = array();
        $data_array['$cat'] = $cat;
        $data_array['$kathname'] = $kathname;
        $data_array['$boardname'] = $boardname;
        $data_array['$moderators'] = $moderators;

        $forum_head = $GLOBALS["_template"]->replaceTemplate("forum_head", $data_array);
        echo $forum_head;

        #$bg1 = BG_1;

        $message = '';

        if ($loggedin) {
            if (isset($_POST[ 'preview' ])) {
                #$bg1 = BG_1;
                #$bg2 = BG_2;

                $time = getformattime(time());
                $date = "today";
                $message = cleartext(
                    stripslashes(
                        str_replace(
                            array('\r\n', '\n'),
                            array("\n", "\n"),
                            $_POST[ 'message' ]
                        )
                    )
                );
                $message = toggle($message, 'xx');
                $username =
                    '<a href="index.php?site=profile&amp;id=' . $userID . '"><strong>' . getnickname($userID) .
                    '</strong></a>';

                $board = (int)$_POST[ 'board' ];
                $topicname = stripslashes($_POST[ 'topicname' ]);
                if (!isset($postID)) {
                    $postID = '';
                }

                if (isclanmember($userID)) {
                    $member = ' <i class="fa fa-user" aria-hidden="true" title="Clanmember"></i>';
                } else {
                    $member = '';
                }
                if (getavatar($userID)) {
                    $avatar = '<img src="images/avatars/' . getavatar($userID) . '" alt="">';
                } else {
                    $avatar = '';
                }
                if (getsignatur($userID)) {
                    $signatur = cleartext(getsignatur($userID));
                } else {
                    $signatur = '';
                }
                if (getemail($userID) && !getemailhide($userID)) {
                    $email = '<a href="mailto:' . mail_protect(getemail($userID)) .
                        '"><i class="fa fa-envelope" title="mail"></i></a>';
                } else {
                    $email = '';
                }

                $pm = '';
                $buddy = '';
                $statuspic = '<i class="fa fa-circle text-success" aria-hidden="true"></i>';

                if (!validate_url(gethomepage($userID))) {
                    $hp = '';
                } else {
                    $hp = '<a href="' . gethomepage($userID) .
                        '" target="_blank"><i class="fa fa-home" aria-hidden="true" title="' .
                        $_language->module[ 'homepage' ] . '"></i></a>';
                }

                $registered = getregistered($userID);
                $posts = getuserforumposts($userID);
                $_sticky = '';
                if (isforumadmin($userID)) {
                    $usertype = "Administrator";
                    $rang = '<img src="images/icons/ranks/admin.gif" alt="">';
                } elseif (ismoderator($userID, $board)) {
                    $usertype = $_language->module[ 'moderator' ];
                    $rang = '<img src="images/icons/ranks/moderator.gif" alt="">';
                } else {
                    $ergebnis = safe_query(
                        "SELECT * FROM " . PREFIX .
                        "forum_ranks WHERE $posts >= postmin AND $posts <= postmax AND special='0'"
                    );
                    $ds = mysqli_fetch_array($ergebnis);
                    $usertype = $ds[ 'rank' ];
                    $rang = '<img src="images/icons/ranks/' . $ds[ 'pic' ] . '" alt="">';
                }

                $specialrang = "";
                $specialtype = "";
                $getrank = safe_query(
                    "SELECT IF
                        (u.special_rank = 0, 0, CONCAT_WS('__', r.rank, r.pic)) as RANK
                    FROM
                        " . PREFIX . "user u LEFT JOIN " . PREFIX . "forum_ranks r ON u.special_rank = r.rankID
                    WHERE
                        userID = '" . $userID . "'"
                );
                $rank_data = mysqli_fetch_assoc($getrank);

                if ($rank_data[ 'RANK' ] != '0') {
                    $tmp_rank = explode("__", $rank_data[ 'RANK' ], 2);
                    $specialrang = $tmp_rank[ 0 ];
                    if (!empty($tmp_rank[1]) && file_exists("images/icons/ranks/" . $tmp_rank[1])) {
                        $specialtype =
                        "<img src='images/icons/ranks/" . $tmp_rank[ 1 ] . "' alt = '" . $specialrang . "' />";
                    }
                }

                $actions = '';
                $quote = '';

                echo '<table class="table">
                <tr>
                    <td colspan="2" class="title" class="text-center">' . cleartext($topicname) . '</td>
                </tr>';

                $data_array = array();
                $data_array['$statuspic'] = $statuspic;
                $data_array['$username'] = $username;
                $data_array['$usertype'] = $usertype;
                $data_array['$quote'] = $quote;
                $data_array['$date'] = $date;
                $data_array['$time'] = $time;
                $data_array['$pm'] = $pm;
                $data_array['$buddy'] = $buddy;
                $data_array['$email'] = $email;
                $data_array['$hp'] = $hp;
                $data_array['$actions'] = $actions;
                $data_array['$avatar'] = $avatar;
                $data_array['$rang'] = $rang;
                $data_array['$posts'] = $posts;
                $data_array['$registered'] = $registered;
                $data_array['$message'] = $message;
                $data_array['$signatur'] = $signatur;
                $data_array['$specialrang'] = $specialrang;
                $data_array['$specialtype'] = $specialtype;
                $forum_topic_content = $GLOBALS["_template"]->replaceTemplate("forum_topic_content", $data_array);
                echo $forum_topic_content;

                echo '</table>';
            } else {
                $topicname = "";
            }

            $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());

            if (isforumadmin($userID) || ismoderator($userID, $board)) {
                if (isset($_POST[ 'sticky' ])) {
                    $chk_sticky =
                        '<br>' . "\n" . ' <input class="input" type="checkbox" name="sticky" value="1" '.
                        'checked="checked"> ' . $_language->module[ 'make_sticky' ];
                } else {
                    $chk_sticky = '<br>' . "\n" . ' <input class="input" type="checkbox" name="sticky" value="1"> ' .
                        $_language->module[ 'make_sticky' ];
                }
            } else {
                $chk_sticky = '';
            }
            if (isset($_POST[ 'notify' ])) {
                $notify = ' checked="checked"';
            } else {
                $notify = '';
            }
            if (isset($_POST[ 'topicname' ])) {
                $topicname = getforminput($_POST[ 'topicname' ]);
            }
            if (isset($_POST[ 'message' ])) {
                $message = getforminput($_POST[ 'message' ]);
            }
            $data_array = array();
            $data_array['$topicname'] = $topicname;
            $data_array['$addbbcode'] = $addbbcode;
            $data_array['$message'] = $message;
            $data_array['$notify'] = $notify;
            $data_array['$chk_sticky'] = $chk_sticky;
            $data_array['$board'] = $board;
            $data_array['$userID'] = $userID;
            $forum_newtopic = $GLOBALS["_template"]->replaceTemplate("forum_newtopic", $data_array);
            echo $forum_newtopic;
        } else {
            echo $_language->module[ 'not_logged_msg' ];
        }
    } elseif (!$_POST[ 'admaction' ]) {
        header("Location: index.php?site=forum");
    }
} elseif (!isset($board)) {
    boardmain();
} else {
    showboard($board);
}
