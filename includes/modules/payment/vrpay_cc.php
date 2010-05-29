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

require_once (DIR_WS_CLASSES . 'class.vrpay_checkout.php');

class vrpay_cc extends vrpay_checkout {

	// class constructor
	function vrpay_cc() {

		// module description
		$this->code			= 'vrpay_cc';
		$this->title		= MODULE_PAYMENT_VRPAY_CC_TEXT_TITLE;
		$this->description	= MODULE_PAYMENT_VRPAY_CC_TEXT_DESCRIPTION;
		$this->info			= MODULE_PAYMENT_VRPAY_CC_TEXT_INFO;
		$this->sort_order	= MODULE_PAYMENT_VRPAY_CC_SORT_ORDER;
		$this->enabled     = ((MODULE_PAYMENT_VRPAY_CC_STATUS == 'True') ? true : false);
		
		$this->tmpOrders = true;
		$this->tmpStatus = MODULE_PAYMENT_PAYPAL_TMP_STATUS_ID;
		
		if (is_object($order))
			$this->update_status();
			
				
		$this->HAENDLERNR	= MODULE_PAYMENT_VRPAY_CC_HAENDLERNR;
		$this->PASSWORD		= MODULE_PAYMENT_VRPAY_CC_PASSWORT;
		$this->ORDERPREFIX	= MODULE_PAYMENT_VRPAY_CC_ORDERPREFIX;
		$this->ZAHLART		= MODULE_PAYMENT_VRPAY_CC_ZAHLART;
		$this->ANTWGEHEIMNIS= MODULE_PAYMENT_VRPAY_CC_ANTWGEHEIMNIS;
		$this->VERWENDUNG1	= MODULE_PAYMENT_VRPAY_CC_VERWENDUNG1;
		$this->VERWENDUNG2	= MODULE_PAYMENT_VRPAY_CC_VERWENDUNG2;
	}


	function update_status() {
		global $order;

		if (($this->enabled == true) && ((int) MODULE_PAYMENT_VRPAY_CC_ZONE > 0)) {
			$check_flag = false;
			$check_query = xtc_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".MODULE_PAYMENT_VRPAY_CC_ZONE."' and zone_country_id = '".$order->billing['country']['id']."' order by zone_id");
			while ($check = xtc_db_fetch_array($check_query)) {
				if ($check['zone_id'] < 1) {
					$check_flag = true;
					break;
				}
				elseif ($check['zone_id'] == $order->billing['zone_id']) {
					$check_flag = true;
					break;
				}
			}

			if ($check_flag == false) {
				$this->enabled = false;
			}
		}
	}


	function javascript_validation() {
		return false;
	}

	function selection() {
		return array ('id' => $this->code, 'module' => $this->title, 'description' => $this->info);
	}

	function pre_confirmation_check() {
		return false;
	}

	function confirmation() {
		return false;
	}

	function process_button() {
		return false;
	}

	function before_process() {
		return false;
	}

	function payment_action() {
		global $order, $insert_id, $xtPrice;
		
print_r($order);
		print_r($this->build_post($order, 'CC'));
		die();

		

		


		return false;
	}
//

	// call from checkout_payment with "payment_error=module..."
	function get_error() {

		$this->epay_log( "start function get_error()..." );

		$error = array (
			'title' => stripslashes(urldecode($_GET['FEHLERCODE']) ),
			'error' => stripslashes(urldecode($_GET['FEHLERTEXT']) )
		);

		// error occurred -> vrepay_dialog disabled
		$this->enabled = false;

		return $error;
	}


	function after_process() {
		global $insert_id;
		if ($this->order_status)
		xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$this->order_status."' WHERE orders_id='". xtc_db_input($insert_id)."'");
	}

	/**
	 * Check if Module is active
	 * @return bool
	 */
	function check() {
		if (!isset($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_VRPAY_CC_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	

	/**
	 * Install Module
	 */
	function install() {

		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ZONE', '0', '6', '14', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ALLOWED', '', '6', '0', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_CC_SORT_ORDER', '10', '6', '16', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_GATEWAY', 'LIVE', '6', '2', 'xtc_cfg_select_option(array(\'LIVE\', \'TEST\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_CC_HAENDLERNR', '', '6', '3', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_CC_PASSWORT', '', '6', '4', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ZAHLART', 'RESERVIEREN', '6', '5', 'xtc_cfg_select_option(array(\'RESERVIEREN\', \'KAUFEN\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_CC_URLAGB', 'popup_content.php?coID=3', '6', '7', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ORDERPREFIX', '', '6', '8', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ANTWGEHEIMNIS', '', '6', '8', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_CC_VERWENDUNG2', '". STORE_NAME ."', '6', '10', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_IFRAMEFLAG', 'False', '6', '13', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_CC_IFRAME', 'width=\"750px\" height=\"540px\" frameborder=\"0\"', '6', '11', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_CC_LOG', '/tmp/vrepay_kreditkarte.log', '6', '12', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_LOGGING', 'False', '6', '13', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
	}

	/**
	 * Uninstall Module
	 */
	function remove() {
		xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
	}

	/**
	 * List all configuration keys
	 * @return array
	 */
	function keys() {
		$keys = array();
		$check_keys_query = xtc_db_query("select configuration_key from " . TABLE_CONFIGURATION . " where configuration_key LIKE 'MODULE_PAYMENT_VRPAY_CC_%'");
		while($check_keys = xtc_db_fetch_array($check_keys_query)) {
			$keys[] = $check_keys['configuration_key'];
		}
		return $keys;
	}

}
?>
