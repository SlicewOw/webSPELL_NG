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

$_language->readModule('countries', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ getConstNameRequestUri() ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

$filepath = "../images/flags/";

$action = getAction();

if ($action == "add") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
  echo'<div class="card">
  <div class="card-header">
                            <span class="bi bi-globe"></span> ' . $_language->module['countries'] . '
                        </div>
    <div class="card-body">
  <a href="admincenter.php?site=countries" class="white">'.$_language->module['countries'].':</a> &raquo; '.$_language->module['add_country'].'<br><br>';

	echo'<form class="form-horizontal" method="post" action="admincenter.php?site=countries" enctype="multipart/form-data">
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['icon_upload'].':</label>
    <div class="col-sm-8">
      <input name="icon" type="file" size="40" /> <small>'.$_language->module['max_18x12'].'</small>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['country'].':</label>
    <div class="col-sm-8">
      <input class="form-control" type="text" name="country" maxlength="255" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['shorthandle'].':</label>
    <div class="col-sm-8">
      <input class="form-control" type="text" name="shorthandle" size="5" maxlength="3" />
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" />
		<button class=" btn btn-success btn-xs" type="submit" name="save"  />'.$_language->module['add_country'].'</button>
    </div>
  </div>
  </form>
  </div>
  </div>';


} else if ($action == "edit") {
    $ds =
        mysqli_fetch_array(safe_query(
            "SELECT * FROM " . PREFIX . "countries WHERE countryID='" . $_GET[ "countryID" ] .
            "'"
        ));
    $pic = '<img src="../images/flags/' . $ds[ 'short' ] . '.gif" alt="' . $ds[ 'country' ] . '" />';
    if ($ds[ 'fav' ] == '1') {
        $fav = '<input type="checkbox" name="fav" value="1" checked="checked" />';
    } else {
        $fav = '<input type="checkbox" name="fav" value="1" />';
    }

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

  echo'<div class="card">
  <div class="card-header">
                            <span class="bi bi-globe"></span> ' . $_language->module['countries'] . '
                        </div>
    <div class="card-body">
  <a href="admincenter.php?site=countries" class="white">'.$_language->module['countries'].'</a> &raquo; '.$_language->module['edit_country'].'<br><br>';

	echo'<form class="form-horizontal" method="post" action="admincenter.php?site=countries" enctype="multipart/form-data">
  <input type="hidden" name="countryID" value="'.$ds['countryID'].'" />
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['present_icon'].':</label>
    <div class="col-sm-8">
      <p class="form-control-static">'.$pic.'</p>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['icon_upload'].':</label>
    <div class="col-sm-8">
      <input name="icon" type="file" size="40" /> <small>'.$_language->module['max_18x12'].'</small>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['country'].':</label>
    <div class="col-sm-8">
      <input class="form-control" type="text" name="country" maxlength="255" value="'.getinput($ds['country']).'" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['shorthandle'].':</label>
    <div class="col-sm-8">
      <input class="form-control" type="text" name="shorthandle" size="5" maxlength="3" value="'.getinput($ds['short']).'" />
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" />
		<button class=" btn btn-success btn-xs" type="submit" name="saveedit"  />'.$_language->module['edit_country'].'</button>
    </div>
  </div>
  </form>
  </div>
  </div>';


} else if (isset($_POST[ 'save' ])) {
    $icon = $_FILES[ "icon" ];
    $country = $_POST[ "country" ];
    $short = $_POST[ "shorthandle" ];
    if (isset($POST[ "fav" ])) {
        $fav = 1;
    } else {
        $fav = 0;
    }
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {

      if (checkforempty(array('shorthandle','country'))) {

        if ($file = uploadFile('icon', $short, $filepath)) {
          safe_query(
              "INSERT INTO
                  `" . PREFIX . "countries` (
                      `country`,
                      `short`,
                      `fav`
                  ) VALUES (
                      '" . $country . "',
                      '" . $short . "',
                      '" . $fav . "'
                  )"
          );
        }

      } else {
        echo $_language->module['information_incomplete'];
      }

    } else {
      echo $_language->module[ 'transaction_invalid' ];
    }

} else if (isset($_POST[ "saveedit" ])) {
    $icon = $_FILES[ "icon" ];
    $country = $_POST[ "country" ];
    $short = $_POST[ "shorthandle" ];
    if (isset($POST[ "fav" ])) {
        $fav = 1;
    } else {
        $fav = 0;
    }
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('shorthandle','country'))) {
            safe_query(
                "UPDATE
                    `" . PREFIX . "countries`
                SET
                    `country` = '" . $country . "',
                    `short` = '" . $short . "',
                    `fav` = '" . $fav . "'
                WHERE `countryID` = '" . $_POST[ "countryID" ] . "'"
            );

            uploadFile('icon', $short, $filepath, false);

        } else {
            echo $_language->module['information_incomplete'];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else if (isset($_GET[ "delete" ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        $countryID = (int) $_GET[ "countryID" ];
        $ds = mysqli_fetch_array(
            safe_query(
                "SELECT short FROM `" . PREFIX . "countries` WHERE `countryID` = '" . $countryID . "'"
            )
        );
        safe_query("DELETE FROM `" . PREFIX . "countries` WHERE `countryID` = '" . $countryID . "'");
        $file = $ds['short'].".gif";
        if (file_exists($filepath.$file)) {
            unlink($filepath.$file);
        }
        redirect("admincenter.php?site=countries", "", 0);
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else {

  $page = getPage();

    echo'<div class="card">
    <div class="card-header">
                            <span class="bi bi-globe"></span> ' . $_language->module['countries'] . '
                        </div>
    <div class="card-body">';

    echo'<a href="admincenter.php?site=countries&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_country' ] . '</a><br><br>';


    $alle=safe_query("SELECT countryID FROM ".PREFIX."countries");
  $gesamt = mysqli_num_rows($alle);

  $max = 15;
  $pages = getCountOfPages($gesamt, $max);

  $page_link = makepagelink("admincenter.php?site=countries", $page, $pages);

  if ($page == "1") {
    $ergebnis = safe_query("SELECT * FROM ".PREFIX."countries ORDER BY country ASC LIMIT 0,$max");
    $n=1;
  } else {
    $start=$page*$max-$max;
    $ergebnis = safe_query("SELECT * FROM ".PREFIX."countries ORDER BY country ASC LIMIT $start,$max");
    $n = ($gesamt+1)-$page*$max+$max;
  }

     echo'   <table class="table table-striped">
    <thead>
      <th><strong>' . $_language->module['icons'] . '</strong></th>
      <th><strong>' . $_language->module['country'] . '</strong></th>
      <th><strong>' . $_language->module['shorthandle'] . '</strong></th>
      <th><strong>' . $_language->module['actions'] . '</strong></th>
    </thead>';

          $ds = safe_query("SELECT * FROM `" . PREFIX . "countries` ORDER BY `country`");

   $n=1;
while($ds=mysqli_fetch_array($ergebnis)) {
  $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();


         while ($flags = mysqli_fetch_array($ergebnis)) {

            $pic = '<img src="../images/flags/' . $flags[ 'short' ] . '.gif" alt="' . $flags[ 'country' ] . '">';
            if ($flags[ 'fav' ] == 1) {
                $fav = ' <small style="color:green"><strong>(' . $_language->module[ 'favorite' ] . ')</strong></small>';
            } else {
                $fav = '';
            }

            echo '<tr>
        <td>'.$pic.'</td>
        <td>'.getinput($flags['country']).'</td>
        <td>'.getinput($flags['short']).'</td>
        <td><a href="admincenter.php?site=countries&amp;action=edit&amp;countryID='.$flags['countryID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'admincenter.php?site=countries&amp;delete=true&amp;countryID='.$flags['countryID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" />

        <a href="admincenter.php?site=countries&amp;action=edit&amp;countryID='.$flags['countryID'].'"  class="mobile visible-xs visible-sm" type="button"><span class="bi bi-pencil-square"></span></a>
        <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'admincenter.php?site=countries&amp;delete=true&amp;countryID='.$flags['countryID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" /><span class="bi bi-trash-fill"></span></a></td>
      </tr>';


      $n++;
		}

    }

  echo '</table>';
  if ($pages>1) {
    echo $page_link;
  }

}
echo '</div></div>';
?>