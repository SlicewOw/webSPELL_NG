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

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if ($action == "add") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('buddys');
    if (!$userID) {
        redirect('index.php?site=buddies', $_language->module[ 'not_logged' ], 3);
    } elseif (!isset($_GET[ 'id' ]) || !is_numeric($_GET[ 'id' ])) {
        redirect('index.php?site=buddies', $_language->module[ 'add_nouserid' ], 3);
    } else {
        if ($_GET[ 'id' ] == $userID) {
            redirect('index.php?site=buddies', $_language->module[ 'add_yourself' ], 3);
            die();
        }
        if (mysqli_num_rows(
            safe_query(
                "SELECT
                    userID
                FROM
                    " . PREFIX . "user
                WHERE
                    userID='" . (int)$_GET[ 'id' ] . "'"
            )
        )
        ) {
            safe_query(
                "INSERT INTO
                    " . PREFIX . "buddys (userID, buddy, banned)
                values
                    ('$userID', '" . intval($_GET[ 'id' ]) . "', '0') "
            );
            header("Location: index.php?site=buddies");
        } else {
            redirect('index.php?site=buddies', $_language->module[ 'add_notexists' ], 3);
        }
    }
} elseif ($action == "ignore") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('buddys');
    if (!$userID) {
        redirect('index.php?site=buddies', $_language->module[ 'not_logged' ], 3);
    } elseif (!isset($_GET[ 'id' ]) || !is_numeric($_GET[ 'id' ])) {
        redirect('index.php?site=buddies', $_language->module[ 'add_nouserid' ], 3);
    } else {
        if ($_GET[ 'id' ] == $userID) {
            redirect('index.php?site=buddies', $_language->module[ 'add_yourself' ], 3);
            die();
        }
        if (mysqli_num_rows(
            safe_query(
                "SELECT
                    userID
                FROM
                    " . PREFIX . "user
                WHERE userID='" . (int)$_GET[ 'id' ] . "'"
            )
        )
        ) {
            safe_query(
                "UPDATE
                    " . PREFIX . "buddys
                SET
                    banned='1'
                WHERE
                    userID='$userID'
                AND
                    buddy='" . (int)$_GET[ 'id' ] . "'"
            );
            header("Location: index.php?site=buddies");
        } else {
            redirect('index.php?site=buddies', $_language->module[ 'add_notexists' ], 3);
        }
    }
} elseif ($action == "readd") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('buddys');
    if (!$userID) {
        redirect('index.php?site=buddies', $_language->module[ 'not_logged' ], 3);
    } elseif (!isset($_GET[ 'id' ]) || !is_numeric($_GET[ 'id' ])) {
        redirect('index.php?site=buddies', $_language->module[ 'add_nouserid' ], 3);
    } else {
        safe_query(
            "UPDATE " . PREFIX . "buddys SET banned='0' WHERE userID='$userID' AND buddy='" . (int)$_GET[ 'id' ] . "'"
        );
        header("Location: index.php?site=buddies");
    }
} elseif ($action == "delete") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('buddys');
    if (!$userID) {
        redirect('index.php?site=buddies', $_language->module[ 'not_logged' ], 3);
    } elseif (!isset($_GET[ 'id' ]) || !is_numeric($_GET[ 'id' ])) {
        redirect('index.php?site=buddies', $_language->module[ 'add_nouserid' ], 3);
    } else {
        safe_query(
            "DELETE FROM " . PREFIX . "buddys WHERE userID='$userID' AND buddy='" . (int)$_GET[ 'id' ] . "'"
        );
        header("Location: index.php?site=buddies");
    }
} elseif ($userID) {
    $_language->readModule('buddys');

    $title_buddys = $GLOBALS["_template"]->replaceTemplate("title_buddys", array());
    echo $title_buddys;

    $buddys_head = $GLOBALS["_template"]->replaceTemplate("buddys_head", array());
    echo $buddys_head;
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "buddys WHERE userID='$userID' AND banned='0'");
    $anz = mysqli_num_rows($ergebnis);
    if ($anz) {
        $n = 1;
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $flag = '[flag]' . getcountry($ds[ 'buddy' ]) . '[/flag]';
            $country = flags($flag);
            $nickname = getnickname($ds[ 'buddy' ]);
            if (isclanmember($ds[ 'buddy' ])) {
                $member = '<i class="fa fa-user" aria-hidden="true" title="Clanmember"></i>';
            } else {
                $member = '';
            }
            if (isonline($ds[ 'buddy' ]) == "offline") {
                $statuspic = '<span class="label label-danger">' . $_language->module[ 'offline' ] . '</span>';
            } else {
                $statuspic = '<span class="label label-success">' . $_language->module[ 'online' ] . '</span>';
            }

            $data_array = array();
            $data_array['$country'] = $country;
            $data_array['$buddyID'] = $ds['buddy'];
            $data_array['$nickname'] = $nickname;
            $data_array['$member'] = $member;
            $data_array['$userID'] = $userID;
            $buddys_content = $GLOBALS["_template"]->replaceTemplate("buddys_content", $data_array);
            echo $buddys_content;
            $n++;
        }
    } else {
        echo '<tr><td colspan="4">' . $_language->module[ 'buddy_nousers' ] . '</td></tr>';
    }

    $buddys_foot = $GLOBALS["_template"]->replaceTemplate("buddys_foot", array());
    echo $buddys_foot;

    $ignore_head = $GLOBALS["_template"]->replaceTemplate("ignore_head", array());
    echo $ignore_head;
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "buddys WHERE userID='$userID' AND banned='1'");
    $anz = mysqli_num_rows($ergebnis);
    if ($anz) {
        $n = 1;
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $flag = '[flag]' . getcountry($ds[ 'buddy' ]) . '[/flag]';
            $country = flags($flag);
            $nickname = getnickname($ds[ 'buddy' ]);
            if (isclanmember($ds[ 'buddy' ])) {
                $member = ' <i class="fa fa-user" aria-hidden="true" title="Clanmember"></i>';
            } else {
                $member = '';
            }
            if (isonline($ds[ 'buddy' ]) == "offline") {
                $statuspic = '<span class="label label-danger">' . $_language->module[ 'offline' ] . '</span>';
            } else {
                $statuspic = '<span class="label label-success">' . $_language->module[ 'online' ] . '</span>';
            }
            $data_array = array();
            $data_array['$country'] = $country;
            $data_array['$nickname'] = $nickname;
            $data_array['$member'] = $member;
            $data_array['$userID'] = $userID;
            $data_array['$buddyID'] = $ds['buddy'];
            $ignore_content = $GLOBALS["_template"]->replaceTemplate("ignore_content", $data_array);
            echo $ignore_content;
            $n++;
        }
    } else {
        echo $_language->module[ 'ignore_nousers' ];
    }

    $ignore_foot = $GLOBALS["_template"]->replaceTemplate("ignore_foot", array());
    echo $ignore_foot;
} else {
    $_language->readModule('buddys');
    if (!$userID) {
        redirect('index.php?site=buddies', $_language->module[ 'not_logged' ], 3);
    }
}
