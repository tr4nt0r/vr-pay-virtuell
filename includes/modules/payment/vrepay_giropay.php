<?php
/*
   $Id: vrepay_dialog.php,v 1.2.4 2008/07/08 $
   
   Path: xtc/includes/modules/payment

   osCommerce, Open Source E-Commerce Solutions
   http://www.oscommerce.com

   Copyright (c) 2002 osCommerce

   Released under the GNU General Public License

   -----------------------------------------------------------------------------------------
   Third Party contribution:

   Copyright (c) 2009 Netzkollektiv / Cardprocess

   Commercial License
   ---------------------------------------------------------------------------------------*/

class vrepay_giropay {

	// class constructor
	function vrepay_giropay() {

		// module description
		$this->code        = 'vrepay_giropay';
		$this->title       = MODULE_PAYMENT_VREPAY_GIROPAY_TEXT_TITLE;
		$this->modul_title = MODULE_PAYMENT_VREPAY_GIROPAY_TEXT_MODUL_TITLE;
		$this->description = MODULE_PAYMENT_VREPAY_GIROPAY_TEXT_DESCRIPTION;
		// module enabled / disabled
		$this->enabled     = ((MODULE_PAYMENT_VREPAY_GIROPAY_STATUS == 'True') ? true : false);
		// sort order
		$this->sort_order  = MODULE_PAYMENT_VREPAY_GIROPAY_SORT_ORDER;
		// logging
		$this->log         = MODULE_PAYMENT_VREPAY_GIROPAY_LOG;
		$this->logging     = ((MODULE_PAYMENT_VREPAY_GIROPAY_LOGGING == 'True') ? true : false);
		// geozone e.g. (EU)
		$this->zone        = MODULE_PAYMENT_VREPAY_GIROPAY_ZONE;
		// checkout function for vrepay
		$this->form_action_url = xtc_href_link("checkout_vrepay_giropay.php", '', 'SSL');

		// new vrepay stats, corresponds to table order_status
		$this->statusWait = '5';
		$this->statusFailed = '7';
		$this->statusSuccess = '10';

		// temp status for checkout_process, results in payment_action() -> corresponds to statusWait
		$this->tmpOrders = true;
		$this->tmpStatus = '5';	// in progress

		// request parameter
		$this->haendlernr = false;
		$this->referenznr = false;
		$this->betrag = false;
		$this->waehrung = false;
		$this->urlerfolg = false;
		$this->urlfehler = false;
		$this->urlabbruch = false;
		$this->urlantwort = false;
		$this->urlagb = false;
		$this->zahlart = 'KAUFEN';
		$this->service = false;
		$this->verwendung1 = false;
		$this->verwendung2 = false;
		$this->sprache = false;
		$this->tsatyp = 'ECOM';
		
		$this->auswahl = 'N';
		$this->brand = 'GIROPAY';

		$this->epay_log( "end function vrepay_dialog()");
	}

	// call from checkout_confirmation.php[1.]
	function update_status() {

		global $order;

		$this->epay_log( "start function update_status() with epay geozone -> " . print_r($this->zone, TRUE) );

		// vrepay is enabled ? and existing payment geozone ?
		if ( ($this->enabled == true) && ((int) $this->zone > 0) ) {

			$this->epay_log( '...check by epay payment zone and zone from billing country' );

			$check_flag = false;
			// determine country zone (e.g. NRW)
			$check_query = xtc_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".$this->zone."' and zone_country_id = '".$order->billing['country']['id']."' order by zone_id");
			while ($check = xtc_db_fetch_array($check_query)) {
				// no country zone for payment zone and country --> OK
				if ($check['zone_id'] < 1) {
					$this->epay_log( 'zone_id: ' . $check['zone_id'] . '...keine Landeszone für epay Steuerzone + Land hinterlegt --> OK' );
					$check_flag = true;
					break;
				}
				// country zone and billing country + payment zone equal --> OK
				elseif ($check['zone_id'] == $order->billing['zone_id']) {
					$this->epay_log( 'zone_id: ' . $check['zone_id'] . ' billing_zone_id: ' . $order->billing['zone_id'] . '...Landeszone aus Rechnung & aus epay Steuerzone + Land gleich --> OK' );
					$check_flag = true;
					break;
				}
			}
			// NOK --> diable module
			if ($check_flag == false) {
				$this->epay_log( '...billing country zone and payment zone not equal --> module disabled !' );
				$this->enabled = false;
			}
		}

	} // end update_status

	// call from checkout_payment.php[1.]
	function javascript_validation() {

		$this->epay_log( "start function javascript_validation()..." );

		return false;
	}

	// call from checkout_confirmation.php[2.]
	function selection() {

		$this->epay_log( "start function selection()..." );

		if ($this->enabled == true)	{
			return array('id'     => $this->code,
						 'module' => $this->title);
		}

	}

	// call from checkout_confirmation.php[2.]
	function pre_confirmation_check() {

		$this->epay_log( "start function pre_confirmation_check()..." );

		return false;
	}

	// call from checkout_confirmation.php[3.]
	function confirmation() {

		$this->epay_log( "start function confirmation()..." );

		$confirmation = array('title' =>  $this->modul_title );
		return $confirmation;
	}

	// call from checkout_confirmation.php[4.]
	function process_button() {

		//global $order, $currencies;
		$this->epay_log( "start function process_button()..."  );

		return false;
	}

	// call from checkout_process[1.]
	function before_process() {

		$this->epay_log( "start function before_process()..." );

		return false;
	}

	// call from checkout_process[2.] -> used by tmpOrders & tmpStatus
	function payment_action() {

		$this->epay_log( "start function payment_action()..." );

		global $order, $insert_id, $xtPrice;

		// shop reference no.
		$this->referenznr = $insert_id;

		// success URL
		$this->urlerfolg = xtc_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');

		// error & cancel URL to checkout_payment
		$fc = urlencode("VREPAY_DIALOG");
		$ft = urlencode(MODULE_PAYMENT_VREPAY_GIROPAY_TEXT_ERR01);
		$urlmsg = 'payment_error=' . $this->code . '&FEHLERCODE='. $fc . '&FEHLERTEXT=' . $ft;
		$this->urlfehler = xtc_href_link( FILENAME_CHECKOUT_PAYMENT, $urlmsg, 'SSL', true, false);
		$this->urlfehler = substr($this->urlfehler, 0, 254);

		// cancel URL = failure URL
		$this->urlabbruch = $this->urlfehler;

		// notify URL
		$this->urlantwort = xtc_href_link("notify_vrepay_giropay.php", '', 'SSL');
		$this->urlantwort = substr($this->urlantwort, 0, 254);

		// condition´s
		$this->urlagb = xtc_href_link(MODULE_PAYMENT_VREPAY_GIROPAY_URLAGB, '', 'SSL');
		$this->urlagb = substr($this->urlagb, 0, 254);

		// merchantno.
		$this->haendlernr = MODULE_PAYMENT_VREPAY_GIROPAY_INSTITUT;

		// used servicetype in vrepay
		$this->service = 'DIALOG';

		// additional data no.1 == customer lastname + firstname (char 25)
		$this->verwendung1 = $order->customer['lastname'] . ", " . $order->customer['firstname'];
		$this->verwendung1 = convert2ascii($this->verwendung1);
		$this->verwendung1 = substr($this->verwendung1, 0, 25);
		
		// additional data no.2  (char 25)
		$this->verwendung2 = substr(MODULE_PAYMENT_VREPAY_GIROPAY_VERWENDUNG2, 0, 25);

		// language for payment dialogue
                if($_SESSION[languages_id] == 2) {
                        $this->sprache = "DE";
                } else {
                        $this->sprache = "EN";
                }

		// currency
		$this->waehrung = $order->info['currency'];
		// amount depends on currency
		$dec = $xtPrice->get_decimal_places( $this->waehrung );
		$exp = pow(10, $dec);
		$this->betrag = round($order->info['total'], $dec);
		$this->betrag = ($this->betrag * $exp);

		$md5 = md5($this->betrag.$this->referenznr.$this->zahlart.MODULE_PAYMENT_VREPAY_GIROPAY_ANTWGEHEIMNIS);

		// collect http-postdata
		$post = array(
			'HAENDLERNR'	=> $this->haendlernr,
			'REFERENZNR'	=> substr(MODULE_PAYMENT_VREPAY_GIROPAY_ORDERPREFIX, 0, 5) . $this->referenznr,
			'BETRAG'		=> $this->betrag,
			'WAEHRUNG'		=> $this->waehrung,
			'URLERFOLG'		=> $this->urlerfolg,
			'URLFEHLER'		=> $this->urlfehler,
			'URLABBRUCH'	=> $this->urlabbruch,
			'URLANTWORT'	=> $this->urlantwort,
			'URLAGB'		=> $this->urlagb,
			'ZAHLART'		=> $this->zahlart,
			'SERVICENAME'	=> $this->service,
			'ANTWGEHEIMNIS'	=> $md5,
			'SPRACHE'		=> $this->sprache,
			// some defines...
			'ARTIKELANZ'	=> '1',
			'BENACHRPROF'	=> "ZHL",
			'VERWENDANZ'	=> "0",
			'TSATYP'		=> $this->tsatyp,
			'AUSWAHL'		=> $this->auswahl,
			'BRAND'			=> $this->brand);

		// additional informations for eurocheck card
		if (!empty( $this->verwendung1 )) {
			$post['VERWENDUNG1'] = $this->verwendung1;
			$post['VERWENDANZ'] = '1';
		}
		if (!empty( $this->verwendung2 )) {
			$post['VERWENDUNG2'] = $this->verwendung2;
			$post['VERWENDANZ']='2';
		}

		$this->epay_log( "function payment_action() postdata -> " . MODULE_PAYMENT_VREPAY_GIROPAY_SERVER . print_r($post, TRUE) );

		// compose HTTP-Query
		$query = http_build_query($post,'','&');

		// HTTP-Basic authorisation
		$auth = MODULE_PAYMENT_VREPAY_GIROPAY_INSTITUT . ":" . MODULE_PAYMENT_VREPAY_GIROPAY_PASSWORT;

		// cURL init & options
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,'https://'. MODULE_PAYMENT_VREPAY_GIROPAY_SERVER);
		curl_setopt($ch, CURLOPT_USERPWD, $auth);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, 1.1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
		curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// proxy for testing
		//curl_setopt($ch, CURLOPT_PROXY, "proxy:8080");
		//curl_setopt($ch, CURLOPT_PROXYUSERPWD, "" .":".  "");

		// cURL execute request
		$ret = curl_exec($ch);

		if ($ret == false) {

			// cURL error occurred
			$this->epay_log( ( "function payment_action() curl error -> " . curl_error($ch) ), true  );

			// compose error message
			$fc = urlencode("CURL");
			$ft = urlencode(MODULE_PAYMENT_VREPAY_GIROPAY_TEXT_ERR03 ." [". curl_error($ch) ."]");
			$urlmsg = 'payment_error=' . $this->code . '&FEHLERCODE='. $fc . '&FEHLERTEXT=' . $ft;

			// close cURL handler
			curl_close($ch);

			// redirect to checkout_payment with error message
			xtc_redirect( xtc_href_link( FILENAME_CHECKOUT_PAYMENT, $urlmsg, 'SSL', true, false) );

		} else {

			// get more info from cURL request
			$info = curl_getinfo($ch);

			// close cURL handler
			curl_close($ch);

			// some debugging
			$this->epay_log( "function payment_action() curl info -> " . print_r($info, TRUE) );
			$this->epay_log( "function payment_action() curl ret -> " . print_r($ret, TRUE) );
			$this->epay_log( "function payment_action() curl total time (sec.) -> " . print_r($info['total_time'], TRUE) );
	        $this->epay_log( "function payment_action() curl http_code -> " . print_r($info['http_code'], TRUE) );

			// control HTTP-Code
		    switch ( $info['http_code'] ) {
		    case "302": // redirect = success

				$httpMsg = $this->http_parse_message($ret);
				$this->epay_log( "function payment_action() curl httpMsg -> " . print_r($httpMsg, TRUE) );
				$httpHeader = $this->http_parse_headers($httpMsg[0]);
				$this->epay_log( "function payment_action() curl httpHeader -> " . print_r($httpHeader, TRUE) );

				// redirect URL for iframe, result from vrepay server
				$this->redirect = $httpHeader['LOCATION'];				

				// Sessiondaten mit redirect URL füllen	zur Weiterverarbeitung
				$_SESSION['vrepaydialog'] = array (
						'redirect'	=> $this->redirect,
				);

				$this->epay_log( "function payment_action() curl sessiondata -> " . print_r($_SESSION['vrepaydialog'], TRUE) );
				
				// redirect to checkout_vrepay_dialog.php
				xtc_redirect($this->form_action_url);

		        break;

		    case "200": // POST-content = error

				$httpMsg = $this->http_parse_message($ret);

				$this->epay_log( ("function payment_action() curl payment error -> " . print_r($httpMsg[1], TRUE)), true );

				// compose error message
				$urlmsg = 'payment_error=' . $this->code . '&'. $httpMsg[1];

				// redirect to checkout_payment with error message
				xtc_redirect( xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $urlmsg, 'SSL', true, false) );

		        break;

		    default:	// others = systemerror

				// compose error message
				$fc = urlencode("HTTP");
				$ft = urlencode(MODULE_PAYMENT_VREPAY_GIROPAY_TEXT_ERR02 . " http-Code [". $info['http_code'] ."]");
				$urlmsg = 'payment_error=' . $this->code . '&FEHLERCODE='. $fc . '&FEHLERTEXT=' . $ft;

				$this->epay_log( ("function payment_action() curl payment error -> " . print_r($urlmsg, TRUE)), true );

				// redirect to checkout_payment with error message
				xtc_redirect( xtc_href_link( FILENAME_CHECKOUT_PAYMENT, $urlmsg, 'SSL', true, false) );

		        break;
		    }

		} // end else empty ret

		return false;
	}

	// http_parse_headers: without PECL Library
	function http_parse_message($message=false){
		$this->epay_log( "start function http_parse_message()..." );

		if($message === false) {
			return false;
		}
		// carriage return to nothing
		$message = str_replace("\r","", $message);
		// header & body divided by TWO new lines
		$message = explode("\n\n", $message, 2);

		return $message;
	}

	// http_parse_headers: without PECL Library
	function http_parse_headers($headers=false){
		$this->epay_log( "start function http_parse_headers()..." );

		if($headers === false) {
			return false;
		}
		// carriage return to nothing
		$headers = str_replace("\r","",$headers);
		// header divided by new line
		$headers = explode("\n",$headers);

		foreach($headers as $value) {
			$header = explode(": ",$value);
			if($header[0] && !$header[1]) {
				$headerdata['STATUS'] = $header[0];
			}
			elseif($header[0] && $header[1]) {
				// uppercase for all keys
				$headerdata[strtoupper($header[0])] = $header[1];
			}
		}
		return $headerdata;
	}

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

	// call from checkout_process[3.] -> success URL
	function after_process() {

		// OrderID
		global $order, $insert_id;

		$this->epay_log( "start function after_process()..." );

		unset ($_SESSION['vrepaydialog']);

		return false;
	}

	// call from notify_vrepay_dialog after receiving notification
	function update_order_status( $orderId, $status ) {

		$this->epay_log( "start function update_order_status()...orderID: " . $orderId . " new status: " . $status);

		$this->order_status = $status;

		$rc = false;
		if ( !empty($orderId) && !empty($this->order_status) ) {
			$rc = xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$this->order_status."' WHERE orders_id='".$orderId."'" );
		}

		$this->epay_log( "end function update_order_status()...rc: " . print_r($rc, TRUE) );
		return $rc;
	}

	// call from notify_vrepay_dialog after receiving notification
	function update_order_status_history( $orderId, $status, $comment = false ) {

		$this->epay_log( "start function update_order_status_history()...orderID: " . $orderId . " new status: " . $status);

		$this->order_status = $status;

		$rc = false;
		if ( !empty($orderId) && !empty($this->order_status) ) {

			$sql_data_array = array (
							'orders_id' => $orderId,
							'orders_status_id' => $this->order_status,
							'date_added' => 'now()',
							'customer_notified' => '0',
							'comments' => $comment
							);

			$rc = xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

		}
		$this->epay_log( "end function update_order_status_history()...rc: " . print_r($rc, TRUE) );
		return $rc;
	}

	// call from checkout_payment link payment options
	function check() {

		$this->epay_log( "start function check()..." );

		// query for checking module stat --> MODULE_PAYMENT_VREPAY_GIROPAY_STATUS
		if (!isset($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_VREPAY_GIROPAY_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	// write epay-logfile nor webserver log
	function epay_log($text, $wwwlog = false) {

		if ( $this->logging == True ) {
			$log_fp = fopen( $this->log ,"a");
			fwrite($log_fp, gmdate("M d Y H:i:s", time()). "> " . $text . "\n");
			fclose($log_fp);
		}
		if ( $wwwlog == True ) {
			error_log(gmdate("M d Y H:i:s", time()). "> vrepay - " . $text);
		}
	}

	// vrepay_dialog install button
	function install() {

		// check for other vrepay modules
		$pos1 = strpos(MODULE_PAYMENT_INSTALLED, "vrepay_elv");
		$pos2 = strpos(MODULE_PAYMENT_INSTALLED, "vrepay_kreditkarte");
		if ( $pos1 !== false || $pos2 !== false )
		{
			$pos = true;
		}
		else
		{
			$pos = false;
		}
		if ($pos === false) {
			// insert new order status table "orders_status"
			xtc_db_query("insert into " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name) values ('".$this->statusWait."', '1', 'vrepay - wait for notification')");
			xtc_db_query("insert into " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name) values ('".$this->statusWait."', '2', 'vrepay - warte auf Benachrichtg.')");
			xtc_db_query("insert into " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name) values ('".$this->statusFailed."', '1', 'vrepay - payment declined')");
			xtc_db_query("insert into " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name) values ('".$this->statusFailed."', '2', 'vrepay - Bezahlung abgelehnt')");
			xtc_db_query("insert into " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name) values ('".$this->statusSuccess."', '1', 'vrepay - payment successful')");
			xtc_db_query("insert into " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name) values ('".$this->statusSuccess."', '2', 'vrepay - Bezahlung erfolgreich')");
		}

		// fill table "configuration"
		// xtc Standard
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VREPAY_GIROPAY_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_VREPAY_GIROPAY_ZONE', '0', '6', '14', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VREPAY_GIROPAY_ALLOWED', '', '6', '0', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VREPAY_GIROPAY_SORT_ORDER', '12', '6', '16', now())");
		// vrepay_dialog
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VREPAY_GIROPAY_SERVER', 'payinte.vr-epay.de/pbr/transaktion', '6', '2', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VREPAY_GIROPAY_INSTITUT', 'vrepay_merchant', '6', '3', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VREPAY_GIROPAY_PASSWORT', 'vrepay_password', '6', '4', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VREPAY_GIROPAY_URLAGB', 'popup_content.php?coID=3', '6', '7', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VREPAY_GIROPAY_ORDERPREFIX', '', '6', '8', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VREPAY_GIROPAY_ANTWGEHEIMNIS', 'your_secret', '6', '8', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VREPAY_GIROPAY_VERWENDUNG2', 'enjoy xtCommerce shopping', '6', '10', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_VREPAY_GIROPAY_LOG', '/tmp/vrepay_giropay.log', '6', '12', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_VREPAY_GIROPAY_LOGGING', 'False', '6', '13', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
	}

	// function to deinstall the vrepay_dialog module
	function remove() {

		xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");

		// check for other vrepay modules
		$pos1 = strpos(MODULE_PAYMENT_INSTALLED, "vrepay_elv");
		$pos2 = strpos(MODULE_PAYMENT_INSTALLED, "vrepay_kreditkarte");
		if ( $pos1 !== false || $pos2 !== false )
		{
			$pos = true;
		}
		else
		{
			$pos = false;
		}
		// delete order_status if leftover
		if ($pos === false) {
			xtc_db_query("delete from " . TABLE_ORDERS_STATUS . " where orders_status_id='".$this->statusWait."'");
			xtc_db_query("delete from " . TABLE_ORDERS_STATUS . " where orders_status_id='".$this->statusFailed."'");
			xtc_db_query("delete from " . TABLE_ORDERS_STATUS . " where orders_status_id='".$this->statusSuccess."'");
		}
	}

	// listing all configuration keys, used from remove() and for configuration modules.php
	function keys() {
		return array(
			'MODULE_PAYMENT_VREPAY_GIROPAY_STATUS',
			'MODULE_PAYMENT_VREPAY_GIROPAY_ZONE',
			'MODULE_PAYMENT_VREPAY_GIROPAY_ALLOWED',
			'MODULE_PAYMENT_VREPAY_GIROPAY_SORT_ORDER',
			'MODULE_PAYMENT_VREPAY_GIROPAY_SERVER',
			'MODULE_PAYMENT_VREPAY_GIROPAY_INSTITUT',
			'MODULE_PAYMENT_VREPAY_GIROPAY_PASSWORT',
			'MODULE_PAYMENT_VREPAY_GIROPAY_URLAGB',
			'MODULE_PAYMENT_VREPAY_GIROPAY_ORDERPREFIX',
			'MODULE_PAYMENT_VREPAY_GIROPAY_ANTWGEHEIMNIS',
			'MODULE_PAYMENT_VREPAY_GIROPAY_VERWENDUNG2',
			'MODULE_PAYMENT_VREPAY_GIROPAY_LOG',
			'MODULE_PAYMENT_VREPAY_GIROPAY_LOGGING',
		);
	}

} // end class vrepay_dialog

if(!function_exists('http_build_query')) {
   function http_build_query($data,$prefix=null,$sep='',$key='') {
       $ret    = array();
           foreach((array)$data as $k => $v) {
               $k    = urlencode($k);
               if(is_int($k) && $prefix != null) {
                   $k    = $prefix.$k;
               };
               if(!empty($key)) {
                   $k    = $key."[".$k."]";
               };

               if(is_array($v) || is_object($v)) {
                   array_push($ret,http_build_query($v,"",$sep,$k));
               }
               else {
                   array_push($ret,$k."=".urlencode($v));
               };
           };

       if(empty($sep)) {
           $sep = ini_get("arg_separator.output");
       };

       return    implode($sep, $ret);
   };
};


if(!function_exists('convert2ascii')) {

	function convert2ascii($text){

		$trans = array (     chr(192) => "A", chr(193) => "A", chr(194) => "A", chr(195) => "A", chr(196) => "Ae", 
   			chr(197) => "A", chr(198) => "AE",chr(199) => "C", chr(200) => "E", chr(201) => "E", chr(202) => "E",
   			chr(203) => "E", chr(204) => "I", chr(205) => "I", chr(206) => "I", chr(207) => "I", chr(208) => "Eth",
   			chr(209) => "N", chr(210) => "O", chr(211) => "O", chr(212) => "O", chr(213) => "O", chr(214) => "Oe",
   			chr(216) => "O", chr(217) => "U", chr(218) => "U", chr(219) => "U", chr(220) => "Ue", chr(221) => "Y",
   			chr(222) => "Thorn", chr(223) => "ss", chr(224) => "a", chr(225) => "a", chr(226) => "a", chr(227) => "a",
   			chr(228) => "ae",chr(229) => "a",chr(230) => "ae", chr(231) => "c", chr(232) => "e", chr(233) => "e",
   			chr(234) => "e", chr(235) => "e", chr(236) => "i", chr(237) => "i", chr(238) => "i", chr(239) => "i",
   			chr(240) => "Eth",chr(241) => "n",chr(242) => "o", chr(243) => "o", chr(244) => "o", chr(245) => "o",
   			chr(246) => "oe",chr(248) => "o", chr(249) => "u", chr(250) => "u", chr(251) => "u", chr(252) => "ue",
   			chr(253) => "y", chr(254) => "thorn",chr(255) => "Trema");

   		$str = strtr($text,$trans);

   		return $str;
	};
};
?>
