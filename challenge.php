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
    $_language->readModule('challenge');
}

$title_challenge = $GLOBALS["_template"]->replaceTemplate("title_challenge", array());
echo $title_challenge;

$action = getAction();

$show = true;
if ($action == "save" && isset($_POST['post'])) {
    $opponent = $_POST['opponent'];
    $opphp = $_POST['opphp'];
    $oppcountry = $_POST['oppcountry'];
    if (isset($_POST['squad'])) {
        $squad = $_POST['squad'];
    } else {
        $squad = "";
    }
    $league = $_POST['league'];
    $map = $_POST['map'];
    $server = $_POST['server'];
    $email = $_POST['email'];
    $info = $_POST['info'];
    $datetime = strtotime($_POST['datetime']);
    $run = 0;

    $error = array();
    if (!(mb_strlen(trim($datetime)))) {
        $error[] = $_language->module['date'];
    }
	if (!(mb_strlen(trim($opponent)))) {
        $error[] = $_language->module['enter_clanname'];
    }
    if (!validate_url($opphp)) {
        $error[] = $_language->module['enter_url'];
    }
    if (!validate_email($email)) {
        $error[] = $_language->module['enter_email'];
    }
    if (!(mb_strlen(trim($league)))) {
        $error[] = $_language->module['enter_league'];
    }
    if (!(mb_strlen(trim($map)))) {
        $error[] = $_language->module['enter_map'];
    }
    if (!(mb_strlen(trim($server)))) {
        $error[] = $_language->module['enter_server'];
    }

    if ($userID) {
        $run = 1;
    } else {
        $CAPCLASS = new \webspell\Captcha;
        if (!$CAPCLASS->checkCaptcha($_POST['captcha'], $_POST['captcha_hash'])) {
            $error[] = $_language->module['wrong_security_code'];
        } else {
            $run = 1;
        }
    }

    if (!count($error) && $run) {
        $date = time();
        $touser = array();
        safe_query(
            "INSERT INTO
                `" . PREFIX . "challenge` (
                    `date`,
                    `cwdate`,
                    `squadID`,
                    `opponent`,
                    `opphp`,
                    `oppcountry`,
                    `league`,
                    `map`,
                    `server`,
                    `email`,
                    `info`
                )
                values(
                    '$date',
                    '$datetime',
                    '$squad',
                    '$opponent',
                    '$opphp',
                    '$oppcountry',
                    '$league',
                    '$map',
                    '$server',
                    '$email',
                    '$info'
                )"
        );
        $ergebnis =
            safe_query(
                "SELECT
                    `userID`
                FROM
                    `" . PREFIX . "squads_members`
                WHERE
                    `warmember` = '1' AND
                    `squadID` = '" . (int)$squad ."'"
            );
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $touser[] = $ds['userID'];
        }
        if (!count($touser)) {
            $touser[] = 1;
        }
        $date = time();
        $tmp_lang = new \webspell\Language();
        foreach ($touser as $id) {
            $tmp_lang->setLanguage(getuserlanguage($id));
            $tmp_lang->readModule('challenge');
            $message = $tmp_lang->module['challenge_message'];
            sendmessage($id, $tmp_lang->module['message_title'], $message);
        }
        echo generateAlert($_language->module['thank_you'], 'alert-success');
        unset(
            $_POST['opponent'],
            $_POST['opphp'],
            $_POST['league'],
            $_POST['map'],
            $_POST['server'],
            $_POST['info'],
            $_POST['email']
        );
        $show = false;
    } else {
        $show = true;
        $showerror = generateErrorBoxFromArray($_language->module['problems'], $error);
    }
} else if ($action == "delete") {
    $chID = $_GET['chID'];
    if (isclanwaradmin($userID)) {
        safe_query("DELETE FROM `" . PREFIX . "challenge` WHERE `chID` = '" . (int)$chID ."'");
        if (!empty($_language)) {
            redirect('index.php?site=challenge', $_language->module['entry_deleted'], 3);
        }
    } else {
        redirect('index.php?site=challenge', $_language->module['no_access'], 3);
    }
}

$type = getSortOrderType("DESC");

if ($show === true) {
    $squads = getgamesquads();
    $countries = getcountries();
	$showform = 1;

    if (!isset($showerror)) {
        $showerror = '';
    }
	if (!$squads) {
        $showform = 0;
    }
    if (isset($_POST['datetime'])) {
        $datetime = getforminput($_POST['datetime']);
    } else {
        $datetime = '';
    }
    if (isset($_POST['opponent'])) {
        $opponent = getforminput($_POST['opponent']);
    } else {
        $opponent = '';
    }
    if (isset($_POST['opphp'])) {
        $opphp = getforminput($_POST['opphp']);
    } else {
        $opphp = '';
    }
    if (isset($_POST['league'])) {
        $league = getforminput($_POST['league']);
    } else {
        $league = '';
    }
    if (isset($_POST['map'])) {
        $map = getforminput($_POST['map']);
    } else {
        $map = '';
    }
    if (isset($_POST['server'])) {
        $server = getforminput($_POST['server']);
    } else {
        $server = '';
    }
    if (isset($_POST['info'])) {
        $info = getforminput($_POST['info']);
    } else {
        $info = '';
    }

    $date_now = date("Y-m-d\TH:m");

    if ($loggedin) {
        $email = getemail($userID);
        $data_array = array();
        $data_array['$showerror'] = $showerror;
        $data_array['$date_now'] = $date_now;
        $data_array['$datetime'] = $datetime;
        $data_array['$squads'] = $squads;
        $data_array['$opponent'] = $opponent;
        $data_array['$opphp'] = $opphp;
        $data_array['$league'] = $league;
        $data_array['$countries'] = $countries;
        $data_array['$map'] = $map;
        $data_array['$server'] = $server;
        $data_array['$email'] = $email;
        $data_array['$info'] = $info;
        $challenge_loggedin = $GLOBALS["_template"]->replaceTemplate("challenge_loggedin", $data_array);
		if (!empty($squads)) {
			echo $challenge_loggedin;
		}
    } else {
        $CAPCLASS = new \webspell\Captcha;
        $captcha = $CAPCLASS->createCaptcha();
        $hash = $CAPCLASS->getHash();
        $CAPCLASS->clearOldCaptcha();
        if (isset($_POST['email'])) {
            $email = getforminput($_POST['email']);
        } else {
            $email = "";
        }
        $data_array = array();
        $data_array['$showerror'] = $showerror;
        $data_array['$date_now'] = $date_now;
        $data_array['$datetime'] = $datetime;
        $data_array['$squads'] = $squads;
        $data_array['$opponent'] = $opponent;
        $data_array['$opphp'] = $opphp;
        $data_array['$league'] = $league;
        $data_array['$countries'] = $countries;
        $data_array['$map'] = $map;
        $data_array['$server'] = $server;
        $data_array['$email'] = $email;
        $data_array['$info'] = $info;
        $data_array['$captcha'] = $captcha;
        $data_array['$hash'] = $hash;
		$template = $GLOBALS["_template"]->replaceTemplate("challenge_notloggedin", $data_array);
    }
		if ($showform==0) {
			$data_array = array();
			$data_array['$showerror'] = generateErrorBoxFromArray($_language->module['problems'], array($_language->module['no_gamingsquad']));
			$alertbox = $GLOBALS["_template"]->replaceTemplate("challenge_noteam", $data_array);
			echo $alertbox;
			return false;
		} else {
			if (!$loggedin) {
				if (!empty($squads)) {
					echo $template;
				}
			}
		}
}

if (isclanwaradmin($userID)) {
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "challenge ORDER BY date $type");
    $anz = mysqli_num_rows($ergebnis);
    if ($anz) {
        if (!isset($type)) {
            $type = "DESC";
        }

        echo '<p>';
        if ($type == "ASC") {
            echo '<a class="btn btn-default btn-xs" href="index.php?site=challenge&amp;type=DESC">' .
                $_language->module['sort'] . ' <span class="bi bi-arrow-down-circle"></span></a>';
        } else {
            echo '<a class="btn btn-default btn-xs" href="index.php?site=challenge&amp;type=ASC">' .
                $_language->module['sort'] . ' <span class="bi bi-arrow-up-circle"></span></a>';
        }
        echo '</p>';

        while ($ds = mysqli_fetch_array($ergebnis)) {

            $date = getformatdate($ds['date']);
            $cwdate = getformatdatetime($ds['cwdate']);
            $squad = getsquadname($ds['squadID']);
            $oppcountry = "[flag]" . $ds['oppcountry'] . "[/flag]";
            $country = flags($oppcountry);
            $opponent = '<a href="' . $ds['opphp'] . '" target="_blank">' . clearfromtags($ds['opponent']) . '</a>';
            $league = clearfromtags($ds['league']);
            $map = clearfromtags($ds['map']);
            $server = clearfromtags($ds['server']);
            $info = cleartext($ds['info']);
            $email = '<a href="mailto:' . mail_protect(cleartext($ds['email'])) . '">' . $ds['email'] . '</a>';

            if (isset($ds['hp'])) {
                if (!validate_url($ds['hp'])) {
                    $homepage = '';
                } else {
                    $homepage = '<a href="' . $ds['hp'] .
                        '" target="_blank"><img src="images/icons/hp.gif" width="14" height="14" alt="homepage"></a>';
                }
            }

            if (isset($ds['name'])) {
                $name = cleartext($ds['name']);
            }
            if (isset($ds['comment'])) {
                $message = cleartext($ds['comment']);
            }

            $actions = '<a href="index.php?site=calendar&amp;action=addwar&amp;chID=' . $ds['chID'] .
                '" class="btn btn-primary btn-xs" role="button">' . $_language->module['insert_in_calendar'] .
                '</a> <a href="index.php?site=challenge&amp;action=delete&amp;chID=' . $ds['chID'] .
                '" class="btn btn-danger btn-xs" role="button">' . $_language->module['delete_challenge'] . '</a>';

            $data_array = array();
            $data_array['$date'] = $date;
            $data_array['$country'] = $country;
            $data_array['$opponent'] = $opponent;
            $data_array['$cwdate'] = $cwdate;
            $data_array['$squad'] = $squad;
            $data_array['$league'] = $league;
            $data_array['$map'] = $map;
            $data_array['$server'] = $server;
            $data_array['$email'] = $email;
            $data_array['$info'] = $info;
            $data_array['$actions'] = $actions;
            $challenges = $GLOBALS["_template"]->replaceTemplate("challenges", $data_array);
            echo $challenges;

        }
        echo '<br>';
    } else {
        echo generateAlert($_language->module['no_entries'], 'alert-info');
    }
}
?>