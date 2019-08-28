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

$_language->readModule('lostpassword');

$title_lostpassword = $GLOBALS["_template"]->replaceTemplate("title_lostpassword", array());
echo $title_lostpassword;

if (isset($_POST[ 'submit' ])) {
    $email = trim($_POST[ 'email' ]);
    if ($email != '') {
        $ergebnis = safe_query(
            "SELECT
                *
            FROM
                " . PREFIX . "user
            WHERE
                email = '" . $email . "'"
        );
        $anz = mysqli_num_rows($ergebnis);

        if ($anz) {
            $ds = mysqli_fetch_array($ergebnis);
	
			$newpass_random = Gen_PasswordPepper();
			$newpass_hash = Gen_PasswordHash($newpass_random, $ds['userID']);
	
            safe_query(
                "UPDATE
                    " . PREFIX . "user
                SET
                    password='', password_hash='" . $newpass_hash . "'
                WHERE
                    userID='" . intval($ds[ 'userID' ]) . "'"
            );

            $ToEmail = $ds[ 'email' ];
            $vars = array('%pagetitle%', '%username%', '%new_password%', '%homepage_url%');
            $repl = array($hp_title, $ds[ 'username' ], utf8_encode($newpass_random), $hp_url);
            $header = str_replace($vars, $repl, $_language->module[ 'email_subject' ]);
            $Message = str_replace($vars, $repl, $_language->module[ 'email_text' ]);

            $sendmail = \webspell\Email::sendEmail($admin_email, 'Lost Password', $ToEmail, $header, $Message);

            if ($sendmail['result'] == 'fail') {
                if (isset($sendmail['debug'])) {
                    $fehler = array();
                    $fehler[] = $sendmail[ 'error' ];
                    $fehler[] = $sendmail[ 'debug' ];
                    echo generateErrorBoxFromArray($_language->module['email_failed'], $fehler);
                } else {
                    $fehler = array();
                    $fehler[] = $sendmail[ 'error' ];
                    echo generateErrorBoxFromArray($_language->module['email_failed'], $fehler);
                }
            } else {
                if (isset($sendmail['debug'])) {
                    $fehler = array();
                    $fehler[] = $sendmail[ 'debug' ];
                    echo generateBoxFromArray($_language->module[ 'successful' ], 'alert-success', $fehler);
                    echo str_replace($vars, $repl, $_language->module[ 'successful' ]);
                } else {
                    echo  redirect("index.php?site=login", str_replace($vars, $repl, $_language->module[ 'successful' ]), 3);
                }
                
            }
        } else {
            echo $_language->module[ 'no_user_found' ];
        }
    } else {
        echo $_language->module[ 'no_mail_given' ];
    }
} else {
    echo '<form method="post" action="index.php?site=lostpassword" class="form-inline" role="form">
            <div class="form-group">
                <label class="sr-only" for="email">' . $_language->module[ 'your_email' ] . '</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="' .
        $_language->module[ 'your_email' ] . '" required>
            </div>
            <input type="submit" name="submit"
            value="' . $_language->module[ 'get_password' ] . '" class="btn btn-danger">
        </form>';
}
