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

$_language->readModule('linkcategories', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ getConstNameRequestUri() ]), 0, 15) !== "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

if (isset($_POST[ 'save' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('name'))) {
            safe_query("INSERT INTO " . PREFIX . "links_categorys ( name ) values( '" . $_POST[ 'name' ] . "' ) ");
        } else {
            echo $_language->module[ 'information_incomplete' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else if (isset($_POST[ 'saveedit' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('name'))) {
            safe_query(
                "UPDATE " . PREFIX . "links_categorys SET name='" . $_POST[ 'name' ] . "' WHERE linkcatID='" .
                $_POST[ 'linkcatID' ] . "'"
            );
        } else {
            echo $_language->module[ 'information_incomplete' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else if (isset($_GET[ 'delete' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        safe_query("DELETE FROM " . PREFIX . "links_categorys WHERE linkcatID='" . $_GET[ 'linkcatID' ] . "'");
        safe_query("DELETE FROM " . PREFIX . "links WHERE linkcatID='" . $_GET[ 'linkcatID' ] . "'");
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
}

$action = getAction();

if ($action == "add") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

  echo'<div class="card">
  <div class="card-header">
                            <span class="bi bi-link"></span> '.$_language->module['link_categories'].'
                        </div>
                        <div class="card-body">
  <a href="admincenter.php?site=linkcategories" class="white">'.$_language->module['link_categories'].'</a> &raquo; '.$_language->module['add_category'].'<br><br>';

  echo'<form class="form-horizontal" method="post" action="admincenter.php?site=linkcategories">
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['category_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="name" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" /><button class="btn btn-success btn-xs" type="submit" name="save" />'.$_language->module['add_category'].'</button>
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
                            <span class="bi bi-link"></span> '.$_language->module['link_categories'].'
                        </div>
                        <div class="card-body">
  <a href="admincenter.php?site=linkcategories" class="white">'.$_language->module['link_categories'].'</a> &raquo; '.$_language->module['edit_category'].'<br><br>';

	$ergebnis =
        safe_query("SELECT * FROM " . PREFIX . "links_categorys WHERE linkcatID='" . $_GET[ 'linkcatID' ] . "'");
    $ds = mysqli_fetch_array($ergebnis);

	echo'<form class="form-horizontal" method="post" action="admincenter.php?site=linkcategories">
	<div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['category_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="name" value="'.getinput($ds['name']).'" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="linkcatID" value="'.$ds['linkcatID'].'" /><button class="btn btn-success btn-xs" type="submit" name="saveedit" />'.$_language->module['edit_category'].'</button>
    </div>
  </div>
  </form></div>
  </div>';
}

else {

  echo'<div class="card">
  <div class="card-header">
                            <span class="bi bi-link"></span> '.$_language->module['link_categories'].'
                        </div>
                        <div class="card-body">';

  echo'<a href="admincenter.php?site=linkcategories&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_category' ] . '</a><br /><br />';

	$ergebnis = safe_query("SELECT * FROM " . PREFIX . "links_categorys ORDER BY name");

  echo'<table class="table table-striped">
    <thead>
      <th><strong>'.$_language->module['category_name'].'</strong></th>
      <th class="text-right"><strong>'.$_language->module['actions'].'</strong></th>
    </thead>';

	 $i = 1;
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    while ($ds = mysqli_fetch_array($ergebnis)) {
        if ($i % 2) {
            $td = 'td1';
        } else {
            $td = 'td2';
        }

		echo'<tr>
      <td>'.getinput($ds['name']).'</td>
      <td class="text-right"><a href="admincenter.php?site=linkcategories&amp;action=edit&amp;linkcatID='.$ds['linkcatID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=linkcategories&amp;delete=true&amp;linkcatID='.$ds['linkcatID'].'&amp;captcha_hash='.$hash.'\')" value="' . $_language->module['delete'] . '" />

        <a href="admincenter.php?site=linkcategories&amp;action=edit&amp;linkcatID='.$ds['linkcatID'].'"  class="mobile visible-xs visible-sm" type="button"><span class="bi bi-pencil-square"></span></a>
      <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=linkcategories&amp;delete=true&amp;linkcatID='.$ds['linkcatID'].'&amp;captcha_hash='.$hash.'\')" /><span class="bi bi-trash-fill"></span></a></td>
    </tr>';

      $i++;
	}
	echo'</table>';
}
echo '</div>
  </div>';
?>