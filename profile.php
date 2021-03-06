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

$_language->readModule('profile');

if (isset($_GET[ 'id' ])) {
    $id = (int)$_GET[ 'id' ];
} else {
    $id = $userID;
}

$action = getAction();

if (isset($id) && getnickname($id) != '') {

    if (isbanned($id)) {
        $banned =
            '<br><p class="text-center" style="color:red;font-weight:bold;font-size:11px;letter-spacing:1px;">' .
            $_language->module[ 'is_banned' ] . '</p>';
    } else {
        $banned = '';
    }

    if ($user_guestbook == 1) {
        if (getuserguestbookstatus($id) == 1) {
            $title_user_guestbook = '<td class="title" width="20%">&nbsp; <a class="titlelink" href="index.php?site=profile&amp;id=' . $id . '&amp;action=guestbook">' . $_language->module[ 'guestbook' ] . '</a></td>';
            $title_width_main = 14;
            $title_width_galleries = 18;
            $title_width_buddys = 18;
            $title_width_lastposts = 30;
            $title_colspan = 5;
        } else {
            $title_user_guestbook = '';
            $title_width_main = 19;
            $title_width_galleries = 23;
            $title_width_buddys = 23;
            $title_width_lastposts = 35;
            $title_colspan = 5;
        }
    } else {
        $title_user_guestbook = '';
        $title_width_main = 19;
        $title_width_galleries = 23;
        $title_width_buddys = 23;
        $title_width_lastposts = 35;
        $title_colspan = 5;
    }

    //profil: buddys
    if ($action == "buddies") {
        $data_array = array();
        $data_array['$id'] = $id;
        $data_array['$profilelast'] = $profilelast;
        $data_array['$banned'] = $banned;
        $title_profile = $GLOBALS["_template"]->replaceTemplate("title_profile", $data_array);
        echo $title_profile;

        $buddylist = "";
        $buddys = safe_query("SELECT buddy FROM " . PREFIX . "buddys WHERE userID='" . $id . "'");
        if (mysqli_num_rows($buddys)) {

            while ($db = mysqli_fetch_array($buddys)) {

                $flag = '[flag]' . getcountry($db[ 'buddy' ]) . '[/flag]';
                $country = flags($flag);
                $nicknamebuddy = getnickname($db[ 'buddy' ]);
                $email = '<a class="btn btn-success btn-xs" href="mailto:' . mail_protect(getemail($db[ 'buddy' ])) . '" data-toggle="tooltip" data-placement="top" title="' . $_language->module[ 'email' ] . '"><span class="bi bi-envelope-fill"></span></a>';

                if (isignored($userID, $db[ 'buddy' ])) {
                    $buddy =
                        '<a class="btn btn-danger btn-xs" href="buddies.php?action=readd&amp;id=' . $db[ 'buddy' ] . '&amp;userID=' . $userID . '" data-toggle="tooltip" data-placement="top" title="' . $_language->module[ 'back_buddylist' ] . '"><span class="bi bi-person-plus-fill"></span></a>';
                } else if (isbuddy($userID, $db[ 'buddy' ])) {
                    $buddy = '<a class="btn btn-danger btn-xs"
                        href="buddies.php?action=ignore&amp;id=' . $db[ 'buddy' ] . '&amp;userID=' . $userID . '" data-toggle="tooltip" data-placement="top" title="' . $_language->module[ 'ignore_user' ] . '"><span class="bi bi-person-dash-fill"></span></a>';
                } else if ($userID == $db[ 'buddy' ]) {
                    $buddy = '';
                } else {
                    $buddy = '<a href="buddies.php?action=add&amp;id=' . $db[ 'buddy' ] . '&amp;userID=' . $userID . '" data-toggle="tooltip" data-placement="top" title="' . $_language->module[ 'add_buddylist' ] . '"><span class="bi bi-person-plus-fill"></span></a>';
                }

                if (isonline($db[ 'buddy' ]) == "offline") {
                    $statuspic = '<span class="label label-danger">' . $_language->module[ 'offline' ] . '</span>';
                } else {
                    $statuspic = '<span class="label label-success">' . $_language->module[ 'online' ] . '</span>';
                }

                $buddylist .= '<tr>
            <td>' . $country . ' <a href="index.php?site=profile&amp;id=' . $db[ 'buddy' ] . '"><strong>' .
                        $nicknamebuddy . '</strong></a>
                    <div class="pull-right">' . $email . '&nbsp;&nbsp;' . $buddy . '&nbsp;&nbsp;' . $statuspic . '</div>
			</td>
            </tr>';

            }
        } else {
            $buddylist = '<tr>
            <td colspan="2">' . $_language->module[ 'no_buddies' ] . '</td>
        </tr>';
        }

        $data_array = array();
        $data_array['$id'] = $id;
        $data_array['$profilelast'] = $profilelast;
        $data_array['$buddylist'] = $buddylist;
        $profile = $GLOBALS["_template"]->replaceTemplate("profile_buddys", $data_array);
        echo $profile;
    } else if ($action == "galleries") {
        //galleries
        $data_array = array();
        $data_array['$id'] = $id;
        $data_array['$profilelast'] = $profilelast;
        $data_array['$banned'] = $banned;
        $title_profile = $GLOBALS["_template"]->replaceTemplate("title_profile", $data_array);
        echo $title_profile;

        $galclass = new \webspell\Gallery();

        $galleries = safe_query("SELECT * FROM " . PREFIX . "gallery WHERE userID='" . $id . "'");

        echo '<div class="row">
	<div class="col-xs-8">
<div class="card">
<div class="card-header">' . $_language->module[ 'galleries' ] . ' ' . $_language->module[ 'by' ] . ' ' . getnickname($id) . '</div>
<table class="table table-hover">
        <tr>
            <td></td>
            <td><strong>' . $_language->module[ 'date' ] . '</strong></td>
            <td><strong>' . $_language->module[ 'name' ] . '</strong></td>
            <td><strong>' . $_language->module[ 'pictures' ] . '</strong></td>
        </tr>';

        if ($usergalleries) {
            if (mysqli_num_rows($galleries)) {

                while ($ds = mysqli_fetch_array($galleries)) {

                    $piccount =
                        mysqli_num_rows(
                            safe_query(
                                "SELECT
                                    *
                                FROM
                                    " . PREFIX . "gallery_pictures
                                WHERE
                                    galleryID='" . (int)$ds[ 'galleryID' ]."'"
                            )
                        );
                    $commentcount = mysqli_num_rows(
                        safe_query(
                            "SELECT
                                *
                            FROM
                                " . PREFIX . "comments
                            WHERE
                                parentID='" . $ds[ 'galleryID' ] . "' AND
                                type='ga'"
                        )
                    );
                    $gallery[ 'count' ] = mysqli_num_rows(
                        safe_query(
                            "SELECT
                                picID
                            FROM
                                `" . PREFIX . "gallery_pictures`
                            WHERE
                                galleryID='" . (int)$ds[ 'galleryID' ] ."'"
                        )
                    );

                    $data_array = array();
                    $data_array['$date'] = getformatdate($ds[ 'date' ]);
                    $data_array['$picID'] = $galclass->randomPic($ds[ 'galleryID' ]);
                    $data_array['$galleryID'] = $ds[ 'galleryID' ];
                    $data_array['$title'] = cleartext($ds[ 'name' ]);
                    $data_array['$thumbwidth'] = $thumbwidth;
                    $data_array['$count'] = $gallery[ 'count' ];
					$data_array['$id'] = $id;
        			$data_array['$profilelast'] = $profilelast;
                    $profile = $GLOBALS["_template"]->replaceTemplate("profile_galleries", $data_array);
                    echo $profile;

                }
            } else {
                echo '<tr><td colspan="4">' . $_language->module[ 'no_galleries' ] . '</td></tr>';
            }
        } else {
            echo '<tr><td colspan="4">' . $_language->module[ 'usergalleries_disabled' ] . '</td></tr>';
        }

        echo '</table></div></div><div class="col-xs-4">
		<ul class="list-group">
        	<li class="list-group-item text-muted">Quick Links</li>
        	<li class="list-group-item"><a href="index.php?site=profile&amp;id=' . $id . '"><span class="bi bi-person-fill"></span> ' . $_language->module[ 'profile' ] . '</a></li>
            <li class="list-group-item active"><span class="bi bi-camera"></span> ' . $_language->module[ 'galleries' ] . '</li>
            <li class="list-group-item"><a href="index.php?site=profile&amp;id=' . $id . '&amp;action=buddies"><span class="bi bi-people-fill"></span> ' . $_language->module[ 'buddys' ] . '</a></li>
            <li class="list-group-item"><a href="index.php?site=profile&amp;id=' . $id . '&amp;action=lastposts"><span class="bi bi-chat-fill"></span> ' . $_language->module[ 'last' ] . ' ' . $profilelast . ' ' . $_language->module[ 'posts' ] . '</a></li>
            <li class="list-group-item"><a href="index.php?site=profile&amp;id=' . $id . '&amp;action=guestbook"><span class="bi bi-book"></span> ' . $_language->module[ 'guestbook' ] . '</a></li>
        </ul>
	</div></div>';
    } else if ($action == "lastposts") {
        //profil: last posts

        $data_array = array();
        $data_array['$id'] = $id;
        $data_array['$profilelast'] = $profilelast;
        $data_array['$banned'] = $banned;
        $title_profile = $GLOBALS["_template"]->replaceTemplate("title_profile", $data_array);
        echo $title_profile;

        $topiclist = "";
        $topics = safe_query(
            "SELECT
                *
            FROM
                " . PREFIX . "forum_topics
            WHERE
                userID = '" . $id . "' AND
                moveID = 0
            ORDER BY
                date DESC"
        );
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

                $topiclist .= '<tr><td>
                        <div style="overflow:hidden;">
                            <div class="pull-right"><small>' . $posttime . '</small></div>
							<a href="index.php?site=forum_topic&amp;topic=' . $db[ 'topicID' ] . '">
                                <strong>' . clearfromtags($db[ 'topic' ]) . '</strong>
                            </a><br>
                            <span>' . $db[ 'views' ] . ' ' . $_language->module[ 'views' ] . ' - ' .
                            $db[ 'replys' ] . ' ' . $_language->module[ 'replys' ] . '</span>
                        </div>
                    </td>
                </tr>';

                if ($profilelast == $n) {
                    break;
                }
                $n++;
            }
        } else {
            $topiclist = '<tr><td colspan="2">' . $_language->module[ 'no_topics' ] . '</td></tr>';
        }

        $postlist = "";
        $posts =
            safe_query(
                "SELECT
                    " . PREFIX . "forum_topics.boardID,
                    " . PREFIX . "forum_topics.readgrps,
                    " . PREFIX . "forum_topics.topicID,
                    " . PREFIX . "forum_topics.topic,
                    " . PREFIX . "forum_posts.date,
                    " . PREFIX . "forum_posts.message
                FROM
                    " . PREFIX . "forum_posts,
                    " . PREFIX . "forum_topics
                WHERE
                    " . PREFIX . "forum_posts.poster = '" . $id . "' AND
                    " . PREFIX . "forum_posts.topicID = " . PREFIX . "forum_topics.topicID
                ORDER BY date DESC"
            );
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
                    ) . "...";
                } else {
                    $message = $db[ 'message' ];
                }

                $postlist .= '<tr><td>
                        <div style="overflow:hidden;">
						<div class="pull-right"><small>' . $posttime . '</small></div>
                            <a href="index.php?site=forum_topic&amp;topic=' . $db[ 'topicID' ] . '">
                                <strong>' . clearfromtags($db[ 'topic' ]) . '</strong>
                            </a><br>
                            <span>' . clearfromtags($message) . '</span>
                        </div>
                    </td>
                </tr>';

                if ($profilelast == $n) {
                    break;
                }
                $n++;
            }
        } else {
            $postlist = '<tr><td colspan="2">' . $_language->module[ 'no_posts' ] . '</td></tr>';
        }

        $data_array = array();
        $data_array['$id'] = $id;
        $data_array['$profilelast'] = $profilelast;
        $data_array['$topiclist'] = $topiclist;
        $data_array['$postlist'] = $postlist;
        $profile = $GLOBALS["_template"]->replaceTemplate("profile_lastposts", $data_array);
        echo $profile;
    } else if ($action == "guestbook") {
        if ($user_guestbook == 1) {
            if (getuserguestbookstatus($id) == 1) {
                //user guestbook
                if (isset($_POST[ 'save' ])) {
                    $date = time();
                    $ip = $GLOBALS[ 'ip' ];
                    $run = 0;

                    if ($userID) {
                        $name = getnickname($userID);
                        if (getemailhide($userID)) {
                            $email = '';
                        } else {
                            $email = getemail($userID);
                        }
                        $url = gethomepage($userID);
                        $icq = geticq($userID);
                        $run = 1;
                    } else {
                        $name = $_POST[ 'gbname' ];
                        $email = $_POST[ 'gbemail' ];
                        $url = $_POST[ 'gburl' ];
                        $icq = $_POST[ 'icq' ];
                        $CAPCLASS = new \webspell\Captcha;
                        if ($CAPCLASS->checkCaptcha($_POST[ 'captcha' ], $_POST[ 'captcha_hash' ])) {
                            $run = 1;
                        }
                    }

                    if ($run) {
                        safe_query(
                            "INSERT INTO
                                " . PREFIX . "user_gbook (
                                    `userID`,
                                    `date`,
                                    `name`,
                                    `email`,
                                    `hp`,
                                    `icq`,
                                    `ip`,
                                    `comment`
                                )
                                values(
                                    '" . $id . "',
                                    '" . $date . "',
                                    '" . $_POST[ 'gbname' ] . "',
                                    '" . $_POST[ 'gbemail' ] . "',
                                    '" . $_POST[ 'gburl' ] . "',
                                    '" . $_POST[ 'icq' ] . "',
                                    '" . $ip . "',
                                    '" . $_POST[ 'message' ] . "'
                                )"
                        );

                        if ($id != $userID) {
                            sendmessage(
                                $id,
                                $_language->module[ 'new_guestbook_entry' ],
                                str_replace('%guestbook_id%', $id, $_language->module[ 'new_guestbook_entry_msg' ])
                            );
                        }
                    }
                    redirect('index.php?site=profile&amp;id=' . $id . '&amp;action=guestbook', '', 0);
                } else if (isset($_GET[ 'delete' ])) {
                    if (!isanyadmin($userID) && $id != $userID) {
                        die($_language->module[ 'no_access' ]);
                    }

                    foreach ($_POST[ 'gbID' ] as $gbook_id) {
                        safe_query("DELETE FROM " . PREFIX . "user_gbook WHERE gbID='" . (int)$gbook_id."'");
                    }
                    redirect('index.php?site=profile&amp;id=' . $id . '&amp;action=guestbook', '', 0);
                } else {
                    $data_array = array();
                    $data_array['$id'] = $id;
                    $data_array['$profilelast'] = $profilelast;
                    $data_array['$banned'] = $banned;
                    $title_profile = $GLOBALS["_template"]->replaceTemplate("title_profile", $data_array);
                    echo $title_profile;

                    $gesamt =
                        mysqli_num_rows(
                            safe_query(
                                "SELECT
                                    `gbID`
                                FROM
                                    " . PREFIX . "user_gbook
                                WHERE
                                    `userID` = '" . (int)$id."'"
                            )
                        );

                    $page = getPage();
                    $type = getSortOrderType("DESC");

                    $pages = 1;

                    if (!isset($type)) {
                        $type = "DESC";
                    }

                    $max = $maxguestbook;
                    $pages = ceil($gesamt / $max);

                    if ($pages > 1) {
                        $page_link =
                            makepagelink(
                                "index.php?site=profile&amp;id=" . $id . "&amp;action=guestbook&amp;type=" . $type,
                                $page,
                                $pages
                            );
                    } else {
                        $page_link = '';
                    }

                    $start = getStartValue($page, $max);

                    $ergebnis = safe_query(
                        "SELECT
                            *
                        FROM
                            " . PREFIX . "user_gbook
                        WHERE
                            userID='" . $id . "'
                        ORDER BY
                            date " . $type . " LIMIT " . (int)$start . ", " . (int)$max
                    );

                    if ($type == "ASC") {
                        $sorter = '<a href="index.php?site=profile&amp;id=' . $id . '&amp;action=guestbook&amp;page=' .
                            $page . '&amp;type=DESC">' . $_language->module[ 'sort' ] .
                            ' <span class="bi bi-arrow-down-circle"></span></a>';
                    } else {
                        $sorter = '<a href="index.php?site=profile&amp;id=' . $id . '&amp;action=guestbook&amp;page=' .
                            $page . '&amp;type=ASC">' . $_language->module[ 'sort' ] .
                            ' <span class="bi bi-arrow-up-circle"></span></a>';
                    }

                    echo '<div class="row form-group"><div class="col-xs-6">' . $sorter . ' ' . $page_link . '</div>
                        <div class="col-xs-6 text-right">
                            <a href="index.php?site=profile&amp;id=' . $id . '&amp;action=guestbook#addcomment" class="btn btn-primary">' .
                                $_language->module[ 'new_entry' ] . '
                            </a>
                        </div>
                    </div>';

                    $data_array = array();
                    $data_array['$id'] = $id;
                    $data_array['$profilelast'] = $profilelast;
                    $quicklinks_profile = $GLOBALS["_template"]->replaceTemplate("profile_guestbook_quicklinks", $data_array);
                    echo $quicklinks_profile;

                    echo '<form method="post" name="form"
                        action="index.php?site=profile&amp;id=' . $id . '&amp;action=guestbook&amp;delete=true">';
                    while ($ds = mysqli_fetch_array($ergebnis)) {

                        $date = getformatdatetime($ds[ 'date' ]);

                        if (validate_email($ds[ 'email' ])) {
                            $email = '<a href="mailto:' . mail_protect($ds[ 'email' ]) . '">
                                <span class="bi bi-envelope-fill" title="' . $_language->module[ 'email' ] .
                                '"></span></a>';
                        } else {
                            $email = '';
                        }

                        if (validate_url($ds[ 'hp' ])) {
                            $hp = '<a href="' . $ds[ 'hp' ] . '" target="_blank"><span class="bi bi-house-fill"></span></a>';
                        } else {
                            $hp = '';
                        }

                        $sem = '/[0-9]{6,11}/si';
                        $icq_number = str_replace('-', '', $ds[ 'icq' ]);
                        if (preg_match($sem, $icq_number)) {
                            $icq = '<a href="http://www.icq.com/people/about_me.php?uin=' . $icq_number . '"
                                target="_blank">
                                <img src="http://online.mirabilis.com/scripts/online.dll?icq=' .
                                    $icq_number . '&amp;img=5" alt="icq">
                            </a>';
                        } else {
                            $icq = "";
                        }

                        $name = strip_tags($ds[ 'name' ]);
                        $message = cleartext($ds[ 'comment' ]);
                        $messagev = htmloutput($ds[ 'comment' ]);
                        $quotemessage = strip_tags($ds[ 'comment' ]);
                        $quotemessage = str_replace("'", "`", $quotemessage);

                        $actions = '';
                        $ip = $_language->module[ 'logged' ];
                        $quote = '<a href="javascript:AddCode(\'[quote=' . $name . ']' . $quotemessage .
                            '[/quote]\')"> <span class="bi bi-chat-left-quote-fill"></span></a>';
                        if (isfeedbackadmin($userID) || $id == $userID) {
                            $actions =
                                '<input class="input" type="checkbox" name="gbID[]" value="' . $ds[ 'gbID' ] . '">';
                            if (isfeedbackadmin($userID)) {
                                $ip = $ds[ 'ip' ];
                            }
                        }

                        $data_array = array();
                        $data_array['$actions'] = $actions;
                        $data_array['$name'] = $name;
                        $data_array['$date'] = $date;
                        $data_array['$email'] = $email;
                        $data_array['$hp'] = $hp;
                        $data_array['$icq'] = $icq;
                        $data_array['$ip'] = $ip;
                        $data_array['$quote'] = $quote;
                        $data_array['$message'] = $message;
                        $profile_guestbook = $GLOBALS["_template"]->replaceTemplate("profile_guestbook", $data_array);
                        echo $profile_guestbook;

                        if ($type == "DESC") {
                            $n--;
                        } else {
                            $n++;
                        }
                    }

                    if (isfeedbackadmin($userID) || $userID == $id) {
                        $submit =
                            '<input class="input" type="checkbox" name="ALL" value="ALL"
                                onclick="SelectAll(this.form);"> ' .
                            $_language->module[ 'select_all' ] . '
                    <input type="submit" value="' .
                        $_language->module[ 'delete_selected' ] . '" class="btn btn-danger">';
                    } else {
                        $submit = '';
                    }

                    echo '<table class="table"><tr>
                        <td>' . $page_link . '</td>
                        <td class="text-right">' . $submit . '</td>
                        </tr></table></form>';

                    echo '<a id="addcomment"></a>';
                    if ($loggedin) {
                        $name = getnickname($userID);
                        if (getemailhide($userID)) {
                            $email = '';
                        } else {
                            $email = getemail($userID);
                        }
                        $url = gethomepage($userID);
                        $icq = geticq($userID);
                        $_language->readModule('bbcode', true);

                        $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
                        $data_array = array();
                        $data_array['$id'] = $id;
                        $data_array['$addbbcode'] = $addbbcode;
                        $data_array['$name'] = $name;
                        $data_array['$email'] = $email;
                        $data_array['$url'] = $url;
                        $data_array['$icq'] = $icq;
                        $profile_guestbook_loggedin = $GLOBALS["_template"]->replaceTemplate(
                            "profile_guestbook_loggedin",
                            $data_array
                        );
                        echo $profile_guestbook_loggedin;
                    } else {
                        $CAPCLASS = new \webspell\Captcha;
                        $captcha = $CAPCLASS->createCaptcha();
                        $hash = $CAPCLASS->getHash();
                        $CAPCLASS->clearOldCaptcha();
                        $_language->readModule('bbcode', true);

                        $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
                        $data_array = array();
                        $data_array['$id'] = $id;
                        $data_array['$addbbcode'] = $addbbcode;
                        $data_array['$captcha'] = $captcha;
                        $data_array['$hash'] = $hash;
                        $profile_guestbook_notloggedin = $GLOBALS["_template"]->replaceTemplate(
                            "profile_guestbook_notloggedin",
                            $data_array
                        );
                        echo $profile_guestbook_notloggedin;
                    }
                }
            } else {
                redirect('index.php?site=404', '', 0);
            }
        } else {
            redirect('index.php?site=404', '', 0);
        }
    } else {
        //profil: home

        $data_array = array();
        $data_array['$id'] = $id;
        $data_array['$profilelast'] = $profilelast;
        $data_array['$banned'] = $banned;
        $title_profile = $GLOBALS["_template"]->replaceTemplate("title_profile", $data_array);
        echo $title_profile;

        $date = time();
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "user WHERE userID='" . $id . "'");
        $anz = mysqli_num_rows($ergebnis);
        $ds = mysqli_fetch_array($ergebnis);

        if ($userID != $id && $userID != 0) {
            safe_query("UPDATE " . PREFIX . "user SET visits=visits+1 WHERE userID='" . $id . "'");
            if (mysqli_num_rows(
                safe_query(
                    "SELECT
                            visitID
                        FROM
                            " . PREFIX . "user_visitors
                        WHERE
                            userID='" . $id . "' AND
                            visitor='" . (int)$userID."'"
                )
            )
            ) {
                safe_query(
                    "UPDATE
                        " . PREFIX . "user_visitors
                        SET
                            date='" . $date . "'
                        WHERE
                            userID='" . $id . "'AND
                            visitor='" . (int)$userID."'"
                );
            } else {
                safe_query(
                    "INSERT INTO
                        " . PREFIX . "user_visitors (
                            userID,
                            visitor,
                            date
                        )
                        values (
                            '" . $id . "',
                            '" . $userID . "',
                            '" . $date . "'
                        )"
                );
            }
        }
        $anzvisits = $ds[ 'visits' ];

        $userpic_img = getuserpic($ds[ 'userID' ]);
        $userpic = '<img class="image-responsive img-circle userpic-wh" src="images/userpics/' . $userpic_img . '" alt="">';
        $profile_bg = '<img class="card-bkimg" src="images/userpics/' . $userpic_img . '" alt="">';

        $nickname = $ds[ 'nickname' ];
        if (isclanmember($id)) {
            $member = '<span class="bi bi-person-fill" aria-hidden="true" title="Clanmember"></span>';
        } else {
            $member = '';
        }
        $registered = getformatdatetime($ds[ 'registerdate' ]);
        $lastlogin = getformatdatetime($ds[ 'lastlogin' ]);
        if ($ds[ 'avatar' ]) {
            $avatar = '<img src="images/avatars/' . $ds[ 'avatar' ] . '" alt="">';
        } else {
            $avatar = '<img src="images/avatars/noavatar.gif" alt="">';
        }
        if (isonline($ds[ 'userID' ])=="offline") {
		  $status = '<span class="label label-danger">offline</span>';
		} else {
		  $status = '<span class="label label-success">online</span>';
		}

        if ($ds[ 'email_hide' ]) {
            $email = $_language->module[ 'n_a' ];
        } else {
            $email = '<a href="mailto:' . mail_protect(cleartext($ds[ 'email' ])) .
                '"><span class="bi bi-envelope-fill" title="' . $_language->module[ 'email' ] . '">
                </span></a>';
        }
        $sem = '/[0-9]{4,11}/si';
        if (preg_match($sem, $ds[ 'icq' ])) {
            $icq = '<a href="http://www.icq.com/people/about_me.php?uin=' . sprintf('%d', $ds[ 'icq' ]) .
                '" target="_blank"><img src="http://online.mirabilis.com/scripts/online.dll?icq=' .
                sprintf('%d', $ds[ 'icq' ]) . '&amp;img=5" alt="icq"></a>';
        } else {
            $icq = '';
        }
        if ($loggedin && $ds[ 'userID' ] != $userID) {
            $pm = '<a class="btn btn-success" href="index.php?site=messenger&amp;action=touser&amp;touser=' . $ds[ 'userID' ] . '" title="' . $_language->module[ 'email' ] . '">
                <span class="bi bi-envelope-fill"></span></a>';
            if (isignored($userID, $ds[ 'userID' ])) {
                $buddy = '<a class="btn btn-warning" href="buddies.php?action=readd&amp;id=' . $ds[ 'userID' ] . '&amp;userID=' . $userID . '" title="' . $_language->module[ 'back_buddylist' ] . '"><span class="bi bi-person-plus-fill"></span></a>';
            } else if (isbuddy($userID, $ds[ 'userID' ])) {
                $buddy = '<a class="btn btn-danger" href="buddies.php?action=ignore&amp;id=' . $ds[ 'userID' ] . '&amp;userID=' . $userID . '" title="' . $_language->module[ 'ignore_user' ] . '"><span class="bi bi-person-dash-fill"></span>
                </a>';
            } else if ($userID == $ds[ 'userID' ]) {
                $buddy = '';
            } else {
                $buddy = '<a class="btn btn-primary" href="buddies.php?action=add&amp;id=' . $ds[ 'userID' ] . '&amp;userID=' . $userID . '" title="' . $_language->module[ 'add_buddylist' ] . '"><span class="bi bi-person-plus-fill"></span></a>';
            }
        } else {
            $pm = '' & $buddy = '';
        }

        if ($ds[ 'homepage' ] != '') {
			if (preg_match('%^https?://[^\s]+$%', $ds[ 'homepage' ])) {
				$homepage = '<a href="' . htmlspecialchars($ds[ 'homepage' ]) . '" target="_blank" rel="nofollow">' .
						htmlspecialchars($ds[ 'homepage' ]) . '</a>';
			} else {
				$homepage = '<a href="http://' . htmlspecialchars($ds[ 'homepage' ]) . '" target="_blank"
						rel="nofollow">
						http://' . htmlspecialchars($ds[ 'homepage' ]) . '
					</a>';
			}
        } else {
            $homepage = $_language->module[ 'n_a' ];
        }

        $clanhistory = clearfromtags($ds[ 'clanhistory' ]);
        if ($clanhistory == '') {
            $clanhistory = $_language->module[ 'n_a' ];
        }
        $clanname = clearfromtags($ds[ 'clanname' ]);
        if ($clanname == '') {
            $clanname = $_language->module[ 'n_a' ];
        }
        $clanirc = clearfromtags($ds[ 'clanirc' ]);
        if ($clanirc == '') {
            $clanirc = $_language->module[ 'n_a' ];
        }
        if ($ds[ 'clanhp' ] == '') {
            $clanhp = $_language->module[ 'n_a' ];
        } else {
			if (preg_match('%^https?://[^\s]+$%', $ds[ 'clanhp' ])) {
				$clanhp = '<a href="' . htmlspecialchars($ds[ 'clanhp' ]) . '" target="_blank" rel="nofollow">' .
                    htmlspecialchars($ds[ 'clanhp' ]) . '</a>';
			} else {
				$clanhp = '<a href="http://' . htmlspecialchars($ds[ 'clanhp' ]) . '" target="_blank" rel="nofollow">' .
                    htmlspecialchars($ds[ 'clanhp' ]) . '</a>';
			}
        }
        $clantag = clearfromtags($ds[ 'clantag' ]);
        if ($clantag == '') {
            $clantag = '';
        } else {
            $clantag = '(' . $clantag . ') ';
        }
        $firstname = clearfromtags($ds[ 'firstname' ]);
        $lastname = clearfromtags($ds[ 'lastname' ]);

        $birthday = getformatdate(strtotime($ds['birthday']));

        $res =
            safe_query(
                "SELECT
                    birthday,
                    DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(birthday)), '%Y') 'age'
                FROM
                    " . PREFIX . "user
                WHERE
                    userID = '" . (int)$id."'"
            );
        $cur = mysqli_fetch_array($res);
        $birthday = $birthday . " (" . (int)$cur[ 'age' ] . " " . $_language->module[ 'years' ] . ")";

        if ($ds[ 'sex' ] == "f") {
            $sex = $_language->module[ 'female' ];
        } else if ($ds[ 'sex' ] == "m") {
            $sex = $_language->module[ 'male' ];
        } else {
            $sex = $_language->module[ 'unknown' ];
        }
        $flag = '[flag]' . $ds[ 'country' ] . '[/flag]';
        $profilecountry = flags($flag);
        $town = clearfromtags($ds[ 'town' ]);
        if ($town == '') {
            $town = $_language->module[ 'n_a' ];
        }
        $cpu = clearfromtags($ds[ 'cpu' ]);
        if ($cpu == '') {
            $cpu = $_language->module[ 'n_a' ];
        }
        $mainboard = clearfromtags($ds[ 'mainboard' ]);
        if ($mainboard == '') {
            $mainboard = $_language->module[ 'n_a' ];
        }
        $ram = clearfromtags($ds[ 'ram' ]);
        if ($ram == '') {
            $ram = $_language->module[ 'n_a' ];
        }
        $monitor = clearfromtags($ds[ 'monitor' ]);
        if ($monitor == '') {
            $monitor = $_language->module[ 'n_a' ];
        }
        $graphiccard = clearfromtags($ds[ 'graphiccard' ]);
        if ($graphiccard == '') {
            $graphiccard = $_language->module[ 'n_a' ];
        }
        $soundcard = clearfromtags($ds[ 'soundcard' ]);
        if ($soundcard == '') {
            $soundcard = $_language->module[ 'n_a' ];
        }
        $connection = clearfromtags($ds[ 'verbindung' ]);
        if ($connection == '') {
            $connection = $_language->module[ 'n_a' ];
        }
        $keyboard = clearfromtags($ds[ 'keyboard' ]);
        if ($keyboard == '') {
            $keyboard = $_language->module[ 'n_a' ];
        }
        $mouse = clearfromtags($ds[ 'mouse' ]);
        if ($mouse == '') {
            $mouse = $_language->module[ 'n_a' ];
        }
        $mousepad = clearfromtags($ds[ 'mousepad' ]);
        if ($mousepad == '') {
            $mousepad = $_language->module[ 'n_a' ];
        }
        $hdd = clearfromtags($ds[ 'hdd' ]);
        if ($hdd == '') {
            $hdd = $_language->module[ 'n_a' ];
        }
        $headset = clearfromtags($ds[ 'headset' ]);
        if ($headset == '') {
            $headset = $_language->module[ 'n_a' ];
        }

        $anznewsposts = getusernewsposts($ds[ 'userID' ]);
        $anzforumtopics = getuserforumtopics($ds[ 'userID' ]);
        $anzforumposts = getuserforumposts($ds[ 'userID' ]);

        $pmgot = 0;
        $pmgot = $ds[ 'pmgot' ];

        $pmsent = 0;
        $pmsent = $ds[ 'pmsent' ];

        if ($ds[ 'about' ]) {
            $about = cleartext($ds[ 'about' ]);
        } else {
            $about = $_language->module[ 'n_a' ];
        }

        if (isforumadmin($ds[ 'userID' ])) {
            $usertype = $_language->module[ 'administrator' ];
            $rang = '<img src="images/icons/ranks/admin.gif" alt="">';
        } else if (isanymoderator($ds[ 'userID' ])) {
            $usertype = $_language->module[ 'moderator' ];
            $rang = '<img src="images/icons/ranks/moderator.gif" alt="">';
        } else {
            $posts = getuserforumposts($ds[ 'userID' ]);
            $ergebnis =
                safe_query(
                    "SELECT
                        *
                    FROM
                        " . PREFIX . "forum_ranks
                    WHERE
                        " . $posts . " >= postmin AND
                        " . $posts . " <= postmax AND
                        postmax > 0 AND
                        special='0'"
                );
            $dt = mysqli_fetch_array($ergebnis);
            $usertype = $dt[ 'rank' ];
            $rang = '<img src="images/icons/ranks/' . $dt[ 'pic' ] . '" alt="">';
        }

        $specialrank = '';
        $getrank = safe_query(
            "SELECT IF
                (u.special_rank = 0, 0, CONCAT_WS('__', r.rank, r.pic)) as RANK
            FROM
                " . PREFIX . "user u LEFT JOIN " . PREFIX . "forum_ranks r ON u.special_rank = r.rankID
            WHERE
                userID='" . $ds[ 'userID' ] . "'"
        );
        $rank_data = mysqli_fetch_assoc($getrank);

        if ($rank_data[ 'RANK' ] != '0') {
            $specialrank  = '<br/>';
            $tmp_rank = explode("__", $rank_data[ 'RANK' ], 2);
            $specialrank .= $tmp_rank[0];
            if (!empty($tmp_rank[1]) && file_exists("images/icons/ranks/" . $tmp_rank[1])) {
                $specialrank .= '<br/>';
                $specialrank .= "<img src='images/icons/ranks/" . $tmp_rank[1] . "' alt = '' />";
            }
        }

        $lastvisits = "";
        $visitors = safe_query(
            "SELECT
                v.*,
                u.nickname,
                u.country
            FROM
                " . PREFIX . "user_visitors v
            JOIN " . PREFIX . "user u ON
                u.userID = v.visitor
            WHERE
                v.userID='" . $id . "'
            ORDER BY
                v.date DESC
                LIMIT 0,10"
        );
        if (mysqli_num_rows($visitors)) {

            while ($dv = mysqli_fetch_array($visitors)) {

                $flag = '[flag]' . $dv[ 'country' ] . '[/flag]';
                $country = flags($flag);
                $nicknamevisitor = $dv[ 'nickname' ];
                if (isonline($dv[ 'visitor' ]) == "offline") {
                    $statuspic = '' . $_language->module[ 'offline' ] . '';
                } else {
                    $statuspic = '' . $_language->module[ 'online' ] . '';
                }
                $time = time();
                $visittime = $dv[ 'date' ];

                $sec = $time - $visittime;
                $days = $sec / 86400;                                // sekunden / (60*60*24)
                $days = mb_substr($days, 0, mb_strpos($days, "."));        // kommastelle

                $sec = $sec - $days * 86400;
                $hours = $sec / 3600;
                $hours = mb_substr($hours, 0, mb_strpos($hours, "."));

                $sec = $sec - $hours * 3600;
                $minutes = $sec / 60;
                $minutes = mb_substr($minutes, 0, mb_strpos($minutes, "."));

                if ($time - $visittime < 60) {
                    $now = $_language->module[ 'now' ];
                    $days = "";
                    $hours = "";
                    $minutes = "";
                } else {
                    $now = '';
                    $days == 0 ? $days = "" : $days = $days . 'd';
                    $hours == 0 ? $hours = "" : $hours = $hours . 'h';
                    $minutes == 0 ? $minutes = "" : $minutes = $minutes . 'm';
                }

                $lastvisits .= '<tr>
                <td>' . $country . ' <a href="index.php?site=profile&amp;id=' . $dv[ 'visitor' ] . '"><strong>' .
                    $nicknamevisitor . '</strong></a></td>
                <td class="text-right"><small>' . $now . $days . $hours . $minutes . '</small> ' . $statuspic . '</td>
            </tr>';

            }
        } else {
            $lastvisits = '<tr><td colspan="2">' . $_language->module[ 'no_visits' ] . '</td>
    </tr>';
        }

        $data_array = array();
		$data_array['$id'] = $id;
        $data_array['$userpic'] = $userpic;
		$data_array['$profile_bg'] = $profile_bg;
        $data_array['$nickname'] = $nickname;
        $data_array['$member'] = $member;
        $data_array['$firstname'] = $firstname;
        $data_array['$lastname'] = $lastname;
        $data_array['$sex'] = $sex;
        $data_array['$birthday'] = $birthday;
        $data_array['$profilecountry'] = $profilecountry;
        $data_array['$town'] = $town;
        $data_array['$status'] = $status;
        $data_array['$usertype'] = $usertype;
        $data_array['$rang'] = $rang;
        $data_array['$registered'] = $registered;
        $data_array['$lastlogin'] = $lastlogin;
        $data_array['$email'] = $email;
        $data_array['$pm'] = $pm;
        $data_array['$buddy'] = $buddy;
        $data_array['$icq'] = $icq;
        $data_array['$homepage'] = $homepage;
        $data_array['$about'] = $about;
        $data_array['$clanname'] = $clanname;
        $data_array['$clantag'] = $clantag;
        $data_array['$clanhp'] = $clanhp;
        $data_array['$clanirc'] = $clanirc;
        $data_array['$clanhistory'] = $clanhistory;
        $data_array['$cpu'] = $cpu;
        $data_array['$mainboard'] = $mainboard;
        $data_array['$ram'] = $ram;
        $data_array['$hdd'] = $hdd;
        $data_array['$monitor'] = $monitor;
        $data_array['$headset'] = $headset;
        $data_array['$graphiccard'] = $graphiccard;
        $data_array['$soundcard'] = $soundcard;
        $data_array['$connection'] = $connection;
        $data_array['$keyboard'] = $keyboard;
        $data_array['$mouse'] = $mouse;
        $data_array['$mousepad'] = $mousepad;
        $data_array['$anzvisits'] = $anzvisits;
        $data_array['$lastvisits'] = $lastvisits;
        $data_array['$anzforumtopics'] = $anzforumtopics;
        $data_array['$anznewsposts'] = $anznewsposts;
        $data_array['$anzforumposts'] = $anzforumposts;
        $data_array['$pmgot'] = $pmgot;
        $data_array['$pmsent'] = $pmsent;
		$data_array['$profilelast'] = $profilelast;
        $data_array['$news_comments'] = getusercomments($ds[ 'userID' ], 'ne');
        $data_array['$clanwar_comments'] = getusercomments($ds[ 'userID' ], 'cw');
        $data_array['$articles_comments'] = getusercomments($ds[ 'userID' ], 'ar');
        $data_array['$demo_comments'] = getusercomments($ds[ 'userID' ], 'de');
        $data_array['$specialrank'] = $specialrank;
        $profile = $GLOBALS["_template"]->replaceTemplate("profile", $data_array);
        echo $profile;
    }
} else {
    redirect('index.php', $_language->module[ 'user_doesnt_exist' ], 3);
}