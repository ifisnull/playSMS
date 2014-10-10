<?php
defined('_SECURE_') or die('Forbidden');
if(!auth_isadmin()){auth_block();};

include $core_config['apps_path']['plug']."/gateway/cdyne/config.php";

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
		    <td class=label-sizer>"._('Gateway Cdyne')."</td><td>template</td>
		</tr>
		<tr>
		    <td>"._('Cdyne installation path')."</td><td><input type=text maxlength=250 name=up_path value=\"".$cdyne_param['path']."\"> ("._('No trailing slash')." \"/\")</td>
		</tr>
	    </table>
	    <p><input type=submit class=button value=\""._('Save')."\">
	    </form>";
		_p($content);
		break;
	case "manage_save":
		$up_path = $_POST['up_path'];
		$_SESSION['error_string'] = _('No changes have been made');
		if ($up_path)
		{
			$db_query = "
		UPDATE "._DB_PREF_."_gatewayCdyne_config
		SET c_timestamp='".mktime()."',cfg_path='$up_path'
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
