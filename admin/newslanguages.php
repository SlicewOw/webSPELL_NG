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

$_language->readModule('newslanguages', false, true);

if (!isnewsadmin($userID) || mb_substr(basename($_SERVER[ getConstNameRequestUri() ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

if (isset($_POST[ 'save' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('language', 'lang', 'alt'))) {
            safe_query(
                "INSERT INTO
                    " . PREFIX . "news_languages (
                        language,
                        lang,
                        alt
                    )
                    VALUES (
                        '" . $_POST[ 'language' ] . "',
                        '" . $_POST[ 'lang' ] . "',
                        '" . $_POST[ 'alt' ] . "'
                    )"
            );
        } else {
            echo $_language->module[ 'information_incomplete' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else if (isset($_POST[ 'saveedit' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('language', 'lang', 'alt'))) {
            safe_query(
                "UPDATE
                    " . PREFIX . "news_languages
                SET
                    language='" . $_POST[ 'language' ] . "',
                    lang='" . $_POST[ 'lang' ] . "',
                    alt='" . $_POST[ 'alt' ] . "'
                WHERE
                    langID='" . $_POST[ 'langID' ] . "'"
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
        safe_query("DELETE FROM " . PREFIX . "news_languages WHERE langID='" . $_GET[ 'langID' ] . "'");
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
}

$langs = '';
$getlangs = safe_query("SELECT country, short FROM " . PREFIX . "countries ORDER BY country");
while ($dt = mysqli_fetch_array($getlangs)) {
    $langs .= '<option value="' . $dt[ 'short' ] . '">' . $dt[ 'country' ] . '</option>';
}

$action = getAction();

if ($action == "add") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    $flag = '[flag][/flag]';
    $country = flags($flag, getConstNameAdmin());
    $country = str_replace("<img", "<img id='getcountry'", $country);

  echo'<div class="card"><div class="card-header">
                            <span class="bi bi-file-earmark"></span> '.$_language->module['news_languages'].'
                        </div>
      <div class="card-body">
  <a href="admincenter.php?site=newslanguages" class="white">'.$_language->module['news_languages'].'</a> &raquo; '.$_language->module['add_language'].'<br><br>';

  echo'<form class="form-horizontal" method="post" action="admincenter.php?site=newslanguages">
    <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['language'].'</label>
    <div class="col-sm-8">
      <input type="text" class="form-control" name="language" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['title'].'</label>
    <div class="col-sm-8">
     <input class="form-control" type="text" name="alt" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['flag'].'</label>
    <div class="col-sm-8">
     <select class="form-control" name="lang" onchange="document.getElementById(\'getcountry\').src=\'../images/flags/\'+this.options[this.selectedIndex].value+\'.gif\'">'.$langs.'</select> &nbsp; '.$_language->module['preview'].': '.$country.'
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" /><button class="btn btn-success btn-xs" type="submit" name="save" />'.$_language->module['add_language'].'</button>
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
                            <span class="bi bi-file-earmark"></span> '.$_language->module['news_languages'].'
                        </div>
            <div class="card-body">
  <a href="admincenter.php?site=newslanguages" class="white">'.$_language->module['news_languages'].'</a> &raquo; '.$_language->module['edit_language'].'<br><br>';

	 $ergebnis = safe_query("SELECT * FROM " . PREFIX . "news_languages WHERE langID='" . $_GET[ 'langID' ] . "'");
    $ds = mysqli_fetch_array($ergebnis);
    $flag = '[flag]' . $ds[ 'lang' ] . '[/flag]';
    $country = flags($flag, getConstNameAdmin());
    $country = str_replace("<img", "<img id='getcountry'", $country);
    $langs = str_replace(' selected="selected"', '', $langs);
    $langs = str_replace('value="' . $ds[ 'lang' ] . '"', 'value="' . $ds[ 'lang' ] . '" selected="selected"', $langs);

  echo'<form class="form-horizontal" method="post" action="admincenter.php?site=newslanguages">
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['language'].'</label>
    <div class="col-sm-8">
      <input type="text" class="form-control" name="language" value="'.getinput($ds['language']).'" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['title'].'</label>
    <div class="col-sm-8">
     <input class="form-control" type="text" name="alt" value="'.getinput($ds['alt']).'" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['flag'].'</label>
    <div class="col-sm-8">
     <select class="form-control" name="lang" onchange="document.getElementById(\'getcountry\').src=\'../images/flags/\'+this.options[this.selectedIndex].value+\'.gif\'">'.$langs.'</select> &nbsp; '.$_language->module['preview'].': '.$country.'
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="langID" value="'.$ds['langID'].'" /><button class="btn btn-success btn-xs" type="submit" name="saveedit" />'.$_language->module['edit_language'].'</button>
    </div>
  </div>
  </form></div>
  </div>';
}

else {

  $page = getPage();

  echo'<div class="card">
  <div class="card-header">
                            <span class="bi bi-file-earmark"></span> '.$_language->module['news_languages'].'
                        </div>
            <div class="card-body">';

  echo'<a href="admincenter.php?site=newslanguages&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_language' ] . '</a><br /><br />';


  $alle=safe_query("SELECT langID FROM ".PREFIX."news_languages");
  $gesamt = mysqli_num_rows($alle);

  $max = 15;
  $pages = getCountOfPages($gesamt, $max);

  $page_link = makepagelink("admincenter.php?site=newslanguages", $page, $pages);

  $start = getStartValue($page, $max);
  $ergebnis = safe_query("SELECT * FROM ".PREFIX."news_languages ORDER BY lang ASC LIMIT $start,$max");

   echo'<table class="table table-striped">
    <thead>
      <th><strong>'.$_language->module['flag'].'</strong></th>
      <th><strong>'.$_language->module['language'].'</strong></th>
      <th><strong>'.$_language->module['title'].'</strong></th>
      <th><strong>'.$_language->module['actions'].'</strong></th>
    </thead>';

  $CAPCLASS = new \webspell\Captcha;
  $CAPCLASS->createTransaction();
  $hash = $CAPCLASS->getHash();

  while($ds=mysqli_fetch_array($ergebnis)) {

    $getflag = '<img src="../images/flags/' . $ds[ 'lang' ] . '.gif" alt="' . $ds[ 'alt' ] . '">';

    echo'<tr>
      <td>'.$getflag.'</td>
      <td>'.getinput($ds['language']).'</td>
      <td>'.getinput($ds['alt']).'</td>
      <td><a href="admincenter.php?site=newslanguages&amp;action=edit&amp;langID='.$ds['langID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=newslanguages&amp;delete=true&amp;langID='.$ds['langID'].'&amp;captcha_hash='.$hash.'\')" value="' . $_language->module['delete'] . '" />

      <a href="admincenter.php?site=newslanguages&amp;action=edit&amp;langID='.$ds['langID'].'"  class="mobile visible-xs visible-sm" type="button"><span class="bi bi-pencil-square"></span></a>
      <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=newslanguages&amp;delete=true&amp;langID='.$ds['langID'].'&amp;captcha_hash='.$hash.'\')" /><span class="bi bi-trash-fill"></span></a>

      </td>
    </tr>';

  }
  echo'</table>';

  if ($pages>1) {
    echo $page_link;
  }

  echo ' </div></div>';
}
?>

