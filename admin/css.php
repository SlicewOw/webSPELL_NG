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

$_language->readModule('styles', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

echo '<div class="panel panel-default">
  <div class="panel-heading">
                            <i class="fa fa-thumbs-up"></i> '.$_language->module['styles'].'
                        </div>
                        </div>
                        <div class="panel panel-default">
                        
            <ul class="nav nav-tabs-primary">    
    <li role="presentation"><a href="admincenter.php?site=styles">Style</a></li>
    <li role="presentation"><a href="admincenter.php?site=buttons">Buttons</a></li>
    <li role="presentation"><a href="admincenter.php?site=moduls">Module</a></li>
    <li role="presentation" class="active"><a href="admincenter.php?site=css">.css</a></li>
</ul>
<ol class="breadcrumb-primary"> </ol>
 <div class="panel-body">';

if (isset($_POST[ 'submit' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        $error = array();
        $sem = '/^#[a-fA-F0-9]{6}/';
        #if (!(preg_match($sem, $_POST[ 'body1' ]))) {
        #    $error[ ] = $_language->module[ 'error_page_bg' ];
        #}
        #if (!(preg_match($sem, $_POST[ 'body2' ]))) {
        #    $error[ ] = $_language->module[ 'error_bordercolor' ];
        #}
        #if (!(preg_match($sem, $_POST[ 'body3' ]))) {
        #    $error[ ] = $_language->module[ 'error_head_bg' ];
        #}
        #if (!(preg_match($sem, $_POST[ 'body4' ]))) {
        #    $error[ ] = $_language->module[ 'error_category_bg' ];
        #}
        #if (!(preg_match($sem, $_POST[ 'bg1' ]))) {
        #    $error[ ] = $_language->module[ 'error_cell_bg1' ];
        #}
        #if (!(preg_match($sem, $_POST[ 'bg2' ]))) {
        #    $error[ ] = $_language->module[ 'error_cell_bg2' ];
        #}
        #if (!(preg_match($sem, $_POST[ 'bg3' ]))) {
        #    $error[ ] = $_language->module[ 'error_cell_bg3' ];
        #}
        #if (!(preg_match($sem, $_POST[ 'bg4' ]))) {
        #    $error[ ] = $_language->module[ 'error_cell_bg4' ];
        #}
        #if (!(preg_match($sem, $_POST[ 'win' ]))) {
        #    $error[ ] = $_language->module[ 'error_win_color' ];
        #}
        #if (!(preg_match($sem, $_POST[ 'loose' ]))) {
        #    $error[ ] = $_language->module[ 'error_loose_color' ];
        #}
        #if (!(preg_match($sem, $_POST[ 'draw' ]))) {
        #    $error[ ] = $_language->module[ 'error_draw_color' ];
        #}
        if (count($error)) {
            echo '<b>' . $_language->module[ 'errors' ] . ':</b><br /><ul>';

            foreach ($error as $err) {
                echo '<li>' . $err . '</li>';
            }
            echo '</ul><br /><input type="button" onclick="javascript:history.back()" value="' .
                $_language->module[ 'back' ] . '" />';
        } else {
            
            $file = ("../_stylesheet.css");
            $fp = fopen($file, "w");
            fwrite($fp, stripslashes(str_replace('\r\n', "\n", $_POST[ 'stylesheet' ])));
            fclose($fp);
            redirect("admincenter.php?site=css", "", 0);
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else {
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "styles");
    $ds = mysqli_fetch_array($ergebnis);

    $file = ("../_stylesheet.css");
    $size = filesize($file);
    $fp = fopen($file, "r");
    $stylesheet = fread($fp, $size);
    fclose($fp);

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();


    echo '<form class="form-horizontal" method="post" action="admincenter.php?site=css" enctype="multipart/form-data">
	<div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['stylesheet_info'].'</label>
    <div class="col-sm-8">
        <textarea class="form-control" name="stylesheet" rows="30" cols="" ">'.$stylesheet.'</textarea>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" />
  <button class="btn btn-primary btn-xs" type="submit" name="submit" />'.$_language->module['update'].'</button>
    </div>
  </div>
</form>';
}
echo '</div></div>';
?>


