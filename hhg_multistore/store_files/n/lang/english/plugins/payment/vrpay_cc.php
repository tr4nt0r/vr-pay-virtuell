<?php
/**
 * Modul für Kreditkartenzahlungen
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


	define('MODULE_PAYMENT_VRPAY_CC_TEXT_TITLE', 'Credit Card');
	define('MODULE_PAYMENT_VRPAY_CC_TEXT_DESC', xtc_image(DIR_WS_ICONS . 'vrepay/VR.gif', 'Credit Card', '', '', ' align="middle"' ) . 'VR-Pay virtuell Credit card<br>Payment module for credit card<br><br>Please contact your Volks- und Raiffeisenbank.<br>');
	define('MODULE_PAYMENT_VRPAY_CC_TEXT_INFO', '');

	define('MODULE_PAYMENT_VRPAY_CC_ZONE_TITLE', 'Payment Zone');
	define('MODULE_PAYMENT_VRPAY_CC_ZONE_DESC', 'If a zone is selected, only enable this payment method for that zone.');
	
	define('MODULE_PAYMENT_VRPAY_CC_ALLOWED_TITLE', 'Allowed zones');
	define('MODULE_PAYMENT_VRPAY_CC_ALLOWED_DESC', 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');

	define('MODULE_PAYMENT_VRPAY_CC_SORT_ORDER_TITLE', 'Sort order of display.');
	define('MODULE_PAYMENT_VRPAY_CC_SORT_ORDER_DESC', 'Sort order of display. Lowest is displayed first.');

	define('MODULE_PAYMENT_VRPAY_CC_STATUS_TITLE', 'Enable VR-Pay virtuell Credit Card Module');
	define('MODULE_PAYMENT_VRPAY_CC_STATUS_DESC', 'Do you want to accept credit card payments with VR-Pay virtuell?');

	if(!defined('MODULE_PAYMENT_VRPAY_SHARED_GATEWAY_TITLE')) {

		define('MODULE_PAYMENT_VRPAY_SHARED_GATEWAY_TITLE', 'VR-Pay virtuell Gateway');
		define('MODULE_PAYMENT_VRPAY_SHARED_GATEWAY_DESC', 'Operate VR-Pay virtuell in live or test mode');

		define('MODULE_PAYMENT_VRPAY_SHARED_HAENDLERNR_TITLE', 'VR-Pay virtuell Partner-No.');
		define('MODULE_PAYMENT_VRPAY_SHARED_HAENDLERNR_DESC', 'The Partner-No. you will receive from your bank.');

		define('MODULE_PAYMENT_VRPAY_SHARED_PASSWORT_TITLE', 'VR-Pay virtuell password');
		define('MODULE_PAYMENT_VRPAY_SHARED_PASSWORT_DESC', 'Password is sent to you by email or post.');

		define('MODULE_PAYMENT_VRPAY_SHARED_REFERENCEPREFIX_TITLE', 'Prefix for Referencenumber');
		define('MODULE_PAYMENT_VRPAY_SHARED_REFERENCEPREFIX_DESC', 'By default, the order number is passed as reference to VR Pay. If the order number is no unique e.g. if payments are handled from several shops, you should use a prefix. The reference number must no exceed a total of 20 characters.');

		define('MODULE_PAYMENT_VRPAY_SHARED_ANTWGEHEIMNIS_TITLE', 'Secret for Callback');
		define('MODULE_PAYMENT_VRPAY_SHARED_ANTWGEHEIMNIS_DESC', 'All transactions will be provided with a notification service with the final status. To prevent tampering notifications are signed with this secret.');
		
		define('MODULE_PAYMENT_VRPAY_SHARED_URLAGB_TITLE', 'Content Page Conditions');
		define('MODULE_PAYMENT_VRPAY_SHARED_URLAGB_DESC', 'content page for conditions that is linked from VR-Pay virtuell dialog');

		define('MODULE_PAYMENT_VRPAY_SHARED_DEBUG_TITLE', 'Debug Messages');
		define('MODULE_PAYMENT_VRPAY_SHARED_DEBUG_DESC', 'Send debug messages to this e-mail address in case of errors.');

		define('MODULE_PAYMENT_VRPAY_SHARED_SUBMITCART_TITLE', 'Submit Cart');
		define('MODULE_PAYMENT_VRPAY_SHARED_SUBMITCART_DESC', 'Submit Cart Contents to VR Pay.');
	}
	
	define('MODULE_PAYMENT_VRPAY_CC_ZAHLART_TITLE', 'Payment method');
	define('MODULE_PAYMENT_VRPAY_CC_ZAHLART_DESC', 'Type of transaction that is communicated to VR-Pay virtuell.');

	define('MODULE_PAYMENT_VRPAY_CC_URLCVC_TITLE', 'Content Page CVC');
	define('MODULE_PAYMENT_VRPAY_CC_URLCVC_DESC', 'Content Page for help on card verification number that is linked from VR-Pay virtuell dialog (optional)');

	define('MODULE_PAYMENT_VRPAY_CC_VERWENDUNG1_TITLE', 'Purpose 1');
	define('MODULE_PAYMENT_VRPAY_CC_VERWENDUNG1_DESC', 'Purpose line 1');

	define('MODULE_PAYMENT_VRPAY_CC_VERWENDUNG2_TITLE', 'Purpose 2');
	define('MODULE_PAYMENT_VRPAY_CC_VERWENDUNG2_DESC', 'Purpose line 2');

	define('MODULE_PAYMENT_VRPAY_CC_INFOTEXT_TITLE', 'Infotext');
	define('MODULE_PAYMENT_VRPAY_CC_INFOTEXT_DESC', 'Infotext (max. 1024 Chars).');
	

	define('MODULE_PAYMENT_VRPAY_CC_ORDER_STATUS_ID_TITLE' , 'Set Order Status');
	define('MODULE_PAYMENT_VRPAY_CC_ORDER_STATUS_ID_DESC' , 'Set the status of orders made with this payment module to this value');

	define('MODULE_PAYMENT_VRPAY_CC_ORDER_FAILED_STATUS_ID_TITLE' , 'Set Order Status Failed');
	define('MODULE_PAYMENT_VRPAY_CC_ORDER_FAILED_STATUS_ID_DESC' , 'Orders for which payment is declined, fail for other technical reasons or are canceled by the customer, will be set to this status.');
	
	define('MODULE_PAYMENT_VRPAY_CC_TMP_STATUS_ID_TITLE','Pending Order Status');
	define('MODULE_PAYMENT_VRPAY_CC_TMP_STATUS_ID_DESC','Set the status for pending transactions');
	
	define('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_VISA_TITLE', 'Accept VISA cards');
	define('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_VISA_DESC', '');
	
	define('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_ECMC_TITLE', 'Accept Master Card cards');
	define('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_ECMC_DESC', '');
	
	define('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_AMEX_TITLE', 'Accept AMERICAN EXPRESS cards');
	define('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_AMEX_DESC', '');
	
	define('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_DINERS_TITLE', 'Accept Diners Club cards');
	define('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_DINERS_DESC', '');
	
	define('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_JCB_TITLE', 'Accept JCB cards');
	define('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_JCB_DESC', '');
	
	define('MODULE_PAYMENT_VRPAY_CC_SHOW_VRPAY_TITLE', 'VR Pay virtuell Logo');
	define('MODULE_PAYMENT_VRPAY_CC_SHOW_VRPAY_DESC', 'Should the VR Pay virtuell Logo be shown on the payment page next to the logos of the payment method?');
	
	define('MODULE_PAYMENT_VRPAY_CC_TEXT_FAILED', 'Your transaction failed. Please choose a different payment method.');
	define('MODULE_PAYMENT_VRPAY_CC_TEXT_CANCELED', 'Transaction canceled. Please choose a different payment method.');
	define('MODULE_PAYMENT_VRPAY_CC_TEXT_GATEWAY_UNAVAILABLE', 'Payment system temporarily unavailable. Please choose a different payment method.');	
	define('MODULE_PAYMENT_VRPAY_CC_TEXT_GATEWAY_AUTHENTICATION', 'Authentication with payment system failed. Please choose a different payment method.');
	define('MODULE_PAYMENT_VRPAY_CC_TEXT_UNKNOWN_ERROR', 'Unknown error. Please choose a different payment method.');
	define('MODULE_PAYMENT_VRPAY_CC_TEXT_CURRENCY_NOT_SUPPORTED', 'The currency is not supported by the payment method. Please change the currency or choose a different payment method.');
	
	define('TEXT_VRPAY_CC_PAYMENT', 'Payment Status');
	define('TEXT_VRPAY_CC_SICHERHEIT', 'Security features');
	define('TEXT_VRPAY_CC_BETRAG', 'Amount:');
	
?>