<?php

error_reporting(0);

if (!$called_from_hook_call) {
	chdir("../../../");
	
	// ignore CSRF
	$core_config['init']['ignore_csrf'] = TRUE;
	
	include "init.php";
	include $core_config['apps_path']['libs'] . "/function.php";
	chdir("plugin/gateway/cdyne/");
	$requests = $_REQUEST;
}

$log = '';
if (is_array($requests)) {
	foreach ($requests as $key => $val ) {
		$log .= $key . ':' . $val . ' ';
	}
	logger_print("pushed " . $log, 2, "cdyne callback");
}

$remote_smslog_id = $requests['MessageID'];
$sent_confirm = $requests['SMSSent'];
$incoming = $requests['SMSResponse'];
$delivery_receipt = $requests['DeliveryReceipt'];

//Message Sent
if ($remote_smslog_id && $sent_confirm) {
    
}

//Delivery Receipt
if ($remote_smslog_id && $delivery_receipt) {
    
}

//Incoming Message
if ($remote_smslog_id && $incoming) {
    
}

?>
