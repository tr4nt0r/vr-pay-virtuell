<?php
/* -----------------------------------------------------------------------------------------
   $Id: notify_vrepay_dialog.php v1.2 2007/11/20 fbi $   
   
   Path: xtc/

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce; www.oscommerce.com 
   (c) 2003	 nextcommerce; www.nextcommerce.org

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   Copyright (c) 2009 Netzkollektiv / Cardprocess

   Commercial License
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');
require (DIR_WS_MODULES . 'payment/vrepay_elv.php');

$rc = "STATUS=SUCCESS";
$vrepay = new vrepay_elv();

// response from vrepay e.g.: ZAHLART=KAUFEN&REFERENZNR=95&ANTWGEHEIMNIS=bitte+angeben&RMSG=Transaktion+erfolgreich&BETRAG=883&STATUS=GEKAUFT&TSAID=77E0B4E9&ZEITPUNKT=2007-03-19+12%3A10%3A51.000000&HAENDLERNR=0000100001
// write epay - LogFile
$vrepay->epay_log( "notify_vrepay_elv getting POST´s -> " . print_r($_POST, TRUE) );

$statusNotify = strtoupper($_POST['STATUS']);
$orderId = $_POST['REFERENZNR'];
// orderId without prefix
$orderId = preg_replace('/'.substr(MODULE_PAYMENT_VREPAY_ELV_ORDERPREFIX, 0, 5).'/', '', $orderId);
$amount = $_POST['BETRAG'];
$paytype = $_POST['ZAHLART'];
$rmsg = $_POST['RMSG'];

$vrepay->epay_log( "notify_vrepay_elv orderId -> " . print_r($orderId, TRUE) );

// test, if md5 sum is correct
$md5_in = $_POST['ANTWGEHEIMNIS'];
$md5_new = md5($amount.$orderId.$paytype.MODULE_PAYMENT_VREPAY_ELV_ANTWGEHEIMNIS);

// check for message manipulation
if ($md5_in !== $md5_new) {
	// WARNING YOUR SECRET SEEMS NOT TO BE SAVE !!!
	$vrepay->epay_log( ("VREPAY MESSAGE MANUPULATION - COMPUTE MD5 FAILED, PLEASE CHANGE YOUR SECRET !!! -> " . print_r($md5_in . " !== " . $md5_new, TRUE) ), true );
	$vrepay->epay_log( "notify_vrepay_elv sending result -> " . print_r($rc, TRUE) );
	echo $rc;
	return false;
}

// check transaction status from vrepay server
switch ( $statusNotify ) {

	case "RESERVIERT": 

		$vrepay->epay_log( ("notify_vrepay_elv regular payment status -> " . print_r('STATUS: ' . $statusNotify . ' - ' . $rmsg, TRUE)), true );	
		$vrepay->update_order_status($orderId, $vrepay->statusSuccess);
		$vrepay->update_order_status_history($orderId, $vrepay->statusSuccess, 'STATUS: ' . $statusNotify . ' - ' . $rmsg );
		break;
	    
	case "GEKAUFT":

		$vrepay->epay_log( ("notify_vrepay_elv regular payment status -> " . print_r('STATUS: ' . $statusNotify . ' - ' . $rmsg, TRUE)), true );
		$vrepay->update_order_status($orderId, $vrepay->statusSuccess);
		$vrepay->update_order_status_history($orderId, $vrepay->statusSuccess, 'STATUS: ' . $statusNotify . ' - ' . $rmsg );
		break;
	
	case "ABGELEHNT":

		$vrepay->epay_log( ("notify_vrepay_elv regular payment status -> " . print_r('STATUS: ' . $statusNotify . ' - ' . $rmsg, TRUE)), true );	
		$vrepay->update_order_status($orderId, $vrepay->statusFailed);
		$vrepay->update_order_status_history($orderId, $vrepay->statusFailed, 'STATUS: ' . $statusNotify . ' - ' . $rmsg );
		break;
	
	default:
		
		$vrepay->epay_log( ("notify_vrepay_elv unknown status -> " . print_r('STATUS: ' . $statusNotify . ' - ' . $rmsg, TRUE)), true );
		break;
}

// sending return message
$vrepay->epay_log( "notify_vrepay_elv sending result -> " . print_r($rc, TRUE) );
echo $rc;
return true;

?>
