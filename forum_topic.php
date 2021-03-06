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

use webspell_ng\Email;

$page = getPage();

if (isset($_GET['delete'])) {
    $delete = (bool)$_GET['delete'];
} else {
    $delete = '';
}
if (isset($_GET['edit'])) {
    $edit = (bool)$_GET['edit'];
} else {
    $edit = '';
}
if (isset($_REQUEST['topic'])) {
    $topic = (int)$_REQUEST['topic'];
} else {
    $topic = '';
}
if (isset($_REQUEST['addreply'])) {
    $addreply = (bool)$_REQUEST['addreply'];
} else {
    $addreply = '';
}

$type = getSortOrderType("DESC");

if (isset($_GET['quoteID'])) {
    $quoteID = (int)$_GET['quoteID'];
} else {
    $quoteID = '';
}
$do_sticky = (isset($_POST['sticky']));

if (isset($_POST['newreply']) && !isset($_POST['preview'])) {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('forum');

    if (!$userID) {
        die($_language->module['not_logged']);
    }

    $message = $_POST['message'];
    $topic = (int)$_POST['topic'];
    $page = (int)$_POST['page'];

    if (!(mb_strlen(trim($message)))) {
        die($_language->module['forgot_message']);
    }
    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT closed, writegrps, boardID FROM " . PREFIX .
            "forum_topics WHERE topicID='" . $topic . "'"
        )
    );
    if ($ds['closed']) {
        die($_language->module['topic_closed']);
    }

    $writer = 0;
    if ($ds['writegrps'] != "") {
        $writegrps = explode(";", $ds['writegrps']);
        foreach ($writegrps as $value) {
            if (isinusergrp($value, $userID)) {
                $writer = 1;
                break;
            }
        }
        if (ismoderator($userID, $ds['boardID'])) {
            $writer = 1;
        }
    } else {
        $writer = 1;
    }
    if (!$writer) {
        die($_language->module['no_access_write']);
    }
    $do_sticky = '';
    if (isforumadmin($userID) || ismoderator($userID, $ds['boardID'])) {
        $do_sticky = (isset($_POST['sticky'])) ? ', sticky=1' : ', sticky=0';
    }

    $spamApi = \webspell\SpamApi::getInstance();
    $validation = $spamApi->validate($message);

    $date = time();
    if ($validation == \webspell\SpamApi::NOSPAM) {
        safe_query(
            "INSERT INTO " . PREFIX . "forum_posts ( boardID, topicID, date, poster, message ) VALUES( '" .
            $_REQUEST['board'] . "', '$topic', '$date', '$userID', '" . $message . "' ) "
        );
        $lastpostID = mysqli_insert_id($_database);
        safe_query("UPDATE " . PREFIX . "forum_boards SET posts=posts+1 WHERE boardID='" . $_REQUEST['board'] . "' ");
        safe_query(
            "UPDATE " . PREFIX . "forum_topics SET lastdate='" . $date . "', lastposter='" . $userID .
            "', lastpostID='" . $lastpostID . "', replys=replys+1 $do_sticky WHERE topicID='$topic' "
        );

        // check if there are more than 1000 unread topics => delete oldest one
        $dv = mysqli_fetch_array(safe_query("SELECT topics FROM " . PREFIX . "user WHERE userID='" . $userID . "'"));
        $array = explode('|', $dv['topics']);
        if (count($array) >= 1000) {
            safe_query(
                "UPDATE " . PREFIX . "user SET topics='|" . implode('|', array_slice($array, 2)) .
                "' WHERE userID='" . $userID . "'"
            );
        }
        unset($array);

        // add this topic to unread
        safe_query(
            "UPDATE " . PREFIX . "user SET topics=CONCAT(topics, '" . $topic . "|') WHERE topics NOT LIKE '%|" . $topic . "|%'"
        ); // update unread topics, format: |oldstring| => |oldstring|topicID|

        $emails = array();
        $ergebnis = safe_query(
            "SELECT f.userID, u.email, u.language FROM " . PREFIX . "forum_notify f JOIN " . PREFIX .
            "user u ON u.userID=f.userID WHERE f.topicID=$topic"
        );
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $emails[] = array('mail' => $ds['email'], 'lang' => $ds['language']);
        }
        safe_query("DELETE FROM " . PREFIX . "forum_notify WHERE topicID='$topic'");

        if (count($emails)) {
            $de = mysqli_fetch_array(safe_query("SELECT nickname FROM " . PREFIX . "user WHERE userID='$userID'"));
            $poster = $de['nickname'];
            $de = mysqli_fetch_array(safe_query("SELECT topic FROM " . PREFIX . "forum_topics WHERE topicID='$topic'"));
            $topicname = getinput($de['topic']);

            $link = $hp_url . "/index.php?site=forum_topic&topic=" . $topic;
            $maillanguage = new \webspell\Language();
            $maillanguage->setLanguage($default_language);
            $_language->readModule('formvalidation', true);

            foreach ($emails as $email) {
                $maillanguage->setLanguage($email['lang']);
                $maillanguage->readModule('forum');
                $forum_topic_notify = str_replace(
                    array('%poster%', '%topic_link%', '%pagetitle%', '%hpurl%'),
                    array(html_entity_decode($poster), $link, $hp_title, $hp_url),
                    $maillanguage->module['notify_mail']
                );
                $subject = $maillanguage->module['new_reply'] . ' (' . $hp_title . ')';
                $sendmail = Email::sendEmail(
                    $admin_email,
                    'Forum',
                    $email['mail'],
                    $subject,
                    $forum_topic_notify
                );

                if ($sendmail['result'] == 'fail') {
                    if (isset($sendmail['debug'])) {
                        $fehler = array();
                        $fehler[] = $sendmail['error'];
                        $fehler[] = $sendmail['debug'];
                        echo generateErrorBoxFromArray($_language->module['errors_there'], $fehler);
                    }
                }
            }
        }

        if (isset($_POST['notify']) && (bool)$_POST['notify']) {
            safe_query(
                "INSERT INTO " . PREFIX . "forum_notify (topicID, userID) VALUES('" . $topic . "', '" . $userID .
                "') "
            );
        }
    } else {
        safe_query(
            "INSERT INTO " . PREFIX .
            "forum_posts_spam ( boardID, topicID, date, poster, message, rating ) VALUES( '" . $_REQUEST['board'] .
            "', '$topic', '$date', '$userID', '" . $message . "', '" . $rating . "' ) "
        );
    }
    header("Location: index.php?site=forum_topic&topic=" . $topic . "&page=" . $page);
    exit();
} else if (isset($_POST['editreply']) && (bool)$_POST['editreply']) {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('forum');

    if (!isforumposter($userID, $_POST['id']) && !isforumadmin($userID) && !ismoderator($userID, $_GET['board'])
    ) {
        die($_language->module['no_accses']);
    }

    $message = $_POST['message'];
    $id = (int)$_POST['id'];
    $check = mysqli_num_rows(
        safe_query(
            "SELECT postID FROM " . PREFIX . "forum_posts WHERE postID='" . $id .
            "' AND poster='" . $userID . "'"
        )
    );
    if (($check || isforumadmin($userID) || ismoderator($userID, (int)$_GET['board'])) && mb_strlen(trim($message))
    ) {
        if (isforumadmin($userID) || ismoderator($userID, (int)$_GET['board'])) {
            $do_sticky = (isset($_POST['sticky'])) ? 'sticky=1' : 'sticky=0';
            safe_query(
                "UPDATE " . PREFIX . "forum_topics SET $do_sticky WHERE topicID='" . (int)$_GET['topic'] .
                "'"
            );
        }

        $date = getformatdatetime(time());
        safe_query("UPDATE " . PREFIX . "forum_posts SET message = '" . $message . "' WHERE postID='$id' ");
        safe_query(
            "DELETE FROM " . PREFIX . "forum_notify WHERE userID='$userID' AND topicID='" .
            (int)$_GET['topic'] . "'"
        );
        if (isset($_POST['notify'])) {
            if ((bool)$_POST['notify']) {
                safe_query(
                    "INSERT INTO " . PREFIX .
                    "forum_notify (`notifyID`, `userID`, `topicID`) VALUES ('', '$userID', '" . (int)$_GET['topic'] .
                    "')"
                );
            }
        }
    }
    header("Location: index.php?site=forum_topic&topic=" . (int)$_GET['topic'] . "&page=" . $page);
} else if (isset($_POST['saveedittopic']) && (bool)$_POST['saveedittopic']) {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('forum');

    if (!isforumadmin($userID)
        && !isforumposter($userID, $_POST['post']) && !ismoderator($userID, $_GET['board'])
    ) {
        die($_language->module['no_accses']);
    }

    $board = (int)$_GET['board'];
    $topic = (int)$_GET['topic'];
    $post = $_POST['post'];
    if (isset($_POST['notify'])) {
        $notify = (bool)$_POST['notify'];
    } else {
        $notify = false;
    }
    $topicname = $_POST['topicname'];
    if (!$topicname) {
        $topicname = $_language->module['default_topic_title'];
    }
    $message = $_POST['message'];
    if (mb_strlen($message)) {
        if (isset($_POST['icon'])) {
            $icon = $_POST['icon'];
        } else {
            $icon = '';
        }
        if (isforumadmin($userID) || ismoderator($userID, $board)) {
            if (isset($_POST['sticky'])) {
                $do_sticky = 1;
            } else {
                $do_sticky = 0;
            }
            safe_query(
                "UPDATE " . PREFIX . "forum_topics SET sticky='" . $do_sticky . "' WHERE topicID='" . $topic . "'"
            );
        }

        safe_query("UPDATE " . PREFIX . "forum_posts SET message='" . $message . "' WHERE postID='" . $post . "'");
        safe_query(
            "UPDATE " . PREFIX . "forum_topics SET topic='" . $topicname . "', icon='" . $icon . "' " .
            "WHERE topicID='" . $topic . "'"
        );

        if ($notify == 1) {
            $notified =
                safe_query(
                    "SELECT * FROM " . PREFIX . "forum_notify WHERE topicID='" . $topic . "' AND userID='" .
                    $userID . "'"
                );
            if (mysqli_num_rows($notified) != 1) {
                safe_query(
                    "INSERT INTO " . PREFIX .
                    "forum_notify (notifyID, topicID, userID) VALUES ('', '$topic', '$userID')"
                );
            }
        } else {
            safe_query(
                "DELETE FROM " . PREFIX . "forum_notify WHERE topicID='" . $topic . "' AND userID='" . $userID .
                "'"
            );
        }
    }
    header("Location: index.php?site=forum_topic&topic=" . $topic);
}

function showtopic($topic, $edit, $addreply, $quoteID, $type)
{
    global $userID;
    global $loggedin;
    global $page;
    global $maxposts;
    global $preview;
    global $message;
    global $picsize_l;
    global $_language;
    global $spamapikey;

    $_language->readModule('forum');
    $_language->readModule('bbcode', true);

    $thread = safe_query("SELECT * FROM " . PREFIX . "forum_topics WHERE topicID='$topic' ");
    $dt = mysqli_fetch_array($thread);

    $usergrp = 0;
    $writer = 0;
    $ismod = ismoderator($userID, $dt['boardID']);
    if ($dt['writegrps'] != "" && !$ismod) {
        $writegrps = explode(";", $dt['writegrps']);
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
    if ($dt['readgrps'] != "" && !$usergrp && !$ismod) {
        $readgrps = explode(";", $dt['readgrps']);
        foreach ($readgrps as $value) {
            if (isinusergrp($value, $userID)) {
                $usergrp = 1;
                break;
            }
        }
        if (!$usergrp) {
            echo $_language->module['no_permission'];
            redirect('index.php?site=forum', $_language->module['no_permission'], 2);
            return;
        }
    }
    $gesamt = mysqli_num_rows(safe_query("SELECT topicID FROM " . PREFIX . "forum_posts WHERE topicID='$topic'"));
    if ($gesamt == 0) {
        die($_language->module['topic_not_found'] . " <a href=\"javascript:history.back()\">back</a>");
    }
    $pages = 1;
    if (!isset($page)) {
        $page = 1;
    }
    if (isset($type)) {
        if (!(($type == 'ASC') || ($type == 'DESC'))) {
            $type = "ASC";
        }
    } else {
        $type = "ASC";
    }
    $max = $maxposts;
    $pages = ceil($gesamt / $maxposts);

    $page_link = makepagelink("index.php?site=forum_topic&amp;topic=$topic&amp;type=$type", $page, $pages);

    if ($type == "ASC") {
        $sorter =
            '<a href="index.php?site=forum_topic&amp;topic=' . $topic . '&amp;page=' . $page . '&amp;type=DESC">' .
            $_language->module['sort'] . ' <span class="bi bi-arrow-down-circle"></span></a>';
    } else {
        $sorter = '<a href="index.php?site=forum_topic&amp;topic=' . $topic . '&amp;page=' . $page . '&amp;type=ASC">' .
            $_language->module['sort'] . ' <span class="bi bi-arrow-up-circle"></span></a>';
    }

    $start = getStartValue($page, $max);

    safe_query("UPDATE " . PREFIX . "forum_topics SET views=views+1 WHERE topicID='$topic' ");

    // viewed topics

    if (mysqli_num_rows(safe_query("SELECT userID FROM " . PREFIX . "user WHERE topics LIKE '%|" . $topic . "|%'"))) {
        $gv = mysqli_fetch_array(safe_query("SELECT topics FROM " . PREFIX . "user WHERE userID='$userID'"));
        $array = explode("|", $gv['topics']);
        $new = '|';

        foreach ($array as $split) {
            if ($split != "" && $split != $topic) {
                $new = $new . $split . '|';
            }
        }

        safe_query("UPDATE " . PREFIX . "user SET topics='" . $new . "' WHERE userID='$userID'");
    }

    // end viewed topics
	$title_messageboard = $GLOBALS["_template"]->replaceTemplate("title_messageboard", array());
	echo $title_messageboard;

    $topicname = getinput($dt['topic']);

    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "forum_boards WHERE boardID='" . $dt['boardID'] . "' ");
    $db = mysqli_fetch_array($ergebnis);
    $boardname = $db['name'];

    $topicactions = '<a href="printview.php?board=' . $dt['boardID'] . '&amp;topic=' . $topic .
        '" target="_blank" class="btn btn-default"><span class="bi bi-printer-fill"></span></a>';
    if ($loggedin && $writer) {
        $topicactions .=
            '<a href="index.php?site=forum&amp;addtopic=true&amp;action=newtopic&amp;board=' . $dt['boardID'] .
            '" class="btn btn-primary hidden">' . $_language->module['new_topic'] .
            '</a> <a href="index.php?site=forum_topic&amp;topic=' . $topic . '&amp;addreply=true&amp;page=' . $pages .
            '&amp;type=' . $type . '" class="btn btn-primary"><span class="bi bi-share-fill"></span> ' . $_language->module['new_reply'] . '</a>';
    }

    $kathname = getcategoryname($db['category']);
    $data_array = array();
    $data_array['$kathname'] = $kathname;
    $data_array['$category'] = (int)$db['category'];
    $data_array['$board'] = (int)$dt['boardID'];
    $data_array['$boardname'] = $boardname;
    $data_array['$topicname'] = $topicname;
    $forum_topics_title = $GLOBALS["_template"]->replaceTemplate("forum_topics_title", $data_array);
    echo $forum_topics_title;

    $data_array = array();
    $data_array['$sorter'] = $sorter;
    $data_array['$page_link'] = $page_link;
    $data_array['$topicactions'] = $topicactions;
    $forum_topics_actions = $GLOBALS["_template"]->replaceTemplate("forum_topics_actions", $data_array);
    echo $forum_topics_actions;

    if ($dt['closed']) {
        echo generateAlert($_language->module['closed_image'], 'alert-danger');
    }

    if ($edit && !$dt['closed']) {
        $id = $_GET['id'];
        $dr = mysqli_fetch_array(safe_query("SELECT * FROM " . PREFIX . "forum_posts WHERE postID='" . $id . "'"));
        $topic = $_GET['topic'];

        $_sticky = ($dt['sticky'] == '1') ? 'checked="checked"' : '';

        $anz = mysqli_num_rows(
            safe_query(
                "SELECT * FROM " . PREFIX . "forum_posts WHERE topicID='" . $dt['topicID'] .
                "' AND postID='" . $id . "' AND poster='" . $userID . "' ORDER BY DATE ASC LIMIT 0,1"
            )
        );

        $board = $dt['boardID'];

        if ($anz || isforumadmin($userID) || ismoderator($userID, $board)) {
            if (istopicpost($dt['topicID'], $id)) {

                // topicmessage
                $message = getinput($dr['message']);
                $post = $id;

                // notification check
                $notifyqry =
                    safe_query(
                        "SELECT * FROM " . PREFIX . "forum_notify WHERE topicID='" . $topic . "' AND userID='" .
                        $userID . "'"
                    );
                if (mysqli_num_rows($notifyqry)) {
                    $notify = '<input class="input" type="checkbox" name="notify" value="1" checked="checked"> ' .
                        $_language->module['notify_reply'] . '<br>';
                } else {
                    $notify = '<input class="input" type="checkbox" name="notify" value="1"> ' .
                        $_language->module['notify_reply'] . '<br>';
                }
                //STICKY
                if (isforumadmin($userID) || ismoderator($userID, $board)) {
                    $chk_sticky =
                        '<br>' . "\n" . ' <input class="input" type="checkbox" name="sticky" value="1" ' . $_sticky .
                        '> ' . $_language->module['make_sticky'];
                } else {
                    $chk_sticky = '';
                }


                $iconlist = $GLOBALS["_template"]->replaceTemplate("forum_newtopic_iconlist", array());
                if ($dt['icon']) {
                    $iconlist =
                        str_replace(
                            'value="' . $dt['icon'] . '"',
                            'value="' . $dt['icon'] . '" checked="checked"',
                            $iconlist
                        );
                } else {
                    $iconlist = str_replace('value="0"', 'value="0" checked="checked"', $iconlist);
                }
                $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
                $data_array = array();
                $data_array['$board'] = $board;
                $data_array['$topic'] = $topic;
                $data_array['$iconlist'] = $iconlist;
                $data_array['$topicname'] = $topicname;
                $data_array['$addbbcode'] = $addbbcode;
                $data_array['$message'] = $message;
                $data_array['$notify'] = $notify;
                $data_array['$chk_sticky'] = $chk_sticky;
                $data_array['$post'] = $post;
                $forum_edittopic = $GLOBALS["_template"]->replaceTemplate("forum_edittopic", $data_array);
                echo $forum_edittopic;
            } else {
                // notification check
                $notifyqry =
                    safe_query(
                        "SELECT * FROM " . PREFIX . "forum_notify WHERE topicID='" . $topic . "' AND userID='" .
                        $userID . "'"
                    );
                if (mysqli_num_rows($notifyqry)) {
                    $notify = '<input class="input" type="checkbox" name="notify" value="1" checked="checked"> ' .
                        $_language->module['notify_reply'];
                } else {
                    $notify = '<input class="input" type="checkbox" name="notify" value="1"> ' .
                        $_language->module['notify_reply'];
                }

                //STICKY
                if (isforumadmin($userID) || ismoderator($userID, $board)) {
                    $chk_sticky = '<input class="input" type="checkbox" name="sticky" value="1" ' . $_sticky . '> ' .
                        $_language->module['make_sticky'];
                } else {
                    $chk_sticky = '';
                }
                $dr['message'] = getinput($dr['message']);
                $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
                $data_array = array();
                $data_array['$boardID'] = $dr['boardID'];
                $data_array['$message'] = $dr['message'];
                $data_array['$topic'] = $topic;
                $data_array['$page'] = $page;
                $data_array['$addbbcode'] = $addbbcode;
                $data_array['$notify'] = $notify;
                $data_array['$chk_sticky'] = $chk_sticky;
                $data_array['$id'] = $id;
                $forum_editpost = $GLOBALS["_template"]->replaceTemplate("forum_editpost", $data_array);
                echo $forum_editpost;
            }
        } else {
            echo generateAlert($_language->module['permission_denied'], 'alert-danger');
        }

        $replys = safe_query(
            "SELECT * FROM " . PREFIX .
            "forum_posts WHERE topicID='$topic' ORDER BY date DESC LIMIT $start, $max"
        );
    } else if ($addreply && !$dt['closed']) {
        if ($loggedin && $writer) {
            if (isset($_POST['preview'])) {

                $time = getformattime(time());
                $date = $_language->module['today'];

                $message_preview = getforminput($_POST['message']);

                $message = cleartext(getforminput($_POST['message']));

                $message = toggle($message, 'xx');
                $username = '<a href="index.php?site=profile&amp;id=' . $userID . '"><strong>' . getnickname($userID) . '</strong></a>';

                if ($getavatar = getavatar($userID)) {
                    $avatar = '<img src="images/avatars/' . $getavatar . '" alt="">';
                } else {
                    $avatar = '';
                }
                if ($getsignatur = getsignatur($userID)) {
                    $signatur = cleartext($getsignatur);
                } else {
                    $signatur = '';
                }
                if (getemail($userID) && !getemailhide($userID)) {
                    $email = '<a href="mailto:' . mail_protect(getemail($userID)) . '"><span class="bi bi-envelope-fill" title="email"></span></a>';
                } else {
                    $email = '';
                }

                $pm = '';
                $buddy = '';
                $statuspic = '<img src="images/icons/online.gif" alt="online">';
                if (!validate_url(gethomepage($userID))) {
                    $hp = '';
                } else {
                    $hp =
                        '<a href="' . gethomepage($userID) . '" target="_blank" data-toggle="tooltip" data-placement="top" title="' . $_language->module['homepage'] . '"><span class="bi bi-house-fill"></span></a>';
                }
                $registered = getregistered($userID);
                $posts = getuserforumposts($userID);

                if (isforumadmin($userID)) {
                    $usertype = $_language->module[getConstNameAdmin()];
                    $rang = '<img src="images/icons/ranks/admin.gif" alt="">';
                } else if (isanymoderator($userID)) {
                    $usertype = $_language->module['moderator'];
                    $rang = '<img src="images/icons/ranks/moderator.gif" alt="">';
                } else {
                    $ergebnis = safe_query(
                        "SELECT * FROM " . PREFIX .
                        "forum_ranks WHERE $posts >= postmin AND $posts <= postmax AND postmax >0 AND special='0'"
                    );
                    $ds = mysqli_fetch_array($ergebnis);
                    $usertype = $ds['rank'];
                    $rang = '<img src="images/icons/ranks/' . $ds['pic'] . '" alt="">';
                }

                $specialrang = "";
                $specialtype = "";
                $getrank = safe_query(
                    "SELECT IF
                        (u.special_rank = 0, 0, CONCAT_WS('__',r.rank, r.pic)) as RANK
                    FROM
                        " . PREFIX . "user u LEFT JOIN " . PREFIX . "forum_ranks r ON u.special_rank = r.rankID
                    WHERE
                        userID = '" . $userID . "'"
                );
                $rank_data = mysqli_fetch_assoc($getrank);

                if ($rank_data[ 'RANK' ] != '0') {
                    $tmp_rank = explode("__", $rank_data[ 'RANK' ], 2);
                    $specialrang = $tmp_rank[0];
                    if (!empty($tmp_rank[1]) && file_exists("images/icons/ranks/" . $tmp_rank[1])) {
                        $specialtype =
                            "<img src='images/icons/ranks/" . $tmp_rank[1] . "' alt = '" . $specialrang . "' />";
                    }
                }

                $quote = "";
                $actions = "";
                echo '<table class="table">
                <tr>
                <td colspan="2" class="title" class="text-center">' . $_language->module['preview'] . '</td>
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

                $message = $message_preview;
            } else {
                if ($quoteID) {
                    $ergebnis =
                        safe_query("SELECT poster,message FROM " . PREFIX . "forum_posts WHERE postID='$quoteID'");
                    $ds = mysqli_fetch_array($ergebnis);
                    $message = '[quote=' . getnickname($ds['poster']) . ']' . getinput($ds['message']) . '[/quote]';
                }
            }
            if (isset($_POST['sticky'])) {
                $post_sticky = $_POST['sticky'];
            } else {
                $post_sticky = null;
            }
            $_sticky = ($dt['sticky'] == '1' || $post_sticky == '1') ? 'checked="checked"' : '';
            if (isforumadmin($userID) || ismoderator($userID, $dt['boardID'])) {
                $chk_sticky = '<input class="input" type="checkbox" name="sticky" value="1" ' . $_sticky . '> ' . $_language->module['make_sticky'];
            } else {
                $chk_sticky = '';
            }

            if (isset($_POST['notify'])) {
                $post_notify = $_POST['notify'];
            } else {
                $post_notify = null;
            }
            $mysql_notify =
                mysqli_num_rows(
                    safe_query(
                        "SELECT notifyID FROM " . PREFIX . "forum_notify WHERE userID='" . $userID .
                        "' AND topicID='" . $topic . "'"
                    )
                );
            $notify = ($mysql_notify || $post_notify == '1') ? 'checked="checked"' : '';

            $board = $dt['boardID'];

            $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
            $data_array = array();
            $data_array['$addbbcode'] = $addbbcode;
            $data_array['$message'] = $message;
            $data_array['$notify'] = $notify;
            $data_array['$chk_sticky'] = $chk_sticky;
            $data_array['$userID'] = $userID;
            $data_array['$board'] = $board;
            $data_array['$topic'] = $topic;
            $data_array['$page'] = $page;
            $forum_newreply = $GLOBALS["_template"]->replaceTemplate("forum_newreply", $data_array);
            echo $forum_newreply;
        } else if ($loggedin) {
            echo generateAlert($_language->module['no_access_write'], 'alert-danger');
        } else {
            echo generateAlert($_language->module['not_logged_msg'], 'alert-danger');
        }
        $replys =
            safe_query(
                "SELECT * FROM " . PREFIX . "forum_posts WHERE topicID='$topic' ORDER BY date DESC LIMIT 0, " .
                $max . ""
            );
    } else {
        $replys =
            safe_query(
                "SELECT * FROM " . PREFIX . "forum_posts WHERE topicID='$topic' ORDER BY date $type LIMIT " .
                $start . ", " . $max . ""
            );
    }

    $forum_topic_head = $GLOBALS["_template"]->replaceTemplate("forum_topic_head", array());
    echo $forum_topic_head;

    while ($dr = mysqli_fetch_array($replys)) {

        $date = getformatdate($dr['date']);
        $time = getformattime($dr['date']);

        $today = getformatdate(time());
        $yesterday = getformatdate(time() - 3600 * 24);

        if ($date == $today) {
            $date = $_language->module['today'];
        } else if ($date == $yesterday && $date < $today) {
            $date = $_language->module['yesterday'];
        }

        $postID = $dr['postID'];

        $message = cleartext($dr['message']);
        $message = toggle($message, $postID);

        $username = '<a href="index.php?site=profile&amp;id=' . $dr['poster'] . '"><strong>' .
            stripslashes(getnickname($dr['poster'])) . '</strong></a>';

        if ($getavatar = getavatar($dr['poster'])) {
            $avatar = '<img src="images/avatars/' . $getavatar . '" alt="">';
        } else {
            $avatar = '';
        }

        if ($getsignatur = getsignatur($dr['poster'])) {
            $signatur = cleartext($getsignatur);
        } else {
            $signatur = '';
        }

        if (getemail($dr['poster']) && !getemailhide($dr['poster'])) {
            $email =
                '<a href="mailto:' . mail_protect(getemail($dr['poster'])) .'"><span class="bi bi-envelope-fill"></span></a>';
        } else {
            $email = '';
        }

        $pm = '';
        $buddy = '';
        if ($loggedin && $dr['poster'] != $userID) {
            $pm = '<a href="index.php?site=messenger&amp;action=touser&amp;touser=' . $dr['poster'] .'" data-toggle="tooltip" data-placement="top" title="' . $_language->module['message'] . '"><span class="bi bi-envelope-fill"></span></a>';
            if (isignored($userID, $dr['poster'])) {
                $buddy = '<a href="buddies.php?action=readd&amp;id=' . $dr['poster'] . '&amp;userID=' . $userID .
                    '" data-toggle="tooltip" data-placement="top" title="' . $_language->module['back_buddy'] . '"><span class="bi bi-person-plus-fill"></span></a>';
            } else if (isbuddy($userID, $dr['poster'])) {
                $buddy = '<a href="buddies.php?action=ignore&amp;id=' . $dr['poster'] . '&amp;userID=' . $userID .
                    '" data-toggle="tooltip" data-placement="top" title="' . $_language->module['ignore'] . '"><span class="bi bi-person-dash-fill"></span></a></a>';
            } else {
                $buddy = '<a href="buddies.php?action=add&amp;id=' . $dr['poster'] . '&amp;userID=' . $userID .
                    '" data-toggle="tooltip" data-placement="top" title="' . $_language->module['add_buddy'] . '"><span class="bi bi-person-plus-fill"></span></a>';
            }
        }

        if (isonline($dr['poster']) == "offline") {
            $statuspic = '<span class="bi bi-circle-fill text-danger" aria-hidden="true"></span>';
        } else {
            $statuspic = '<span class="bi bi-circle-fill text-success" aria-hidden="true"></span>';
        }

        if (!validate_url(gethomepage($dr['poster']))) {
            $hp = '';
        } else {
            $hp =
                '<a href="' . gethomepage($dr['poster']) . '" target="_blank" title="'.$_language->module['homepage'] .'""><span class="bi bi-house-fill"></span></a>';
        }

        if (!$dt['closed']) {
            $quote =
                '<a href="index.php?site=forum_topic&amp;addreply=true&amp;board=' . $dt['boardID'] . '&amp;topic=' .
                $topic . '&amp;quoteID=' . $postID . '&amp;page=' . $page . '&amp;type=' . $type .
                '" data-toggle="tooltip" data-placement="top" title="' . $_language->module['quote'] . '"><span class="bi bi-chat-left-quote-fill"></span></a>';
        } else {
            $quote = "";
        }

        $registered = getregistered($dr['poster']);

        $posts = getuserforumposts($dr['poster']);

        if (isforumadmin($dr['poster'])) {
            $usertype = $_language->module[getConstNameAdmin()];
            $rang = '<img src="images/icons/ranks/admin.gif" alt="">';
        } else if (isanymoderator($dr['poster'])) {
            $usertype = $_language->module['moderator'];
            $rang = '<img src="images/icons/ranks/moderator.gif" alt="">';
        } else {
            $ergebnis = safe_query(
                "SELECT * FROM " . PREFIX .
                "forum_ranks WHERE $posts >= postmin AND $posts <= postmax AND postmax >0 AND special='0'"
            );
            $ds = mysqli_fetch_array($ergebnis);
            $usertype = $ds['rank'];
            $rang = '<img src="images/icons/ranks/' . $ds['pic'] . '" alt="">';
        }

        $specialrang = "";
        $specialtype = "";
        $getrank = safe_query(
            "SELECT IF
                        (u.special_rank = 0, 0, CONCAT_WS('__',r.rank, r.pic)) as RANK
                    FROM
                        " . PREFIX . "user u LEFT JOIN " . PREFIX . "forum_ranks r ON u.special_rank = r.rankID
                    WHERE
                        userID = '" . $dr['poster'] . "'"
        );
        $rank_data = mysqli_fetch_assoc($getrank);

        if ($rank_data[ 'RANK' ] != '0') {
            $tmp_rank = explode("__", $rank_data[ 'RANK' ], 2);
            $specialrang = $tmp_rank[0];
            if (!empty($tmp_rank[1]) && file_exists("images/icons/ranks/" . $tmp_rank[1])) {
                $specialtype = "<img src='images/icons/ranks/" . $tmp_rank[1] . "' alt = '" . $specialrang . "' />";
            }
        }

        $actions = '';
        if (($userID == $dr['poster'] || isforumadmin($userID) || ismoderator($userID, $dt['boardID']))
            && !$dt['closed']
        ) {
            $actions = ' <a href="index.php?site=forum_topic&amp;topic=' . $topic . '&amp;edit=true&amp;id=' . $postID . '" data-toggle="tooltip" data-placement="top" title="' . $_language->module['edit'] . '"><span class="bi bi-pencil-square"></span></a> ';
        }
        if (isforumadmin($userID) || ismoderator($userID, $dt['boardID'])) {
            $actions .= '<input class="input" type="checkbox" name="postID[]" value="' . $postID . '">';
        }

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
        unset($actions);

    }

    $adminactions = "";
    if (isforumadmin($userID) || ismoderator($userID, $dt['boardID'])) {
        if ($dt['closed']) {
            $close = '<option value="opentopic">- ' . $_language->module['reopen_topic'] . '</option>';
        } else {
            $close = '<option value="closetopic">- ' . $_language->module['close_topic'] . '</option>';
        }

        $adminactions = '<div class="row">
        <div class="col-xs-8 text-left"><input type="checkbox" name="ALL" value="ALL" onclick="SelectAll(this.form);">
            ' . $_language->module['select_all'] . '</div>
            <div class="col-xs-4">
        <div class="input-group text-right">
        <select name="admaction" class="form-control">
            <option value="0">' . $_language->module['admin_actions'] . ':</option>
            <option value="delposts">- ' . $_language->module['delete_posts'] . '</option>
            <option value="stickytopic">- ' . $_language->module['make_topic_sticky'] . '</option>
            <option value="unstickytopic">- ' . $_language->module['make_topic_unsticky'] . '</option>
            <option value="movetopic">- ' . $_language->module['move_topic'] . '</option>
            ' . $close . '
            <option value="deletetopic">- ' . $_language->module['delete_topic'] . '</option>
        </select>
        <span class="input-group-btn">
        <input type="submit" name="submit" value="' . $_language->module['go'] . '" class="btn btn-danger">
        </span></div></div>
        <input type="hidden" name="topicID" value="' . $topic . '">
        <input type="hidden" name="board" value="' . $dt['boardID'] . '"></div>';

    }

    $forum_topic_foot = $GLOBALS["_template"]->replaceTemplate("forum_topic_foot", array());
    echo $forum_topic_foot;

    $data_array = array();
    $data_array['$sorter'] = $sorter;
    $data_array['$page_link'] = $page_link;
    $data_array['$topicactions'] = $topicactions;
    $forum_topics_actions = $GLOBALS["_template"]->replaceTemplate("forum_topics_actions", $data_array);
    echo $forum_topics_actions;

    echo '<div class="text-right">' . $adminactions . '</div></form><br>';

    if ($dt['closed']) {
        echo generateAlert($_language->module['closed_image'], 'alert-danger');
    } else {
        if (!$loggedin && !$edit) {
            echo $_language->module['not_logged_msg'];
        }
    }
}

showtopic($topic, $edit, $addreply, $quoteID, $type);
