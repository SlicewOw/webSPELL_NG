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

if ($_POST['installtype']=="full" && $_POST['hp_url']) {
?>
<div class="row marketing">
    <div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo $_language->module['data_config']; ?></h3>
			</div>
			<div class="panel-body">
                <div class="form-horizontal">
                
					<div class="form-group">
						<label for="hostname" class="col-sm-4 control-label"><?php echo $_language->module['host_name']; ?>:</label>
						<div class="input-group col-sm-5">
							<input type="text" class="form-control" name="host" value="localhost">
                            <div class="input-group-addon"><a href="#" data-toggle="tooltip" data-placement="top" title="<?php echo $_language->module['tooltip_1']; ?>"><i class="fa fa-question-circle"></i></a></div>
						</div>
					</div> <!-- form-group-end -->
                    
					<div class="form-group">
						<label for="mysql" class="col-sm-4 control-label"><?php echo $_language->module['mysql_username']; ?>:</label>
						<div class="input-group col-sm-5">
							<input type="text" class="form-control" name="user">
                            <div class="input-group-addon"><a href="#" data-toggle="tooltip" data-placement="top" title="<?php echo $_language->module['tooltip_2']; ?>"><i class="fa fa-question-circle"></i></a></div>
						</div>
					</div> <!-- form-group-end -->
                    
					<div class="form-group">
						<label for="mysqlpw" class="col-sm-4 control-label"><?php echo $_language->module['mysql_password']; ?>:</label>
						<div class="input-group col-sm-5">
							<input type="password" class="form-control" name="pwd">
                            <div class="input-group-addon"><a href="#" data-toggle="tooltip" data-placement="top" title="<?php echo $_language->module['tooltip_3']; ?>"><i class="fa fa-question-circle"></i></a></div>
						</div>
					</div> <!-- form-group-end -->
                    
					<div class="form-group">
						<label for="mysqldb" class="col-sm-4 control-label"><?php echo $_language->module['mysql_database']; ?>:</label>
						<div class="input-group col-sm-5">
							<input type="text" class="form-control" name="db">
                            <div class="input-group-addon"><a href="#" data-toggle="tooltip" data-placement="top" title="<?php echo $_language->module['tooltip_4']; ?>"><i class="fa fa-question-circle"></i></a></div>
						</div>
					</div> <!-- form-group-end -->
					<div class="form-group">
						<label for="mysqlprefix" class="col-sm-4 control-label"><?php echo $_language->module['mysql_prefix']; ?>:</label>
						<div class="input-group col-sm-2">
							<input type="text" class="form-control" name="prefix" value="<?php echo 'ws_' . RandPass(3) . '_'; ?>">
                            <div class="input-group-addon"><a href="#" data-toggle="tooltip" data-placement="top" title="<?php echo $_language->module['tooltip_5']; ?>"><i class="fa fa-question-circle"></i></a></div>
						</div>
					</div> <!-- form-group-end -->   
                    
                </div> <!-- form-horizontal-end -->
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo $_language->module['webspell_config']; ?></h3>
			</div>
			<div class="panel-body">
                <div class="form-horizontal">
                
					<div class="form-group">
						<label for="adminname" class="col-sm-4 control-label"><?php echo $_language->module['admin_username']; ?>:</label>
						<div class="input-group col-sm-5">
							<input type="text" class="form-control" name="adminname">
                            <div class="input-group-addon"><a href="#" data-toggle="tooltip" data-placement="top" title="<?php echo $_language->module['tooltip_6']; ?>"><i class="fa fa-question-circle"></i></a></div>
						</div>
					</div> <!-- form-group-end -->
                    
					<div class="form-group">
						<label for="adminpwd" class="col-sm-4 control-label"><?php echo $_language->module['admin_password']; ?>:</label>
						<div class="input-group col-sm-5">
							<input type="password" class="form-control" name="adminpwd">
                            <div class="input-group-addon"><a href="#" data-toggle="tooltip" data-placement="top" title="<?php echo $_language->module['tooltip_7']; ?>"><i class="fa fa-question-circle"></i></a></div>
						</div>
					</div> <!-- form-group-end -->
                    
					<div class="form-group">
						<label for="adminemail" class="col-sm-4 control-label"><?php echo $_language->module['admin_email']; ?>:</label>
						<div class="input-group col-sm-5">
							<input type="text" class="form-control" name="adminmail">
                            <div class="input-group-addon"><a href="#" data-toggle="tooltip" data-placement="top" title="<?php echo $_language->module['tooltip_8']; ?>"><i class="fa fa-question-circle"></i></a></div>
						</div>
					</div> <!-- form-group-end -->
                    <input type="hidden" name="installtype" value="<?php echo $_POST['installtype']; ?>">
                </div> <!-- form-horizontal-end -->
                <div class="pull-right"><a class="btn btn-primary" href="javascript:document.ws_install.submit()">continue</a></div>
			</div>
		</div>
    </div>
    </div>
</div> <!-- row end -->
        <input type="hidden" name="url" value="<?php echo $_POST['hp_url']; ?>">

        <?php
        } else echo '<div class="row marketing">
						<div class="col-xs-12">
							<ol class="breadcrumb">
							  <li>' . $_language->module['step0'] . '</li>
							  <li>' . $_language->module['step1'] . '</li>
							  <li>' . $_language->module['step2'] . '</li>
							  <li>' . $_language->module['step3'] . '</li>
							  <li>' . $_language->module['step4'] . '</li>
							  <li class="active">' . $_language->module['step5'] . '</li>
							  <li>' . $_language->module['step6'] . '</li>
							</ol>
						</div>
						
    <div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">' . $_language->module['finish_install'] . '</h3>
			</div>
			<div class="panel-body">
				' . $_language->module['finish_next'] . '
				<input type="hidden" name="installtype" value="'.$_POST['installtype'].'">
                <div class="pull-right"><a class="btn btn-primary" href="javascript:document.ws_install.submit()">continue</a></div>
			</div>
		</div>
    </div>';
?>