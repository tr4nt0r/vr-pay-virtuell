<?php
/*
   $Id: vrepay_dialog.php,v 1.2 2007/03/27 $
   
   Path: xtc/lang/english/modules/payment/

   osCommerce, Open Source E-Commerce Solutions
   http://www.oscommerce.com

   Copyright (c) 2002 osCommerce

   Released under the GNU General Public License

   -----------------------------------------------------------------------------------------
   Third Party contribution:

   Copyright (c) 2007 GAD eG

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

	// MODULE_PAYMENT_VREPAY_ELV_TEXT_TITLE
	define('MODULE_PAYMENT_VREPAY_ELV_TEXT_TITLE', 'VR-Pay virtuell Paymentmodule for &quot;Creditcard&quot;, &quot;electronic cash&quot; and &quot;giropay&copy;&quot;');
	// MODULE_PAYMENT_VREPAY_ELV_TEXT_MODUL_TITLE
	define('MODULE_PAYMENT_VREPAY_ELV_TEXT_MODUL_TITLE', 'VR-Pay virtuell Paymentmodule for &quot;Creditcard&quot;, &quot;electronic cash&quot; and &quot;giropay&copy;&quot;');
	// MODULE_PAYMENT_VREPAY_ELV_TEXT_DESCRIPTION
	define('MODULE_PAYMENT_VREPAY_ELV_TEXT_DESCRIPTION', '<img src="images/icons/vrepay/VR.gif" width="30" height="20" vspace="2px" hspace="2px" align="middle" >VR-Pay virtuell<br>Paymentmodule for &quot;Creditcard&quot;, &quot;electronic cash&quot; or &quot;on demand bank transfer by giropay&copy;&quot;.<br><br>Please contact your next banking institut (Volks- und Raiffeisenbanken).<br>');
	// MODULE_PAYMENT_VREPAY_ELV_STATUS
	define('MODULE_PAYMENT_VREPAY_ELV_STATUS_TITLE', 'VR-Pay virtuell Modul is activated');
	define('MODULE_PAYMENT_VREPAY_ELV_STATUS_DESC', 'Do you want to process transactions over VR-Pay virtuell ?');
	// MODULE_PAYMENT_VREPAY_ELV_SERVER
	define('MODULE_PAYMENT_VREPAY_ELV_SERVER_TITLE', 'VR-Pay virtuell Server');
	define('MODULE_PAYMENT_VREPAY_ELV_SERVER_DESC', 'The processing VR-Pay virtuell Server. e.g.: payinte.vr-epay.de/pbr/transaktion');
	// MODULE_PAYMENT_VREPAY_ELV_INSTITUT
	define('MODULE_PAYMENT_VREPAY_ELV_INSTITUT_TITLE', 'VR-Pay virtuell Merchantno.');
	define('MODULE_PAYMENT_VREPAY_ELV_INSTITUT_DESC', 'Your merchant no. can be obtained by your banking institution.');
	// MODULE_PAYMENT_VREPAY_ELV_PASSWORT
	define('MODULE_PAYMENT_VREPAY_ELV_PASSWORT_TITLE', 'VR-Pay virtuell Password');
	define('MODULE_PAYMENT_VREPAY_ELV_PASSWORT_DESC', 'Your password will be transmitted by email or letter.');
	// MODULE_PAYMENT_VREPAY_ELV_ZAHLART
	define('MODULE_PAYMENT_VREPAY_ELV_ZAHLART_TITLE', 'Payment type');
	define('MODULE_PAYMENT_VREPAY_ELV_ZAHLART_DESC', 'The Payment type defines the type of transaction, e.g. sale(KAUFEN) or authorisation(RESERVIEREN).');
	// MODULE_PAYMENT_VREPAY_ELV_URLAGB
	define('MODULE_PAYMENT_VREPAY_ELV_URLAGB_TITLE', 'Your conditions');
	define('MODULE_PAYMENT_VREPAY_ELV_URLAGB_DESC', 'The link to your conditions.');
	// MODULE_PAYMENT_VREPAY_ELV_SERVICENAME
	define('MODULE_PAYMENT_VREPAY_ELV_SERVICENAME_TITLE', 'Service in VR-Pay virtuell');
	define('MODULE_PAYMENT_VREPAY_ELV_SERVICENAME_DESC', 'The communication channel and interface type in VR-Pay virtuell.');
	// MODULE_PAYMENT_VREPAY_ELV_ORDERPREFIX
	define('MODULE_PAYMENT_VREPAY_ELV_ORDERPREFIX_TITLE', 'Order Prefix (max. 5 char)');
	define('MODULE_PAYMENT_VREPAY_ELV_ORDERPREFIX_DESC', 'This prefix will be prepended to your order no.');
	// MODULE_PAYMENT_VREPAY_ELV_ANTWGEHEIMNIS
	define('MODULE_PAYMENT_VREPAY_ELV_ANTWGEHEIMNIS_TITLE', 'Password for notification');
	define('MODULE_PAYMENT_VREPAY_ELV_ANTWGEHEIMNIS_DESC', 'To avoid manipiulations all transactions will be confirmed by the VR-Pay virtuell notification daemon.');
	// MODULE_PAYMENT_VREPAY_ELV_VERWENDUNG2
	define('MODULE_PAYMENT_VREPAY_ELV_VERWENDUNG2_TITLE', 'Additional Information 2');
	define('MODULE_PAYMENT_VREPAY_ELV_VERWENDUNG2_DESC', 'Additional information for electronic cash.');
	// MODULE_PAYMENT_VREPAY_ELV_IFRAMEFLAG
	define('MODULE_PAYMENT_VREPAY_ELV_IFRAMEFLAG_TITLE', 'Use IFrame true /false');
	define('MODULE_PAYMENT_VREPAY_ELV_IFRAMEFLAG_DESC', '');
	// MODULE_PAYMENT_VREPAY_ELV_IFRAME
	define('MODULE_PAYMENT_VREPAY_ELV_IFRAME_TITLE', 'Layout iFrame');
	define('MODULE_PAYMENT_VREPAY_ELV_IFRAME_DESC', 'The HTML-Tag for calling the VR-Pay virtuell server inside an iFrame.');
	// MODULE_PAYMENT_VREPAY_ELV_LOG
	define('MODULE_PAYMENT_VREPAY_ELV_LOG_TITLE', 'Modul logfile');
	define('MODULE_PAYMENT_VREPAY_ELV_LOG_DESC', 'Path to the logfile from the VR-Pay virtuell Paymentmodule.');
	// MODULE_PAYMENT_VREPAY_ELV_LOGGING
	define('MODULE_PAYMENT_VREPAY_ELV_LOGGING_TITLE', 'Logging true /false');
	define('MODULE_PAYMENT_VREPAY_ELV_LOGGING_DESC', 'Debugging flag for VR-Pay virtuell logfile.');
	// MODULE_PAYMENT_VREPAY_ELV_ZONE
	define('MODULE_PAYMENT_VREPAY_ELV_ZONE_TITLE', 'Payment Zone');
	define('MODULE_PAYMENT_VREPAY_ELV_ZONE_DESC', 'Activate the VR-ePay module only for the selected zone.');
	// MODULE_PAYMENT_VREPAY_ELV_ALLOWED
	define('MODULE_PAYMENT_VREPAY_ELV_ALLOWED_TITLE', 'Allowed zones');
	define('MODULE_PAYMENT_VREPAY_ELV_ALLOWED_DESC', 'Please enter the zones separately which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');
	// MODULE_PAYMENT_VREPAY_ELV_SORT_ORDER
	define('MODULE_PAYMENT_VREPAY_ELV_SORT_ORDER_TITLE', 'Sort order of display.');
	define('MODULE_PAYMENT_VREPAY_ELV_SORT_ORDER_DESC', 'Sort order of display. Lowest is displayed first.');

	// MODULE_PAYMENT_VREPAY_ELV_TEXT_ERR01
	define('MODULE_PAYMENT_VREPAY_ELV_TEXT_ERR01', 'Your transaction failed. Please choose an alternative payment method.');
	// MODULE_PAYMENT_VREPAY_ELV_TEXT_ERR02
	define('MODULE_PAYMENT_VREPAY_ELV_TEXT_ERR02', 'The Paymentsystem is temporarily not available. Please choose an alternative payment method.');
	// MODULE_PAYMENT_VREPAY_ELV_TEXT_ERR03
	define('MODULE_PAYMENT_VREPAY_ELV_TEXT_ERR03', 'The Paymentsystem is temporarily not available.');

	// MODULE_PAYMENT_VREPAY_ELV_TEXT_MSG01
//	define('MODULE_PAYMENT_VREPAY_ELV_TEXT_MSG01', '<b>Please process your transaction to finish your order.</b>');

?>
