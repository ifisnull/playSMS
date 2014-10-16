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

if ($remote_smslog_id) {
    //Have Cdyne MessageID, get local MessageID

    $db_query = "
        SELECT local_smslog_id FROM " . _DB_PREF_ . "_gatewayCdyne
        WHERE remote_smslog_id='$remote_smslog_id'";
    $db_result = dba_query($db_query);
    $db_row = dba_fetch_array($db_result);
    $smslog_id = $db_row['local_smslog_id'];

    //if we have an smslog_id, then this is referring to an outgoing message, else it could be incoming
    if ($smslog_id) {
        
        // p_status :
        // 0 = pending
        // 1 = sent
        // 2 = failed
        // 3 = delivered


        $data = sendsms_get_sms($smslog_id);
        $uid = $data['uid'];
        $p_status = $data['p_status'];

        if ($sent_confirm) {
            //Message Sent POST
            if ($sent_confirm == 1) {
                $p_status = 1;
            } elseif ($sent_confirm == 0) {
                $p_status = 2;
            }

        } elseif ($delivery_receipt) {
            //Delivery Receipt
            //Not used with DIDs; todo: add for short code delivery receipt support
        }

        dlr($smslog_id, $uid, $p_status);

    } elseif ($incoming) {
        $sms_datetime = urldecode($requests['ResponseReceiveDate']);
        $sms_sender = $requests['FromPhoneNumber'];
        $message = $requests['Message'];
        $sms_receiver = $requests['ToPhoneNumber'];
        $smsc = $requests['smsc'];

        if ($message) {
            logger_print("incoming smsc:" . $smsc . "  message_id:" . $remote_smslog_id . " s:" . $sms_sender . " d:" . $sms_receiver, 2, "cdyne callback");
            recvsms($sms_datetime, $sms_sender, $message, $sms_receiver, $smsc);
        }
    }
}

?>
