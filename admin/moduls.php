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

$_language->readModule('moduls', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ getConstNameRequestUri() ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

if (isset($_GET[ 'delete' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        $modulID = (int)$_GET[ 'modulID' ];
        safe_query("DELETE FROM " . PREFIX . "moduls WHERE modulID='" . $modulID . "' ");

    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else if (isset($_POST[ 'sortieren' ])) {
  try {
      sortContentByParameters($_POST[ 'captcha_hash' ], $_POST[ 'sort' ], 'moduls', 'modulID');
  } catch (Exception $e) {
      echo generateAlert($e->getMessage(), 'alert-danger');
  }
} else if (isset($_POST[ 'save' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        $module = $_POST[ 'module' ];


		if (@$_POST['radio1']=="le_activated") {
			  $le_activated = 1;
			  $re_activated = 0;
			  $activated = 0;
		} elseif (@$_POST['radio1']=="re_activated") {
			  $le_activated = 0;
			  $re_activated = 1;
			  $activated = 0;
		} elseif (@$_POST['radio1']=="activated") {
        $le_activated = 0;
        $re_activated = 0;
        $activated = 1;

    } else {
        $le_activated = 0;
        $re_activated = 0;
        $activated = 0;
    }



        safe_query(
            "INSERT INTO
                `" . PREFIX . "moduls` (
                    `module`,
                    `le_activated`,
                    `re_activated`,
                    `activated`,
                    `sort`
                )
                VALUES (
                    '$module',
                    '" . $le_activated . "',
                    '" . $re_activated . "',
                    '" . $activated . "',
                    '1'
                )"
        );
        $id = mysqli_insert_id($_database);


    } else {
        echo  $_language->module[ 'transaction_invalid' ];
    }
} else if (isset($_POST[ 'saveedit' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        $module = $_POST[ 'module' ];

      if (@$_POST['radio1']=="le_activated") {
        $le_activated = 1;
        $re_activated = 0;
        $activated = 0;
        $deactivated = 0;
    } elseif (@$_POST['radio1']=="re_activated") {
        $le_activated = 0;
        $re_activated = 1;
        $activated = 0;
        $deactivated = 0;
    } elseif (@$_POST['radio1']=="activated") {
        $le_activated = 0;
        $re_activated = 0;
        $activated = 1;
        $deactivated = 0;
    } elseif (@$_POST['radio1']=="deactivated") {
        $le_activated = 0;
        $re_activated = 0;
        $activated = 0;
        $deactivated = 1;

    } else {
        $le_activated = 0;
        $re_activated = 0;
        $activated = 0;
        $deactivated = 0;
    }



        $modulID = (int)$_POST[ 'modulID' ];
        $id = $modulID;

        safe_query(
            "UPDATE
                `" . PREFIX . "moduls`
            SET
                `module` = '" . $module . "',
                `le_activated` = '" . $le_activated . "',
                `re_activated` = '" . $re_activated . "',
                `activated` = '" . $activated . "'
            WHERE
                `modulID` = '" . $modulID . "'"
        );


    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
}

$_language->readModule('moduls', false, true);

$action = getAction();

if ($action == "add") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

	echo'<div class="card">
  <div class="card-header">
                            <span class="bi bi-hand-thumbs-up-fill"></span> '.$_language->module['styles'].'
                        </div>
                        </div>
                        <div class="card">

            <ul class="nav nav-tabs-primary">
    <li class="nav-item"><a href="admincenter.php?site=styles">Style</a></li>
    <li class="nav-item"><a href="admincenter.php?site=buttons">Buttons</a></li>
    <li class="nav-item" class="active"><a href="admincenter.php?site=moduls">Module</a></li>
    <li class="nav-item"><a href="admincenter.php?site=css">.css</a></li>
</ul>
<ol class="breadcrumb-primary"> </ol>
	&nbsp;&nbsp;<a href="admincenter.php?site=moduls" class="white">'.$_language->module['module'].'</a> &raquo; '.$_language->module['add_modul'].'<br><br>';

	echo'<form class="form-horizontal" method="post" action="admincenter.php?site=moduls" enctype="multipart/form-data">

     <div class="row">

<div class="col-md-12">

    <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['modul_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input type="text" class="form-control" name="module" size="60" /></em></span>
    </div>
  </div>

<div class="form-group">
<label class="col-sm-2 control-label"></label>

<div class="col-sm-2">
    <label for="activated">'.$_language->module['deactivated'].'</label>
  <input id="deactivated" type="radio" name="radio1" value="deactivated">
<img class="img-thumbnail" src="../images/plugins/layout-haupt2.jpg">
</div>
<div class="col-sm-2">
    <label for="le_activated">'.$_language->module['left_is_activated'].'</label>
  <input id="le_activated" type="radio" name="radio1" value="le_activated">
<img class="img-thumbnail" src="../images/plugins/layout-haupt3.jpg">
</div>
<div class="col-sm-2">
    <label for="re_activated">'.$_language->module['right_is_activated'].'</label>
  <input id="re_activated" type="radio" name="radio1" value="re_activated">
<img class="img-thumbnail" src="../images/plugins/layout-haupt4.jpg">
</div>
<div class="col-sm-2">
    <label for="activated">'.$_language->module['activated'].'</label>
  <input id="activated" type="radio" name="radio1" value="activated">
<img class="img-thumbnail" src="../images/plugins/layout-haupt5.jpg">
</div>
<label class="col-sm-2 control-label"></label>
</div>


  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" />
		<button class="btn btn-success btn-xs" type="submit" name="save"  />'.$_language->module['add_modul'].'</button>
    <br><br>
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
                            <span class="bi bi-hand-thumbs-up-fill"></span> '.$_language->module['styles'].'
                        </div>
                        </div>
                        <div class="card">

            <ul class="nav nav-tabs-primary">
    <li class="nav-item"><a href="admincenter.php?site=styles">Style</a></li>
    <li class="nav-item"><a href="admincenter.php?site=buttons">Buttons</a></li>
    <li class="nav-item" class="active"><a href="admincenter.php?site=moduls">Module</a></li>
    <li class="nav-item"><a href="admincenter.php?site=css">.css</a></li>
</ul>
<ol class="breadcrumb-primary"> </ol>
  &nbsp;&nbsp;<a href="admincenter.php?site=moduls" class="white">'.$_language->module['module'].'</a> &raquo; '.$_language->module['edit_modul'].'<br><br>';

  $modulID = $_GET[ 'modulID' ];
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "moduls WHERE modulID='$modulID'");
    $ds = mysqli_fetch_array($ergebnis);

    if ($ds[ 'le_activated' ] == '1') {
        $le_activated = '<input id="le_activated" type="radio" name="radio1" value="le_activated" checked="checked" />';
    } else {
        $le_activated = '<input id="le_activated" type="radio" name="radio1" value="le_activated">';
    }

    if ($ds[ 're_activated' ] == '1') {
        $re_activated = '<input id="re_activated" type="radio" name="radio1" value="re_activated" checked="checked" />';
    } else {
        $re_activated = '<input id="re_activated" type="radio" name="radio1" value="re_activated">';
    }

    if ($ds[ 'activated' ] == '1') {
        $activated = '<input id="activated" type="radio" name="radio1" value="activated" checked="checked" />';
    } else {
        $activated = '<input id="activated" type="radio" name="radio1" value="activated">';
    }

    if ($ds[ 'deactivated' ] == '1') {
        $deactivated = '<input id="deactivated" type="radio" name="radio1" value="deactivated" checked="checked" />';
    } else {
        $deactivated = '<input id="deactivated" type="radio" name="radio1" value="deactivated">';
    }

    echo'<form class="form-horizontal" method="post" action="admincenter.php?site=moduls" enctype="multipart/form-data">


<div class="row">

<div class="col-md-12">


    <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['modul_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input type="text" class="form-control" name="module" value="'.getinput($ds['module']).'" /></em></span>
    </div>
  </div>

    <div class="form-group">
<div class="col-sm-2"></div>
<div class="col-sm-2">
     <label for="deactivated">'.$_language->module['deactivated'].'</label>
  '.$deactivated.'
<img class="img-thumbnail" src="../images/plugins/layout-haupt2.jpg">
</div>

<div class="col-sm-2">
    <label for="le_activated">'.$_language->module['left_is_activated'].'</label>
  '.$le_activated.'
<img class="img-thumbnail" src="../images/plugins/layout-haupt3.jpg">
</div>

<div class="col-sm-2">
    <label for="re_activated">'.$_language->module['right_is_activated'].'</label>
  '.$re_activated.'
<img class="img-thumbnail" src="../images/plugins/layout-haupt4.jpg">
</div>

<div class="col-sm-2">
     <label for="activated">'.$_language->module['activated'].'</label>
  '.$activated.'
<img class="img-thumbnail" src="../images/plugins/layout-haupt5.jpg">
</div>
<div class="col-sm-2"></div>

</div>


  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="modulID" value="'.$modulID.'" />
		<button class="btn btn-primary btn-xs" type="submit" name="saveedit"  />'.$_language->module['edit_modul'].'</button>
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
                            <span class="bi bi-hand-thumbs-up-fill"></span> '.$_language->module['styles'].'
                        </div>
                        </div>
                        <div class="card">

            <ul class="nav nav-tabs-primary">
    <li class="nav-item"><a class="nav-link" href="admincenter.php?site=styles">Style</a></li>
    <li class="nav-item"><a class="nav-link" href="admincenter.php?site=buttons">Buttons</a></li>
    <li class="nav-item"><a class="nav-link active" href="admincenter.php?site=moduls">Module</a></li>
    <li class="nav-item"><a class="nav-link" href="admincenter.php?site=css">.css</a></li>
</ul>
<ol class="breadcrumb-primary"> </ol>
<div class="card-body">';

  echo'<a href="admincenter.php?site=moduls&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_modul' ] . '</a><br /><br />';

	echo'<form method="post" action="admincenter.php?site=moduls">
  <table class="table table-striped">
    <thead>
      <th><strong>'.$_language->module['module'].'</strong></th>

      <th class="hidden-sm hidden-xs"><strong>'.$_language->module['left_is_activated'].'</strong></th>
      <th class="hidden-sm hidden-xs"><strong>'.$_language->module['right_is_activated'].'</strong></th>
      <th class="hidden-sm hidden-xs"><strong>'.$_language->module['activated'].'</strong></th>
      <th><strong>'.$_language->module['actions'].'</strong></th>
      <th><strong>'.$_language->module['sort'].'</strong></th>
    </thead>';

	$moduls = safe_query("SELECT * FROM " . PREFIX . "moduls ORDER BY sort");
    $tmp = mysqli_fetch_assoc(safe_query("SELECT count(modulID) as cnt FROM " . PREFIX . "moduls"));
    $anzmoduls = $tmp[ 'cnt' ];
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    $CAPCLASS->createTransaction();
    $hash_2 = $CAPCLASS->getHash();

    $i = 1;
    while ($db = mysqli_fetch_array($moduls)) {
        if ($i % 2) {
            $td = 'td1';
        } else {
            $td = 'td2';
        }

        $db[ 'le_activated' ] == 1 ? $le_activated = '<span style="color: #00FF00;"><strong>' . $_language->module[ 'yes' ] . '</strong></span>' :
            $le_activated = '<span style="color: #FF0000;"><strong>' . $_language->module[ 'no' ] . '</strong></span>';
         $db[ 're_activated' ] == 1 ? $re_activated = '<span style="color: #00FF00;"><strong>' . $_language->module[ 'yes' ] . '</strong></span>' :
            $re_activated = '<span style="color: #FF0000;"><strong>' . $_language->module[ 'no' ] . '</strong></span>';
         $db[ 'activated' ] == 1 ? $activated = '<span style="color: #00FF00;"><strong>' . $_language->module[ 'yes' ] . '</strong></span>' :
            $activated = '<span style="color: #FF0000;"><strong>' . $_language->module[ 'no' ] . '</strong></span>';




        echo '<tr>
      <td>'.getinput($db['module']).'</td>

      <td>'.$le_activated.'</td>
      <td>'.$re_activated.'</td>
      <td>'.$activated.'</td>

      <td><a href="admincenter.php?site=moduls&amp;action=edit&amp;modulID='.$db['modulID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=moduls&amp;delete=true&amp;modulID='.$db['modulID'].'&amp;captcha_hash='.$hash.'\')" value="' . $_language->module['delete'] . '" />

	  <a href="admincenter.php?site=moduls&amp;action=edit&amp;modulID='.$db['modulID'].'"  class="mobile visible-xs visible-sm" type="button"><span class="bi bi-pencil-square"></span></a>
      <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=moduls&amp;delete=true&amp;modulID='.$db['modulID'].'&amp;captcha_hash='.$hash.'\')" /><span class="bi bi-trash-fill"></span></a>


      </td>
      <td>
      <select name="sort[]">';

        for ($j = 1; $j <= $anzmoduls; $j++) {
            if ($db[ 'sort' ] == $j) {
                echo '<option value="' . $db[ 'modulID' ] . '-' . $j . '" selected="selected">' . $j . '</option>';
            } else {
                echo '<option value="' . $db[ 'modulID' ] . '-' . $j . '">' . $j . '</option>';
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