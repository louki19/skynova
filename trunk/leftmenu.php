<?PHP
/**
 * @author Chlorel
 * 
 * @package XNova
 * @version 0.8
 * @copyright (c) 2008 XNova Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.'.$phpEx);

// blocking non-users
if ($IsUserChecked == false) {
	includeLang('login');
	message($lang['Login_Ok'], $lang['log_numbreg']);
}

includeLang('leftmenu');

// load the templates
$tpl_menu = gettemplate('left_menu');
$tpl_info = gettemplate('serv_infos');

// a table with the server quick info
$parse                 = $lang;
$parse['lm_tx_serv']   = $game_config['resource_multiplier'];
$parse['lm_tx_game']   = $game_config['game_speed'] / 2500;
$parse['lm_tx_fleet']  = $game_config['fleet_speed'] / 2500;
$parse['lm_tx_queue']  = MAX_FLEET_OR_DEFS_PER_ROW;
// parsing the table
$parse['server_info']  = parsetemplate($tpl_info, $parse);

$parse['XNovaRelease'] = VERSION;
$parse['dpath']        = $dpath;
$parse['forum_url']    = $game_config['forum_url'];
$parse['mf']           = 'Hauptframe';

// for ranking info when we click the stats link
$rank = doquery("SELECT `total_rank` FROM {{table}} WHERE `stat_code`='1' AND `stat_type`='1' AND `id_owner`='{$user['id']}';", 'statpoints', true);

$parse['user_rank'] = $rank['total_rank'];

// admin, moderators link
if ($user['authlevel'] > 0) {
	$parse['ADMIN_LINK']  = '<tr><td colspan="2"><div><a href="admin/leftmenu.php"><font color="lime">'.$lang['user_level'][$user['authlevel']].'</font></a></div></td></tr>';
} else {
	$parse['ADMIN_LINK']  = '';
}

$parse['servername']   = $game_config['game_name'];

// end parsing
$menu = parsetemplate($tpl_menu, $parse);

display($menu, 'Menu', '', false);

// -----------------------------------------------------------------------------
// History version
// 1.0 - Passage en fonction pour XNova version future
// 1.1 - Modification pour gestion Admin / Game OP / Modo
?>
