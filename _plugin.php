<?php
/**
 * Plugin-Manager
 *
 * The Plugin-Manager can load plugins from a folder
 * without overwrite the original file.
 * @author Matti 'Getschonnik' W. <info@Getschonnik.de>
 */

$version = "1.5";

class PluginManager {
	var $_debug;

	//@debug   if debug mode ON show failure messages otherwise hide this
	function set_debug($var) {
		$this->_debug = $var;
	}

	//@info   check if a plugin index-link file exists that i can called by
	//    index.php?site=xxx
	function is_plugin($var) {
		try {
			$query = safe_query("SELECT * FROM ".PREFIX."plugins WHERE `index_link` LIKE '%".$var."%'");
			if (mysqli_num_rows($query)) {
				return 1;
			} else {
				return 0;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	//@info   get the plugin data from database
	function plugin_data($var, $id=0, $admin=false) {
		if ($id>0) {
			$where = "WHERE `pluginID`='".intval($id)."'";
			$query = safe_query("SELECT * FROM ".PREFIX."plugins ".$where);
		} else {
			if ($admin) {
				$where = "WHERE `admin_file`='".$var."' LIMIT 1";
			} else {
				$where = "WHERE `index_link` LIKE '%".$var."%'";
			}
			$q = safe_query("SELECT * FROM ".PREFIX."plugins ".$where);
			if (mysqli_num_rows($q)) {
				$tmp = mysqli_fetch_array($q);
				$ifiles = $tmp['index_link'];
				$tfiles = explode(",",$ifiles);
				if (in_array($var, $tfiles)) {
					$where = "WHERE `pluginID`='".$tmp['pluginID']."'";
					$query = safe_query("SELECT * FROM ".PREFIX."plugins ".$where);
				}
			}
		}
		if (!isset($query)) {
			return false;
		}
		try {
			if (mysqli_num_rows($query)) {
				return mysqli_fetch_array($query);
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	function plugin_check($data, $site) {
		$_language = new \webspell\Language;
		$_language->readModule('plugin');
		$return = array();
		if ($data['activate']==1) {
			if (isset($site)) {
				$ifiles = $data['index_link'];
				$tfiles = explode(",",$ifiles);
				if (in_array($site, $tfiles)) {
					$plugin_path = $data['path'].$site.".php";
					if (file_exists($plugin_path)) {
						$return['status'] = 1;
						$return['data'] = $plugin_path;
					} else {
						if (DEBUG==="ON") {
							echo '<br /><span class="label label-danger">'.$_language->module[ 'plugin_not_found' ].'</span>';
						}

						if (!file_exists($site . ".php")) {
							$site = "news";
						}
						$return['status'] = 1;
						$return['data'] = $site . ".php";

					}
				}
			} else {
				$plugin_path = $data['path'].$data['index_link'].".php";
				if (file_exists($plugin_path)) {
					$return['status'] = 1;
					$return['data'] = $plugin_path;
				} else {

					if (DEBUG==="ON") {
						return '<br /><span class="label label-danger">'.$_language->module[ 'plugin_not_found' ].'</span>';
					}

					if (!file_exists($site . ".php")) {
						$site = "news";
					}
					$return['status'] = 1;
					$return['data']= $site.".php";

				}
			}
		} else {
			if (DEBUG==="ON") {
				echo '<br /><span class="label label-warning">'.$_language->module[ 'plugin_deactivated' ].'</span>';
			}
			if (!file_exists($site . ".php")) {
				$site = "news";
			}
			$return['status'] = 1;
			$return['data']= $site.".php";
		}
		return $return;
	}

	//@info   check if the plugin is activated and exists
	function plugin_sc($id) {
		$pid = intval($id);
		$_language = new \webspell\Language;
		$_language->readModule('plugin');
		if (!empty($pid)) {
			$manager = new PluginManager();
			$row=$manager->plugin_data("", $pid);
			if ($row['activate'] != "1") {
				if ($this->_debug==="ON") {
					return ('<span class="label label-warning">'.$_language->module['plugin_deactivated'].'</span>');
				}
				return false;
			}
			$plugin_path = $row['path'].$row['sc_link'].".php";
			if (file_exists($plugin_path)) {
				require_once($plugin_path);
				return false;
			} else {
				if ($this->_debug === "ON") {
					return ('<span class="label label-danger">'.$_language->module['plugin_not_found'].'</span>');
				}
			}
		}
	}

	//@info  search a plugin by name and return the ID
	function pluginID_by_name($name) {
		$request=safe_query("SELECT * FROM `".PREFIX."plugins` WHERE `name` LIKE '%".$name."%'");
		if (mysqli_num_rows($request)) {
			$tmp=mysqli_fetch_array($request);
			return $tmp['pluginID'];
		}
		return 0;
	}

	//@info  include a file which saved in hiddenfiles
	function plugin_hf($id, $name) {
		$pid = intval($id);
		$_language = new \webspell\Language;
		$_language->readModule('plugin');
		if (!empty($pid) && !empty($name)) {
			$manager = new PluginManager();
			$row=$manager->plugin_data("", $pid);
			$hfiles = $row['hiddenfiles'];
			$tfiles = explode(",",$hfiles);
			if (in_array($name, $tfiles)) {
				$plugin_path = $row['path'].$name.".php";
				if (file_exists($plugin_path)) {
					require_once($plugin_path);
					return false;
				} else if ($this->_debug === "ON") {
					return ('<span class="label label-danger">'.$_language->module['plugin_not_found'].'</span>');
				}
			}
		}
	}

	//@info   get the plugin directories from database and check
	//    if in any plugin (direct) or in the subfolders (css & js)
	//    are file which must load into the <head> Tag
	function plugin_loadheadfile($pluginadmin=false) {
		$css = "";
		$js = "\n";
		$subf = "";

		$query = safe_query("SELECT * FROM `".PREFIX."plugins` WHERE `activate`='1' ");

		if ($pluginadmin) {
			$pluginpath = "../";
		} else {
			$pluginpath="";
		}

		while($res=mysqli_fetch_array($query)) {
			if (is_dir($pluginpath.$res['path']."css/")) {
				$subf .= "css/";
			} else {
				$subf ="";
			}
			$f = array();
			$f[] = glob(preg_replace('/(\*|\?|\[)/', '[$1]', $pluginpath.$res['path'].$subf).'*.css');
			$fc = count($f, COUNT_RECURSIVE);
			for($a=0; $a<=$fc; $a++) {
				if (@count($f[$a])>0) {
					for($b=0; $b<=(@count($f[$a])-1); $b++) {
						$css .= '<link type="text/css" rel="stylesheet" href="'.$f[$a][$b].'">'.chr(0x0D).chr(0x0A);
					}
				}
			}
			if (is_dir($pluginpath.$res['path']."js/")) {
				$subf2 = "js/";
			} else {
				$subf2="";
			}
			$g = array();
			$g[] = glob(preg_replace('/(\*|\?|\[)/', '[$1]', $pluginpath.$res['path'].$subf2).'*.js');
			$fc = count($g, COUNT_RECURSIVE);
			for($c=0; $c<=$fc; $c++) {
				if (@count($g[$c])>0) {
					for($d=0; $d<=(@count($g[$c])-1); $d++) {
						$js .= '<script src="'.$g[$c][$d].'"></script>'.chr(0x0D).chr(0x0A);
					}
				}
			}
		}
		return $css.$js;
	}

	//@info  get the page default language and check if the user / guests
	//   change into his own language otherwise set default language to EN
	//@name  set the name of the language file to load
	/* CALL IT
				/!\ NEVER use the variable $_language (conflict with the main module)

		$pm = new PluginManager();
		$_lang = $pm->plugin_language("my-plugin", $plugin_path);

	*/
	function plugin_language($name, $plugin_path) {
		$res = safe_query("SELECT `default_language` FROM `".PREFIX."settings` WHERE 1");
		$row = mysqli_fetch_array($res);
		$lng = $this->get_plugin_language($row['default_language']);
		$_lang = new webspell\Language();
		$_lang->setLanguage($lng, false);
		$_lang->readModule($name, true, false, $plugin_path);
		return $_lang->module;
	}
	function plugin_newLanguage($plugin, $file, $admin=false) {
		try {
			$res = safe_query("SELECT `default_language` FROM `".PREFIX."settings` WHERE 1");
			$row = mysqli_fetch_array($res);
			$lng = $this->get_plugin_language($row['default_language']);
			$p = __DIR__ . "/php/_plugins/" . $plugin;
			if (isset($admin)) {
				$admin = "admin";
			} else {
				$admin = "";
			}
			$arr = array();
			include("$p/languages/$lng/$admin/$file.php");
			foreach ($language_array as $key => $val) {
				$arr[ $key ] = $val;
			}
			return $arr;
		} catch (Exception $ex) {
			return $ex->getMessage();
		}
	}

	private function get_plugin_language($language) {
		if (isset($_SESSION[ 'language' ])) {
			$lng=$_SESSION[ 'language' ];
		} else {
			if (isset($language) && !empty($language)) {
				$lng=$language;
			} else {
				$lng="en";
			}
		}
		return $lng;
	}

	//@info  update website title for SEO
	function plugin_updatetitle($site) {
		try {
			$pm = new PluginManager();
			if ($pm->is_plugin($site)==1) {
				$arr = $pm->plugin_data($site);
				if (isset($arr['name'])) {
					return settitle($arr['name']);
				}
			}
		} catch (Exception $x) {
			if ($this->_debug === "ON") {
				return ('<span class="label label-danger">'.$x->getMessage().'</span>');
			}
		}
	}

}
//@info  show the version number of this file
if (isset($_GET['info'])){
	echo $version;
}
?>