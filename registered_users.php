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

$_language->readModule('registered_users');

$title_registered_users = $GLOBALS["_template"]->replaceTemplate("title_registered_users", array());
echo $title_registered_users;

function clear($text)
{
    return str_replace(
        "javascript:",
        "",
        strip_tags($text)
    );
}

$alle = safe_query("SELECT userID FROM " . PREFIX . "user");
$gesamt = mysqli_num_rows($alle);
$pages = ceil($gesamt / $maxusers);

$page = getPage();
$sort = getSortOrderValue('nickname', array('nickname', 'country', 'lastlogin', 'registerdate'));
$type = getSortOrderType();

$page_link = makepagelink("index.php?site=registered_users&amp;sort=$sort&amp;type=$type", $page, $pages);

$start = getStartValue($page, $maxusers);

$ergebnis = safe_query(
    "SELECT
        *
    FROM
        " . PREFIX . "user
    ORDER BY
        " . $sort . " " . $type . "
    LIMIT " . $start . "," . (int)$maxusers
);

$anz = mysqli_num_rows($ergebnis);
if ($anz) {
    if ($type == "ASC") {
        $sorter =
            '<a href="index.php?site=registered_users&amp;page=' . $page . '&amp;sort=' . $sort . '&amp;type=DESC">' .
            $_language->module[ 'sort' ] . ' <span class="bi bi-arrow-down-circle"></span></a>';
    } else {
        $sorter =
            '<a href="index.php?site=registered_users&amp;page=' . $page . '&amp;sort=' . $sort . '&amp;type=ASC">' .
            $_language->module[ 'sort' ] . ' <span class="bi bi-arrow-up-circle"></span></a>';
    }
    $data_array = array();
    $data_array['$sorter'] = $sorter;
    $data_array['$page_link'] = $page_link;
    $data_array['$gesamt'] = $gesamt;
    $data_array['$page'] = $page;
    $registered_users_head = $GLOBALS["_template"]->replaceTemplate("registered_users_head", $data_array);
    echo $registered_users_head;

    while ($ds = mysqli_fetch_array($ergebnis)) {

        $id = $ds[ 'userID' ];
        $country = '[flag]' . htmlspecialchars($ds[ 'country' ]) . '[/flag]';
        $country = flags($country);
        $nickname =
            '<a href="index.php?site=profile&amp;id=' . $ds[ 'userID' ] . '"><strong>' . strip_tags($ds[ 'nickname' ]) .
            '</strong></a>';
        if (isclanmember($ds[ 'userID' ])) {
            $member = ' <span class="bi bi-person-fill" aria-hidden="true" title="Clanmember"></span>';
        } else {
            $member = '';
        }
        if ($ds[ 'email_hide' ]) {
            $email = '';
        } else {
            $email = '<a href="mailto:' . mail_protect($ds[ 'email' ]) .
                '"><span class="bi bi-envelope-fill" title="email"></span></a>';
        }

        if (!validate_url($ds[ 'homepage' ])) {
            $homepage = '';
        } else {
            $homepage = '<a href="' . $ds[ 'homepage' ] .
                '" target="_blank"><span class="bi bi-house-fill" aria-hidden="true" title="Homepage"></span></a>';
        }

        $pm = '';
        $buddy = '';
        if ($loggedin && $ds[ 'userID' ] != $userID) {
            $pm = '<a href="index.php?site=messenger&amp;action=touser&amp;touser=' . $ds[ 'userID' ] .
                '"><span class="bi bi-envelope-fill" title="Messenger"></span></a>';
            if (isignored($userID, $ds[ 'userID' ])) {
                $buddy = '<a href="buddies.php?action=readd&amp;id=' . $ds[ 'userID' ] . '&amp;userID=' . $userID .
                    '"><span class="bi bi-person-plus-fill" title="back to buddylist"></span></a>';
            } else if (isbuddy($userID, $ds[ 'userID' ])) {
                $buddy = '<a href="buddies.php?action=ignore&amp;id=' . $ds[ 'userID' ] . '&amp;userID=' . $userID .
                    '"><span class="bi bi-person-dash-fill" title="ignore user"></span></a></a>';
            } else if ($userID == $ds[ 'userID' ]) {
                $buddy = '';
            } else {
                $buddy = '<a href="buddies.php?action=add&amp;id=' . $ds[ 'userID' ] . '&amp;userID=' . $userID .
                    '"><span class="bi bi-person-plus-fill" title="add to buddylist"></span></a>';
            }
        }
        $lastlogindate = getformatdate($ds[ 'lastlogin' ]);
        $lastlogintime = getformattime($ds[ 'lastlogin' ]);
        $registereddate = getformatdate($ds[ 'registerdate' ]);
        $status = isonline($ds[ 'userID' ]);

        if ($status == "offline") {
            $login = $lastlogindate . ' - ' . $lastlogintime;
        } else {
            $login = '<span class="bi bi-circle-fill text-success" aria-hidden="true"></span>' .
                $_language->module[ 'now_on' ];
        }

        $data_array = array();
        $data_array['$country'] = $country;
        $data_array['$nickname'] = $nickname;
        $data_array['$member'] = $member;
        $data_array['$email'] = $email;
        $data_array['$pm'] = $pm;
        $data_array['$buddy'] = $buddy;
        $data_array['$homepage'] = $homepage;
        $data_array['$login'] = $login;
        $data_array['$registereddate'] = $registereddate;
        $registered_users_content = $GLOBALS["_template"]->replaceTemplate("registered_users_content", $data_array);
        echo $registered_users_content;

    }
    $registered_users_foot = $GLOBALS["_template"]->replaceTemplate("registered_users_foot", array());
    echo $registered_users_foot;
} else {
    echo $_language->module[ 'no_users' ];
}
