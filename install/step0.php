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

$languages = '';
if ($handle = opendir('./languages/')) {
    while (false !== ($file = readdir($handle))) {
        if (is_dir('./languages/' . $file) && $file != ".." && $file != "." && $file != ".svn") {
            $languages .= '<a class="btn btn-default btn-margin btn-sm" href="index.php?lang=' . $file . '"><img src="../images/languages/' . $file . '.gif"
            alt="' . $file . '"></a>';
        }
    }
    closedir($handle);
}

?>
<div class="row marketing">
    <div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
                	<div class="pull-left panel-line"><?php echo $_language->module['welcome_to']; ?></div> 
                    <div class="pull-right"><small><?php echo $_language->module['select_a_language']; ?>: <?php echo $languages; ?></small></div>
                    <div class="clearfix"></div>
                </h3>
			</div>
			<div class="panel-body">
				<?php 
				if(file_exists("locked.txt")) {
					echo $_language->module['installerlocked']; 
				} else {
					echo $_language->module['welcome_text']; ?><br /><?php echo $_language->module['webspell_team'];
				}
				
				if(!file_exists("locked.txt")) {
                echo '<div class="pull-right"><a class="btn btn-primary" href="javascript:document.ws_install.submit()">continue</a></div>'; } 
				
				?>
			</div>
		</div>
    </div>
</div> <!-- row end -->