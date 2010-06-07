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

class vrpay_cc extends vrpay_checkout {

	// class constructor
	function vrpay_cc() {

		// module description
		$this->code			= 'vrpay_cc';
		$this->title		= MODULE_PAYMENT_VRPAY_CC_TEXT_TITLE;
		$this->description	= MODULE_PAYMENT_VRPAY_CC_TEXT_DESC;
		$this->info			= MODULE_PAYMENT_VRPAY_CC_TEXT_INFO;
		$this->sort_order	= MODULE_PAYMENT_VRPAY_CC_SORT_ORDER;
		$this->enabled     = ((MODULE_PAYMENT_VRPAY_CC_STATUS == 'True') ? true : false);

		$this->tmpOrders = true;
		$this->tmpStatus = MODULE_PAYMENT_VRPAY_CC_TMP_STATUS_ID;

		if ((int) MODULE_PAYMENT_VRPAY_CC_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_VRPAY_CC_ORDER_STATUS_ID;
		}

		if ((int) MODULE_PAYMENT_VRPAY_CC_ORDER_FAILED_STATUS_ID > 0) {
			$this->failed_order_status = MODULE_PAYMENT_VRPAY_CC_ORDER_FAILED_STATUS_ID;
		}



		$this->GATEWAY = MODULE_PAYMENT_VRPAY_SHARED_GATEWAY;
		$this->form_action_url = (MODULE_PAYMENT_VRPAY_SHARED_GATEWAY == 'LIVE') ? $this->LIVE_URL : $this->TEST_URL;

		if (is_object($order))
		$this->update_status();
			

		$this->HAENDLERNR	= MODULE_PAYMENT_VRPAY_SHARED_HAENDLERNR;
		$this->password		  (MODULE_PAYMENT_VRPAY_SHARED_PASSWORT);
		$this->REFPREFIX	= MODULE_PAYMENT_VRPAY_SHARED_REFERENCEPREFIX;
		$this->ZAHLART		= MODULE_PAYMENT_VRPAY_CC_ZAHLART;
		$this->ANTWGEHEIMNIS= MODULE_PAYMENT_VRPAY_SHARED_ANTWGEHEIMNIS;
		$this->VERWENDUNG1	= MODULE_PAYMENT_VRPAY_CC_VERWENDUNG1;
		$this->VERWENDUNG2	= MODULE_PAYMENT_VRPAY_CC_VERWENDUNG2;
		$this->URLAGB		= MODULE_PAYMENT_VRPAY_CC_URLAGB;
		$this->URLCVC		= MODULE_PAYMENT_VRPAY_CC_URLCVC;

		$this->ACTIVATE_VISA = (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_VISA == 'True') ? true : false;
		$this->ACTIVATE_ECMC = (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_ECMC == 'True') ? true : false;
		$this->ACTIVATE_DINERS = (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_DINERS == 'True') ? true : false;
		$this->ACTIVATE_AMEX = (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_AMEX == 'True') ? true : false;
		$this->ACTIVATE_JCB = (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_JCB == 'True') ? true : false;

		$this->icons = array();
		$this->icons[] = (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_VISA == 'True') ? xtc_image(DIR_WS_ICONS . 'vrpay/visa.png')  : '';
		$this->icons[] = (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_ECMC == 'True') ? xtc_image(DIR_WS_ICONS . 'vrpay/mastercard.png') : '';
		$this->icons[].= (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_AMEX == 'True') ? xtc_image(DIR_WS_ICONS . 'vrpay/americanexpress.png') : '';
		$this->icons[] = (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_DINERS == 'True') ? xtc_image(DIR_WS_ICONS . 'vrpay/dinersclub.png') : '';
		$this->icons[] = (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_JCB == 'True') ? xtc_image(DIR_WS_ICONS . 'vrpay/jcb.png') : '';

		$this->icons_available = xtc_image(DIR_WS_ICONS . 'cc_amex_small.jpg') . ' ' .
		xtc_image(DIR_WS_ICONS . 'cc_mastercard_small.jpg') . ' ' .
		xtc_image(DIR_WS_ICONS . 'cc_visa_small.jpg').' ' .
		xtc_image(DIR_WS_ICONS . 'cc_diners_small.jpg') . ' ' .
		xtc_image(DIR_WS_ICONS . 'jcb_big.jpg', '', '', 21);
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
		$content = array(array (
				'title' => ' ',
				'field' => implode(' ', $this->icons)
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

		$this->send_post($this->build_post($insert_id, $order, 'CC'), $this->form_action_url);

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
			$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_VRPAY_CC_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}



	/**
	 * Install Module
	 */
	function install() {

		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_STATUS', 'True', '6', '10', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ZONE', '0', '6', '11', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ALLOWED', '', '6', '12', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_CC_SORT_ORDER', '10', '13', '16', now())");

		//Shared Config Values
		if(!$this->config_value_exists('MODULE_PAYMENT_VRPAY_SHARED_GATEWAY'))
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_SHARED_GATEWAY', 'TEST', '6', '20', 'xtc_cfg_select_option(array(\'LIVE\', \'TEST\'), ', now())");
		if(!$this->config_value_exists('MODULE_PAYMENT_VRPAY_SHARED_HAENDLERNR'))
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_SHARED_HAENDLERNR', '1000010140', '6', '21', now())");
		if(!$this->config_value_exists('MODULE_PAYMENT_VRPAY_SHARED_PASSWORT'))
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_SHARED_PASSWORT', 'fac114pli', '6', '22','xtc_cfg_get_password', 'xtc_cfg_password(', now())");
		if(!$this->config_value_exists('MODULE_PAYMENT_VRPAY_SHARED_REFERENCEPREFIX'))
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_SHARED_REFERENCEPREFIX', '', '6', '25', now())");
		if(!$this->config_value_exists('MODULE_PAYMENT_VRPAY_SHARED_ANTWGEHEIMNIS'))
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_SHARED_ANTWGEHEIMNIS', '', '6', '24', now())");


		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_URLAGB', '3', '6', '40', 'xtc_cfg_pull_down_content(false, ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_URLCVC', '', '6', '41', 'xtc_cfg_pull_down_content(true, ', now())");


		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_CC_VERWENDUNG1', '', '6', '26', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_CC_VERWENDUNG2', '". STORE_NAME ."', '6', '27', now())");


		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ZAHLART', 'RESERVIEREN', '6', '23', 'xtc_cfg_select_option(array(\'RESERVIEREN\', \'KAUFEN\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_VISA', 'True', '6', '30', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_ECMC', 'True', '6', '31', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_DINERS', 'False', '6', '33', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_AMEX', 'False', '6', '32', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_JCB', 'False', '6', '34', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");

		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ORDER_STATUS_ID', '0',  '6', '50', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ORDER_FAILED_STATUS_ID', '0',  '6', '51', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_TMP_STATUS_ID', '0',  '6', '52', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");

		if(!$this->column_exists('vrpay', TABLE_ADMIN_ACCESS)) {
			xtc_db_query('ALTER TABLE ' . TABLE_ADMIN_ACCESS . ' ADD vrpay int(1) NOT NULL');
			xtc_db_perform(TABLE_ADMIN_ACCESS, array('vrpay' => 1), 'customers_id = 1');
			xtc_db_perform(TABLE_ADMIN_ACCESS, array('vrpay' => 2), 'customers_id = \'groups\'');			
		}
	}

	/**
	 * Uninstall Module
	 */
	function remove() {
		if ( strpos(MODULE_PAYMENT_INSTALLED, 'vrpay_elv') === false && strpos(MODULE_PAYMENT_INSTALLED, 'vrpay_giropay') === false ) {
			//savely remove local and shared config
			xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys('all')) . "')");
		} else {
			//remove only local config
			xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys('local')) . "')");
		}
	}

	/**
	 * List all configuration keys
	 * @return array
	 */
	function keys($scope = 'all') {
		$keys = array();
		$check_keys_query = xtc_db_query("select configuration_key from " . TABLE_CONFIGURATION . " where configuration_key LIKE 'MODULE_PAYMENT_VRPAY_CC_%'" . (($scope == 'all') ? " OR configuration_key LIKE 'MODULE_PAYMENT_VRPAY_SHARED_%' " : "") . "ORDER BY sort_order");
		while($check_keys = xtc_db_fetch_array($check_keys_query)) {
			$keys[] = $check_keys['configuration_key'];
		}
		return $keys;
	}

	private function config_value_exists($key) {

		$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = '" . xtc_db_input($key) . "'");
		if(xtc_db_num_rows($check_query)) {
			return true;
		} else {
			return false;
		}
	}
	
	function column_exists($column, $table) {
		$check_columns_query = xtc_db_query('SHOW COLUMNS FROM ' . xtc_db_input($table) . ' LIKE \''.xtc_db_input($column).'\'');
		if($check_columns = xtc_db_fetch_array($check_columns_query)) {
			return true;
		} else {
			return false;
		}
	}


	function admin_order($oID) {
		
			
		$query = xtc_db_query("SELECT * FROM payment_vrpay WHERE order_id = '" . (int) $oID . "' ORDER BY id DESC");
		if(xtc_db_num_rows($query)){
			$data = xtc_db_fetch_array($query);

			$currencies_query = xtc_db_query("SELECT decimal_places FROM ".TABLE_CURRENCIES . ' WHERE code = \'' . xtc_db_input($data['WAEHRUNG']) . '\'');
			$currency = xtc_db_fetch_array($currencies_query);
			
			$html = '
					<tr>
				    	<td class="main">' . ENTRY_CREDIT_CARD_TYPE . '</td>
				    	<td class="main">' . $data['BRAND'] . '</td>
				    </tr>
					<tr>
				        <td class="main">' . ENTRY_CREDIT_CARD_NUMBER . '</td>
				    	<td class="main">' . $data['KREDITKARTENNR'] . '</td>
				    </tr>
					<tr>
				        <td class="main">' . ENTRY_CREDIT_CARD_EXPIRES . '</td>
				    	<td class="main">' . $data['VERFALLSDATUM'] . '</td>
				    </tr>
				    			<tr>
				        <td class="main">' . TEXT_VRPAY_CC_SICHERHEIT . '</td>
				    	<td class="main">' . $data['SICHERHEIT'] . '</td>
				    </tr>
					<tr>
		            	<td class="main" valign="top"><b>' . TEXT_VRPAY_CC_PAYMENT . ':</b><br /></td>		            
		            	<td class="main" style="border: 1px solid; border-color: #003366; background: #fff;">
							<table width="300">
								<tr>
									<td width="10">';
			
			switch ($data['STATUS']) {
				case 'RESERVIERT':
					$html .= xtc_image('../' .DIR_WS_ICONS . 'vrpay/lock.png', $data['STATUS'], '', '', 'title="' . $data['STATUS'] . '"');
					break;
				case 'GEKAUFT':
					$html .= xtc_image('../' .DIR_WS_ICONS . 'vrpay/accept.png', $data['STATUS'], '', '', 'title="' . $data['STATUS'] . '"');
					break;
				case 'ABGELEHNT':
					$html .= xtc_image('../' .DIR_WS_ICONS . 'vrpay/exclamation.png', $data['STATUS'], '', '', 'title="' . $data['STATUS'] . '"');
					break;
				case 'IN BEARBEITUNG':
					$html .= xtc_image('../' .DIR_WS_ICONS . 'vrpay/error.png', $data['STATUS'], '', '', 'title="' . $data['STATUS'] . '"');
					break;
			}			
			 $html .='</td>
									<td class="main"> ' .TEXT_VRPAY_CC_BETRAG . ' ' . format_price($data['BETRAG'] / pow(10, $currency['decimal_places'] ), 1, $data['WAEHRUNG'] ) .'</td>
								</tr>
								<tr>
									<td width="10"></td>
									<td class="main">' . htmlspecialchars($data['RMSG']) .'</td>
								</tr>
																<tr>
									<td width="10">'.xtc_image('../' .DIR_WS_ICONS . 'vrpay/information.png').'</td>
									<td class="main"><a href="' . xtc_href_link('vrpay.php', 'view=detail&id=' . $data['id']) . '">Details</a></td>
								</tr>
							</table>
						</td>
					</tr>';

			return $html;
		}
	}
}

//set_function
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