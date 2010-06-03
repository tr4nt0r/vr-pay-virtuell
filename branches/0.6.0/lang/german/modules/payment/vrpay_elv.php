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


	define('MODULE_PAYMENT_VRPAY_ELV_TEXT_TITLE', 'Lastschrift');
	define('MODULE_PAYMENT_VRPAY_ELV_TEXT_DESC', xtc_image(DIR_WS_ICONS . 'vrepay/VR.gif', 'Kreditkarte', '', '', ' align="middle"' ) . 'VR-Pay virtuell Lastschrift<br>Zahlungsmodul f&uuml;r Lastschrift<br><br>Bitte wenden Sie sich an Ihre Volks- und Raiffeisenbank.<br>');
	define('MODULE_PAYMENT_VRPAY_ELV_TEXT_INFO', '');

	define('MODULE_PAYMENT_VRPAY_ELV_ZONE_TITLE', 'Zahlungszone');
	define('MODULE_PAYMENT_VRPAY_ELV_ZONE_DESC', 'Wenn eine Zone ausgew&auml;hlt ist, gilt die Zahlungsmethode nur f&uuml;r diese Zone.');
	
	define('MODULE_PAYMENT_VRPAY_ELV_ALLOWED_TITLE', 'Erlaubte Zonen');
	define('MODULE_PAYMENT_VRPAY_ELV_ALLOWED_DESC', 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');

	define('MODULE_PAYMENT_VRPAY_ELV_SORT_ORDER_TITLE', 'Anzeigereihenfolge');
	define('MODULE_PAYMENT_VRPAY_ELV_SORT_ORDER_DESC', 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.');

	define('MODULE_PAYMENT_VRPAY_ELV_STATUS_TITLE', 'VR-Pay virtuell Lastschrift Modul aktivieren');
	define('MODULE_PAYMENT_VRPAY_ELV_STATUS_DESC', 'M&ouml;chten Sie Zahlungen per Lastschrift &uuml;ber VR-Pay virtuell akzeptieren?');

	define('MODULE_PAYMENT_VRPAY_ELV_GATEWAY_TITLE', 'VR-Pay virtuell Gateway');
	define('MODULE_PAYMENT_VRPAY_ELV_GATEWAY_DESC', 'VR-Pay virtuell im Live oder im Test Modus betreiben');

	define('MODULE_PAYMENT_VRPAY_ELV_HAENDLERNR_TITLE', 'VR-Pay virtuell Partner-Nr');
	define('MODULE_PAYMENT_VRPAY_ELV_HAENDLERNR_DESC', 'Die Partner-Nr erhalten Sie von Ihrer Bank.');

	define('MODULE_PAYMENT_VRPAY_ELV_PASSWORT_TITLE', 'VR-Pay virtuell Passwort');
	define('MODULE_PAYMENT_VRPAY_ELV_PASSWORT_DESC', 'Das Passwort wird Ihnen per Pinbrief oder email mitgeteilt.');

	define('MODULE_PAYMENT_VRPAY_ELV_ZAHLART_TITLE', 'Zahlart');
	define('MODULE_PAYMENT_VRPAY_ELV_ZAHLART_DESC', 'Zahlart gibt an, wie die Transaktionen von VR-Pay virtuell verarbeitet werden.');

	define('MODULE_PAYMENT_VRPAY_ELV_URLAGB_TITLE', 'Content Seite AGB');
	define('MODULE_PAYMENT_VRPAY_ELV_URLAGB_DESC', 'Content Seite f&uuml;r die Verlinkung Ihrer allgemeinen Gesch&auml;ftsbedingungen im VR-Pay virtuell Dialogfenster');

	define('MODULE_PAYMENT_VRPAY_ELV_URLCVC_TITLE', 'Content Seite CVC');
	define('MODULE_PAYMENT_VRPAY_ELV_URLCVC_DESC', 'Content Seite f&uuml;r die Verlinkung einer Hilfe zur Kartenpr&uuml;fnummer im VR-Pay virtuell Dialogfenster (optional)');
		
	define('MODULE_PAYMENT_VRPAY_ELV_REFERENCEPREFIX_TITLE', 'Pr&auml;fix f&uuml;r Referenz');
	define('MODULE_PAYMENT_VRPAY_ELV_REFERENCEPREFIX_DESC', 'Standardm&auml;&szlig;ig wird die Bestellnummer als Referenz an VR-Pay &uuml;bergeben. Ist die Bestellnummer bei VR-Pay nicht einmalig, z.B. wenn Zahlungen mehrerer Shopsysteme abgewickelt werden, kann ein Pr&auml;fix verwendet werden. Die Referenz darf insg. 20 Zeichen nicht &uuml;berschreiten.');

	define('MODULE_PAYMENT_VRPAY_ELV_ANTWGEHEIMNIS_TITLE', 'Geheimwort f&uuml;r Callback');
	define('MODULE_PAYMENT_VRPAY_ELV_ANTWGEHEIMNIS_DESC', 'Alle Transaktionen werden &uuml;ber einen Benachrichtigungsdienst mit dem endg&uuml;ltigen Status versehen, um Manipulationen auszuschliessen werden Benachrichtigungen mit diesem Geheimwort signiert.');

	define('MODULE_PAYMENT_VRPAY_ELV_VERWENDUNG1_TITLE', 'Verwendungszweck 1');
	define('MODULE_PAYMENT_VRPAY_ELV_VERWENDUNG1_DESC', 'Verwendungszweckzeile 1');

	define('MODULE_PAYMENT_VRPAY_ELV_VERWENDUNG2_TITLE', 'Verwendungszweck 2');
	define('MODULE_PAYMENT_VRPAY_ELV_VERWENDUNG2_DESC', 'Verwendungszweckzeile 2');

	define('MODULE_PAYMENT_VRPAY_ELV_ORDER_STATUS_ID_TITLE' , 'Bestellstatus festlegen');
	define('MODULE_PAYMENT_VRPAY_ELV_ORDER_STATUS_ID_DESC' , 'Bestellungen, welche mit diesem Modul gemacht werden, auf diesen Status setzen');
	
	define('MODULE_PAYMENT_VRPAY_ELV_TMP_STATUS_ID_TITLE','Tempor&auml;er Bestellstatus');
	define('MODULE_PAYMENT_VRPAY_ELV_TMP_STATUS_ID_DESC','Bestellstatus f&auml;r noch nicht abgeschlossene Transaktionen');
	
	define('MODULE_PAYMENT_VRPAY_ELV_TEXT_FAILED', 'Ihre Transaktion ist fehlgeschlagen. Bitte w&auml;hlen Sie eine alternative Bezahlmethode.');
	define('MODULE_PAYMENT_VRPAY_ELV_TEXT_CANCELED', 'Transaktion abgebrochen. Bitte w&auml;hlen Sie eine alternative Bezahlmethode.');
	define('MODULE_PAYMENT_VRPAY_ELV_TEXT_GATEWAY_UNAVAILABLE', 'Bezahlsystem vor&uuml;bergehend nicht erreichbar. Bitte w&auml;hlen Sie eine alternative Bezahlmethode.');	
	define('MODULE_PAYMENT_VRPAY_ELV_TEXT_GATEWAY_AUTHENTICATION', 'Anmeldung am Bezahlsystem fehlgeschlagen. Bitte w&auml;hlen Sie eine alternative Bezahlmethode.');
	define('MODULE_PAYMENT_VRPAY_ELV_TEXT_UNKNOWN_ERROR', 'Ein unbekannter Fehler ist aufgetreten. Bitte w&auml;hlen Sie eine alternative Bezahlmethode.');
	define('MODULE_PAYMENT_VRPAY_ELV_TEXT_CURRENCY_NOT_SUPPORTED', 'Die W&auml;hrung wird von dieser Bezahlmethode nicht unterst&uuml;tzt. Bitte &auml;ndern Sie die W&auml;hrung oder w&auml;hlen Sie eine alternative Bezahlmethode.');
	
	
?>