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
 */


class vrpay_checkout {

	protected $LIVE_URL = 'https://pay.vr-epay.de/pbr/transaktion';
	protected $TEST_URL = 'https://payinte.vr-epay.de/pbr/transaktion';
	protected $GATEWAY;
	protected $HAENDLERNR;
	protected $ORDERPREFIX;
	protected $ZAHLART;
	protected $VERWENDUNG2;
	protected $ANTWGEHEIMNIS;
	protected $ACTIVATE_VISA;
	protected $ACTIVATE_ECMC;
	protected $ACTIVATE_DINERS;
	protected $ACTIVATE_AMEX;
	protected $ACTIVATE_JCB;
	protected $PASSWORD;
	
	
	/**
	 * POST-Daten erzeugen
	 * @param array $data
	 * @param string $type cc or elv
	 * @return array
	 */
	protected function build_post(&$order, $type) {
		global $xtPrice;
		
		$post_data = array();
		
		//Allgemeine Parameter
		$post_data['HAENDLERNR']	= $this->HAENDLERNR;
		$post_data['TSATYP']		= 'ECOM';
		
		//Bestelldaten
		$post_data['REFERENZNR']	= $this->ORDERPREFIX . $_SESSION['tmp_oID'];
			
		if (XT_VREPAY_SYSTEM == 'TEST') {
			if ($order->order_total['total']['plain'] < 1) {
				$order->order_total['total']['plain'] = 1;
			} elseif ($order->order_total['total']['plain'] > 9) {
				$order->order_total['total']['plain'] = 9;
			} else {
				$order->order_total['total']['plain'] = round($order->order_total['total']['plain']);
			}
		}

		if (!in_array($my_currency, array ('EUR', 'USD', 'CHF', 'GBP', 'CAD', 'PLN', 'CZK', 'DKK', 'ALL', 'BAM', 'BGN', 'BYR', 'EEK', 'GEL', 'GIP', 'HRK', 'HUF', 'LTL', 'LVL', 'NOK', 'RON', 'RSD', 'RUB', 'SEK', 'TRY', 'UAH'))) {
			//TODO: Fehler ausgeben, dass Währung nicht für Zahlung erlaubt.
		}
		

		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			$total = $order->info['total'] + $order->info['tax'];
		} else {
			$total = $order->info['total'];
		}
		
		//Im Testsystem sind nur ganze Beträge zw. 1 und 9 erlaubt 
		if ($this->GATEWAY == 'TEST') {
			if ($order->info['total'] < 1) {
				$order->info['total'] = 1;
			} elseif ($order->info['total'] > 9) {
				$order->info['total'] = 9;
			} else {
				$order->info['total'] = round($order->info['total']);
			}
		}
		$post_data['BETRAG']			= $order->info['total'] * pow(10, $xtPrice->get_decimal_places( $order->info['currency'] ) );
		 		

		$post_data['WAEHRUNG']		= $order->info['currency'];
		$post_data['INFOTEXT']		= '';
		$post_data['ARTIKELANZ']	= count($order->products);
		
		//Warenkorb		
		for($i = 0; $i < count($order->products); $i++) {
			$post_data['ARTIKELNR' . ($i+1)] = $order->products[$i]['model'];
			$post_data['ARTIKELBEZ' . ($i+1)] = utf8_decode($order->products[$i]['name']);
			$post_data['ANZAHL' . ($i+1)] = (int)$order->products[$i]['qty'];
			$post_data['EINZELPREIS' . ($i+1)] = $order->products[$i]['price'] * pow(10, $currency->decimals);
		}

		//Transaktion
		$post_data['ZAHLART'] 		= $this->ZAHLART;
		$post_data['SERVICENAME'] 	= 'DIALOG';
	
		
		if($this->VERWENDUNG2 != '') {
			$post_data['VERWENDUNG2'] = utf8_decode(substr($this->VERWENDUNG2, 0, 25));
			$post_data['VERWENDANZ'] = 2;
		} else {
			$post_data['VERWENDANZ'] = 1;	
		}
		
		
		
		$callback_secret = strtoupper(md5($post_data['BETRAG'].$post_data['REFERENZNR'].$this->ANTWGEHEIMNIS));
			
		$post_data['ANTWGEHEIMNIS']	= $callback_secret;
		

			
		if(defined('XT_VREPAY_CONTENT_AGB') && XT_VREPAY_CONTENT_AGB) {
			$shop_content_agb =  new content(XT_VREPAY_CONTENT_AGB);
			if ($shop_content_agb->data['content_status']) {
				$post_data['URLAGB'] = $shop_content_agb->data['content_link'];
			}

		}

		if(defined('XT_VREPAY_CONTENT_CVC') && XT_VREPAY_CONTENT_CVC) {
			$shop_content_agb =  new content(XT_VREPAY_CONTENT_CVC);
			if ($shop_content_agb->data['content_status']) {
				$post_data['URLCVC'] = $shop_content_agb->content_link;
			}
		}
			
		$post_data['URLERFOLG'] = xtc_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
			
		$post_data['URLFEHLER'] =  xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL');
		$post_data['URLABBRUCH'] = xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL');
		$post_data['URLANTWORT'] = xtc_href_link('callback/vrpay/callback.php', '', 'SSL');
		//
		$post_data['BENACHRPROF']	= "ZHL";

		$post_data['SPRACHE'] = (in_array(strtoupper($_SESSION['language_code']), array('DE', 'EN', 'FR', 'ES', 'IT', 'NL', 'PL', 'CS'))) ? strtoupper($_SESSION['language_code']) : 'DE';
		
		switch($type) {
			case 'ELV':
				$post_data['AUSWAHL'] = 'N';
				$post_data['BRAND'] = 'ELV';
				break;
			case 'GIROPAY':
				$post_data['AUSWAHL'] = 'N';
				$post_data['BRAND'] = 'GIROPAY';
				$post_data['ZAHLART'] = 'KAUFEN';
				break;

			case 'CC':
				$auswahl = array();
					
				//if($this->ACTIVATE_VISA == 'true') {
					$auswahl[] = 'VISA';
				//}
				//if($this->ACTIVATE_ECMC == 'true') {
					$auswahl[] = 'ECMC';
				//}
				if($this->ACTIVATE_DINERS == 'true') {
					$auswahl[] = 'DINERS';
				}
				if($this->ACTIVATE_AMEX == 'true') {
					$auswahl[] = 'AMEX';
				}
				if($this->ACTIVATE_JCB == 'true') {
					$auswahl[] = 'JCB';
				}
				if(count($auswahl) > 0) {
					$post_data['AUSWAHL'] = 'J';
					$post_data['BRAND'] = implode(';', $auswahl);
				} else {
					$post_data['AUSWAHL'] = 'J';
				}
				break;
					
			default:
				$post_data['AUSWAHL'] = 'J';
				break;
		}


		return $post_data;
	}
	
}