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
	$_language->readModule('clanwars');
}

$title_clanwars_details = $GLOBALS["_template"]->replaceTemplate("title_clanwars_details", array());
echo $title_clanwars_details;

echo '<p><a href="index.php?site=clanwars" class="btn btn-primary">' . $_language->module[ 'show_clanwars' ] . '</a>
<a href="index.php?site=clanwars&amp;action=stats" class="btn btn-default">' . $_language->module[ 'stat' ] .
	'</a></p>';

$cwID = (int)$_GET[ 'cwID' ];
$ds = mysqli_fetch_array(safe_query("SELECT * FROM `" . PREFIX . "clanwars` WHERE `cwID` = '" . (int)$cwID."'"));
$extension = explode('.',$ds['game']);
$date = getformatdate($ds[ 'date' ]);
$opponent = '<a href="' . getinput($ds[ 'opphp' ]) . '" target="_blank"><strong>' . getinput($ds[ 'opptag' ]) . ' / ' .
	($ds[ 'opponent' ]) . '</strong></a>';
$league = '<a href="' . getinput($ds[ 'leaguehp' ]) . '" target="_blank">' . getinput($ds[ 'league' ]) . '</a>';
if (is_gamefilexist('images/games/', $ds[ 'game' ])) {
	$game_ico = 'images/games/' . is_gamefilexist('images/games/', $ds[ 'game' ]);
	$game = '<img src="' . $game_ico . '" alt="">';
} else {
	$game = $ds[ 'game' ];
}
$maps = "";
$hometeam = "";
$screens = "";
$score = "";
$extendedresults = "";
$screenshots = "";
$nbr = "";

$homescr = array_sum(unserialize($ds[ 'homescore' ]));
$oppscr = array_sum(unserialize($ds[ 'oppscore' ]));
$theMaps = unserialize($ds[ 'maps' ]);

if (is_array($theMaps)) {
	$n = 1;
	foreach ($theMaps as $map) {
		if ($n == 1) {
			$maps .= $map;
		} else {
			if ($map == '') {
				$maps = $_language->module[ 'no_maps' ];
			} else {
				$maps .= ', ' . $map;
			}
		}
		$n++;
	}
}

if ($homescr > $oppscr) {
	$results_1 = '<span style="color: ' . $wincolor . '">' . $homescr . '</span>';
	$results_2 = '<span style="color: ' . $wincolor . '">' . $oppscr . '</span>';
} else if ($homescr < $oppscr) {
	$results_1 = '<span style="color: ' . $loosecolor . '">' . $homescr . '</span>';
	$results_2 = '<span style="color: ' . $loosecolor . '">' . $oppscr . '</span>';
} else {
	$results_1 = '<span style="color: ' . $drawcolor . '">' . $homescr . '</span>';
	$results_2 = '<span style="color: ' . $drawcolor . '">' . $oppscr . '</span>';
}

if (isclanwaradmin($userID)) {
	$adminaction = '<input type="button" onclick="window.open(
            \'upload.php?cwID=' . $cwID . '\',
            \'Clanwars\',
            \'toolbar=no,status=no,scrollbars=yes,width=800,height=600\'
            )" value="' . $_language->module[ 'upload_screenshot' ] . '" class="btn btn-primary">
<input type="button" onclick="window.open(
    \'clanwars.php?action=edit&amp;cwID=' . $ds[ 'cwID' ] . '\',
    \'Clanwars\',
    \'toolbar=no,status=no,scrollbars=yes,width=800,height=600\'
    )" value="' . $_language->module[ 'edit' ] . '" class="btn btn-warning">
<input type="button" onclick="MM_confirm(
    \'' . $_language->module[ 'really_delete_clanwar' ] . '?\',
    \'clanwars.php?action=delete&amp;cwID=' . $ds[ 'cwID' ] . '\'
    )" value="' . $_language->module[ 'delete' ] . '" class="btn btn-danger">';
} else {
	$adminaction = '';
}

$report = cleartext($ds[ 'report' ]);
$report = toggle($report, $ds[ 'cwID' ]);
if ($report == "") {
	$report = "n/a";
}

$squad = '<a href="index.php?site=clanwars&amp;action=showonly&amp;only=squad&amp;id=' . $ds[ 'squad' ] . '"><strong>' .
	getsquadname($ds[ 'squad' ]) . '</strong></a>';

$opptag = getinput($ds[ 'opptag' ]);
$oppteam = getinput($ds[ 'oppteam' ]);
$server = getinput($ds[ 'server' ]);
$hltv = getinput($ds[ 'hltv' ]);

if (!empty($ds[ 'hometeam' ])) {
	$array = unserialize($ds[ 'hometeam' ]);
	$n = 1;
	foreach ($array as $id) {
		if (!empty($id)) {
			if ($n > 1) {
				$hometeam .= ', <a href="index.php?site=profile&amp;id=' . $id . '">' . getnickname($id) . '</a>';
			} else {
				$hometeam .= '<a href="index.php?site=profile&amp;id=' . $id . '">' . getnickname($id) . '</a>';
			}
			$n++;
		}
	}
}
$screenshots = '';
if (!empty($ds[ 'screens' ])) {
	$screens = explode("|", $ds[ 'screens' ]);
}
if (is_array($screens)) {
	$n = 1;
	foreach ($screens as $screen) {
		if (!empty($screen)) {
			$screenshots .= '<a href="images/clanwar-screens/' . $screen .
				'" target="_blank"><img src="images/clanwar-screens/' . $screen .
				'" width="150" height="100" style="padding-top:3px; padding-right:3px;" alt=""></a>';
			if ($nbr == 2) {
				$nbr = 1;
				$screenshots .= '<br>';
			} else {
				$nbr = 2;
			}
			$n++;
		}
	}
}

if (!(mb_strlen(trim($screenshots)))) {
	$screenshots = $_language->module[ 'no_screenshots' ];
}

$linkpage = cleartext($ds[ 'linkpage' ]);
$linkpage = str_replace('http://', '', $ds[ 'linkpage' ]);
if ($linkpage == "") {
	$linkpage = "#";
}

// -- v1.0, extended results -- //

$scoreHome = unserialize($ds[ 'homescore' ]);
$scoreOpp = unserialize($ds[ 'oppscore' ]);
$homescr = array_sum($scoreHome);
$oppscr = array_sum($scoreOpp);

if ($homescr > $oppscr) {
	$result_map = '[color=' . $wincolor . '][b]' . $homescr . ':' . $oppscr . '[/b][/color]';
	$result_map2 = 'won';
} else if ($homescr < $oppscr) {
	$result_map = '[color=' . $loosecolor . '][b]' . $homescr . ':' . $oppscr . '[/b][/color]';
	$result_map2 = 'lost';
} else {
	$result_map = '[color=' . $drawcolor . '][b]' . $homescr . ':' . $oppscr . '[/b][/color]';
	$result_map2 = 'draw';
}

if (is_array($theMaps)) {
	$d = 0;
	foreach ($theMaps as $map) {

		$score = '';
		if ($scoreHome[ $d ] > $scoreOpp[ $d ]) {
			$score_1 = '<span style="color: ' . $wincolor . '"><strong>' . $scoreHome[ $d ] . '</strong></span>';
			$score_2 = '<span style="color: ' . $wincolor . '"><strong>' . $scoreOpp[ $d ] . '</strong></span>';
		} else if ($scoreHome[ $d ] < $scoreOpp[ $d ]) {
			$score_1 = '<span style="color: ' . $loosecolor . '"><strong>' . $scoreHome[ $d ] . '</strong></span>';
			$score_2 = '<span style="color: ' . $loosecolor . '"><strong>' . $scoreOpp[ $d ] . '</strong></span>';
		} else {
			$score_1 = '<span style="color: ' . $drawcolor . '"><strong>' . $scoreHome[ $d ] . '</strong></span>';
			$score_2 = '<span style="color: ' . $drawcolor . '"><strong>' . $scoreOpp[ $d ] . '</strong></span>';
		}

		$data_array = array();
		$data_array['$map'] = $map;
		$data_array['$score_1'] = $score_1;
		$data_array['$score_2'] = $score_2;
		$clanwars_details_results = $GLOBALS["_template"]->replaceTemplate("clanwars_details_results", $data_array);
		$extendedresults .= $clanwars_details_results;
		unset($score);
		$d++;
	}
} else {
	$extendedresults = '';
}

// -- clanwar output -- //

$data_array = array();
$data_array['$report'] = $report;
$data_array['$date'] = $date;
$data_array['$game'] = $game;
$data_array['$squad'] = $squad;
$data_array['$opponent'] = $opponent;
$data_array['$league'] = $league;
$data_array['$linkpage'] = $linkpage;
$data_array['$maps'] = $maps;
$data_array['$extendedresults'] = $extendedresults;
$data_array['$results_1'] = $results_1;
$data_array['$results_2'] = $results_2;
$data_array['$myclantag'] = $myclantag;
$data_array['$hometeam'] = $hometeam;
$data_array['$opptag'] = $opptag;
$data_array['$oppteam'] = $oppteam;
$data_array['$server'] = $server;
$data_array['$hltv'] = $hltv;
$data_array['$screenshots'] = $screenshots;
$data_array['$adminaction'] = $adminaction;
$clanwars_details = $GLOBALS["_template"]->replaceTemplate("clanwars_details", $data_array);
echo $clanwars_details;

$comments_allowed = $ds[ 'comments' ];
$parentID = $cwID;
$type = "cw";
$referer = "index.php?site=clanwars_details&amp;cwID=$cwID";

include("comments.php");
