<?php

/**
 * login.php
 *
 * @version 1.0
 * @copyright 2008 by ?????? for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

@session_destroy();

$InLogin = true;

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);
includeLang('login');

	// BANIP MOD BY lyra
	checkban($_SERVER['REMOTE_ADDR']);

	if ($_POST) {
		$login = doquery("SELECT * FROM {{table}} WHERE `username` = '" . mysql_escape_string($_POST['username']) . "' LIMIT 1", "users", true);

		if ($login) {
			if ($login['password'] == md5($_POST['password'])) {
				
				include($ugamela_root_path . 'config.' . $phpEx);
				
				$_SESSION[USER_SESSION][id] = $login['id'];
				$_SESSION[USER_SESSION][username] = $login['username'];
				$_SESSION[USER_SESSION][password] = md5($login["password"] . "--" . $dbsettings["secretword"]);
				$_SESSION[USER_SESSION][ip] = getIP();
				
				$Upd  = "UPDATE {{table}} SET ";
				$Upd .= "`user_lastip` = '" . $_SESSION[USER_SESSION][ip] . "'";
				$Upd .= " WHERE ";
				$Upd .= "`id` = " . $login['id'] . " LIMIT 1;";
				doquery($Upd, 'users');
				
				unset($dbsettings);
				header("Location: ./frames.php");
				exit;
			} else {
				message($lang['Login_FailPassword'], $lang['Login_Error']);
			}
		} else {
			message($lang['Login_FailUser'], $lang['Login_Error']);
		}
	} else {
		$parse = $lang;
		$query = doquery('SELECT username FROM {{table}} ORDER BY register_time DESC', 'users', true);
		$parse['last_user'] = $query['username'];
		$query = mysql_fetch_row(doquery("SELECT COUNT(DISTINCT(id)) FROM {{table}} WHERE onlinetime>" . (time()-900), 'users'));
		$parse['online_users'] = $query[0];
		//INICIO FIX TOTAL USUARIOS
		$MaxUsers = doquery ("SELECT COUNT(*) AS `count` FROM {{table}} WHERE `db_deaktjava` = '0' OR `db_deaktjava` = '1';", 'users', true);
		$parse['users_amount'] = $MaxUsers['count'];
		//FIN FIX TOTAL USUARIOS
		$parse['servername'] = $game_config['game_name'];
		$parse['forum_url'] = $game_config['forum_url'];
		$parse['PasswordLost'] = $lang['PasswordLost'];

		$page = parsetemplate(gettemplate('login_body'), $parse);
		display($page, $lang['Login']);
	}

// -----------------------------------------------------------------------------------------------------------
// History version

?>
