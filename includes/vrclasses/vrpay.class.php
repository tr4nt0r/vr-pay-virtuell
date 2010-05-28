<?php
	class NetzkollektivVrpay
	{
		protected $vp_HAENDLERNR		= '';
		protected $vp_REFERENZNR		= '';
		protected $vp_BETRAG			= '';
		protected $vp_WAEHRUNG			= 'EUR';
		protected $vp_URLERFOLG			= '';
		protected $vp_URLFEHLER			= '';
		protected $vp_URLABBRUCH		= '';
		protected $vp_URLANTWORT		= '';
		protected $vp_URLAGB			= '';
		protected $vp_ZAHLART			= '';
		protected $vp_SERVICENAME		= 'DIALOG';
		protected $vp_ANTWGEHEIMNIS		= '';
		protected $vp_SPRACHE			= 'DE';
		protected $vp_ARTIKELANZ		= '1';
		protected $vp_BENACHRPROF		= 'ZHL';
		protected $vp_VERWENDUNG1		= '';
		protected $vp_VERWENDUNG2		= '';
		protected $vp_VERWENDANZ		= '2';
		protected $vp_AUSWAHL			= 'J';
		protected $vp_umgebung			= '';
		protected $vp_verbingungsURL	= '';
		protected $vp_benutzer			= '';
		protected $vp_passwort			= '';
//		protected $vp_artikel_array		= array();
//		protected $vp_artikel_anzeigen	= '0';


		public function __construct ()
		{
			$this->besorgeConfigDaten ();
		}

		public function besorgeConfigDaten ()
		{
			$configDaten = new configvrpay ();

			$this->vp_umgebung		= $configDaten->gibWertAus('nk_vrpay_umgebung');
			if ($this->vp_umgebung == 'testumgebung')
			{
				$this->vp_verbingungsURL	= $configDaten->gibWertAus('nk_vrpay_url_testumgebung');
				$this->vp_benutzer			= $configDaten->gibWertAus('nk_vrpay_benutzer_testumgebung');
				$this->vp_passwort			= $configDaten->gibWertAus('nk_vrpay_passwort_testumgebung');
			}
			else
			{
				$this->vp_verbingungsURL	= $configDaten->gibWertAus('nk_vrpay_url_liveumgebung');
				$this->vp_benutzer			= $configDaten->gibWertAus('nk_vrpay_benutzer_liveumgebung');
				$this->vp_passwort			= $configDaten->gibWertAus('nk_vrpay_passwort_liveumgebung');
			}
			
			$this->vp_HAENDLERNR	= $configDaten->gibWertAus('nk_vrpay_haendlernummer');
			$this->vp_URLERFOLG		= $configDaten->gibWertAus('nk_vrpay_url_erfolg');
			$this->vp_URLANTWORT	= $configDaten->gibWertAus('nk_vrpay_url_antwort');
			$this->vp_URLFEHLER		= $configDaten->gibWertAus('nk_vrpay_url_fehler');
			$this->vp_URLABBRUCH	= $configDaten->gibWertAus('nk_vrpay_url_abbruch');
			$this->vp_URLAGB		= $configDaten->gibWertAus('nk_vrpay_url_agb');

			$this->vp_ANTWGEHEIMNIS	= 'NETZKOLLEKTIV';
			$this->vp_ZAHLART		= 'KAUFEN';
			$this->vp_SPRACHE		= 'DE';
		}

		protected function preisFormatieren ( $preis )
		{
			$preis = str_replace ( '.', '', $preis );
			$preis = str_replace ( ',', '', $preis );
			return $preis;
		}
		
		public function verbindeVrpay ( )
		{
//			$this->dynamischeShopDaten( $werte );
			$uebergabewerte = $this->datenMapping();
			
			$query = http_build_query($uebergabewerte, '', '&');

			$auth = $this->vp_benutzer . ":" . $this->vp_passwort;
echo 'auth = '.$auth.'<br />';
echo 'URL = '.$this->vp_verbingungsURL.'<br />';
echo 'query = '.$query.'<br />';

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->vp_verbingungsURL);
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

			$ret = curl_exec($ch);

			$ubergabestring = curl_multi_getcontent ( $ch );
			$der_link = $this->linkFiltern( $ubergabestring );

			$html = '<html><body>';
			$html.= 'Sie werden weitergeleitet nach VR-Pay';
			$html.= '<form action="'.$der_link.'" method="get" id="vrpay_checkout">';
			$html.= '</form>';

//			$html.= '<script type="text/javascript">document.getElementById("vrpay_checkout").submit();</script>';
			$html.= '</body></html>';

			echo $html;
echo '<pre>';
print_r($ubergabestring);
echo '</pre>';
			die();
		}

		public function dynamischeShopDatenSetzen ( $bestellnummer, $anzahlArtikel, $gesamtbetrag )
		{
			$this->vp_REFERENZNR		= $bestellnummer;
			$this->vp_ARTIKELANZ		= $anzahlArtikel;
			$this->vp_BETRAG			= $this->preisFormatieren ($gesamtbetrag);
			
/*			$this->vp_artikel_anzeigen			= $this->getInfoText('produkte_anzeigen');
			if ($this->vp_artikel_anzeigen == 1)
			{
				$artikel_array = array();
				for ($i=0; $i<$artikel_anzahl; $i++)
				{
					$temp_vari = $i+1;
					$artikel_array["ARTIKELNR$temp_vari"]		= substr($restliche_daten['id'][$i+1], 1, 16);
					$artikel_array["ARTIKELBEZ$temp_vari"]	= $restliche_daten['de'][$i+1];
					$artikel_array["ANZAHL$temp_vari"]		= $restliche_daten['no'][$i+1];
					$artikel_array["EINZELPREIS$temp_vari"]	= $restliche_daten['pr'][$i+1];
				}
				$this->vp_artikel_array = $artikel_array;
			}

			$this->vp_ANTWGEHEIMNIS		= 'netzkollektiv';
			
			$platzhalter1 = '!BNR!';
			$vz_1 = $this->getInfoText('verwendungszweck1');
			$vz_2 = $this->getInfoText('verwendungszweck2');

			$vz_1 = ereg_replace ( $platzhalter1, $this->vp_REFERENZNR, $vz_1 );
			$vz_2 = ereg_replace ( $platzhalter1, $this->vp_REFERENZNR, $vz_2 );
			$this->vp_VERWENDUNG1		= $vz_1;
			$this->vp_VERWENDUNG2		= $vz_2;


			if (empty($this->vp_VERWENDUNG1))
			{
				$verwendungszeilen = '0';
			}
			else
			{
				if (!empty($this->vp_VERWENDUNG2))
				{
					$verwendungszeilen = '2';
				}
				else
				{
					$verwendungszeilen = '1';
				}
			}
			$this->vp_VERWENDANZ		= $verwendungszeilen;
*/
		}

		public function datenMapping ()
		{
			$uebergabewerte = array(
										'HAENDLERNR'		=> $this->vp_HAENDLERNR,
										'REFERENZNR'		=> $this->vp_REFERENZNR,
										'BETRAG' 			=> $this->vp_BETRAG,
										'WAEHRUNG' 			=> $this->vp_WAEHRUNG,
										'URLERFOLG' 		=> $this->vp_URLERFOLG,
										'URLFEHLER' 		=> $this->vp_URLFEHLER,
										'URLABBRUCH' 		=> $this->vp_URLABBRUCH,
										'URLANTWORT' 		=> $this->vp_URLANTWORT,
										'URLAGB' 			=> $this->vp_URLAGB,
										'ZAHLART' 			=> $this->vp_ZAHLART,
										'SERVICENAME' 		=> $this->vp_SERVICENAME,
										'ANTWGEHEIMNIS' 	=> $this->vp_ANTWGEHEIMNIS,
										'SPRACHE' 			=> $this->vp_SPRACHE,
										'ARTIKELANZ' 		=> $this->vp_ARTIKELANZ,
										'BENACHRPROF' 		=> $this->vp_BENACHRPROF, 
										'VERWENDUNG1' 		=> $this->vp_VERWENDUNG1,
										'VERWENDUNG2' 		=> $this->vp_VERWENDUNG2,
										'VERWENDANZ' 		=> $this->vp_VERWENDANZ,
										'AUSWAHL' 			=> $this->vp_AUSWAHL
									);

/*			if ( $this->vp_artikel_anzeigen == 1)
			{
				foreach($this->vp_artikel_array as $variname => $inhalt)
				{
					$uebergabewerte[$variname] = $inhalt;
				}
			}
*/
			return $uebergabewerte;
		}

		protected function linkFiltern( $datenuebergabe ){

			$einzel_zeilen = explode ( "\r\n", $datenuebergabe );

			if (!isset($einzel_zeilen[1]))
			{
				$einzel_zeilen = explode ( "\n", $datenuebergabe );
			}

			foreach ($einzel_zeilen as $inhalt)
			{
				if (substr ( $inhalt, 0, 9 ) == 'Location:')
				{
					$gesuchter_link_temp = split ( ': ', $inhalt );
					$gesuchter_link = $gesuchter_link_temp[1];
				}
			}
		
			return $gesuchter_link;
		} 
/*
		protected function http_parse_message($message=false){
			if($message === false) {
				return false;
			}
			$message = str_replace("\r","", $message);
			$message = explode("\n\n", $message, 2);
			return $message;
		}
	
		protected function http_parse_headers($headers=false){
			if($headers === false) {
				return false;
			}
			$headers = str_replace("\r","",$headers);
			$headers = explode("\n",$headers);
			foreach($headers as $value) {
				$header = explode(": ",$value);
				if($header[0] && !$header[1]) {
					$headerdata['STATUS'] = $header[0];
				} elseif($header[0] && $header[1]) {
					$headerdata[strtoupper($header[0])] = $header[1];
				}
			}
			return $headerdata;
		}
*/
	}

?>