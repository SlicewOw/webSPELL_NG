<?php
/*
##########################################################################
# #
# Version 4 / / / #
# -----------__---/__---__------__----__---/---/- #
# | /| / /___) / ) (_ ` / ) /___) / / #
# _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___ #
# Free Content / Management System #
# / #
# #
# #
# Copyright 2005-2015 by webspell.org #
# #
# visit webSPELL.org, webspell.info to get webSPELL for free #
# - Script runs under the GNU GENERAL PUBLIC LICENSE #
# - It's NOT allowed to remove this copyright-tag #
# -- http://www.fsf.org/licensing/licenses/gpl.html #
# #
# Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at), #
# Far Development by Development Team - webspell.org #
# #
# visit webspell.org #
# #
##########################################################################
*/

$_language->readModule('modrewrite', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[getConstNameRequestUri()]), 0, 15) != "admincenter.php") {
    die($_language->module['access_denied']);
}

$action = getAction();

$types = '';
foreach ($GLOBALS['_modRewrite']->getTypes() as $typ) {
    $types .= '<option value="' . $typ . '">' . $typ . '</option>';
}

if ($action == "add") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    echo '<div class="panel panel-default">
    <div class="panel-heading">
                            <span class="bi bi-geo-alt-fill"></span> ' . $_language->module['modrewrite_settings'] . '
                        </div>
        <div class="panel-body">
    <a href="admincenter.php?site=modrewrite" class="white">' . $_language->module['modrewrite'] .
        '</a> &raquo; ' . $_language->module['add_rule'] . '<br><br>';
    echo '<script type="text/javascript">
    function addRow(){
        table = document.getElementById("fields");
        rows = table.rows;
        text = table.rows[1].innerHTML;
        new_row = table.insertRow(rows.length-1);
        new_row.innerHTML = text;
    }
    </script>';
    echo '<form method="post" action="admincenter.php?site=modrewrite" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
    <td><strong>' . $_language->module['variables'] . ':</strong></td>
    <td><table id="fields" width="100%">
    <tr>
    <td>' . $_language->module['variable'] . ':</td>
    <td>' . $_language->module['type'] . ':</td>
    </tr>
    <tr>
    <td><input type="text" name="keys[]"></td>
    <td><select name="values[]">' . $types . '</select></td>
    </tr>
    <tr>
    <td></td>
    <td><a onclick="javascript:addRow();">' . $_language->module['more'] . '</a></td>
    </tr>
    </table></td>
    </tr>
    <tr>
    <td><strong>' . $_language->module['url'] . ':</strong></td>
    <td><input type="text" name="url" style="width:100%;"></td>
    </tr>
    <tr>
    <td><strong>' . $_language->module['replace'] . ':</strong></td>
    <td><input type="text" name="regex" style="width:100%;"></td>
    </tr>
    <tr>
    <td><input type="hidden" name="captcha_hash" value="' . $hash . '"></td>
    <td><input type="submit" name="save" value="' . $_language->module['save_rule'] . '"></td>
    </tr>
    </table>
    </form></div></div>';
} else if ($action == "edit") {
    $ds = mysqli_fetch_assoc(safe_query(
        "SELECT * FROM " . PREFIX . "modrewrite WHERE ruleID='" . $_GET["ruleID"] .
        "'"
    ));

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    echo '<div class="panel panel-default">
    <div class="panel-heading">
                            <span class="bi bi-geo-alt-fill"></span> ' . $_language->module['modrewrite_settings'] . '
                        </div>
            <div class="panel-body">
    <a href="admincenter.php?site=modrewrite" class="white">' . $_language->module['modrewrite'] .
        '</a> &raquo; ' . $_language->module['edit_rule'] . '<br><br>';

    $rules = '';
    $data = unserialize($ds['fields']);
    if (count($data)) {
        foreach ($data as $key => $field) {
            $rules .= '<tr>
            <td><input type="text" value="' . $key . '" name="keys[]"></td>
            <td><select name="values[]">' .
                str_replace('value="' . $field . '"', 'value="' . $field . '" selected="selected"', $types) .
                '</select></td>
            </tr>';
        }
    } else {
        $rules .= '<tr>
        <td><input type="text" value="" name="keys[]"></td>
        <td><select name="values[]">' . $types . '</select></td>
        </tr>';
    }
    echo '<script type="text/javascript">
    function addRow(){
        table = document.getElementById("fields");
        rows = table.rows;
        text = table.rows[1].innerHTML;
        new_row = table.insertRow(rows.length-1);
        new_row.innerHTML = text;
    }
    </script>';
    echo '<form class="form-horizontal" method="post" action="admincenter.php?site=modrewrite" enctype="multipart/form-data">
    	<table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
    <td><strong>' . $_language->module['variables'] . ':</strong></td>
    <td><table id="fields" width="100%">
    <tr>
    <td>' . $_language->module['variable'] . ':</td>
    <td>' . $_language->module['type'] . ':</td>
    </tr>
    ' . $rules . '
    <tr>
    <td></td>
    <td><a onclick="javascript:addRow();">' . $_language->module['more'] . '</a></td>
    </tr>
    </table></td>
    </tr>
    <tr>
    <td><strong>' . $_language->module['url'] . ':</strong></td>
    <td><input type="text" name="url" value="' . $ds['link'] . '" style="width:100%;"></td>
    </tr>
    <tr>
    <td><strong>' . $_language->module['replace'] . ':</strong></td>
    <td><input type="text" name="regex" value="' . $ds['regex'] . '" style="width:100%;"></td>
    </tr>
    <tr>
    <td><input type="hidden" name="ruleID" value="' . $ds['ruleID'] .
        '"><input type="hidden" name="captcha_hash" value="' . $hash . '"></td>
    <td><input class="btn btn-primary btn-xs" type="submit" name="saveedit" value="' . $_language->module['save_rule'] . '"></td>
    </tr>
    </table>
    </form></div></div>';
} else if ($action == 'rebuild') {
    $ds = safe_query("SELECT * FROM " . PREFIX . "modrewrite");
    $anz = mysqli_num_rows($ds);
    while ($flags = mysqli_fetch_array($ds)) {
        $data = unserialize($flags['fields']);
        $replace = $GLOBALS['_modRewrite']->buildReplace($flags['link'], $flags['regex'], $data);
        security_slashes($replace);
        $rebuild = $GLOBALS['_modRewrite']->buildRebuild($flags['regex'], $flags['link'], $data);
        security_slashes($rebuild);

        safe_query(
            "UPDATE " . PREFIX . "modrewrite SET
            replace_regex ='" . $replace[0] . "',
            replace_result ='" . $replace[1] . "',
            rebuild_regex ='" . $rebuild[0] . "',
            rebuild_result ='" . $rebuild[1] . "'
            WHERE ruleID='" . $flags["ruleID"] . "'"
        );
    }
    echo "Done";
} else if (isset($_POST['save'])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST['captcha_hash'])) {
        $data = array();
        foreach ($_POST['keys'] as $key => $val) {
            if (!empty($val)) {
                $data[$val] = $_POST['values'][$key];
            }
        }

        $replace = $GLOBALS['_modRewrite']->buildReplace(
            stripslashes($_POST['url']),
            stripslashes($_POST['regex']),
            $data
        );
        security_slashes($replace);
        $rebuild = $GLOBALS['_modRewrite']->buildRebuild(
            stripslashes($_POST['regex']),
            stripslashes($_POST['url']),
            $data
        );
        security_slashes($rebuild);

        $data = serialize($data);
        safe_query(
            "INSERT INTO " . PREFIX .
            "modrewrite (link,regex, fields, replace_regex, replace_result, rebuild_regex, rebuild_result) values('" .
            $_POST['url'] . "', '" . $_POST['regex'] . "','" . $data . "','" . $replace[0] . "','" .
            $replace[1] . "','" . $rebuild[0] . "','" . $rebuild[1] . "')"
        );
        redirect("admincenter.php?site=modrewrite", "", 0);
    } else {
        echo $_language->module['transaction_invalid'];
    }
} else if (isset($_POST["saveedit"])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST['captcha_hash'])) {
        $data = array();
        foreach ($_POST['keys'] as $key => $val) {
            if (!empty($val)) {
                $data[$val] = $_POST['values'][$key];
            }
        }

        $replace = $GLOBALS['_modRewrite']->buildReplace(
            stripslashes($_POST['url']),
            stripslashes($_POST['regex']),
            $data
        );
        security_slashes($replace);
        $rebuild = $GLOBALS['_modRewrite']->buildRebuild(
            stripslashes($_POST['regex']),
            stripslashes($_POST['url']),
            $data
        );
        security_slashes($rebuild);

        $data = serialize($data);
        safe_query(
            "UPDATE " . PREFIX . "modrewrite SET link='" . $_POST['url'] . "',
            regex='" . $_POST['regex'] . "',
            fields='" . $data . "',
            replace_regex ='" . $replace[0] . "',
            replace_result ='" . $replace[1] . "',
            rebuild_regex ='" . $rebuild[0] . "',
            rebuild_result ='" . $rebuild[1] . "'
            WHERE ruleID='" . $_POST["ruleID"] . "'"
        );
        redirect("admincenter.php?site=modrewrite", "", 0);
    } else {
        echo $_language->module['transaction_invalid'];
    }
} else if (isset($_GET["delete"])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET['captcha_hash'])) {
        safe_query("DELETE FROM " . PREFIX . "modrewrite WHERE ruleID='" . $_GET["ruleID"] . "'");
        redirect("admincenter.php?site=modrewrite", "", 0);
    } else {
        echo $_language->module['transaction_invalid'];
    }
} else if (isset($_POST['test'])) {
    echo '<div class="panel panel-default">
    <div class="panel-heading">
                            <span class="bi bi-geo-alt-fill"></span> ' . $_language->module['modrewrite_settings'] . '
                        </div>
            <div class="panel-body">';
    $do_test = false;
    if (function_exists("apache_get_modules")) {
        $info = $_language->module['apache_with_module'] . '<br>';
        if (in_array('mod_rewrite', apache_get_modules())) {
            $info .= $_language->module['modrewrite_is_enabled'] . '<br>';
            $do_test = true;
        } else {
            $info .= $_language->module['modrewrite_is_disabled'] . '<br>';
        }
    } else if (stristr($_SERVER['SERVER_SOFTWARE'], 'Apache')) {
        $info = $_language->module['apache_with_cgi'] . '<br>';
        $do_test = true;
    } else {
        $info = $_language->module['unsupported_webserver'] . '<br>';
    }

    $enable = "";
    $status = $_language->module['unexpected_result'];

    if ($do_test) {
        $folder = 'ht_test';
        if (!is_dir($folder)) {
            mkdir($folder, 0777);
        }
        $file = ".htaccess";
        $path = $_POST['base'] . 'admin/' . $folder . '/';
        $content = $GLOBALS['_modRewrite']->generateHtAccess($path, 'test.php');
        file_put_contents($folder . '/test.php', '<?php echo @$_GET["url"];?>');

        $written = @file_put_contents($folder . '/' . $file, $content);

        $enable = "";
        $unlink = true;

        if ($written === false) {
            $info .= sprintf($_language->module['can_not_write_file'], $file);
        } else {
            $protocol = 'http';
            if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
                $protocol .= 's';
            }

            $port = '';
            if ($_SERVER["SERVER_PORT"] != "80") {
                $port = ":" . $_SERVER["SERVER_PORT"];
            }

            $base_test = $protocol . '://' . $_SERVER["SERVER_NAME"] . $port . dirname($_SERVER["REQUEST_URI"]) .
                '/ht_test/not_existing_file';
            $mutliview_test = $protocol . '://' . $_SERVER["SERVER_NAME"] . $port . dirname($_SERVER["REQUEST_URI"]) .
                '/ht_test/test/multiview';
            $headers = @get_headers($base_test, 1);
            if ($headers === false) {
                $info .= $_language->module['fopen_disabled'];
                $status = '<div id="result"></div>';
                $unlink = false;
            } else if (stristr($headers[0], '404')) {
                $status = $_language->module['modrewrite_failed'];
            } else if (stristr($headers[0], '500')) {
                $status = $_language->module['htaccess_failed'];
            } else if (stristr($headers[0], '200') && file_get_contents($base_test) == "not_existing_file") {
                $headers = @get_headers($mutliview_test, 1);
                if (stristr($headers[0], '200') && file_get_contents($mutliview_test) == "test/multiview") {
                    $status = $_language->module['test_successful'];
                    $enable = '<input class="btn btn-success btn-xs" type="submit" name="enable" value="' . $_language->module['enable'] . '">';
                } else {
                    $status = $_language->module['modrewrite_available_but_multiview_enabled'];
                    $info = $_language->module['add_apache_options'];
                }
            } else {
                $status = $_language->module['unexpected_result'];
                $info .= implode(', ', $headers);
            }
        }
        if ($unlink) {
            unlink($folder . '/test.php');
            unlink($folder . '/' . $file);
            rmdir($folder);
        }
    }

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    echo '<form method="post" action="admincenter.php?site=modrewrite" enctype="multipart/form-data">
<div class="row">
<div class="form-group">
    <label class="col-md-2 control-label">' . $_language->module['result'] . ':</label>
    <div class="col-md-8"><span class="text-muted small"><em>
      ' . $status . '</em></span>
    </div>
  </div>
  <br>
  <div class="form-group">
    <label class="col-md-2 control-label">' . $_language->module['debug'] . ':</label>
    <div class="col-md-8"><span class="text-muted small"><em>
      ' . $info . '</em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-12">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" />' . $enable . '
    </div>
  </div>

</div>

    </form></div></div>';
} else if (isset($_POST['enable'])) {
    $folder = '../';
    $file = ".htaccess";
    $path = $GLOBALS['_modRewrite']->getRewriteBase();
    $content = $GLOBALS['_modRewrite']->generateHtAccess($path);

    $info = '';
    if (file_exists($folder . '/' . $file)) {
        $info .= $_language->module['htaccess_exists_merge'];
        $file = '.htaccess_ws';
    }

    $written = @file_put_contents($folder . '/' . $file, $content);

    if ($written === false) {
        $info .= sprintf($_language->module['can_not_write_file'], $file);
        echo $info;
    } else {
        safe_query("UPDATE " . PREFIX . "settings SET modRewrite='1'");
        echo $info;
        redirect("admincenter.php?site=modrewrite", $_language->module['successful'], 2);
    }
} else if (isset($_POST['disable'])) {
    $folder = '../';
    $file = ".htaccess";
    $path = $GLOBALS['_modRewrite']->getRewriteBase();
    $content = $GLOBALS['_modRewrite']->generateHtAccess($path);

    if (file_get_contents($folder . '/' . $file) == $content) {
        unlink($folder . '/' . $file);
    }

    safe_query("UPDATE " . PREFIX . "settings SET modRewrite='0'");
    redirect("admincenter.php?site=modrewrite", $_language->module['successful'], 2);
} else {
    echo '<div class="panel panel-default"><div class="panel-heading">
                            <span class="bi bi-geo-alt-fill"></span> ' . $_language->module['modrewrite_settings'] . '
                        </div>
            <div class="panel-body">';
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    if ($modRewrite === false) {
        echo '<form method="post" action="admincenter.php?site=modrewrite" enctype="multipart/form-data">

<div class="row">
<div class="form-group">
    <label class="col-md-2 control-label">RewriteBase:</label>
    <div class="col-md-8"><span class="text-muted small"><em>
      <input type="text" name="base" value="' . $GLOBALS['_modRewrite']->getRewriteBase() .
            '" style="width:70%;"></em></span>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-12">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" />
      <input class="btn btn-success btn-xs" type="submit" name="test" value="' . $_language->module['test_support'] . '">
    </div>
  </div>

</div>







        </form></div></div>';
    } else {
        echo '<form method="post" action="admincenter.php?site=modrewrite" enctype="multipart/form-data">
        <div class="row">
<div class="form-group">
    <label class="col-md-2 control-label">' . $_language->module['state'] . ':</label>
    <div class="col-md-8"><span class="text-muted small"><em>
      ' . $_language->module['enabled'] . '</em></span>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-12">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" />
      <input class="btn btn-danger btn-xs"type="submit" name="disable" value="' . $_language->module['disable'] . '">
    </div>
  </div>

</div>
        </div></div>';
    }

    echo '<div class="panel panel-default">
    <div class="panel-heading">
                            <span class="bi bi-geo-alt-fill"></span> ' . $_language->module['modrewrite_rules'] . '
                        </div>
            <div class="panel-body">';

    echo
        '<a class="btn btn-primary btn-xs" type="button" href="admincenter.php?site=modrewrite&amp;action=add" class="input">' . $_language->module['new_rule'] .
        '</a> ';
    echo
        '<a class="btn btn-primary btn-xs" type="button" href="admincenter.php?site=modrewrite&amp;action=rebuild" class="input">' .
        $_language->module['rebuild'] . '</a><br><br>';

    echo '<table class="table table-striped">
<thead>
    <tr>
      <th><strong>'.$_language->module['rule'].'</strong></th>
      <th><strong>'.$_language->module['variables'].'</strong></th>
      <th><strong>'.$_language->module['actions'].'</strong></th>
    </tr></thead>';

    $ds = safe_query("SELECT * FROM " . PREFIX . "modrewrite ORDER BY regex");
    $anz = mysqli_num_rows($ds);
    if ($anz) {
        $i = 1;
        $CAPCLASS = new \webspell\Captcha;
        $CAPCLASS->createTransaction();
        $hash = $CAPCLASS->getHash();

        while ($flags = mysqli_fetch_array($ds)) {
            if ($i % 2) {
                $td = 'td1';
            } else {
                $td = 'td2';
            }
            echo '<tr>
            <td class="' . $td . '" align="left">' . $flags['regex'] . '<br>' . $flags['link'] . '</td>
            <td class="' . $td . '">' . count(unserialize($flags['fields'])) . '</td>
            <td class="' . $td . '" align="center"><a href="admincenter.php?site=modrewrite&amp;action=edit&amp;ruleID=' . $flags['ruleID'] . '" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=modrewrite&amp;delete=true&amp;ruleID=' . $flags['ruleID'] . '&amp;captcha_hash=' . $hash . '\')" value="' . $_language->module['delete'] . '" />

	  <a href="admincenter.php?site=modrewrite&amp;action=edit&amp;ruleID=' . $flags['ruleID'] . '"  class="mobile visible-xs visible-sm" type="button"><span class="bi bi-pencil-square"></span></a>
      <button class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=modrewrite&amp;delete=true&amp;ruleID=' . $flags['ruleID'] . '&amp;captcha_hash=' . $hash . '\')" /><span class="bi bi-trash-fill"></span></button>


                </td>
            </tr>';

            $i++;
        }
    } else {
        echo '<tr><td class="td1" colspan="5">' . $_language->module['no_entries'] . '</td></tr>';
    }

    echo '</table>
     </form></div></div>';
}
