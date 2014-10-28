<?php

/**
 * This file is part of playSMS.
 *
 * playSMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * playSMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with playSMS. If not, see <http://www.gnu.org/licenses/>.
 */
defined('_SECURE_') or die('Forbidden');

if (!auth_isadmin()) {
	auth_block();
}

include $core_config['apps_path']['plug'] . "/gateway/cdyne/config.php";

$callback_url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/plugin/gateway/cdyne/callback.php";
$callback_url = str_replace("//", "/", $callback_url);
$callback_url = "http://" . $callback_url;

switch (_OP_) {
	case "manage" :
		if ($err = $_SESSION['error_string']) {
			$error_content = "<div class=error_string>$err</div>";
		}
        $content .= "
	    <h2>"._('Manage Cdyne')."</h2>
	    <p>
	    <form action=index.php?app=main&inc=gateway_cdyne&op=manage_save method=post>
	    "._CSRF_FORM_."
	    <table class=playsms-table cellpadding=1 cellspacing=2 border=0>
		<tr>
		    <td class=label-sizer>"._('Gateway name')."</td><td>cdyne</td>
		</tr>
        <tr>
			<td>" . _ ( 'LicenseKey' ) . "</td><td><input type=text maxlength=36 name=up_api_licensekey value=\"" . $plugin_config['cdyne']['api_licensekey'] . "\"></td>
		</tr>
		<tr>
			<td>" . _ ( 'Module SenderID' ) . "</td><td><input type=text maxlength=20 name=up_did_sender value=\"" . $plugin_config['cdyne']['assigned_did'] . "\"></td>
		</tr>
        <tr>
			<td>" . _ ( 'Module timezone' ) . "</td><td><input type=text maxlength=20 name=up_global_timezone=\"" . $plugin_config['cdyne']['global_timezone'] . "\"></td>
		</tr>
		<tr>
			<td>" . _ ( 'PostBackUrl' ) . "</td><td><input type=text maxlength=20 name=up_callback value=\"" . $plugin_config['cdyne']['callback_url'] . "\"></td>
		</tr>
	    </table>
	    <p><input type=submit class=button value=\""._('Save')."\">
	    </form>
        <br />
        " . _ ( 'Notes' ) . ":<br />
        - " . _ ( 'Your Cdyne PostBackURL is' ) . " " . $callback_url . "<br />
	    - " . _ ( 'Your callback URL should be accessible from Cdyne' ) . "<br />
	    - " . _ ( 'Cdyne will push incoming SMS to your PostBackURL' ) . "<br />
	    - " . _ ( 'Cdyne SMS Notify! is a SMS gateway' ) . ", <a href=\"http://cdyne.com/api/phone/sms/\" target=\"_blank\">" . _ ( 'free credits are available for testing purposes' ) . "</a><br />";
		$content .= _back('index.php?app=main&inc=core_gateway&op=gateway_list');
		_p($content);
		break;
	case "manage_save" :
		$up_licensekey = $_POST['up_api_licensekey'];
		$up_module_sender = $_POST['up_did_sender'];
		$up_global_timezone = $_POST['up_global_timezone'];
        $up_callback_url = $_POST['up_callback'];

		$_SESSION['error_string'] = _('No changes have been made');

			$db_query = "
				UPDATE " . _DB_PREF_ . "_gatewayCdyne_config
				SET c_timestamp='" . mktime() . "',
				cfg_api_licensekey='$up_licensekey',
				cfg_module_sender='$up_module_sender',
				cfg_datetime_timezone='$up_global_timezone',
                cfg_param_callback_url='$up_callback_url'";
			if (@dba_affected_rows($db_query)) {
				$_SESSION['error_string'] = _('Gateway module configurations has been saved');
			}

		header("Location: " . _u('index.php?app=main&inc=gateway_cdyne&op=manage'));
		exit();
		break;
}


//        {{ ERROR }}
//<h2>{{ Manage cdyne }}</h2>
//<form action=index.php?app=main&inc=gateway_cdyne&op=manage_save method=post>
//{{ CSRF_FORM }}
//<table class=playsms-table>
//	<tbody>
//		<tr><td class=label-sizer>{{ Gateway name }}</td><td>cdyne</td></tr>
//		<tr><td>{{ LicenseKey }}</td><td><input type=text maxlength=36 name=up_licensekey value="{{ cdyne_param_licensekey }}"> {{ HINT_FILL_KEY }}</td></tr>
//		<tr><td>{{ Module SenderID }}</td><td><input type=text maxlength=16 name=up_module_sender value="{{ cdyne_param_module_sender }}"> {{ HINT_GLOBAL_SENDER }}</td></tr>
//    	<tr><td>{{ Module timezone }}</td><td><input type=text size=5 maxlength=5 name=up_global_timezone value="{{ cdyne_param_datetime_timezone }}"> {{ HINT_TIMEZONE }}</td></tr>
//        <tr><td>{{ PostBackUrl }}</td><td><input type=text maxlength=200 name=up_callback_url value="{{ cdyne_param_callback_url }}"> {{ HINT_CALLBACK }}</td></tr>
//	</tbody>
//</table>
//<p><input type=submit class=button value="{{ Save }}">
//</form>
//<br />
//{{ Notes }}:<br />
//- {{ CALLBACK_URL_IS }} {{ callback_url }}<br />
//- {{ CALLBACK_URL_ACCESSIBLE }}<br />
//- {{ CDYNE_PUSH_DLR }}<br />
//- {{ CDYNE_DESC }}, <a href="http://cdyne.com/api/phone/sms/" target="_blank">{{ CDYNE_FREE_CREDIT }}</a><br />
//{{ BUTTON_BACK }}


        // something wrong with template...
		//$tpl = array(
		//	'name' => 'cdyne',
		//	'vars' => array(
		//		'ERROR' => $error_content,
		//		'Manage cdyne' => _('Manage cdyne'),
		//		'Gateway name' => _('Gateway name'),
		//		'LicenseKey' => _('LicenseKey'),
		//		'Module SenderID' => _('Module SenderID'),
  //              'PostBackUrl' => _('PostBackUrl'),
		//		'Save' => _('Save'),
		//		'Notes' => _('Notes'),
		//		'HINT_FILL_KEY' => _hint(_('Fill to change the LicenseKey')),
		//		'HINT_GLOBAL_SENDER' => _hint(_('Assigned DID/Short Code associated with LicenseKey')),
  //              'HINT_TIMEZONE' => _hint(_('Eg: +0700 for Jakarta/Bangkok timezone')),
  //              'HINT_CALLBACK' => _hint(_('URL for Cdyne to send incoming messages and DLR')),
		//		'CALLBACK_URL_IS' => _('Your callback URL is'),
		//		'CALLBACK_URL_ACCESSIBLE' => _('Your callback URL should be accessible from Cdyne'),
		//		'CDYNE_PUSH_DLR' => _('Cdyne will push DLR and incoming SMS to your callback URL'),
		//		'CDYNE_DESC' => _('Cdyne SMS Notify! is a SMS gateway'),
		//		'CDYNE_FREE_CREDIT' => _('free credits are available for testing purposes'),
		//		'BUTTON_BACK' => _back('index.php?app=main&inc=core_gateway&op=gateway_list'),
		//		'status_active' => $status_active,
		//		'cdyne_param_licensekey' => $plugin_config['cdyne']['api_licensekey'],
		//		'cdyne_param_module_sender' => $plugin_config['cdyne']['assigned_did'],
		//		'cdyne_param_datetime_timezone' => $plugin_config['cdyne']['datetime_timezone'],
  //              'cdyne_param_callback_url' => $plugin_config['cdyne']['callback_url'],
		//		'callback_url' => $callback_url 
		//	) 
		//);
		//_p(tpl_apply($tpl));
		//break;
