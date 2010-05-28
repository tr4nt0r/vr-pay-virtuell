<?php
	class configvrpay
	{
		protected $configVariablen = array (
												'nk_vrpay_aktiviert'				=> '',
												'nk_vrpay_ueberschrift'				=> '',
												'nk_vrpay_bestellstatus'			=> '',
												'nk_vrpay_haendlernummer'			=> '',
												'nk_vrpay_umgebung'					=> '',
												'nk_vrpay_url_liveumgebung'			=> '',
												'nk_vrpay_benutzer_liveumgebung'	=> '',
												'nk_vrpay_passwort_liveumgebung'	=> '',
												'nk_vrpay_url_testumgebung'			=> '',
												'nk_vrpay_benutzer_testumgebung'	=> '',
												'nk_vrpay_passwort_testumgebung'	=> '',
												'nk_vrpay_url_erfolg'				=> '',
												'nk_vrpay_url_fehler'				=> '',
												'nk_vrpay_url_abbruch'				=> '',
												'nk_vrpay_url_antwort'				=> '',
												'nk_vrpay_url_agb'					=> ''
											);

		public function __construct ()
		{
			include_once ('config-vrpay.php');
			foreach ( $this->configVariablen as $schluessel => $inhalt)
			{
				$this->configVariablen[$schluessel] = $$schluessel;
			}
		}
		
		public function gibWertAus ( $variablenname = '')
		{
			foreach ( $this->configVariablen as $schluessel => $inhalt)
			{
				if ($variablenname == $schluessel)
				{
					$istinarray = true;
				}
			}
			if ($istinarray)
			{
				return $this->configVariablen[$variablenname];
			}
			else
			{
				return '';
			}
		}
		
		protected function speichereConfigDatei ()
		{
			$DateiInhalt = '<?php'."\n";
			$DateiInhalt .= '// !!!! Bitte den Inhalt der Datei nur über die Konfigurationsoberfläche verwalten !!!!'."\n";
			foreach ( $this->configVariablen as $schluessel => $inhalt)
			{
				$DateiInhalt .= '$'.$schluessel.' = \''.$inhalt.'\';'."\n";
			}
			$DateiInhalt .= '?>'."\n";
			
			$fp = fopen ( 'config-vrpay.php', 'w' );
			fwrite ( $fp, $DateiInhalt);
			fclose ( $fp );
			
			return true;
		}
		
		public function speichereDaten ( $uebergabeArray = '' )
		{
			if (is_array($uebergabeArray))
			{
				foreach ( $this->configVariablen as $schluessel => $inhalt)
				{
					if (isset($uebergabeArray[$schluessel]))
					{
						$this->configVariablen[$schluessel] = $uebergabeArray[$schluessel];
					}
				}

				if ($this->speichereConfigDatei())
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
	}
?>