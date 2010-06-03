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


	define('MODULE_PAYMENT_VRPAY_GIROPAY_TEXT_TITLE', 'Giropay');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_TEXT_DESC', 'VR-Pay virtuell Giropay<br>Payment module for Giropay<br><br>Please contact your Volks- und Raiffeisenbank.<br>');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_TEXT_INFO', '');

	define('MODULE_PAYMENT_VRPAY_GIROPAY_ZONE_TITLE', 'Payment Zone');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_ZONE_DESC', 'If a zone is selected, only enable this payment method for that zone.');
	
	define('MODULE_PAYMENT_VRPAY_GIROPAY_ALLOWED_TITLE', 'Allowed zones');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_ALLOWED_DESC', 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');

	define('MODULE_PAYMENT_VRPAY_GIROPAY_SORT_ORDER_TITLE', 'Sort order of display.');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_SORT_ORDER_DESC', 'Sort order of display. Lowest is displayed first.');

	define('MODULE_PAYMENT_VRPAY_GIROPAY_STATUS_TITLE', 'Enable VR-Pay virtuell Giropay Module');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_STATUS_DESC', 'Do you want to accept Giropay payments with VR-Pay virtuell?');

	define('MODULE_PAYMENT_VRPAY_GIROPAY_GATEWAY_TITLE', 'VR-Pay virtuell Gateway');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_GATEWAY_DESC', 'Operate VR-Pay virtuell in live or test mode');

	define('MODULE_PAYMENT_VRPAY_GIROPAY_HAENDLERNR_TITLE', 'VR-Pay virtuell Partner-No.');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_HAENDLERNR_DESC', 'The Partner-No. you will receive from your bank.');

	define('MODULE_PAYMENT_VRPAY_GIROPAY_PASSWORT_TITLE', 'VR-Pay virtuell password');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_PASSWORT_DESC', 'Password is sent to you by email or post.');

	define('MODULE_PAYMENT_VRPAY_GIROPAY_URLAGB_TITLE', 'Content Page Conditions');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_URLAGB_DESC', 'content page for conditions that is linked from VR-Pay virtuell dialog');

	define('MODULE_PAYMENT_VRPAY_GIROPAY_URLCVC_TITLE', 'Content Page CVC');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_URLCVC_DESC', 'Content Page for help on card verification number that is linked from VR-Pay virtuell dialog (optional)');
		
	define('MODULE_PAYMENT_VRPAY_GIROPAY_REFERENCEPREFIX_TITLE', 'Prefix for Referencenumber');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_REFERENCEPREFIX_DESC', 'By default, the order number is passed as reference to VR Pay. If the order number is no unique e.g. if payments are handled from several shops, you should use a prefix. The reference number must no exceed a total of 20 characters.');

	define('MODULE_PAYMENT_VRPAY_GIROPAY_ANTWGEHEIMNIS_TITLE', 'Secret for Callback');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_ANTWGEHEIMNIS_DESC', 'All transactions will be provided with a notification service with the final status. To prevent tampering notifications are signed with this secret.');

	define('MODULE_PAYMENT_VRPAY_GIROPAY_VERWENDUNG1_TITLE', 'Purpose 1');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_VERWENDUNG1_DESC', 'Purpose line 1');

	define('MODULE_PAYMENT_VRPAY_GIROPAY_VERWENDUNG2_TITLE', 'Purpose 2');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_VERWENDUNG2_DESC', 'Purpose line 2');

	define('MODULE_PAYMENT_VRPAY_GIROPAY_ORDER_STATUS_ID_TITLE' , 'Set Order Status');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_ORDER_STATUS_ID_DESC' , 'Set the status of orders made with this payment module to this value');
	
	define('MODULE_PAYMENT_VRPAY_GIROPAY_TMP_STATUS_ID_TITLE','Pending Order Status');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_TMP_STATUS_ID_DESC','Set the status for pending transactions');

	define('MODULE_PAYMENT_VRPAY_GIROPAY_TEXT_FAILED', 'Your transaction failed. Please choose a different payment method.');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_TEXT_CANCELED', 'Transaction canceled. Please choose a different payment method.');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_TEXT_GATEWAY_UNAVAILABLE', 'Payment system temporarily unavailable. Please choose a different payment method.');	
	define('MODULE_PAYMENT_VRPAY_GIROPAY_TEXT_GATEWAY_AUTHENTICATION', 'Authentication with payment system failed. Please choose a different payment method.');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_TEXT_UNKNOWN_ERROR', 'Unknown error. Please choose a different payment method.');
	define('MODULE_PAYMENT_VRPAY_GIROPAY_TEXT_CURRENCY_NOT_SUPPORTED', 'The currency is not supported by the payment method. Please change the currency or choose a different payment method.');
	
	
?>