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

$_language->readModule('sponsors', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ getConstNameRequestUri() ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

$filepath = "../images/sponsors/";

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if ($action == "add") {

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    $_language->readModule('bbcode', true, true);

    $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
    $addflags = $GLOBALS["_template"]->replaceTemplate("flags_admin", array());

    echo '<script>
    <!--
    function chkFormular() {
        if (!validbbcode(document.getElementById(\'message\').value, \'admin\')) {
           return false;
       }
   }
-->
</script>';

echo'<div class="panel panel-default">
<div class="panel-heading">
                            <span class="fa fa-credit-card"></span> '.$_language->module['sponsors'].'
                        </div>
                        <div class="panel-body">
  <a href="admincenter.php?site=sponsors" class="white">' . $_language->module['sponsors'] . '</a> &raquo; ' . $_language->module['add_sponsor'] . '<br><br>';

  echo'<form class="form-horizontal" method="post" id="post" name="post" action="admincenter.php?site=sponsors" enctype="multipart/form-data" onsubmit="return chkFormular();">
   <div class="row">

<div class="col-md-6">

  <div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['sponsor_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
    <input class="form-control" type="text" name="name" maxlength="255" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['sponsor_url'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
    <input class="form-control" type="text" name="url" maxlength="255" value="http://" /></em></span>
    </div>
  </div>


  </div>

<div class="col-md-6">

 <div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['banner_upload'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input name="banner" type="file" size="40" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['banner_upload_small'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
    <input name="banner_small" type="file" size="40" /> <small>('.$_language->module['banner_upload_info'].')</small></em></span>
    </div>
  </div>


</div>

<div class="col-md-12">


  <div class="col-md-12">
  '.$addflags.'<br>'.$addbbcode.'<br>
  </div>
  <div class="form-group">

    <div class="col-md-12"><span class="text-muted small"><em>
      <textarea class="form-control" id="message" name="message" rows="10" cols="" ></textarea></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['is_displayed'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
		<input type="checkbox" name="displayed" value="1" checked="checked" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['mainsponsor'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
		<input type="checkbox" name="mainsponsor" value="1" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" />
		<button class="btn btn-success btn-xs" type="submit" name="save"  />'.$_language->module['add_sponsor'].'</button>
    </div>
  </div>

  </div>
  </form></div>
  </div>';
} else if ($action == "edit") {

    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT * FROM " . PREFIX . "sponsors WHERE sponsorID='" . $_GET[ "sponsorID" ] ."'"
        )
    );
    if (!empty($ds[ 'banner' ])) {
        $pic = '<img src="' . $filepath . $ds[ 'banner' ] . '" alt="">';
    } else {
        $pic = $_language->module[ 'no_upload' ];
    }
    if (!empty($ds[ 'banner_small' ])) {
        $pic_small = '<img src="' . $filepath . $ds[ 'banner_small' ] . '" alt="">';
    } else {
        $pic_small = $_language->module[ 'no_upload' ];
    }

    if ($ds[ 'displayed' ] == 1) {
        $displayed = '<input type="checkbox" name="displayed" value="1" checked="checked" />';
    } else {
        $displayed = '<input type="checkbox" name="displayed" value="1" />';
    }

    if ($ds[ 'mainsponsor' ] == 1) {
        $mainsponsor = '<input type="checkbox" name="mainsponsor" value="1" checked="checked" />';
    } else {
        $mainsponsor = '<input type="checkbox" name="mainsponsor" value="1" />';
    }

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    $_language->readModule('bbcode', true, true);

    $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
    $addflags = $GLOBALS["_template"]->replaceTemplate("flags_admin", array());

    echo '<script>
    <!--
    function chkFormular() {
        if (!validbbcode(document.getElementById(\'message\').value, \'admin\')) {
           return false;
       }
   }
-->
</script>';

  echo'<div class="panel panel-default">
  <div class="panel-heading">
                            <span class="fa fa-credit-card"></span> '.$_language->module['sponsors'].'
                        </div>
                        <div class="panel-body">
  <a href="admincenter.php?site=sponsors" class="white">' . $_language->module['sponsors'] . '</a> &raquo; ' . $_language->module['edit_sponsor'] . '<br><br>';

  echo'<form class="form-horizontal" method="post" id="post" name="post" action="admincenter.php?site=sponsors" enctype="multipart/form-data" onsubmit="return chkFormular();">
  <input type="hidden" name="sponsorID" value="'.$ds['sponsorID'].'" />

      <div class="row">

<div class="col-md-6">

  <div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['sponsor_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
    <input class="form-control" type="text" name="name" maxlength="255" value="'.getinput($ds['name']).'" /></em></span>
    </div>
  </div>

  </div>

<div class="col-md-6">

  <div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['sponsor_url'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
    <input class="form-control" type="text" name="url" maxlength="255" value="'.getinput($ds['url']).'" /></em></span>
    </div>
  </div>

  </div>

  <div class="row">

<div class="col-md-6">

<div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['current_banner'].':</label>
    <div class="col-sm-8">
      '.$pic.'
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['current_banner_small'].':</label>
    <div class="col-sm-8">
		'.$pic_small.'
    </div>
  </div>

   </div>

<div class="col-md-6">

  <div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['banner_upload'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input name="banner" type="file" size="40" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['banner_upload_small'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
		<input name="banner_small" type="file" size="40" /> <small>('.$_language->module['banner_upload_info'].')</small></em></span>
    </div>
  </div>

 </div>





  <div class="col-md-12">
  '.$addflags.'<br>'.$addbbcode.'<br>
  </div>


    <div class="col-md-12"><span class="text-muted small"><em>
      <textarea class="form-control" id="message" name="message" rows="10" cols="" >'.getinput($ds['info']).'</textarea></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['is_displayed'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
		'.$displayed.'</em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['mainsponsor'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
		'.$mainsponsor.'</em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" />
		<button class="btn btn-success btn-xs" type="submit" name="saveedit"  />'.$_language->module['edit_sponsor'].'</button>
    </div>
  </div>
  <div>
  </form></div>
  </div>';
} else if (isset($_POST[ 'sortieren' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        $sort = $_POST[ 'sort' ];
        if (is_array($sort)) {
            foreach ($sort as $sortstring) {
                $sorter = explode("-", $sortstring);
                safe_query("UPDATE " . PREFIX . "sponsors SET sort='$sorter[1]' WHERE sponsorID='$sorter[0]' ");
                redirect("admincenter.php?site=sponsors", "", 0);
            }
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else if (isset($_POST[ "save" ])) {
    $name = $_POST[ "name" ];
    $url = $_POST[ "url" ];
    $info = $_POST[ "message" ];
    if (isset($_POST[ "displayed" ])) {
        $displayed = 1;
    } else {
        $displayed = 0;
    }
    if (!$displayed) {
        $displayed = 0;
    }
    if (isset($_POST[ "mainsponsor" ])) {
        $mainsponsor = 1;
    } else {
        $mainsponsor = 0;
    }

    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        safe_query(
            "INSERT INTO " . PREFIX .
            "sponsors (sponsorID, name, url, info, displayed, mainsponsor, date, sort) values('', '" . $name . "', '" .
            $url . "', '" . $info . "', '" . $displayed . "', '" . $mainsponsor . "', '" . time() . "', '1')"
        );

        $id = mysqli_insert_id($_database);

        $errors = array();

        //TODO: should be loaded from root language folder
        $_language->readModule('formvalidation', true);

        $upload = new \webspell\HttpUpload('banner');
        if ($upload->hasFile()) {
            if ($upload->hasError() === false) {
                $mime_types = array('image/jpeg','image/png','image/gif');

                if ($upload->supportedMimeType($mime_types)) {
                    $imageInformation =  getimagesize($upload->getTempFile());

                    if (is_array($imageInformation)) {
                        switch ($imageInformation[ 2 ]) {
                            case 1:
                                $endung = '.gif';
                                break;
                            case 3:
                                $endung = '.png';
                                break;
                            default:
                                $endung = '.jpg';
                                break;
                        }
                        $file = $id.$endung;

                        if ($upload->saveAs($filepath.$file, true)) {
                            @chmod($file, $new_chmod);
                            safe_query(
                                "UPDATE " . PREFIX . "sponsors SET banner='" . $file . "' WHERE sponsorID='" . $id . "'"
                            );
                        }
                    } else {
                        $errors[] = $_language->module[ 'broken_image' ];
                    }
                } else {
                    $errors[] = $_language->module[ 'unsupported_image_type' ];
                }
            } else {
                $errors[] = $upload->translateError();
            }
        }

        $upload = new \webspell\HttpUpload('banner_small');
        if ($upload->hasFile()) {
            if ($upload->hasError() === false) {
                $mime_types = array('image/jpeg','image/png','image/gif');

                if ($upload->supportedMimeType($mime_types)) {
                    $imageInformation =  getimagesize($upload->getTempFile());

                    if (is_array($imageInformation)) {
                        switch ($imageInformation[ 2 ]) {
                            case 1:
                                $endung = '.gif';
                                break;
                            case 3:
                                $endung = '.png';
                                break;
                            default:
                                $endung = '.jpg';
                                break;
                        }
                        $file = $id.'_small'.$endung;

                        if ($upload->saveAs($filepath.$file, true)) {
                            @chmod($file, $new_chmod);
                            safe_query(
                                "UPDATE " . PREFIX . "sponsors SET banner_small='" . $file . "'
                                WHERE sponsorID='" . $id . "'"
                            );
                        }
                    } else {
                        $errors[] = $_language->module[ 'broken_image' ];
                    }
                } else {
                    $errors[] = $_language->module[ 'unsupported_image_type' ];
                }
            } else {
                $errors[] = $upload->translateError();
            }
        }

        if (count($errors)) {
            $errors = array_unique($errors);
            echo generateErrorBoxFromArray($_language->module['errors_there'], $errors);
        } else {
            redirect("admincenter.php?site=sponsors", "", 0);
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else if (isset($_POST[ "saveedit" ])) {
    $name = $_POST[ "name" ];
    $url = $_POST[ "url" ];
    $info = $_POST[ "message" ];
    if (isset($_POST[ "displayed" ])) {
        $displayed = 1;
    } else {
        $displayed = 0;
    }
    if (isset($_POST[ "mainsponsor" ])) {
        $mainsponsor = 1;
    } else {
        $mainsponsor = 0;
    }
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {

        $url = getDefaultUrlStr($url);

        safe_query(
            "UPDATE " . PREFIX . "sponsors SET name='" . $name . "', url='" . $url . "', info='" . $info .
            "', displayed='" . $displayed . "', mainsponsor='" . $mainsponsor . "' WHERE sponsorID='" .
            $_POST[ "sponsorID" ] . "'"
        );

        $id = $_POST[ 'sponsorID' ];

        $errors = array();

        //TODO: should be loaded from root language folder
        $_language->readModule('formvalidation', true);

        $upload = new \webspell\HttpUpload('banner');
        if ($upload->hasFile()) {
            if ($upload->hasError() === false) {
                $mime_types = array('image/jpeg','image/png','image/gif');

                if ($upload->supportedMimeType($mime_types)) {
                    $imageInformation =  getimagesize($upload->getTempFile());

                    if (is_array($imageInformation)) {
                        switch ($imageInformation[ 2 ]) {
                            case 1:
                                $endung = '.gif';
                                break;
                            case 3:
                                $endung = '.png';
                                break;
                            default:
                                $endung = '.jpg';
                                break;
                        }
                        $file = $id.$endung;

                        if ($upload->saveAs($filepath.$file, true)) {
                            @chmod($file, $new_chmod);
                            safe_query(
                                "UPDATE " . PREFIX . "sponsors SET banner='" . $file . "' WHERE sponsorID='" . $id . "'"
                            );
                        }
                    } else {
                        $errors[] = $_language->module[ 'broken_image' ];
                    }
                } else {
                    $errors[] = $_language->module[ 'unsupported_image_type' ];
                }
            } else {
                $errors[] = $upload->translateError();
            }
        }

        $upload = new \webspell\HttpUpload('banner_small');
        if ($upload->hasFile()) {
            if ($upload->hasError() === false) {
                $mime_types = array('image/jpeg','image/png','image/gif');

                if ($upload->supportedMimeType($mime_types)) {
                    $imageInformation =  getimagesize($upload->getTempFile());

                    if (is_array($imageInformation)) {
                        switch ($imageInformation[ 2 ]) {
                            case 1:
                                $endung = '.gif';
                                break;
                            case 3:
                                $endung = '.png';
                                break;
                            default:
                                $endung = '.jpg';
                                break;
                        }
                        $file = $id.'_small'.$endung;

                        if ($upload->saveAs($filepath.$file, true)) {
                            @chmod($file, $new_chmod);
                            safe_query(
                                "UPDATE " . PREFIX . "sponsors SET banner_small='" . $file . "' ".
                                "WHERE sponsorID='" . $id . "'"
                            );
                        }
                    } else {
                        $errors[] = $_language->module[ 'broken_image' ];
                    }
                } else {
                    $errors[] = $_language->module[ 'unsupported_image_type' ];
                }
            } else {
                $errors[] = $upload->translateError();
            }
        }

        if (count($errors)) {
            $errors = array_unique($errors);
            echo generateErrorBoxFromArray($_language->module['errors_there'], $errors);
        } else {
            redirect("admincenter.php?site=sponsors", "", 0);
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else if (isset($_GET[ "delete" ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        $get = safe_query("SELECT * FROM " . PREFIX . "sponsors WHERE sponsorID='" . $_GET[ "sponsorID" ] . "'");
        $data = mysqli_fetch_assoc($get);

        if (safe_query("DELETE FROM " . PREFIX . "sponsors WHERE sponsorID='" . $_GET[ "sponsorID" ] . "'")) {
            @unlink($filepath.$data['banner']);
            @unlink($filepath.$data['banner_small']);
            redirect("admincenter.php?site=sponsors", "", 0);
        } else {
            redirect("admincenter.php?site=sponsors", "", 0);
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else {

  echo'<div class="panel panel-default">
  <div class="panel-heading">
                            <span class="fa fa-credit-card"></span> '.$_language->module['sponsors'].'
                        </div>
                        <div class="panel-body">';

  echo'<a href="admincenter.php?site=sponsors&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_sponsor' ] . '</a><br /><br />';

  echo'<form method="post" action="admincenter.php?site=sponsors">
  <table class="table table-striped">
    <thead>
      <th><b>'.$_language->module['sponsor'].'</b></th>
      <th><b>'.$_language->module['clicks'].'</b></th>
      <th class="hidden-xs hidden-sm"><b>'.$_language->module['is_displayed'].'</b></th>
		<th class="hidden-xs hidden-sm"><b>'.$_language->module['mainsponsor'].'</b></th>
      <th><b>'.$_language->module['actions'].'</b></th>
      <th><b>'.$_language->module['sort'].'</b></th>
    </thead>';

	 $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    $qry = safe_query("SELECT * FROM " . PREFIX . "sponsors ORDER BY sort");
    $anz = mysqli_num_rows($qry);
    if ($anz) {
        $i = 1;
        while ($ds = mysqli_fetch_array($qry)) {
            if ($i % 2) {
                $td = 'td1';
            } else {
                $td = 'td2';
            }

            $ds[ 'displayed' ] == 1 ?
            $displayed = '<span style="color: #00FF00;"><b>' . $_language->module[ 'yes' ] . '</b></span>' :
            $displayed = '<span style="color: #FF0000;"><b>' . $_language->module[ 'no' ] . '</b></span>';
            $ds[ 'mainsponsor' ] == 1 ?
            $mainsponsor = '<span style="color: #00FF00;"><b>' . $_language->module[ 'yes' ] . '</b></span>' :
            $mainsponsor = '<span style="color: #FF0000;"><b>' . $_language->module[ 'no' ] . '</b></span>';

            $url = getDefaultUrlStr($ds[ 'url' ]);
            $name = '<a href="' . getinput($url) . '" target="_blank">' . getinput($ds[ 'name' ]) . '</a>';

            $days = round((time() - $ds[ 'date' ]) / (60 * 60 * 24));
            if ($days) {
                $perday = round($ds[ 'hits' ] / $days, 2);
            } else {
                $perday = $ds[ 'hits' ];
            }

			echo'<tr>
        <td>'.$name.'</td>
        <td>'.$ds['hits'].' ('.$perday.')</td>
        <td class="hidden-xs hidden-sm">'.$displayed.'</td>
		  <td class="hidden-xs hidden-sm">'.$mainsponsor.'</td>
        <td><a href="admincenter.php?site=sponsors&amp;action=edit&amp;sponsorID='.$ds['sponsorID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=sponsors&amp;delete=true&amp;sponsorID='.$ds['sponsorID'].'&amp;captcha_hash='.$hash.'\')" value="' . $_language->module['delete'] . '" />

	  <a href="admincenter.php?site=sponsors&amp;action=edit&amp;sponsorID='.$ds['sponsorID'].'"  class="mobile visible-xs visible-sm" type="button"><span class="fa fa-pencil"></span></a>
      <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=sponsors&amp;delete=true&amp;sponsorID='.$ds['sponsorID'].'&amp;captcha_hash='.$hash.'\')" /><span class="fa fa-times"></span></a></td>
        <td><select name="sort[]">';
            for ($j = 1; $j <= $anz; $j++) {
                if ($ds[ 'sort' ] == $j) {
                    echo '<option value="' . $ds[ 'sponsorID' ] . '-' . $j . '" selected="selected">' . $j .
                        '</option>';
                } else {
                    echo '<option value="' . $ds[ 'sponsorID' ] . '-' . $j . '">' . $j . '</option>';
                }
            }
            echo '</select>
        </td>
      </tr>';

      $i++;
		}
	}
  else echo'<tr><td class="td1" colspan="6">'.$_language->module['no_entries'].'</td></tr>';

  echo'<tr>
      <td class="td_head" colspan="6" align="right"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input class="btn btn-primary btn-xs" type="submit" name="sortieren" value="'.$_language->module['to_sort'].'" /></td>
    </tr>
  </table>
  </form>';
}
echo '</div></div>';
?>