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

$_language->readModule('partners', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ getConstNameRequestUri() ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

$filepath = "../images/partners/";

if (isset($_GET[ 'delete' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        $partnerID = (int)$_GET[ 'partnerID' ];
        safe_query("DELETE FROM " . PREFIX . "partners WHERE partnerID='" . $partnerID . "' ");
        if (file_exists($filepath . $partnerID . '.gif')) {
            unlink($filepath . $partnerID . '.gif');
        }
        if (file_exists($filepath . $partnerID . '.jpg')) {
            unlink($filepath . $partnerID . '.jpg');
        }
        if (file_exists($filepath . $partnerID . '.png')) {
            unlink($filepath . $partnerID . '.png');
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else if (isset($_POST[ 'sortieren' ])) {
    try {
        sortContentByParameters($_POST[ 'captcha_hash' ], $_POST[ 'sort' ], 'partners', 'partnerID');
    } catch (Exception $e) {
        echo generateAlert($e->getMessage(), 'alert-danger');
    }
} else if (isset($_POST[ 'save' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        $name = $_POST[ 'name' ];
        $url = $_POST[ 'url' ];
        if (isset($_POST[ "displayed" ])) {
            $displayed = 1;
        } else {
            $displayed = 0;
        }

        safe_query(
            "INSERT INTO
                `" . PREFIX . "partners` (
                    `name`,
                    `url`,
                    `displayed`,
                    `date`,
                    `sort`
                )
                VALUES (
                    '$name',
                    '$url',
                    '" . $displayed . "',
                    '" . time() . "',
                    '1'
                )"
        );
        $id = mysqli_insert_id($_database);

        if ($file = uploadFile('banner', $id, $filepath)) {
            safe_query(
                "UPDATE " . PREFIX . "partners
                    SET banner='" . $file . "'
                    WHERE partnerID='" . $id . "'"
            );
        }

    } else {
        echo  $_language->module[ 'transaction_invalid' ];
    }
} else if (isset($_POST[ 'saveedit' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        $name = $_POST[ 'name' ];
        $url = $_POST[ 'url' ];
        if (isset($_POST[ "displayed" ])) {
            $displayed = 1;
        } else {
            $displayed = 0;
        }

        $partnerID = (int)$_POST[ 'partnerID' ];
        $id = $partnerID;

        safe_query(
            "UPDATE
                `" . PREFIX . "partners`
            SET
                `name` = '" . $name . "',
                `url` = '" . $url . "',
                `displayed` = '" . $displayed . "'
            WHERE
                `partnerID` = '" . $partnerID . "'"
        );

        if ($file = uploadFile('banner', $partnerID, $filepath)) {
            safe_query(
                "UPDATE " . PREFIX . "partners
                    SET banner='" . $file . "'
                    WHERE partnerID='" . $partnerID . "'"
            );
        }

    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
}

$_language->readModule('partners', false, true);

$action = getAction();

if ($action == "add") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

	echo'<div class="card">
    <div class="card-header">
                            <span class="bi bi-hand-thumbs-up-fill"></span> '.$_language->module['partners'].'
                        </div>
                        <div class="card-body">
	<a href="admincenter.php?site=partners" class="white">'.$_language->module['partners'].'</a> &raquo; '.$_language->module['add_partner'].'<br><br>';

	echo'<form class="form-horizontal" method="post" action="admincenter.php?site=partners" enctype="multipart/form-data">

     <div class="row">

<div class="col-md-6">

	<div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['partner_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input type="text" class="form-control" name="name" size="60" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['banner'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input name="banner" type="file" size="40" /> <small>'.$_language->module['max_88x31'].'</small></em></span>
    </div>
  </div>

  </div>

<div class="col-md-6">

  <div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['homepage_url'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input type="text" class="form-control" name="url" size="60" value="http://" /></em></span>
    </div>
  </div>

  </div>

<div class="col-md-12">

  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['is_displayed'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input type="checkbox" name="displayed" value="1" checked="checked" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" />
		<button class="btn btn-success btn-xs" type="submit" name="save"  />'.$_language->module['add_partner'].'</button>
    </div>
  </div>

</div>
  </div>

  </form></div>
  </div>';
} else if ($action == "edit") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

  echo'<div class="card">
  <div class="card-header">
                            <span class="bi bi-hand-thumbs-up-fill"></span> '.$_language->module['partners'].'
                        </div>
                        <div class="card-body">
  <a href="admincenter.php?site=partners" class="white">'.$_language->module['partners'].'</a> &raquo; '.$_language->module['edit_partner'].'<br><br>';

  $partnerID = $_GET[ 'partnerID' ];
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "partners WHERE partnerID='$partnerID'");
    $ds = mysqli_fetch_array($ergebnis);

    if ($ds[ 'displayed' ] == '1') {
        $displayed = '<input type="checkbox" name="displayed" value="1" checked="checked" />';
    } else {
        $displayed = '<input type="checkbox" name="displayed" value="1" />';
    }

	echo'<form class="form-horizontal" method="post" action="admincenter.php?site=partners" enctype="multipart/form-data">

     <div class="row">

<div class="col-md-6">

	<div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['current_banner'].':</label>
    <div class="col-sm-8">
      <img src="../images/partners/'.$ds['banner'].'" alt="" /></em></span>
    </div>
  </div>
	<div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['partner_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input type="text" class="form-control" name="name" value="'.getinput($ds['name']).'" /></em></span>
    </div>
  </div>

  </div>

<div class="col-md-6">

  <div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['banner'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input name="banner" type="file" size="40" /> <small>'.$_language->module['max_88x31'].'</small></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['homepage_url'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input type="text" class="form-control" name="url" value="'.getinput($ds['url']).'" /></em></span>
    </div>
  </div>

   </div>

<div class="col-md-12">

  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['is_displayed'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
     '.$displayed.'</em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="partnerID" value="'.$partnerID.'" />
		<button class="btn btn-primary btn-xs" type="submit" name="saveedit"  />'.$_language->module['edit_partner'].'</button>
    </div>
  </div>

  </div>
  </div>

  </form></div>
  </div>';
}

else {

  echo'<div class="card">
  <div class="card-header">
                            <span class="bi bi-hand-thumbs-up-fill"></span> '.$_language->module['partners'].'
                        </div>
                        <div class="card-body">';

  echo'<a href="admincenter.php?site=partners&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_partner' ] . '</a><br /><br />';

	echo'<form method="post" action="admincenter.php?site=partners">
  <table class="table table-striped">
    <thead>
      <th><strong>'.$_language->module['partners'].'</strong></th>
      <th><strong>'.$_language->module['clicks'].'</strong></th>
      <th class="hidden-sm hidden-xs"><strong>'.$_language->module['is_displayed'].'</strong></th>
      <th><strong>'.$_language->module['actions'].'</strong></th>
      <th><strong>'.$_language->module['sort'].'</strong></th>
    </thead>';

	$partners = safe_query("SELECT * FROM " . PREFIX . "partners ORDER BY sort");
    $tmp = mysqli_fetch_assoc(safe_query("SELECT count(partnerID) as cnt FROM " . PREFIX . "partners"));
    $anzpartners = $tmp[ 'cnt' ];
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    $CAPCLASS->createTransaction();
    $hash_2 = $CAPCLASS->getHash();

    $i = 1;
    while ($db = mysqli_fetch_array($partners)) {
        if ($i % 2) {
            $td = 'td1';
        } else {
            $td = 'td2';
        }

        $db[ 'displayed' ] == 1 ? $displayed = '<span style="color: #00FF00;"><strong>' . $_language->module[ 'yes' ] . '</strong></span>' :
            $displayed = '<span style="color: #FF0000;"><strong>' . $_language->module[ 'no' ] . '</strong></span>';

        $days = round((time() - $db[ 'date' ]) / (60 * 60 * 24));
        if ($days) {
            $perday = round($db[ 'hits' ] / $days, 2);
        } else {
            $perday = $db[ 'hits' ];
        }

        echo '<tr>
      <td><a href="'.getinput($db['url']).'" target="_blank">'.getinput($db['name']).'</a></td>
      <td>'.$db['hits'].' ('.$perday.')</td>
      <td class="hidden-sm hidden-xs">'.$displayed.'</td>
      <td><a href="admincenter.php?site=partners&amp;action=edit&amp;partnerID='.$db['partnerID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=partners&amp;delete=true&amp;partnerID='.$db['partnerID'].'&amp;captcha_hash='.$hash.'\')" value="' . $_language->module['delete'] . '" />

	  <a href="admincenter.php?site=partners&amp;action=edit&amp;partnerID='.$db['partnerID'].'"  class="mobile visible-xs visible-sm" type="button"><span class="bi bi-pencil-square"></span></a>
      <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=partners&amp;delete=true&amp;partnerID='.$db['partnerID'].'&amp;captcha_hash='.$hash.'\')" /><span class="bi bi-trash-fill"></span></a>


      </td>
      <td>
      <select name="sort[]">';

        for ($j = 1; $j <= $anzpartners; $j++) {
            if ($db[ 'sort' ] == $j) {
                echo '<option value="' . $db[ 'partnerID' ] . '-' . $j . '" selected="selected">' . $j . '</option>';
            } else {
                echo '<option value="' . $db[ 'partnerID' ] . '-' . $j . '">' . $j . '</option>';
            }
        }

        echo '</select>
      </td>
    </tr>';
    $i++;

	}
	echo'<tr class="td_head">
      <td colspan="5" align="right"><input type="hidden" name="captcha_hash" value="'.$hash_2.'" /><button class="btn btn-primary btn-xs" type="submit" name="sortieren" />'.$_language->module['to_sort'].'</button></td>
    </tr>
  </table>
  </form>';
}
echo '</div></div>';
?>