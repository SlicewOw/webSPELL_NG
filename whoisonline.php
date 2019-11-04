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

// WHO IS ONLINE

$_language->readModule('whoisonline');

$title_whoisonline = $GLOBALS["_template"]->replaceTemplate("title_whoisonline", array());
echo $title_whoisonline;

$result_guests = safe_query("SELECT * FROM " . PREFIX . "whoisonline WHERE userID=''");
$guests = mysqli_num_rows($result_guests);
$result_user = safe_query("SELECT * FROM " . PREFIX . "whoisonline WHERE ip=''");
$user = mysqli_num_rows($result_user);
$useronline = $guests + $user;
if ($user == 1) {
    $user_on = '<strong>1</strong> ' . $_language->module[ 'registered_user' ];
} else {
    $user_on = '<strong>' . $user . '</strong> ' . $_language->module[ 'registered_users' ];
}

if ($guests == 1) {
    $guests_on = '<strong>1</strong> ' . $_language->module[ 'guest' ];
} else {
    $guests_on = '<strong>' . $guests . '</strong> ' . $_language->module[ 'guests' ];
}

$online = $_language->module[ 'now_online' ] . ' ' . $user_on . ' ' . $_language->module[ 'and' ] . ' ' . $guests_on;
$sort = getSortOrderValue('time', array('time', 'nickname'));
$type = getSortOrderType("DESC");

if ($type == "ASC") {
    $sorter =
        '<a href="index.php?site=whoisonline&amp;sort=' . $sort . '&amp;type=DESC">' . $_language->module[ 'sort' ] . '
        </a> <span class="fa fa-chevron-down"></span>';
} else {
    $sorter =
        '<a href="index.php?site=whoisonline&amp;sort=' . $sort . '&amp;type=ASC">' . $_language->module[ 'sort' ] . '
        </a> <span class="fa fa-chevron-up"></span>';
}

$ergebnis = safe_query(
    "SELECT
        w.*,
        u.nickname
    FROM
        " . PREFIX . "whoisonline w
    LEFT JOIN
        " . PREFIX . "user u
    ON
        u.userID = w.userID
    ORDER BY
        $sort $type"
);

$data_array = array();
$data_array['$sorter'] = $sorter;
$data_array['$online'] = $online;
$data_array['$type'] = $type;
$whoisonline_head = $GLOBALS["_template"]->replaceTemplate("whoisonline_head", $data_array);
echo $whoisonline_head;

while ($ds = mysqli_fetch_array($ergebnis)) {

    if ($ds[ 'ip' ] == '') {
        $nickname =
            '<a href="index.php?site=profile&amp;id=' . $ds[ 'userID' ] . '"><strong>' . $ds[ 'nickname' ] .
            '</strong></a>';
        if (isclanmember($ds[ 'userID' ])) {
            $member = ' <span class="fa fa-user" aria-hidden="true" title="Clanmember"></span>';
        } else {
            $member = '';
        }
        if (getemailhide($ds[ 'userID' ])) {
            $email = '';
        } else {
            $email = '<a href="mailto:' . mail_protect(getemail($ds[ 'userID' ])) . '">
            <span class="fa fa-envelope" title="email"></span>
        </a>';
        }

        $country = '[flag]' . getcountry($ds[ 'userID' ]) . '[/flag]';
        $country = flags($country);

        if (!validate_url(gethomepage($ds[ 'userID' ]))) {
            $homepage = '';
        } else {
            $homepage = '<a href="' . gethomepage($ds[ 'userID' ]) . '" target="_blank"><span class="fa fa-home" aria-hidden="true" title="Homepage"></span></a>';
        }

        $pm = '';
        $buddy = '';
        if ($loggedin && $ds[ 'userID' ] != $userID) {
            $pm = '<a href="index.php?site=messenger&amp;action=touser&amp;touser=' . $ds[ 'userID' ] . '">
                <span class="fa fa-envelope" title="Messenger"></span>
            </a>';
            if (isignored($userID, $ds[ 'userID' ])) {
                $buddy = '<a href="buddies.php?action=readd&amp;id=' . $ds[ 'userID' ] . '&amp;userID=' . $userID . '">
                    <span class="fa fa-user-plus"></span>
                </a>';
            } else if (isbuddy($userID, $ds[ 'userID' ])) {
                $buddy = '<a href="buddies.php?action=ignore&amp;id=' . $ds[ 'userID' ] . '&amp;userID=' . $userID . '">
                    <span class="fa fa-user-times"></span></a>
                </a>';
            } else if ($userID == $ds[ 'userID' ]) {
                $buddy = '';
            } else {
                $buddy = '<a href="buddies.php?action=add&amp;id=' . $ds[ 'userID' ] . '&amp;userID=' . $userID . '">
                    <span class="fa fa-user-plus"></span>
                </a>';
            }
        }
    } else {
        $nickname = $_language->module[ 'guest' ];
        $member = "";
        $email = "";
        $country = "";
        $homepage = "";
        $pm = "";
        $buddy = "";
    }

    $array_watching = array(
        'about',
        'awards',
        'calendar',
        'clanwars',
        'counter_stats',
        'demos',
        'files',
        'forum',
        'gallery',
        'links',
        'linkus',
        'loginoverview',
        'members',
        'polls',
        'registered_users',
        'server',
        'sponsors',
        'squads',
        'whoisonline',
        'newsletter'
    );
    $array_reading = array('articles', 'contact', 'faq', 'guestbook', 'history', 'imprint');

    if (in_array($ds[ 'site' ], $array_watching)) {
        $status = $_language->module[ 'is_watching_the' ] . ' <a href="index.php?site=' . $ds[ 'site' ] . '">' .
            $_language->module[ $ds[ 'site' ] ] . '
        </a>';
    } else if (in_array($ds[ 'site' ], $array_reading)) {
        $status = $_language->module[ 'is_reading_the' ] . ' <a href="index.php?site=' . $ds[ 'site' ] . '">' .
            $_language->module[ $ds[ 'site' ] ] . '
        </a>';
    } else if ($ds[ 'site' ] == "buddies") {
        $status = $_language->module[ 'is_watching_his' ] . ' <a href="index.php?site=buddies">' .
            $_language->module[ 'buddys' ] . '</a>';
    } else if ($ds[ 'site' ] == "clanwars_details") {
        $status = $_language->module[ 'is_watching_details_clanwar' ];
    } else if ($ds[ 'site' ] == "forum_topic") {
        $status = $_language->module[ 'is_reading_forum' ];
    } else if ($ds[ 'site' ] == "messenger") {
        $status = $_language->module[ 'is_watching_his' ] . ' <a href="index.php?site=messenger">' .
            $_language->module[ 'messenger' ] . '</a>';
    } else if ($ds[ 'site' ] == "myprofile") {
        $status =
            $_language->module[ 'is_editing_his' ] . ' <a href="index.php?site=profile&amp;id=' . $ds[ 'userID' ] .
            '">' . $_language->module[ 'profile' ] . '</a>';
    } else if ($ds[ 'site' ] == "news_comments") {
        $status = $_language->module[ 'is_reading_newscomments' ];
    } else if ($ds[ 'site' ] == "profile") {
        $status = $_language->module[ 'is_watching_profile' ];
    } else {
        $status =
            $_language->module[ 'is_watching_the' ] . ' <a href="index.php?site=news">' . $_language->module[ 'news' ] .
            '</a>';
    }

    $data_array = array();
    $data_array['$country'] = $country;
    $data_array['$nickname'] = $nickname;
    $data_array['$member'] = $member;
    $data_array['$email'] = $email;
    $data_array['$pm'] = $pm;
    $data_array['$buddy'] = $buddy;
    $data_array['$status'] = $status;
    $whoisonline_content = $GLOBALS["_template"]->replaceTemplate("whoisonline_content", $data_array);
    echo $whoisonline_content;

}

$whoisonline_foot = $GLOBALS["_template"]->replaceTemplate("whoisonline_foot", array());
echo $whoisonline_foot;


// WHO WAS ONLINE

if ($type == "ASC") {
    $sorter =
        '<a href="index.php?site=whoisonline&amp;sort=' . $sort . '&amp;type=DESC">' . $_language->module[ 'sort' ] .
        '</a> <span class="fa fa-chevron-down"></span>';
} else {
    $sorter =
        '<a href="index.php?site=whoisonline&amp;sort=' . $sort . '&amp;type=ASC">' . $_language->module[ 'sort' ] .
        '</a> <span class="fa fa-chevron-up"></span>';
}

$ergebnis = safe_query(
    "SELECT
        w.*,
        u.nickname
    FROM
        " . PREFIX . "whowasonline w
    LEFT JOIN
        " . PREFIX . "user u
    ON
        u.userID = w.userID
    ORDER BY
        $sort $type"
);

$data_array = array();
$data_array['$sorter'] = $sorter;
$data_array['$type'] = $type;
$whowasonline_head = $GLOBALS["_template"]->replaceTemplate("whowasonline_head", $data_array);
echo $whowasonline_head;

while ($ds = mysqli_fetch_array($ergebnis)) {

    $date = getformatdatetime($ds[ 'time' ]);
    $nickname = '<a href="index.php?site=profile&amp;id=' . $ds[ 'userID' ] . '"><strong>' . $ds[ 'nickname' ] .
        '</strong></a>';
    if (isclanmember($ds[ 'userID' ])) {
        $member = ' <span class="fa fa-user" aria-hidden="true" title="Clanmember"></span>';
    } else {
        $member = '';
    }
    if (getemailhide($ds[ 'userID' ])) {
        $email = '';
    } else {
        $email = '<a href="mailto:' . mail_protect(getemail($ds[ 'userID' ])) . '">
            <span class="fa fa-envelope" title="email"></span>
        </a>';
    }

    $country = '[flag]' . getcountry($ds[ 'userID' ]) . '[/flag]';
    $country = flags($country);

    if (!validate_url($ds[ 'userID' ])) {
        $homepage = '';
    } else {
        $homepage = '<a href="' . gethomepage($ds[ 'userID' ]) . '" target="_blank">
            <span class="fa fa-home" aria-hidden="true" title="Homepage"></span>
        </a>';
    }

    $pm = '';
    $buddy = '';
    if ($loggedin && $ds[ 'userID' ] != $userID) {
        $pm = '<a href="index.php?site=messenger&amp;action=touser&amp;touser=' . $ds[ 'userID' ] . '">
            <span class="fa fa-envelope"></span>
        </a>';
        if (isignored($userID, $ds[ 'userID' ])) {
            $buddy = '<a href="buddies.php?action=readd&amp;id=' . $ds[ 'userID' ] . '&amp;userID=' . $userID . '">
                <span class="fa fa-user-plus"></span>
            </a>';
        } else if (isbuddy($userID, $ds[ 'userID' ])) {
            $buddy = '<a href="buddies.php?action=ignore&amp;id=' . $ds[ 'userID' ] . '&amp;userID=' . $userID . '">
                <span class="fa fa-user-times"></span></a>
            </a>';
        } else if ($userID == $ds[ 'userID' ]) {
            $buddy = '';
        } else {
            $buddy = '<a href="buddies.php?action=add&amp;id=' . $ds[ 'userID' ] . '&amp;userID=' . $userID . '">
                <span class="fa fa-user-plus"></span>
            </a>';
        }
    }

    $array_watching = array(
        'about',
        'awards',
        'calendar',
        'clanwars',
        'counter_stats',
        'demos',
        'files',
        'forum',
        'gallery',
        'links',
        'linkus',
        'loginoverview',
        'members',
        'polls',
        'registered_users',
        'server',
        'sponsors',
        'squads',
        'whoisonline',
        'newsletter'
    );
    $array_reading = array('articles', 'contact', 'faq', 'guestbook', 'history', 'imprint');

    if (in_array($ds[ 'site' ], $array_watching)) {
        $status = $_language->module[ 'was_watching_the' ] . ' <a href="index.php?site=' . $ds[ 'site' ] . '">' .
            $_language->module[ $ds[ 'site' ] ] . '</a>';
    } else if (in_array($ds[ 'site' ], $array_reading)) {
        $status = $_language->module[ 'was_reading_the' ] . ' <a href="index.php?site=' . $ds[ 'site' ] . '">' .
            $_language->module[ $ds[ 'site' ] ] . '</a>';
    } else if ($ds[ 'site' ] == "buddies") {
        $status = $_language->module[ 'was_watching_his' ] . ' <a href="index.php?site=buddies">' .
            $_language->module[ 'buddys' ] . '</a>';
    } else if ($ds[ 'site' ] == "clanwars_details") {
        $status = $_language->module[ 'was_watching_details_clanwar' ];
    } else if ($ds[ 'site' ] == "forum_topic") {
        $status = $_language->module[ 'was_reading_forum' ];
    } else if ($ds[ 'site' ] == "messenger") {
        $status = $_language->module[ 'was_watching_his' ] . ' <a href="index.php?site=messenger">' .
            $_language->module[ 'messenger' ] . '</a>';
    } else if ($ds[ 'site' ] == "myprofile") {
        $status =
            $_language->module[ 'was_editing_his' ] . ' <a href="index.php?site=profile&amp;id=' . $ds[ 'userID' ] .
            '">' . $_language->module[ 'profile' ] . '</a>';
    } else if ($ds[ 'site' ] == "news_comments") {
        $status = $_language->module[ 'was_reading_newscomments' ];
    } else if ($ds[ 'site' ] == "profile") {
        $status = $_language->module[ 'was_watching_profile' ];
    } else {
        $status = $_language->module[ 'was_watching_the' ] . ' <a href="index.php?site=news">' .
            $_language->module[ 'news' ] . '</a>';
    }

    $data_array = array();
    $data_array['$country'] = $country;
    $data_array['$nickname'] = $nickname;
    $data_array['$member'] = $member;
    $data_array['$email'] = $email;
    $data_array['$pm'] = $pm;
    $data_array['$buddy'] = $buddy;
    $data_array['$status'] = $status;
    $data_array['$date'] = $date;
    $whowasonline_content = $GLOBALS["_template"]->replaceTemplate("whowasonline_content", $data_array);
    echo $whowasonline_content;

}

$whowasonline_foot = $GLOBALS["_template"]->replaceTemplate("whowasonline_foot", array());
echo $whowasonline_foot;
