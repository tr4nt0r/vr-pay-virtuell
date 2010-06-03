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
		
	protected $HAENDLERNR;
	private $PASSWORD;
	
	protected $REFPREFIX;
	protected $ZAHLART;
	private   $SERVICENAME = 'DIALOG';
	protected $GATEWAY;
	protected $ANTWGEHEIMNIS;
	
	protected $VERWENDUNG1;
	protected $VERWENDUNG2;
	
	protected $URLAGB;
	protected $URLCVC;
	
	
	protected $ACTIVATE_VISA;
	protected $ACTIVATE_ECMC;
	protected $ACTIVATE_DINERS;
	protected $ACTIVATE_AMEX;
	protected $ACTIVATE_JCB;
	
	
	protected $ch = false;
	
	protected function password($password) {
		$this->PASSWORD = $password;
	}
	
	
	/**
	 * POST-Daten erzeugen
	 * @param array $data
	 * @param string $type cc or elv
	 * @return array
	 */
	protected function build_post($order_id, &$order, $type) {
		global $xtPrice;
		
		$post_data = array();
		
		//Allgemeine Parameter
		$post_data['HAENDLERNR']	= $this->HAENDLERNR;
		$post_data['TSATYP']		= 'ECOM';
		
		//Bestelldaten
		$post_data['REFERENZNR']	= str_pad($this->REFPREFIX . $order_id, 4, '0', STR_PAD_LEFT);

		if (!in_array($order->info['currency'], array ('EUR', 'USD', 'CHF', 'GBP', 'CAD', 'PLN', 'CZK', 'DKK', 'ALL', 'BAM', 'BGN', 'BYR', 'EEK', 'GEL', 'GIP', 'HRK', 'HUF', 'LTL', 'LVL', 'NOK', 'RON', 'RSD', 'RUB', 'SEK', 'TRY', 'UAH'))) {
			xtc_redirect( xtc_href_link(FILENAME_CHECKOUT_PAYMENT,  'payment_error=' . $this->code . '&error=' . urlencode(MODULE_PAYMENT_VRPAY_CC_TEXT_CURRENCY_NOT_SUPPORTED), 'SSL', true, false));
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
			$post_data['ARTIKELNR' . ($i+1)] = ($order->products[$i]['model']) ? $order->products[$i]['model'] : $order->products[$i]['id'];
			$post_data['ARTIKELBEZ' . ($i+1)] = utf8_decode($order->products[$i]['name']);
			$post_data['ANZAHL' . ($i+1)] = (int)$order->products[$i]['qty'];
			$post_data['EINZELPREIS' . ($i+1)] = $order->products[$i]['price'] * pow(10, $xtPrice->get_decimal_places( $order->info['currency'] ) );
		}

		//Transaktion
		$post_data['SERVICENAME'] 	= $this->SERVICENAME;
		$post_data['VERWENDUNG2'] =  utf8_decode(substr($this->VERWENDUNG1, 0, 25));
		
		if($this->VERWENDUNG2 != '') {
			$post_data['VERWENDUNG2'] = utf8_decode(substr($this->VERWENDUNG2, 0, 25));
			$post_data['VERWENDANZ'] = 2;
		} else {
			$post_data['VERWENDANZ'] = 1;	
		}
		
		
		
		$callback_secret = strtoupper(md5($post_data['BETRAG'].$post_data['REFERENZNR'].$this->ANTWGEHEIMNIS));
			
		$post_data['ANTWGEHEIMNIS']	= $callback_secret;
			
		$shop_content_query = "SELECT content_title FROM " . TABLE_CONTENT_MANAGER . " WHERE content_group='". $this->URLAGB."' AND languages_id='" . $_SESSION['languages_id'] . "'";
		$shop_content_query = xtc_db_query($shop_content_query);
		$shop_content_data = xtc_db_fetch_array($shop_content_query);
		$SEF_parameter = '&content='.xtc_cleanName($shop_content_data['content_title']);
		$post_data['URLAGB'] = xtc_href_link(FILENAME_CONTENT, 'coID=' . $this->URLAGB .$SEF_parameter);

		if($this->URLCVC) {
			$shop_content_query = "SELECT content_title FROM " . TABLE_CONTENT_MANAGER . " WHERE content_group='". $this->URLCVC."' AND languages_id='" . $_SESSION['languages_id'] . "'";
			$shop_content_query = xtc_db_query($shop_content_query);
			$shop_content_data = xtc_db_fetch_array($shop_content_query);
			$SEF_parameter = '&content='.xtc_cleanName($shop_content_data['content_title']);
			$post_data['URLCVC'] = xtc_href_link(FILENAME_CONTENT, 'coID=' . $this->URLCVC .$SEF_parameter);

		}
			
		$post_data['URLERFOLG'] = xtc_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
			
		$post_data['URLFEHLER'] =  xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code . '&error=' . urlencode(MODULE_PAYMENT_VRPAY_CC_TEXT_FAILED), 'SSL');
		$post_data['URLABBRUCH'] = xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code . '&error=' . urlencode(MODULE_PAYMENT_VRPAY_CC_TEXT_CANCELED), 'SSL');
		$post_data['URLANTWORT'] = xtc_href_link('callback/vrpay/callback.php', '', 'SSL');
		//
		$post_data['BENACHRPROF']	= "ZHL";

		$post_data['SPRACHE'] = (in_array(strtoupper($_SESSION['language_code']), array('DE', 'EN', 'FR', 'ES', 'IT', 'NL', 'PL', 'CS'))) ? strtoupper($_SESSION['language_code']) : 'DE';
		
		switch($type) {
			case 'ELV':
				$post_data['AUSWAHL'] = 'N';
				$post_data['BRAND'] = 'ELV';
				$post_data['ZAHLART'] = $this->ZAHLART;
				break;
			case 'GIROPAY':
				$post_data['AUSWAHL'] = 'N';
				$post_data['BRAND'] = 'GIROPAY';
				$post_data['ZAHLART'] = 'KAUFEN';
				break;

			case 'CC':
				$auswahl = array();
					
				if($this->ACTIVATE_VISA == 'true') {
					$auswahl[] = 'VISA';
				}
				if($this->ACTIVATE_ECMC == 'true') {
					$auswahl[] = 'ECMC';
				}
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
				$post_data['ZAHLART'] = $this->ZAHLART;
				break;
					
			default:
				$post_data['AUSWAHL'] = 'J';
				$post_data['ZAHLART'] = $this->ZAHLART;
				break;
		}


		return $post_data;
	}
	
	
	protected function send_post(&$post_data, $target) {
		
		
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_URL, $target);

		curl_setopt($this->ch, CURLOPT_USERPWD, $this->HAENDLERNR . ':' . $this->PASSWORD);
		curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($post_data, '', '&'));
		curl_setopt($this->ch, CURLOPT_POST, true);
		curl_setopt($this->ch, CURLOPT_SSLVERSION, 3);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
		
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($this->ch);

		$this->process_response($response);
		
		curl_close($this->ch);
	}
	

	protected function process_response(&$response) {

		if($response === false) {
			xtc_redirect( xtc_href_link(FILENAME_CHECKOUT_PAYMENT,  'payment_error=' . $this->code . '&error=' . urlencode(MODULE_PAYMENT_VRPAY_CC_TEXT_GATEWAY_UNAVAILABLE), 'SSL', true, false));
		} else {
			$header = curl_getinfo($this->ch);
			switch ($header['http_code']) {
					
				case '200':
					//Im Fehlerfall erfolgt kein Redirect zur Zahlungsseite
					if($header['redirect_count'] == 0) {
						parse_str($response);
						xtc_redirect( xtc_href_link(FILENAME_CHECKOUT_PAYMENT,  'payment_error=' . $this->code . '&error=' . urlencode($FEHLERTEXT), 'SSL', true, false));
					} else {
						xtc_redirect($header['url']);
					}
					break;
				case '401':
					xtc_redirect( xtc_href_link(FILENAME_CHECKOUT_PAYMENT,  'payment_error=' . $this->code . '&error=' . urlencode(MODULE_PAYMENT_VRPAY_CC_TEXT_GATEWAY_AUTHENTICATION), 'SSL', true, false));
					break;
				default:
					xtc_redirect( xtc_href_link(FILENAME_CHECKOUT_PAYMENT,  'payment_error=' . $this->code . '&error=' . urlencode(MODULE_PAYMENT_VRPAY_CC_TEXT_UNKNOWN_ERROR), 'SSL', true, false));
					break;
			}
		}
	}
}
?>