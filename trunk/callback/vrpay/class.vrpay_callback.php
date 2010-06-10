<?php
/**
 * Modul für Kreditkartenzahlungen
 * 
 * @version     $Id: vrpay_cc.php 16 2010-06-03 22:12:54Z tr4nt0r $
 * 
 * @package     xt-commerce
 * @subpackage	vr-pay
 * @copyright   (c) 2010 Manfred Dennerlein. All rights reserved.
 * @license     GNU/GPL, see LICENSE.txt
 * @author		Manfred Dennerlein <manni@zapto.de>
 */

class vrpay_callback {
	
	private $data = array();
	private $oID;
	
	function __construct() {
		
		if(!is_array($_POST)) return false;
		
		$this->data = array_map('xtc_db_prepare_input', $_POST);
		
		$this->callback_process();
	}
	
	private function callback_process() {
		
		if(!$this->find()) 
			return false;
		
		if(!$this->validate_secret())
			return false;
		
		$this->save();
		
		$this->update_status();
	}
	
	/**
	 * Find corresponding order
	 */
	private function find() {
		if(MODULE_PAYMENT_VRPAY_SHARED_REFERENCEPREFIX != '') {
			$oID = substr_replace($this->data['REFERENZNR'],'',  0, strlen(MODULE_PAYMENT_VRPAY_SHARED_REFERENCEPREFIX));
		}

		$order_query = xtc_db_query('SELECT orders_id, payment_method FROM ' . TABLE_ORDERS . ' WHERE orders_id = \'' . xtc_db_input($oID) . '\'');
		if(xtc_db_num_rows($order_query)) {
			$order = xtc_db_fetch_array($order_query);
			$this->oID = $order['orders_id'];
			$this->payment_method = $order['payment_method'];
			return true;
		} else {
			return false;
		}		
	}
	
	/**
	 * Validate callback secret (ANTWGEHEIMNIS)
	 */
	private function validate_secret() {
		$callback_secret = strtoupper(md5($this->data['BETRAG'].$this->data['REFERENZNR']. MODULE_PAYMENT_VRPAY_SHARED_ANTWGEHEIMNIS));
		if($callback_secret == $this->data['ANTWGEHEIMNIS']) {
			return true;
		} else {
			return false;
		}
	}
	
	private function save() {

		$data = array();
		$data['order_id'] = $this->oID;
		$data['REFERENZNR'] = $this->data['REFERENZNR'];
		$data['BETRAG'] = $this->data['BETRAG'];
		$data['WAEHRUNG'] = $this->data['WAEHRUNG'];
		$data['ZAHLART'] = $this->data['ZAHLART'];
		$data['STATUS'] = $this->data['STATUS'];
		$data['RMSG'] = $this->data['RMSG'];
		$data['ZEITPUNKT'] = $this->data['ZEITPUNKT'];
		$data['TSAID'] = $this->data['TSAID'];
		$data['SICHERHEIT'] = $this->data['SICHERHEIT'];
		$data['BRAND'] = $this->data['BRAND'];
		$data['KONTONR'] = $this->data['KONTONR'];
		$data['BLZ'] = $this->data['BLZ'];
		$data['KREDITKARTENNR'] = $this->data['KREDITKARTENNR'];
		$data['VERFALLSDATUM'] = $this->data['VERFALLSDATUM'];
		$data['NACHRICHTNR'] = $this->data['NACHRICHTNR'];
		
		xtc_db_perform('payment_vrpay', $data);		
	}
	
	
	private function update_status() {
		$new_status = 0;
		
		switch ($this->payment_method) {
			case 'vrpay_cc':
				$this->order_status = MODULE_PAYMENT_VRPAY_CC_ORDER_STATUS_ID;
				$this->failed_order_status = MODULE_PAYMENT_VRPAY_CC_ORDER_FAILED_STATUS_ID;
				break;
			case 'vrpay_giropay':
				$this->order_status = MODULE_PAYMENT_VRPAY_GIROPAY_ORDER_STATUS_ID;
				$this->failed_order_status = MODULE_PAYMENT_VRPAY_GIROPAY_ORDER_FAILED_STATUS_ID;
				break;
			case 'vrpay_elv':
				$this->order_status = MODULE_PAYMENT_VRPAY_ELV_ORDER_STATUS_ID;
				$this->failed_order_status = MODULE_PAYMENT_VRPAY_ELV_ORDER_FAILED_STATUS_ID;
				break;
		}
		
		switch ($this->data['STATUS']) {

			case 'RESERVIERT' :
			case 'GEBUCHT':
				$new_status = $this->order_status;
				break;

			case 'ABGELEHNT' :
			case 'IN BEARBEITUNG' :
				$new_status = $this->failed_order_status;
				break;
		}
		
		if($new_status) {
			xtc_db_query("UPDATE " . TABLE_ORDERS . " SET orders_status='" . (int) $new_status . "' WHERE orders_id='" . (int) $this->oID . "'");
			xtc_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified) values ('" . (int) $this->oID . "', '" . (int) $new_status . "', now(), '0')");
		}
	}
}

?>