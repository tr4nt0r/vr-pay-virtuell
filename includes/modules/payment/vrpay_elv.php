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

require_once ( DIR_FS_DOCUMENT_ROOT . 'includes/classes/class.vrpay_checkout.php');

class vrpay_elv extends vrpay_checkout {

	// class constructor
	function vrpay_elv() {

		// module description
		$this->code			= 'vrpay_elv';
		$this->title		= MODULE_PAYMENT_VRPAY_ELV_TEXT_TITLE;
		$this->description	= MODULE_PAYMENT_VRPAY_ELV_TEXT_DESC;
		$this->info			= MODULE_PAYMENT_VRPAY_ELV_TEXT_INFO;
		$this->sort_order	= MODULE_PAYMENT_VRPAY_ELV_SORT_ORDER;
		$this->enabled     = ((MODULE_PAYMENT_VRPAY_ELV_STATUS == 'True') ? true : false);
		
		$this->tmpOrders = true;
		$this->tmpStatus = MODULE_PAYMENT_VRPAY_ELV_TMP_STATUS_ID;

		if ((int) MODULE_PAYMENT_VRPAY_ELV_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_VRPAY_ELV_ORDER_STATUS_ID;
		}
		
		if ((int) MODULE_PAYMENT_VRPAY_ELV_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_VRPAY_ELV_ORDER_STATUS_ID;
		}
		
		
		
		$this->GATEWAY = MODULE_PAYMENT_VRPAY_ELV_GATEWAY;
		$this->form_action_url = (MODULE_PAYMENT_VRPAY_ELV_GATEWAY == 'LIVE') ? $this->LIVE_URL : $this->TEST_URL;
		
		if (is_object($order))
			$this->update_status();
			
				
		$this->HAENDLERNR	= MODULE_PAYMENT_VRPAY_ELV_HAENDLERNR;
		$this->password		  (MODULE_PAYMENT_VRPAY_ELV_PASSWORT);
		$this->REFPREFIX	= MODULE_PAYMENT_VRPAY_ELV_REFERENCEPREFIX;
		$this->ZAHLART		= MODULE_PAYMENT_VRPAY_ELV_ZAHLART;
		$this->ANTWGEHEIMNIS= MODULE_PAYMENT_VRPAY_ELV_ANTWGEHEIMNIS;
		$this->VERWENDUNG1	= MODULE_PAYMENT_VRPAY_ELV_VERWENDUNG1;
		$this->VERWENDUNG2	= MODULE_PAYMENT_VRPAY_ELV_VERWENDUNG2;
		$this->URLAGB		= MODULE_PAYMENT_VRPAY_ELV_URLAGB;
		$this->URLCVC		= MODULE_PAYMENT_VRPAY_ELV_URLCVC;
		
		$this->icons = xtc_image(DIR_WS_ICONS . 'vrpay/elv.png') ;
		
		$this->icons_available = xtc_image(DIR_WS_ICONS . 'elv_small.jpg');
	}


	function update_status() {
		global $order;

		if (($this->enabled == true) && ((int) MODULE_PAYMENT_VRPAY_ELV_ZONE > 0)) {
			$check_flag = false;
			$check_query = xtc_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".MODULE_PAYMENT_VRPAY_ELV_ZONE."' and zone_country_id = '".$order->billing['country']['id']."' order by zone_id");
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
		$content = array(array (
				'title' => ' ',
				'field' => $this->icons
			));
		return array ('id' => $this->code, 'module' => $this->title, 'fields' => $content, 'description' => $this->info);
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

	/**
	 * Process payment
	 */
	function payment_action() {
		global $order, $insert_id, $xtPrice;

		$this->send_post($this->build_post($insert_id, $order, 'ELV'), $this->form_action_url);
		
		return false;
	}

	/**
	 * Display Error Message
	 */
	function get_error() {
		$error = array ('error' => stripslashes(urldecode($_GET['error'])));
		return $error;
	}

	/**
	 * Update status when returned from payment
	 */
	function after_process() {
		global $insert_id;
//		if ($this->order_status)
//		xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$this->order_status."' WHERE orders_id='". xtc_db_input($insert_id)."'");
	}

	/**
	 * Check if Module is active
	 * @return bool
	 */
	function check() {
		if (!isset($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_VRPAY_ELV_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	

	/**
	 * Install Module
	 */
	function install() {

		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_ELV_STATUS', 'True', '6', '10', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_ELV_ZONE', '0', '6', '11', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_ELV_ALLOWED', '', '6', '12', now())");		
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_ELV_SORT_ORDER', '11', '13', '16', now())");
		
		
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_ELV_GATEWAY', 'TEST', '6', '20', 'xtc_cfg_select_option(array(\'LIVE\', \'TEST\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_ELV_HAENDLERNR', '1000010140', '6', '21', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_ELV_PASSWORT', 'fac114pli', '6', '22','xtc_cfg_get_password', 'xtc_cfg_password(', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_ELV_ZAHLART', 'RESERVIEREN', '6', '23', 'xtc_cfg_select_option(array(\'RESERVIEREN\', \'KAUFEN\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_ELV_ANTWGEHEIMNIS', '', '6', '24', now())");
		
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_ELV_URLAGB', '3', '6', '40', 'xtc_cfg_pull_down_content(false, ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_ELV_URLCVC', '', '6', '41', 'xtc_cfg_pull_down_content(true, ', now())");
		
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_ELV_REFERENCEPREFIX', '', '6', '25', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_ELV_VERWENDUNG1', '', '6', '26', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_ELV_VERWENDUNG2', '". STORE_NAME ."', '6', '27', now())");
		
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_VRPAY_ELV_ORDER_STATUS_ID', '0',  '6', '50', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_VRPAY_ELV_TMP_STATUS_ID', '0',  '6', '51', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
	
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
		$check_keys_query = xtc_db_query("select configuration_key from " . TABLE_CONFIGURATION . " where configuration_key LIKE 'MODULE_PAYMENT_VRPAY_ELV_%' ORDER BY sort_order");
		while($check_keys = xtc_db_fetch_array($check_keys_query)) {
			$keys[] = $check_keys['configuration_key'];
		}
		return $keys;
	}
	


}

//set_functin
if(!function_exists('xtc_cfg_pull_down_content')) {

	function xtc_cfg_pull_down_content($allow_empty = false, $content_id, $key = '') {
		$name = (($key) ? 'configuration['.$key.']' : 'configuration_value');
		$content_query_array = array();
		if($allow_empty) {
			$content_query_array[] = array ('id' => '', 'text' => TEXT_NONE);
		}
		$content_query = xtc_db_query("select content_group, content_title from ".TABLE_CONTENT_MANAGER." WHERE languages_id='" . $_SESSION['languages_id'] . "' order by content_group ");
		while ($content = xtc_db_fetch_array($content_query)) {
			$content_query_array[] = array ('id' => $content['content_group'], 'text' => $content['content_title']);
		}

		return xtc_draw_pull_down_menu($name, $content_query_array, $content_id);
	}
}

//set_function
if(!function_exists('xtc_cfg_password')) {

	function xtc_cfg_password($password, $key = '') {
		$name = (($key) ? 'configuration['.$key.']' : 'configuration_value');
		return '<input type="password" name="' . $name . '" value="' . $password . '">';
	}
}

//use_function
if(!function_exists('xtc_cfg_get_password')) {
	function xtc_cfg_get_password($password) {
		return '&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;';
	}
}
?>