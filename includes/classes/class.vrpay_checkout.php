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
	private $post_data = array();
	
	protected $REFPREFIX;
	protected $ZAHLART;
	private   $SERVICENAME = 'DIALOG';
	protected $GATEWAY;
	protected $ANTWGEHEIMNIS;
	
	protected $VERWENDUNG1;
	protected $VERWENDUNG2;
	protected $INFOTEXT;
	
	protected $URLAGB;
	protected $URLCVC;
	protected $SUBMITCART;
	
	protected $ACTIVATE_VISA;
	protected $ACTIVATE_ECMC;
	protected $ACTIVATE_DINERS;
	protected $ACTIVATE_AMEX;
	protected $ACTIVATE_JCB;
	protected $CC_BRAND;
	
	
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
			xtc_redirect( xtc_href_link(FILENAME_CHECKOUT_PAYMENT,  'payment_error=' . $this->code . '&error=' . urlencode(utf8_decode(MODULE_PAYMENT_VRPAY_CC_TEXT_CURRENCY_NOT_SUPPORTED)), 'SSL', true, false));
		}
		

		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			$total = $order->info['total'] + $order->info['tax'];
		} else {
			$total = $order->info['total'];
		}
		
		//Im Testsystem sind nur ganze Beträge zw. 1 und 9 erlaubt 
		if ($this->GATEWAY == 'TEST') {
			if ($total < 1) {
				$total = 1;
			} elseif ($total > 9) {
				$total = 9;
			} else {
				$total = round($order->info['total']);
			}
		}
		$post_data['BETRAG']			= round($total * pow(10, $xtPrice->get_decimal_places( $order->info['currency'] ) ),0);
		 		

		$post_data['WAEHRUNG']		= $order->info['currency'];
		$post_data['INFOTEXT']		= '';
		
		if($this->SUBMITCART) {
			$post_data['ARTIKELANZ']	= count($order->products);

			//Warenkorb
			for($i = 0; $i < count($order->products); $i++) {
				$post_data['ARTIKELNR' . ($i+1)] = ($order->products[$i]['model']) ? $order->products[$i]['model'] : $order->products[$i]['id'];
				$post_data['ARTIKELBEZ' . ($i+1)] = $order->products[$i]['name'];
				$post_data['ANZAHL' . ($i+1)] = (int)$order->products[$i]['qty'];
				$post_data['EINZELPREIS' . ($i+1)] = $order->products[$i]['price'] * pow(10, $xtPrice->get_decimal_places( $order->info['currency'] ) );
			}
		} else {
			$post_data['ARTIKELANZ']	= 0;
		}
		//Transaktion
		$post_data['SERVICENAME'] 	= $this->SERVICENAME;
		
		$array_search = array('{$order_id}', '{$customers_cid}', '{$customers_name}', '{$customers_lastname}', '{$customers_firstname}', '{$customers_company}', '{$customers_city}', '{$customers_email_address}');
		$array_replace = array($order_id, $order->customer['csID'], $order->customer['firstname'] . ' ' . $order->customer['lastname'], $order->customer['lastname'], $order->customer['firstname'], $order->customer['company'], $order->customer['city'], $order->customer['email_address']);
		
		$post_data['VERWENDUNG1'] =  substr(str_replace($array_search, $array_replace, $this->VERWENDUNG1), 0, 24);
		
		if($this->VERWENDUNG2 != '') {
			$post_data['VERWENDUNG2'] = substr(str_replace($array_search, $array_replace, $this->VERWENDUNG2), 0, 24);
			$post_data['VERWENDANZ'] = 2;
		} else {
			$post_data['VERWENDANZ'] = 1;	
		}
		
		
		$post_data['INFOTEXT'] =  substr(str_replace($array_search, $array_replace, $this->INFOTEXT), 0, 1000);
		
		
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
			
		$post_data['URLFEHLER'] =  xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code . '&error=' . urlencode(utf8_decode(MODULE_PAYMENT_VRPAY_CC_TEXT_FAILED)), 'SSL');
		$post_data['URLABBRUCH'] = xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code . '&error=' . urlencode(utf8_decode(MODULE_PAYMENT_VRPAY_CC_TEXT_CANCELED)), 'SSL');
		$post_data['URLANTWORT'] = xtc_href_link('callback/vrpay/callback.php', '', 'SSL');
		//
		$post_data['BENACHRPROF']	= "ALL";

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
					
				if($this->CC_BRAND) {
					$auswahl[] = $this->CC_BRAND;
				} else {
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
				}
				if(count($auswahl) > 0) {
					$post_data['AUSWAHL'] = 'J';
					$post_data['BRAND'] = implode(';', $auswahl);
				} elseif(count($auswahl) == 1) {
					$post_data['AUSWAHL'] = 'N';
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

		foreach($post_data as $k => $v) {
			if($k == 'VERWENDUNG1' || $k == 'VERWENDUNG2') {
				setlocale(LC_CTYPE, 'de_DE.utf8');				
				$post_data[$k] = iconv( strtoupper($_SESSION['language_charset']), 'ASCII//TRANSLIT', $post_data[$k]);
				$post_data[$k] = preg_replace('/[^a-zA-Z0-9äöüÄÖÜß\$\%\*\+\-\/\,\. ]/s', '', $post_data[$k]);
			}
			$post_data[$k] = iconv( strtoupper($_SESSION['language_charset']), 'ISO-8859-1//TRANSLIT', $post_data[$k]);

		}

		return $this->post_data = $post_data;
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


		if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
			curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
		} else {			
			curl_setopt($this->ch, CURLOPT_HEADER, true);
		}
		$response = curl_exec($this->ch);

		$this->process_response($response);
		
		curl_close($this->ch);
	}
	

	protected function process_response(&$response) {

		if($response === false) {
			xtc_redirect( xtc_href_link(FILENAME_CHECKOUT_PAYMENT,  'payment_error=' . $this->code . '&error=' . urlencode(utf8_decode(MODULE_PAYMENT_VRPAY_CC_TEXT_GATEWAY_UNAVAILABLE)), 'SSL', true, false));
		} else {
			$header = curl_getinfo($this->ch);
			switch ($header['http_code']) {
					
				case '200':
					//Im Fehlerfall erfolgt kein Redirect zur Zahlungsseite
					if($header['redirect_count'] == 0) {
						parse_str($response);
						$this->debug_message($header, $response);
						xtc_redirect( xtc_href_link(FILENAME_CHECKOUT_PAYMENT,  'payment_error=' . $this->code . '&error=' . urlencode($FEHLERTEXT), 'SSL', true, false));
					} else {
						xtc_redirect($header['url']);
					}
					break;
				
				case '302':
					//Follow Location nicht möglich, weiterleitung "händisch" vornehmen					
					list($header_raw, $response) = explode("\n\n", $response, 2);
					$matches = array();
					preg_match('/(Location:|URI:)(.*?)\n/', $header_raw, $matches);
					$url = @parse_url(trim(array_pop($matches)));
					if (!$url) {
						//redirect url konnte nicht ermittelt werden
						$this->debug_message($header, $response);
						xtc_redirect( xtc_href_link(FILENAME_CHECKOUT_PAYMENT,  'payment_error=' . $this->code . '&error=' . urlencode(utf8_decode(MODULE_PAYMENT_VRPAY_CC_TEXT_UNKNOWN_ERROR)), 'SSL', true, false));
					}
					//relativen pfad zu absoluten Pfad ergänzen 
					$last_url = parse_url($header['url']);
					if (!$url['scheme']) $url['scheme'] = $last_url['scheme'];
					if (!$url['host']) $url['host'] = $last_url['host'];
					if (!$url['path']) $url['path'] = $last_url['path'];
					$new_url = $url['scheme'].'://'.$url['host'].$url['path'].($url['query'] ? '?'.$url['query'] : '');
					xtc_redirect($new_url);
					break;
					
				case '401':
					$this->debug_message($header, $response);
					xtc_redirect( xtc_href_link(FILENAME_CHECKOUT_PAYMENT,  'payment_error=' . $this->code . '&error=' . urlencode(utf8_decode(MODULE_PAYMENT_VRPAY_CC_TEXT_GATEWAY_AUTHENTICATION)), 'SSL', true, false));
					break;
				default:
					$this->debug_message($header, $response);
					xtc_redirect( xtc_href_link(FILENAME_CHECKOUT_PAYMENT,  'payment_error=' . $this->code . '&error=' . urlencode(utf8_decode(MODULE_PAYMENT_VRPAY_CC_TEXT_UNKNOWN_ERROR)), 'SSL', true, false));
					break;
			}
		}
	}
	
	private function debug_message ($header, $response) {

		if($this->DEBUG != '') {
				
			$body = '==================================================================' . "\n";
			$body .= print_r($this->post_data, true);
			$body .= '==================================================================' ."\n";
			$body .= print_r($header, true);
			$body .= '==================================================================' ."\n";
			$body .= $response;

			error_log($body, 1, $this->DEBUG);

		}
	}
}
?>