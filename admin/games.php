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

$_language->readModule('games', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ getConstNameRequestUri() ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

$filepath = "../images/games/";

$action = getAction();

if ($action == "add") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
  echo'<div class="card">
  <div class="card-header">
                            <span class="bi bi-controller"></span> ' . $_language->module['games'] . '
                        </div>
      <div class="card-body">
  <a href="admincenter.php?site=games" class="white">' . $_language->module['games'] . '</a> &raquo; ' . $_language->module['add_game'] . '<br><br>';

	echo'<form class="form-horizontal" method="post" action="admincenter.php?site=games" enctype="multipart/form-data">
	<div class="form-group">
    <label class="col-sm-2 control-label">' . $_language->module['game_icon'] . ':</label>
    <div class="col-sm-8">
      <input name="icon" type="file" size="40" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">' . $_language->module['game_name'] . ':</label>
    <div class="col-sm-8">
      <input class="form-control" type="text" name="name" maxlength="255" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">' . $_language->module['game_tag'] . ':</label>
    <div class="col-sm-8">
      <input class="form-control" type="text" name="tag" size="7" maxlength="10" />
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="captcha_hash" value="' . $hash . '" />
		<button class="btn btn-success btn-xs" type="submit" name="save"  />' . $_language->module['add_game'] . '</button>
    </div>
  </div>
  </form>
  </div>
  </div>';

} else if ($action == "edit") {
    $ds = mysqli_fetch_array(safe_query("SELECT * FROM " . PREFIX . "games WHERE gameID='" . $_GET[ "gameID" ] . "'"));
    $pic = '<img src="../images/games/'.is_gamefilexist($filepath, $ds[ 'tag' ]).'" alt="' . $ds[ 'name' ] . '">';

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

  echo'<div class="card">
  <div class="card-header">
                            <span class="bi bi-controller"></span> ' . $_language->module['games'] . '
                        </div>
      <div class="card-body">
  <a href="admincenter.php?site=games" class="white">' . $_language->module['games'] . '</a> &raquo; ' . $_language->module['edit_game'] . '<br><br>';

	echo'<form class="form-horizontal" method="post" action="admincenter.php?site=games" enctype="multipart/form-data">
  <input type="hidden" name="gameID" value="' . $ds['gameID'] . '" />
  <div class="form-group">
    <label class="col-sm-2 control-label">' . $_language->module['present_icon'] . ':</label>
    <div class="col-sm-8">
      <p class="form-control-static">' . $pic . '</p>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">' . $_language->module['game_icon'] . ':</label>
    <div class="col-sm-8">
      <input name="icon" type="file" size="40" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">' . $_language->module['game_name'] . ':</label>
    <div class="col-sm-8">
      <input class="form-control" type="text" name="name" maxlength="255" value="' . getinput($ds['name']) . '" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">' . $_language->module['game_tag'] . ':</label>
    <div class="col-sm-8">
      <input class="form-control" type="text" name="tag" size="7" maxlength="10" value="' . getinput($ds['tag']) . '" />
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="captcha_hash" value="' . $hash . '" />
		<button class="btn btn-success btn-xs" type="submit" name="saveedit"  />' . $_language->module['edit_game'] . '</button>
    </div>
  </div>
  </form>
  </div>
  </div>';

} else if (isset($_POST[ 'save' ])) {
    $icon = $_FILES[ "icon" ];
    $name = $_POST[ "name" ];
    $tag = $_POST[ "tag" ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('name','tag'))) {

          if ($file = uploadFile('icon', $tag, $filepath)) {
            safe_query(
              "INSERT INTO " . PREFIX . "games (
                  name,
                  tag
              ) VALUES (
                  '" . $name . "',
                  '" . $tag ."'
              )"
            );
          }

        } else {
            echo $_language->module[ 'fill_correctly' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
    redirect("admincenter.php?site=games", "", 0);
} else if (isset($_POST[ "saveedit" ])) {
    $icon = $_FILES[ "icon" ];
    $name = $_POST[ "name" ];
    $tag = $_POST[ "tag" ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('name','tag'))) {
            safe_query(
                "UPDATE
                    " . PREFIX . "games
                SET
                    name='" . $name . "',
                    tag='" . $tag ."'
                WHERE gameID='" . $_POST[ "gameID" ] . "'"
            );

            uploadFile('icon', $tag, $filepath, false);

        } else {
            echo $_language->module[ 'fill_correctly' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
    redirect("admincenter.php?site=games", "", 0);
} else if (isset($_GET[ "delete" ])) {
    $CAPCLASS = new \webspell\Captcha();
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        $ds = mysqli_fetch_array(
            safe_query(
                "SELECT tag FROM " . PREFIX . "games WHERE gameID='" . $_GET[ "gameID" ] . "'"
            )
        );
        $extension = explode('.',$ds['tag']);
        safe_query("DELETE FROM " . PREFIX . "games WHERE gameID='" . $_GET[ "gameID" ] . "'");
        if (is_gamefilexist($filepath, $ds[ 'tag' ])) {
            unlink(is_gamefilexist($filepath, $ds[ 'tag' ]));
        }
        redirect("admincenter.php?site=games", "", 0);
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else {

  $page = getPage();

  echo'<div class="card">
   <div class="card-header">
                            <span class="bi bi-controller"></span> ' . $_language->module['games'] . '
                        </div>
  <div class="card-body">';

  echo'<a href="admincenter.php?site=games&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_game' ] . '</a><br /><br />';

  $alle=safe_query("SELECT gameID FROM ".PREFIX."games");
  $gesamt = mysqli_num_rows($alle);

  $max = 15;
  $pages = getCountOfPages($gesamt, $max);

  $page_link = makepagelink("admincenter.php?site=games", $page, $pages);

  if ($page == "1") {
    $ergebnis = safe_query("SELECT * FROM ".PREFIX."games ORDER BY name ASC LIMIT 0,$max");
    $n=1;
  }
  else {
    $start=$page*$max-$max;
    $ergebnis = safe_query("SELECT * FROM ".PREFIX."games ORDER BY name ASC LIMIT $start,$max");
    $n = ($gesamt+1)-$page*$max+$max;
  }


  echo'<table class="table table-striped">
    <thead>
      <th><strong>' . $_language->module['icons'] . '</strong></th>
      <th><strong>' . $_language->module['game_name'] . '</strong></th>
      <th><strong>' . $_language->module['game_tag'] . '</strong></th>
      <th><strong>' . $_language->module['actions'] . '</strong></th>
    </thead>';

	 $n=1;

  $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

  while($ds=mysqli_fetch_array($ergebnis)) {
	  if (is_gamefilexist($filepath,$ds[ 'tag' ])) {
		 $pic = '<img src="../images/games/' . is_gamefilexist($filepath,$ds[ 'tag' ]).'" alt="">';
	  }
	  else {
		  $pic = $ds[ 'tag' ];
	  }

      echo'<tr>
        <td>' . $pic . '</td>
        <td>' . getinput($ds['name']) . '</td>
        <td>' . getinput($ds['tag']) . '</td>
        <td><a href="admincenter.php?site=games&amp;action=edit&amp;gameID=' . $ds['gameID'] . '" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=games&amp;delete=true&amp;gameID=' . $ds['gameID'] . '&amp;captcha_hash=' . $hash . '\')" value="' . $_language->module['delete'] . '" />

    <a href="admincenter.php?site=games&amp;action=edit&amp;gameID=' . $ds['gameID'] . '"  class="mobile visible-xs visible-sm" type="button"><span class="bi bi-pencil-square"></span></a>
        <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=games&amp;delete=true&amp;gameID=' . $ds['gameID'] . '&amp;captcha_hash=' . $hash . '\')" /><span class="bi bi-trash-fill"></span></a></td>
      </tr>';

      $n++;
  }
    echo'</table>';

  if ($pages>1) {
    echo $page_link;
  }
}
echo '</div></div>';
?>