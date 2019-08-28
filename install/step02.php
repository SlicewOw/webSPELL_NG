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

if($_POST['agree'] == "1") {
	function getwspath() {
		$path=$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
		return str_replace('/install/index.php','',$path);
	}

	function getwebserver($path) {
		$path=str_replace('http://','',$path);
		$server = str_replace(strstr($path,'/'),'',$path);
		if(mb_substr($server,0,3) == 'www') $server = mb_substr(strstr($server,'.'),1);
		return $server;

	}
	//version test
	$versionerror=false;
	if(phpversion()=='5.2.6') $versionerror=true;
?>
<div class="row marketing">
    <div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<?php if ($versionerror) {
                    echo $_language->module['error'];
                } else {
                    echo $_language->module['your_site_url'];
                } ?>
            	</h3>
			</div>
			<div class="panel-body">
				<?php if ($versionerror) {
                echo '<p style="color: #FF0000; font-weight: bold;">' . $_language->module['php_version'] . ':</p>
		<p>' . $_language->module['php_info'] . '</p><br><br>';
            } else {
                echo '
				<div class="form-group">
					<label for="wheretoinstall">' . $_language->module['enter_url'] . '</label>
					<div class="input-group">
						<div class="input-group-addon">http://</div>
						<input type="text" class="form-control" name="hp_url" value="' . getwspath() . '">
					</div>
				</div>
				<span id="helpBlock" class="help-block"><small>' . $_language->module['tooltip'] . '</small></span>';
            }
            ?>
                <div class="pull-right"><a class="btn btn-primary" href="javascript:document.ws_install.submit()">continue</a></div>
			</div>
		</div>
    </div>
</div> <!-- row end -->

<?php
} else {
?>
<div class="row marketing">
    <div class="col-xs-12">
        <div class="alert alert-danger">
			<?php echo $_language->module['you_have_to_agree']; ?>
            <div class="pull-right"><a class="alert-link" href="javascript:history.back()">back</a></div>
		</div>
    </div>
</div> <!-- row end -->
<?php
}
