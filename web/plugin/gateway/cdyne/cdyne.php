<?php
defined('_SECURE_') or die('Forbidden');
if(!auth_isadmin()){auth_block();};

include $core_config['apps_path']['plug']."/gateway/cdyne/config.php";

$callback_url = $_SERVER['HTTP_HOST'] . dirname ( $_SERVER['PHP_SELF'] ) . "/plugin/gateway/cdyne/callback.php";
$callback_url = str_replace ( "//", "/", $callback_url );
$callback_url = "http://" . $callback_url;

switch (_OP_) {
	case "manage":
		if ($err = $_SESSION['error_string'])
		{
			$content = "<div class=error_string>$err</div>";
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
			<td>" . _ ( 'License Key' ) . "</td><td><input type=text maxlength=36 name=up_api_licensekey value=\"" . $plugin_config['cdyne']['licensekey'] . "\"></td>
		</tr>
		<tr>
			<td>" . _ ( 'Assigned DID' ) . "</td><td><input type=text maxlength=20 name=up_did_sender value=\"" . $plugin_config['cdyne']['did_sender'] . "\"></td>
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
	case "manage_save":
		$up_api_licensekey = $_POST['up_api_licensekey'];
		$up_did_sender = $_POST['up_did_sender'];
		$_SESSION['error_string'] = _('No changes have been made');
		if ($up_api_licensekey && $up_did_sender)
		{
			$db_query = "
		UPDATE "._DB_PREF_."_gatewayCdyne_config
		SET c_timestamp='".mktime()."',cfg_api_licensekey='$up_api_licensekey',cfg_did_sender='$up_did_sender'
	    ";
			if (@dba_affected_rows($db_query))
			{
				$_SESSION['error_string'] = _('Gateway module configurations has been saved');
			}
		}
		header("Location: "._u('index.php?app=main&inc=gateway_cdyne&op=manage'));
		exit();
		break;
}
