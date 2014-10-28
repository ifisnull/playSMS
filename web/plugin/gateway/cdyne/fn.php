<?php
defined('_SECURE_') or die('Forbidden');

// hook_sendsms
// called by main sms sender
// return true for success delivery
// $smsc			: smsc 
// $sms_sender	: sender mobile number
// $sms_footer	: sender sms footer or sms sender ID
// $sms_to		: destination sms number
// $sms_msg		: sms message tobe delivered
// $uid			: sender User ID
// $gpid		: group phonebook id (optional)
// $smslog_id	: sms ID
// $sms_type	: send flash message when the value is "flash"
// $unicode		: send unicode character (16 bit)
function cdyne_hook_sendsms($smsc, $sms_sender,$sms_footer,$sms_to,$sms_msg,$uid='',$gpid=0,$smslog_id=0,$sms_type='text',$unicode=0) {
    include $core_config['apps_path']['plug']."/gateway/cdyne/config.php";

	_log("enter smsc:" . $smsc . " smslog_id:" . $smslog_id . " uid:" . $uid . " to:" . $sms_to, 3, "cdyne_hook_sendsms");
	
    global $plugin_config;

    // override plugin gateway configuration by smsc configuration
    $plugin_config = gateway_apply_smsc_config($smsc, $plugin_config);

	// $sms_sender = stripslashes($sms_sender);
	$sms_footer = stripslashes($sms_footer);
	$sms_msg = stripslashes($sms_msg);
	
    if ($unicode == 0) {
        $unicode = 'FALSE';
    } else {
        $unicode = 'TRUE';
    }

    $key = $plugin_config['cdyne']['api_licensekey'];
    $assigned_did = $plugin_config['cdyne']['assigned_did'];

    $callback_url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/plugin/gateway/nexmo/callback.php";
    $callback_url = str_replace("//", "/", $callback_url);
    $callback_url = "http://" . $callback_url;

    // build request
	$json = '{
              "LicenseKey":"' . $key . '",
              "IsUnicode":' . $unicode . '
              "SMSRequests":[{
                  "AssignedDID":"' . $assigned_did . '",
                  "Message":"' . $sms_msg . ' ' . $sms_footer . '",
                  "PhoneNumbers":["' . $sms_to . '"],
                  "ReferenceID":"' . 'smslog_id: ' . $smslog_id . ' uid:' . $uid . '",
                  "StatusPostBackURL":" ' . $callback_url . '?smsc=' . $smsc . '&client-ref=' . $smslog_id . '"
                  }]
             }';

    _log($json, 3, "cdyne_hook_sendsms request");

    $url = 'http://sms2.cdyne.com/sms.svc/AdvancedSMSSend';

    $cURL = curl_init();
    curl_setopt($cURL,CURLOPT_URL,$url);
    curl_setopt($cURL,CURLOPT_POST,true);
    curl_setopt($cURL,CURLOPT_POSTFIELDS,$json);
    curl_setopt($cURL,CURLOPT_RETURNTRANSFER, true);  
    curl_setopt($cURL, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json'));

    $result = curl_exec($cURL);
    curl_close($cURL);

    $decoded = json_decode($result);
    $logText = "enter smsc:" . $smsc . " smslog_id:" . $smslog_id . " uid:" . $uid . " to:" . $sms_to . " CdyneMessageID: " . $decoded['0']->MessageID . " SMSErrorCode: " . $decoded['0']->SMSError;

    _log($logText, 3, "cdyne_hook_sendsms result");

    if ($decoded['0']->SMSError == 0) {
        return TRUE;
    } else {
        dlr($smslog_id, $uid, 2); //mark as failed
        return FALSE;
    }
}

// Not used
// hook_playsmsd
// used by index.php?app=main&inc=daemon to execute custom commands
function cdyne_hook_playsmsd() {
	// custom commands
}

// Not used - using callback for incoming messages
// hook_getsmsstatus
// called by index.php?app=main&inc=daemon (periodic daemon) to set sms status
// no returns needed
// $p_datetime	: first sms delivery datetime
// $p_update	: last status update datetime
function cdyne_hook_getsmsstatus($gpid=0,$uid="",$smslog_id="",$p_datetime="",$p_update="") {
	// global $tmpl_param;
	// p_status :
	// 0 = pending
	// 1 = sent
	// 2 = failed
	// 3 = delivered
	// dlr($smslog_id,$uid,$p_status);
}

// Not used - using callback for incoming messages
// hook_getsmsinbox
// called by incoming sms processor
// no returns needed
function cdyne_hook_getsmsinbox() {
	// global $tmpl_param;
	// $sms_datetime	: incoming sms datetime
	// $message		: incoming sms message
	// if $sms_sender and $message are not coming from $_REQUEST then you need to addslashes it
	// $sms_sender = addslashes($sms_sender);
	// $message = addslashes($message);
	// recvsms($sms_datetime,$sms_sender,$message,$sms_receiver,'cdyne')
	// you must retrieve all informations needed by recvsms()
	// from incoming sms, have a look gnokii gateway module
}

?>