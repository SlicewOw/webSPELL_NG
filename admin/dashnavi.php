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

$_language->readModule('dashnavi', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) !== "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

if (isset($_GET[ 'delete' ])) {
    $linkID = $_GET[ 'linkID' ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        safe_query("DELETE FROM " . PREFIX . "dashnavi_links WHERE linkID='$linkID' ");
    } else {
        echo $_language->module[ 'transaction_invalid' ];
        redirect("admincenter.php?site=dashnavi",3);
    return false;
    }
} elseif (isset($_GET[ 'delcat' ])) {
    $catID = $_GET[ 'catID' ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        safe_query("UPDATE " . PREFIX . "dashnavi_links SET catID='0' WHERE catID='$catID' ");
        safe_query("DELETE FROM " . PREFIX . "dashnavi_categories WHERE catID='$catID' ");
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'sortieren' ])) {
    if(isset($_POST[ 'sortcat' ])) { $sortcat = $_POST[ 'sortcat' ]; } else { $sortcat="";}
    $sortlinks = $_POST[ 'sortlinks' ];

    if (is_array($sortcat) AND !empty($sortcat)) {
        foreach ($sortcat as $sortstring) {
            $sorter = explode("-", $sortstring);
            safe_query("UPDATE " . PREFIX . "dashnavi_categories SET sort='$sorter[1]' WHERE catID='$sorter[0]' ");
        }
    }
    if (is_array($sortlinks)) {
        foreach ($sortlinks as $sortstring) {
            $sorter = explode("-", $sortstring);
            safe_query("UPDATE " . PREFIX . "dashnavi_links SET sort='$sorter[1]' WHERE linkID='$sorter[0]' ");
        }
    }
} elseif (isset($_POST[ 'save' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        $anz = mysqli_num_rows(
            safe_query("SELECT linkID FROM " . PREFIX . "dashnavi_links WHERE catID='" . $_POST[ 'catID' ] . "'")
        );
        safe_query(
            "INSERT INTO " . PREFIX . "dashnavi_links ( catID, name, url, accesslevel, sort )
            values (
            '" . $_POST[ 'catID' ] . "',
            '" . $_POST[ 'name' ] . "',
            '" . $_POST[ 'url' ] . "',
            '" . $_POST[ 'accesslevel' ] . "',
            '" . ($anz + 1) . "'
            )"
        );
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'savecat' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])
    ) {
        $anz = mysqli_num_rows(safe_query("SELECT catID FROM " . PREFIX . "dashnavi_categories"));
        safe_query(
            "INSERT INTO " . PREFIX . "dashnavi_categories ( name, sort )
            values( '" . $_POST[ 'name' ] . "', '" . ($anz + 1) . "' )"
        );
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'saveedit' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        safe_query(
            "UPDATE " . PREFIX . "dashnavi_links
            SET catID='" . $_POST[ 'catID' ] . "', name='" . $_POST[ 'name' ] . "', url='" . $_POST[ 'url' ] . "',
                accesslevel='" . $_POST[ 'accesslevel' ] . "'
            WHERE linkID='" . $_POST[ 'linkID' ] . "'"
        );
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'saveeditcat' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        safe_query(
            "UPDATE " . PREFIX . "dashnavi_categories SET name='" . $_POST[ 'name' ] . "'
            WHERE catID='" . $_POST[ 'catID' ] . "' "
        );
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
}

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if ($action == "add") {
    echo '<div class="panel panel-default">
    <div class="panel-heading">
                            <i class="fa fa-bars"></i> ' . $_language->module[ 'dashnavi' ] . '
                        </div>
    <div class="panel-body">
    <a href="admincenter.php?site=dashnavi" class="white">' . $_language->module[ 'dashnavi' ] .
        '</a> &raquo; ' . $_language->module[ 'add_link' ] . '<br><br>';

    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "dashnavi_categories ORDER BY sort");
    $cats = '<select class="form-control" name="catID">';
    while ($ds = mysqli_fetch_array($ergebnis)) {
        if ($ds[ 'default' ] == 1) {
            $name = $_language->module[ 'cat_' . getinput($ds[ 'name' ]) ];
        } else {
            $name = getinput($ds[ 'name' ]);
        }
        $cats .= '<option value="' . $ds[ 'catID' ] . '">' . $name . '</option>';
    }
    $cats .= '</select>';

    $accesslevel = '<option value="any">' . $_language->module[ 'admin_any' ] . '</option>
    <option value="super">' . $_language->module[ 'admin_super' ] . '</option>
    <option value="forum">' . $_language->module[ 'admin_forum' ] . '</option>
    <option value="file">' . $_language->module[ 'admin_file' ] . '</option>
    <option value="page">' . $_language->module[ 'admin_page' ] . '</option>
    <option value="feedback">' . $_language->module[ 'admin_feedback' ] . '</option>
    <option value="news">' . $_language->module[ 'admin_news' ] . '</option>
    <option value="polls">' . $_language->module[ 'admin_polls' ] . '</option>
    <option value="clanwar">' . $_language->module[ 'admin_clanwar' ] . '</option>
    <option value="user">' . $_language->module[ 'admin_user' ] . '</option>
    <option value="cash">' . $_language->module[ 'admin_cash' ] . '</option>
    <option value="gallery">' . $_language->module[ 'admin_gallery' ] . '</option>
    <option value="plugins">' . $_language->module[ 'admin_super' ] . '</option>';

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    echo '<form class="form-horizontal" method="post" action="admincenter.php?site=dashnavi">
    <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['category'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      ' . $cats . '</em></span>
    </div>
    </div>
    <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
        <input class="form-control" type="text" name="name" size="60"></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['url'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
        <input class="form-control" type="text" name="url" size="60"></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['accesslevel'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
        <select class="form-control" name="accesslevel">' . $accesslevel . '</select></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="hidden" name="captcha_hash" value="' . $hash . '"><input class="btn btn-success btn-xs" type="submit" name="save" value="' . $_language->module[ 'add_link' ] . '">
    </div>
  </div>
   
          </form></div></div>';
} elseif ($action == "edit") {
    echo '<div class="panel panel-default">
    <div class="panel-heading">
                            <i class="fa fa-bars"></i> ' . $_language->module[ 'dashnavi' ] . '
                        </div>
                <div class="panel-body">
    <a href="admincenter.php?site=dashnavi" class="white">' . $_language->module[ 'dashnavi' ] .
        '</a> &raquo; ' . $_language->module[ 'edit_link' ] . '<br><br>';

    $linkID = $_GET[ 'linkID' ];
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "dashnavi_links WHERE linkID='$linkID'");
    $ds = mysqli_fetch_array($ergebnis);

    $category = safe_query("SELECT * FROM " . PREFIX . "dashnavi_categories ORDER BY sort");
    $cats = '<select class="form-control" name="catID">';
    while ($dc = mysqli_fetch_array($category)) {
        if ($dc[ 'default' ] == 1) {
            $name = $_language->module[ 'cat_' . getinput($dc[ 'name' ]) ];
        } else {
            $name = getinput($dc[ 'name' ]);
        }
        if ($ds[ 'catID' ] == $dc[ 'catID' ]) {
            $selected = " selected=\"selected\"";
        } else {
            $selected = "";
        }
        $cats .= '<option value="' . $dc[ 'catID' ] . '"' . $selected . '>' . $name . '</option>';
    }
    $cats .= '</select>';

    $accesslevel = '<option value="any">' . $_language->module[ 'admin_any' ] . '</option>
    <option value="super">' . $_language->module[ 'admin_super' ] . '</option>
    <option value="forum">' . $_language->module[ 'admin_forum' ] . '</option>
    <option value="file">' . $_language->module[ 'admin_file' ] . '</option>
    <option value="page">' . $_language->module[ 'admin_page' ] . '</option>
    <option value="feedback">' . $_language->module[ 'admin_feedback' ] . '</option>
    <option value="news">' . $_language->module[ 'admin_news' ] . '</option>
    <option value="polls">' . $_language->module[ 'admin_polls' ] . '</option>
    <option value="clanwar">' . $_language->module[ 'admin_clanwar' ] . '</option>
    <option value="user">' . $_language->module[ 'admin_user' ] . '</option>
    <option value="cash">' . $_language->module[ 'admin_cash' ] . '</option>
    <option value="gallery">' . $_language->module[ 'admin_gallery' ] . '</option>
    <option value="plugins">' . $_language->module[ 'admin_super' ] . '</option>';
    $accesslevel =
        str_replace(
            'value="' . $ds[ 'accesslevel' ] . '"',
            'value="' . $ds[ 'accesslevel' ] . '" selected="selected"',
            $accesslevel
        );

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    echo '<form class="form-horizontal" method="post" action="admincenter.php?site=dashnavi">

    <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['category'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      ' . $cats . '</em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="name" value="' . getinput($ds[ 'name' ]) . '" size="60"></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['url'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="url" value="' . getinput($ds[ 'url' ]) . '" size="60"></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['accesslevel'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <select class="form-control" name="accesslevel">' . $accesslevel . '</select></em></span>
    </div>
  </div>
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="linkID" value="' . $linkID . '">
      <input class="btn btn-success btn-xs" type="submit" name="saveedit" value="' . $_language->module[ 'edit_link' ] . '">
    </div>
  </div>

    </form>
    </div></div>';
} elseif ($action == "addcat") {
    echo '<div class="panel panel-default">
    <div class="panel-heading">
                            <i class="fa fa-bars"></i> ' . $_language->module[ 'dashnavi' ] . '
                        </div>
            <div class="panel-body">
    <a href="admincenter.php?site=dashnavi" class="white">' . $_language->module[ 'dashnavi' ] .
        '</a> &raquo; ' . $_language->module[ 'add_category' ] . '<br><br>';

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    echo '<form class="form-horizontal" method="post" action="admincenter.php?site=dashnavi">

    <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="name" size="60"></em></span>
    </div>
  </div>
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" />
      <input class="btn btn-success btn-xs" type="submit" name="savecat" value="' . $_language->module[ 'add_category' ] . '">
    </div>
  </div>

    </form>
    </div></div>';
} elseif ($action == "editcat") {
    echo '<div class="panel panel-default">
    <div class="panel-heading">
                            <i class="fa fa-bars"></i> ' . $_language->module[ 'dashnavi' ] . '
                        </div>
            <div class="panel-body">
    <a href="admincenter.php?site=dashnavi" class="white">' . $_language->module[ 'dashnavi' ] .
        '</a> &raquo; ' . $_language->module[ 'edit_category' ] . '<br><br>';

    $catID = $_GET[ 'catID' ];
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "dashnavi_categories WHERE catID='$catID'");
    $ds = mysqli_fetch_array($ergebnis);

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    echo '<form method="post" action="admincenter.php?site=dashnavi">

        <div class="form-group">
    <label class="col-sm-2 control-label">' . $_language->module[ 'name' ] . ':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="name" value="' . getinput($ds[ 'name' ]) . '" size="60"></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" /><br>
      <input class="btn btn-success btn-xs" type="submit" name="saveeditcat" value="' . $_language->module[ 'edit_category' ] . '">
    </div>
  </div>
    </form></div></div>';
} else {
    echo '<div class="panel panel-default">
    <div class="panel-heading">
                            <i class="fa fa-bars"></i> ' . $_language->module[ 'dashnavi' ] . '
                        </div>
        <div class="panel-body">';

    echo
        '<a class="btn btn-primary btn-xs" href="admincenter.php?site=dashnavi&amp;action=addcat" class="input">' .
        $_language->module[ 'new_category' ] . '</a>
        <a class="btn btn-primary btn-xs" href="admincenter.php?site=dashnavi&amp;action=add" class="input">' .
        $_language->module[ 'new_link' ] . '</a><br><br>';

    echo '<form method="post" action="admincenter.php?site=dashnavi">
    <table class="table">
<thead>
    <tr>
      <th width="55%" ><b>' . $_language->module[ 'name' ] . '</b></th>
            <th width="17%" align="center"><b>' . $_language->module[ 'accesslevel' ] . '</b></th>
            <th width="20%" ><b>' . $_language->module[ 'actions' ] . '</b></th>
            <th width="8%" ><b>' . $_language->module[ 'sort' ] . '</b></th>
    </tr></thead>';

    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "dashnavi_categories ORDER BY sort");
    $tmp = mysqli_fetch_assoc(safe_query("SELECT count(catID) as cnt FROM " . PREFIX . "dashnavi_categories"));
    $anz = $tmp[ 'cnt' ];

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    while ($ds = mysqli_fetch_array($ergebnis)) {
        $list = '<select name="sortcat[]">';
        for ($n = 1; $n <= $anz; $n++) {
            if ($n <= 8) {
                $list .= '';
            } else {
                $list .= '<option value="' . $ds[ 'catID' ] . '-' . $n . '">' . $n . '</option>';
            }
        }
        $list .= '</select>';
        $list = str_replace(
            'value="' . $ds[ 'catID' ] . '-' . $ds[ 'sort' ] . '"',
            'value="' . $ds[ 'catID' ] . '-' . $ds[ 'sort' ] . '" selected="selected"',
            $list
        );
        if ($ds[ 'default' ] == 1) {
            $sort = '<b>' . $ds[ 'sort' ] . '</b>';
            $catactions = '';
            $name = $_language->module[ 'cat_' . getinput($ds[ 'name' ]) ];
        } else {
            $sort = $list;
            $catactions =
                '<a class="btn btn-warning btn-xs" href="admincenter.php?site=dashnavi&amp;action=editcat&amp;catID=' . $ds[ 'catID' ] .
                '" class="input">' . $_language->module[ 'edit' ] . '</a>
<input class="btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete_category'] . '\', \'admincenter.php?site=dashnavi&amp;delcat=true&amp;catID=' . $ds[ 'catID' ] .
                '&amp;captcha_hash=' . $hash . '\')" value="' . $_language->module['delete'] . '" />';

            $name = getinput($ds[ 'name' ]);
        }

        echo '<tr bgcolor="#CCCCCC">
            <td class="td_head" colspan="2"><b>' . $name . '</b></td>
            <td class="td_head" align="center">' . $catactions . '</td>
            <td class="td_head" align="center">' . $sort . '</td>
        </tr>';

        $links =
            safe_query("SELECT * FROM " . PREFIX . "dashnavi_links WHERE catID='" . $ds[ 'catID' ] . "' ORDER BY sort");
        $tmp = mysqli_fetch_assoc(
            safe_query(
                "SELECT count(linkID) as cnt
                  FROM " . PREFIX . "dashnavi_links WHERE catID='" . $ds[ 'catID' ] . "'"
            )
        );
        $anzlinks = $tmp[ 'cnt' ];

        $i = 1;
        $CAPCLASS = new \webspell\Captcha;
        $CAPCLASS->createTransaction();
        $hash = $CAPCLASS->getHash();
        if (mysqli_num_rows($links)) {
            while ($db = mysqli_fetch_array($links)) {
                if ($i % 2) {
                    $td = 'td1';
                } else {
                    $td = 'td2';
                }

                $linklist = '<select name="sortlinks[]">';
                for ($n = 1; $n <= $anzlinks; $n++) {
                    $linklist .= '<option value="' . $db[ 'linkID' ] . '-' . $n . '">' . $n . '</option>';
                }
                $linklist .= '</select>';
                $linklist = str_replace(
                    'value="' . $db[ 'linkID' ] . '-' . $db[ 'sort' ] . '"',
                    'value="' . $db[ 'linkID' ] . '-' . $db[ 'sort' ] . '" selected="selected"',
                    $linklist
                );

                echo '<tr>
                    <td class="' . $td . '"><b>' . $db[ 'name' ] . '</b><br><small>' . $db[ 'url' ] . '</small></td>
                    <td class="' . $td . '" align="center"><small><b>' .
                    $_language->module[ 'admin_' . getinput($db[ 'accesslevel' ]) ] . '</b></small></td>
                    <td class="' . $td . '" align="center">
<a href="admincenter.php?site=dashnavi&amp;action=edit&amp;linkID=' . $db[ 'linkID' ] .'" class="hidden-xs hidden-sm btn btn-warning btn-xs">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete_link'] . '\', \'admincenter.php?site=dashnavi&amp;delete=true&amp;linkID=' . $db[ 'linkID' ] . '&amp;captcha_hash=' . $hash . '\')" value="' . $_language->module['delete'] . '" />

      <a href="admincenter.php?site=dashnavi&amp;action=edit&amp;linkID=' . $db[ 'linkID' ] .'"  class="mobile visible-xs visible-sm"><i class="fa fa-pencil"></i></a>
      <a class="mobile visible-xs visible-sm" onclick="MM_confirm(\'' . $_language->module['really_delete_link'] . '\', \'admincenter.php?site=dashnavi&amp;delete=true&amp;linkID=' . $db[ 'linkID' ] . '&amp;captcha_hash=' . $hash . '\')" /><i class="fa fa-times"></i></a>
                    </td>
                    <td class="' . $td . '" align="center">' . $linklist . '</td>
                </tr>';
                $i++;
            }
        } else {
            echo '<tr>'.
                    '<td class="td1" colspan="4">' . $_language->module[ 'no_additional_links_available' ] . '</td>'.
                 '</tr>';
        }
    }

    $links = safe_query("SELECT * FROM " . PREFIX . "dashnavi_links WHERE catID='0' ORDER BY sort");
    $tmp = mysqli_fetch_assoc(safe_query("SELECT count(linkID) as cnt FROM " . PREFIX . "dashnavi_links WHERE catID='0'"));
    $anzlinks = $tmp[ 'cnt' ];

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    while ($db = mysqli_fetch_array($links)) {
        $noncatlist = '<select name="sortlinks[]">';
        for ($n = 1; $n <= $anz; $n++) {
            $noncatlist .= '<option value="' . $db[ 'linkID' ] . '-' . $n . '">' . $n . '</option>';
        }
        $noncatlist .= '</select>';
        $noncatlist = str_replace(
            'value="' . $db[ 'linkID' ] . '-' . $db[ 'sort' ] . '"',
            'value="' . $db[ 'linkID' ] . '-' . $db[ 'sort' ] . '" selected="selected"',
            $noncatlist
        );
        echo '<tr bgcolor="#dcdcdc">
            <td bgcolor="#FFFFFF"><b>' . getinput($db[ 'name' ]) . '</b><br><small>' . $db[ 'url' ] . '</small></td>
            <td bgcolor="#FFFFFF">
                <small><b>' . $_language->module[ 'admin_' . getinput($db[ 'accesslevel' ]) ] . '</b></small>
            </td>
            <td bgcolor="#FFFFFF">
<a href="admincenter.php?site=dashnavi&amp;action=edit&amp;linkID=' . $db[ 'linkID' ] . '" class="hidden-xs hidden-sm btn btn-warning btn-xs">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=dashnavi&amp;delete=true&amp;linkID=' . $db[ 'linkID' ] . '&amp;captcha_hash=' . $hash . '\')" value="' . $_language->module['delete'] . '" />

      <a href="admincenter.php?site=dashnavi&amp;action=edit&amp;linkID=' . $db[ 'linkID' ] . '"  class="mobile visible-xs visible-sm"><i class="fa fa-pencil"></i></a>
      <a class="mobile visible-xs visible-sm" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=dashnavi&amp;delete=true&amp;linkID=' . $db[ 'linkID' ] . '&amp;captcha_hash=' . $hash . '\')" /><i class="fa fa-times"></i></a>

                     </td>
            <td bgcolor="#FFFFFF">' . $noncatlist . '</td>
        </tr>';
    }
    echo '	<tr>
                <td class="td_head" colspan="5" align="right"><input class="btn btn-primary btn-xs" type="submit" name="sortieren" value="' .
        $_language->module[ 'to_sort' ] . '"></td>
            </tr>
        </table>
    </form></div></div>';
}
