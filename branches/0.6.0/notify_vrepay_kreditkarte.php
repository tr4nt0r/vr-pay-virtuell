<?php
/**
 * Callback für Kreditkartenzahlungen
 * 
 * @version     $Id$
 * 
 * @package     xt-commerce
 * @subpackage	vr-pay
 * @copyright   (c) 2010 Manfred Dennerlein. All rights reserved.
 * @license     GNU/GPL, see LICENSE.txt
 * @author		Manfred Dennerlein <manni@zapto.de>
 * 
 * based on:
 * @copyright	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * @copyright	(c) 2002-2003 osCommerce; www.oscommerce.com 
 * @copyright	(c) 2003 nextcommerce; www.nextcommerce.org
 * @copyright	(c) 2007 GAD eG
 * @copyright	(c) 2009 Netzkollektiv / Cardprocess
 */

include ('includes/application_top.php');
require (DIR_WS_MODULES . 'payment/vrepay_kreditkarte.php');

$rc = "STATUS=SUCCESS";
$vrepay = new vrepay_kreditkarte();

// response from vrepay e.g.: ZAHLART=KAUFEN&REFERENZNR=95&ANTWGEHEIMNIS=bitte+angeben&RMSG=Transaktion+erfolgreich&BETRAG=883&STATUS=GEKAUFT&TSAID=77E0B4E9&ZEITPUNKT=2007-03-19+12%3A10%3A51.000000&HAENDLERNR=0000100001
// write epay - LogFile
$vrepay->epay_log( "notify_vrepay_kreditkarte getting POST�s -> " . print_r($_POST, TRUE) );

$statusNotify = strtoupper($_POST['STATUS']);
$orderId = $_POST['REFERENZNR'];
// orderId without prefix
$orderId = preg_replace('/'.substr(MODULE_PAYMENT_VREPAY_KREDITKARTE_ORDERPREFIX, 0, 5).'/', '', $orderId);
$amount = $_POST['BETRAG'];
$paytype = $_POST['ZAHLART'];
$rmsg = $_POST['RMSG'];

$vrepay->epay_log( "notify_vrepay_kreditkarte orderId -> " . print_r($orderId, TRUE) );

// test, if md5 sum is correct
$md5_in = $_POST['ANTWGEHEIMNIS'];
$md5_new = md5($amount.$orderId.$paytype.MODULE_PAYMENT_VREPAY_KREDITKARTE_ANTWGEHEIMNIS);

// check for message manipulation
if ($md5_in !== $md5_new) {
	// WARNING YOUR SECRET SEEMS NOT TO BE SAVE !!!
	$vrepay->epay_log( ("VREPAY MESSAGE MANUPULATION - COMPUTE MD5 FAILED, PLEASE CHANGE YOUR SECRET !!! -> " . print_r($md5_in . " !== " . $md5_new, TRUE) ), true );
	$vrepay->epay_log( "notify_vrepay_kreditkarte sending result -> " . print_r($rc, TRUE) );
	echo $rc;
	return false;
}

// check transaction status from vrepay server
switch ( $statusNotify ) {

	case "RESERVIERT": 

		$vrepay->epay_log( ("notify_vrepay_kreditkarte regular payment status -> " . print_r('STATUS: ' . $statusNotify . ' - ' . $rmsg, TRUE)), true );	
		$vrepay->update_order_status($orderId, $vrepay->statusSuccess);
		$vrepay->update_order_status_history($orderId, $vrepay->statusSuccess, 'STATUS: ' . $statusNotify . ' - ' . $rmsg );
		break;
	    
	case "GEKAUFT":

		$vrepay->epay_log( ("notify_vrepay_kreditkarte regular payment status -> " . print_r('STATUS: ' . $statusNotify . ' - ' . $rmsg, TRUE)), true );
		$vrepay->update_order_status($orderId, $vrepay->statusSuccess);
		$vrepay->update_order_status_history($orderId, $vrepay->statusSuccess, 'STATUS: ' . $statusNotify . ' - ' . $rmsg );
		break;
	
	case "ABGELEHNT":

		$vrepay->epay_log( ("notify_vrepay_kreditkarte regular payment status -> " . print_r('STATUS: ' . $statusNotify . ' - ' . $rmsg, TRUE)), true );	
		$vrepay->update_order_status($orderId, $vrepay->statusFailed);
		$vrepay->update_order_status_history($orderId, $vrepay->statusFailed, 'STATUS: ' . $statusNotify . ' - ' . $rmsg );
		break;
	
	default:
		
		$vrepay->epay_log( ("notify_vrepay_kreditkarte unknown status -> " . print_r('STATUS: ' . $statusNotify . ' - ' . $rmsg, TRUE)), true );
		break;
}

// sending return message
$vrepay->epay_log( "notify_vrepay_kreditkarte sending result -> " . print_r($rc, TRUE) );
echo $rc;
return true;

?>
