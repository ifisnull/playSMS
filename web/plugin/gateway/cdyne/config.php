<?php
defined('_SECURE_') or die('Forbidden');

$db_query = "SELECT * FROM " . _DB_PREF_ . "_gatewayCdyne_config";
$db_result = dba_query($db_query);
if ($db_row = dba_fetch_array($db_result)) {
	$plugin_config['cdyne']['name'] = $db_row['cfg_name'];
    $plugin_config['cdyne']['licensekey'] = $db_row['cfg_api_licensekey'];
	$plugin_config['cdyne']['did_sender'] = $db_row['cfg_did_sender'];
}

// smsc configuration
$plugin_config['cdyne']['_smsc_config_'] = array();

//$gateway_number = $template_param['module_sender'];

// insert to left menu array
//if (isadmin()) {
//	$menutab_gateway = $core_config['menutab']['gateway'];
//	$menu_config[$menutab_gateway][] = array("index.php?app=main&inc=gateway_template&op=manage", _('Manage template'));
//}