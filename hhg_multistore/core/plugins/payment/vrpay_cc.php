<?php
/**
 * Modul für Kreditkartenzahlungen
 *
 * @version     $Id$
 *
 * @package     hhg multistore
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

require_once (DIR_FS_CATALOG . DIR_WS_CORE_CLASSES . 'class.vrpay_checkout.php');

class vrpay_cc extends vrpay_checkout {
	var $code, $title, $description, $info, $enabled, $store_id, $order_status;
	// class constructor
	function vrpay_cc() {
		$this->store_id = hhg_get_store_id();
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
		$this->INFOTEXT		= MODULE_PAYMENT_VRPAY_CC_INFOTEXT;
		$this->URLAGB		= MODULE_PAYMENT_VRPAY_SHARED_URLAGB;
		$this->URLCVC		= MODULE_PAYMENT_VRPAY_CC_URLCVC;
		$this->DEBUG		= MODULE_PAYMENT_VRPAY_SHARED_DEBUG;
		$this->SUBMITCART = (MODULE_PAYMENT_VRPAY_SHARED_SUBMITCART == 'True') ? true : false;

		$this->ACTIVATE_VISA = (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_VISA == 'True') ? true : false;
		$this->ACTIVATE_ECMC = (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_ECMC == 'True') ? true : false;
		$this->ACTIVATE_DINERS = (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_DINERS == 'True') ? true : false;
		$this->ACTIVATE_AMEX = (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_AMEX == 'True') ? true : false;
		$this->ACTIVATE_JCB = (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_JCB == 'True') ? true : false;

		$this->icons = array();
		$this->icons[] = (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_VISA == 'True') ? hhg_image(DIR_WS_CATALOG . DIR_WS_ACTUAL_STORE_FILES . DIR_WS_ICONS . 'vrpay/visa.png')  : '';
		$this->icons[] = (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_ECMC == 'True') ? hhg_image(DIR_WS_CATALOG . DIR_WS_ACTUAL_STORE_FILES . DIR_WS_ICONS . 'vrpay/mastercard.png') : '';
		$this->icons[].= (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_AMEX == 'True') ? hhg_image(DIR_WS_CATALOG . DIR_WS_ACTUAL_STORE_FILES . DIR_WS_ICONS . 'vrpay/americanexpress.png') : '';
		$this->icons[] = (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_DINERS == 'True') ? hhg_image(DIR_WS_CATALOG . DIR_WS_ACTUAL_STORE_FILES . DIR_WS_ICONS . 'vrpay/dinersclub.png') : '';
		$this->icons[] = (MODULE_PAYMENT_VRPAY_CC_ACTIVATE_JCB == 'True') ? hhg_image(DIR_WS_CATALOG . DIR_WS_ACTUAL_STORE_FILES . DIR_WS_ICONS . 'vrpay/jcb.png') : '';
		$this->icons[] = (MODULE_PAYMENT_VRPAY_CC_SHOW_VRPAY == 'True') ? hhg_image(DIR_WS_CATALOG . DIR_WS_ACTUAL_STORE_FILES . DIR_WS_ICONS . 'vrpay/vrpay.png') : '';

	}


	function update_status() {
		global $order;
		if (($this->enabled == true) && ((int)MODULE_PAYMENT_PAYPAL_ZONE > 0)) {
			$check_flag = false;
			$check = hhg_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_VRPAY_CC_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
			while (!$check->EOF) {
				if ($check->fields['zone_id'] < 1) {
					$check_flag = true;
					break;
				} elseif ($check->fields['zone_id'] == $order->billing['zone_id']) {
					$check_flag = true;
					break;
				}
				$check->MoveNext();
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
		$error['error'] = iconv( 'UTF-8', strtoupper($_SESSION['language_charset']).'//TRANSLIT', $error['error']);
		return $error;
	}

	/**
	 * Update status when returned from payment
	 */
	function after_process() {
		global $insert_id;

		return false;
	}

	/**
	 * Check if Module is active
	 * @return bool
	 */
	function check() {
		if (!isset($this->_check)) {
			$check_query = hhg_db_query("select configuration_value from configuration_" . $this->store_id . "_modules where configuration_key = 'MODULE_PAYMENT_VRPAY_CC_STATUS'");
			$this->_check = $check_query->RecordCount();
		}
		return $this->_check;
	}

	/**
	 * Install Module
	 */
	function install() {

		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_STATUS', 'True', '6', '10', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ZONE', '0', '6', '11', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ALLOWED', '', '6', '12', now())");
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_CC_SORT_ORDER', '10', '13', '16', now())");

		//Shared Config Values
		if(!$this->config_value_exists('MODULE_PAYMENT_VRPAY_SHARED_GATEWAY'))
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_SHARED_GATEWAY', 'TEST', '6', '20', 'xtc_cfg_select_option(array(\'LIVE\', \'TEST\'), ', now())");
		if(!$this->config_value_exists('MODULE_PAYMENT_VRPAY_SHARED_HAENDLERNR'))
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_SHARED_HAENDLERNR', '1000010140', '6', '21', now())");
		if(!$this->config_value_exists('MODULE_PAYMENT_VRPAY_SHARED_PASSWORT'))
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_SHARED_PASSWORT', 'fac114pli', '6', '22','xtc_cfg_get_password', 'xtc_cfg_password(', now())");
		if(!$this->config_value_exists('MODULE_PAYMENT_VRPAY_SHARED_REFERENCEPREFIX'))
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_SHARED_REFERENCEPREFIX', '', '6', '25', now())");
		if(!$this->config_value_exists('MODULE_PAYMENT_VRPAY_SHARED_ANTWGEHEIMNIS'))
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_SHARED_ANTWGEHEIMNIS', '', '6', '24', now())");
		if(!$this->config_value_exists('MODULE_PAYMENT_VRPAY_SHARED_URLAGB'))
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_SHARED_URLAGB', '3', '6', '40', 'xtc_cfg_pull_down_content(false, ', now())");
		if(!$this->config_value_exists('MODULE_PAYMENT_VRPAY_SHARED_SUBMITCART'))
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_SHARED_SUBMITCART', 'True', '6', '42', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		if(!$this->config_value_exists('MODULE_PAYMENT_VRPAY_SHARED_DEBUG'))
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_SHARED_DEBUG', '', '6', '60', now())");

		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_URLCVC', '', '6', '41', 'xtc_cfg_pull_down_content(true, ', now())");


		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_CC_VERWENDUNG1', '', '6', '26', now())");
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_CC_VERWENDUNG2', '". STORE_NAME ."', '6', '27', now())");
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VRPAY_CC_INFOTEXT', '', '6', '28', now())");

		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ZAHLART', 'RESERVIEREN', '6', '23', 'xtc_cfg_select_option(array(\'RESERVIEREN\', \'KAUFEN\'), ', now())");
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_VISA', 'True', '6', '30', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_ECMC', 'True', '6', '31', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_DINERS', 'False', '6', '33', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_AMEX', 'False', '6', '32', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ACTIVATE_JCB', 'False', '6', '34', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_SHOW_VRPAY', 'True', '6', '35', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");

		hhg_db_query("insert into configuration_" . $this->store_id . "_modules ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ORDER_STATUS_ID', '0',  '6', '50', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_ORDER_FAILED_STATUS_ID', '0',  '6', '51', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		hhg_db_query("insert into configuration_" . $this->store_id . "_modules ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_VRPAY_CC_TMP_STATUS_ID', '0',  '6', '52', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");

//		if(!$this->column_exists('vrpay', TABLE_ADMIN_ACCESS)) {
//			hhg_db_query('ALTER TABLE ' . TABLE_ADMIN_ACCESS . ' ADD vrpay int(1) NOT NULL');
//			hhg_db_perform(TABLE_ADMIN_ACCESS, array('vrpay' => 1), 'update', 'customers_id = 1');
//			hhg_db_perform(TABLE_ADMIN_ACCESS, array('vrpay' => 2), 'update', 'customers_id = \'groups\'');
//		}

		hhg_db_query('CREATE TABLE IF NOT EXISTS `payment_vrpay` (`id` int(11) NOT NULL AUTO_INCREMENT,`order_id` int(11) NOT NULL, `REFERENZNR` varchar(20) NOT NULL, `BETRAG` int(11) NOT NULL, `WAEHRUNG` varchar(3) NOT NULL, `ZAHLART` varchar(20) NOT NULL, `STATUS` varchar(20) NOT NULL, `RMSG` varchar(255) NOT NULL,  `ZEITPUNKT` datetime NOT NULL, `TSAID` varchar(32) NOT NULL, `SICHERHEIT` varchar(64) NOT NULL, `BRAND` varchar(20) NOT NULL, `KONTONR` varchar(10) NOT NULL, `BLZ` varchar(8) NOT NULL, `KREDITKARTENNR` varchar(20) NOT NULL, `VERFALLSDATUM` varchar(4) NOT NULL, `NACHRICHTNR` int(11) NOT NULL, PRIMARY KEY (`id`))');
	}

	/**
	 * Uninstall Module
	 */
	function remove() {
		if ( strpos(MODULE_PAYMENT_INSTALLED, 'vrpay_elv') === false && strpos(MODULE_PAYMENT_INSTALLED, 'vrpay_giropay') === false ) {
			//savely remove local and shared config
			hhg_db_query("delete from configuration_" . $this->store_id . "_modules where configuration_key in ('" . implode("', '", $this->keys('all')) . "')");
		} else {
			//remove only local config
			hhg_db_query("delete from configuration_" . $this->store_id . "_modules where configuration_key in ('" . implode("', '", $this->keys('local')) . "')");
		}
	}

	/**
	 * List all configuration keys
	 * @return array
	 */
	function keys($scope = 'all') {
		$keys = array();
		$check_keys = hhg_db_query("select configuration_key from configuration_" . $this->store_id . "_modules where configuration_key LIKE 'MODULE_PAYMENT_VRPAY_CC_%'" . (($scope == 'all') ? " OR configuration_key LIKE 'MODULE_PAYMENT_VRPAY_SHARED_%' " : "") . "ORDER BY sort_order");
		while(!$check_keys->EOF) {
			$keys[] = $check_keys->fields['configuration_key'];
			$check_keys->MoveNext();
		}
		return $keys;
	}

	private function config_value_exists($key) {

		$check_value = hhg_db_query("select configuration_value from configuration_" . $this->store_id . "_modules where configuration_key = " . hhg_db_prepare($key));
		if($check_value->RecordCount()) {
			return true;
		} else {
			return false;
		}
	}

	function column_exists($column, $table) {
		$check_columns = hhg_db_query('SHOW COLUMNS FROM ' . hhg_db_prepare($table) . ' LIKE '.hhg_db_prepare($column));
		if($check_columns->RecordCount()) {
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
									<td class="main"><a href="' . hhg_href_link('vrpay.php', 'view=detail&id=' . $data['id']) . '">Details</a></td>
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
		$content = hhg_db_query($sql = "SELECT c.content_id, c.content_name FROM " . TABLE_CONTENT_MANAGER . " AS c, " . TABLE_MS_CONTENT_MANAGER_TO_STORE . " AS c2s WHERE c.content_id = c2s.content_id AND c2s.store_" . STORE_ID . " = 1 AND c.language_id='" . (int)$_SESSION['languages_id'] . "'");
		
		while (!$content->EOF) {
			$content_query_array[] = array ('id' => $content->fields['content_id'], 'text' => $content->fields['content_name']);
			$content->MoveNext();
		}

		return hhg_draw_pull_down_menu($name, $content_query_array, $content_id);
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
		return '***********';
	}
}
?>