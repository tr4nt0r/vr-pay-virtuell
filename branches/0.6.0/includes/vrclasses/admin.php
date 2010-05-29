<?php
	include_once ('config.class.php');
	
	$configWerte = new configvrpay();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//DE"
		"http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Config</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	</head>
	<body>
<?php
		if (isset ($_POST['nk_vrpay_aufruf']) && !empty($_POST['nk_vrpay_aufruf']))
		{
			if ($configWerte->speichereDaten($_POST))
			{
				echo '<p>!!!Konfiguration gespeichert!!!</p>';
			}
		}
?>
		<form action="admin.php" method="post">
			<p>Konfiguration</p>
			<p>VRPAY</p>
			<table width="500">
				<tr>
					<td></td>
					<td>
						<a href="http://www.netzkollektiv.de" target="_blank"><img src="http://www.netzkollektiv.de/templates/gk_themoment/images/logo.png" border="0"></a>
						<div style=""><b>VR-Pay-Module</b> <a href="http://www.netzkollektiv.de" target="_blank">www.netzkollektiv.de</a></div>
						<div style="margin-top: 4px;"><b>Netzkollektiv, <a href="mailto:info@netzkollektiv.de">info@netzkollektiv.de</a></b><br>
						Namen<br>Version: 0.4.6</div>
					</td>
				</tr>
				<tr>
					<td>Aktiviert</td>
					<td>
						<select name="nk_vrpay_aktiviert">
							<option value="1" selected="selected">Ja</option>
							<option value="0">Nein</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Überschrift</td>
					<td>
						<input name="nk_vrpay_ueberschrift" value="<?php echo $configWerte->gibWertAus('nk_vrpay_ueberschrift'); ?>" type="text" />
					</td>
				</tr>
				<tr>
					<td>Bestellstatus</td>
					<td>
						<select name="nk_vrpay_bestellstatus">
							<option value="ausstehend" selected="selected">Ausstehend</option>
							<option value="vollstaendig">Vollständig</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Händler Nummer</td>
					<td>
						<input name="nk_vrpay_haendlernummer" value="<?php echo $configWerte->gibWertAus('nk_vrpay_haendlernummer'); ?>" type="text" />
					</td>
				</tr>
				<tr>
					<td>Umgebung</td>
					<td>
						<select name="nk_vrpay_umgebung">
							<option value="testumgebung" selected="selected">Test-Umgebung</option>
							<option value="liveumgebung">Live-Umgebung</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>URL Live-Umgebung</td>
					<td>
						<input name="nk_vrpay_url_liveumgebung" value="<?php echo $configWerte->gibWertAus('nk_vrpay_url_liveumgebung'); ?>" type="text" />
					</td>
				</tr>
				<tr>
					<td>Benutzer Live-Umgebung</td>
					<td>
						<input name="nk_vrpay_benutzer_liveumgebung" value="<?php echo $configWerte->gibWertAus('nk_vrpay_benutzer_liveumgebung'); ?>" type="text" />
					</td>
				</tr>
				<tr>
					<td>Passwort Live-Umgebung</td>
					<td>
						<input name="nk_vrpay_passwort_liveumgebung" value="<?php echo $configWerte->gibWertAus('nk_vrpay_passwort_liveumgebung'); ?>" type="text" />
					</td>
				</tr>
				<tr>
					<td>URL Test-Umgebung</td>
					<td>
						<input name="nk_vrpay_url_testumgebung" value="<?php echo $configWerte->gibWertAus('nk_vrpay_url_testumgebung'); ?>" type="text" />
					</td>
				</tr>
				<tr>
					<td>Benutzer Test-Umgebung</td>
					<td>
						<input name="nk_vrpay_benutzer_testumgebung" value="<?php echo $configWerte->gibWertAus('nk_vrpay_benutzer_testumgebung'); ?>" type="text" />
					</td>
				</tr>
				<tr>
					<td>Passwort Test-Umgebung</td>
					<td>
						<input name="nk_vrpay_passwort_testumgebung" value="<?php echo $configWerte->gibWertAus('nk_vrpay_passwort_testumgebung'); ?>" type="text" />
					</td>
				</tr>
				<tr>
					<td>Landeseite Erfolg</td>
					<td>
						<input name="nk_vrpay_url_erfolg" value="<?php echo $configWerte->gibWertAus('nk_vrpay_url_erfolg'); ?>" type="text" />
					</td>
				</tr>
				<tr>
					<td>Landeseite Antwort</td>
					<td>
						<input name="nk_vrpay_url_antwort" value="<?php echo $configWerte->gibWertAus('nk_vrpay_url_antwort'); ?>" type="text" />
					</td>
				</tr>
				<tr>
					<td>Landeseite Fehler</td>
					<td>
						<input name="nk_vrpay_url_fehler" value="<?php echo $configWerte->gibWertAus('nk_vrpay_url_fehler'); ?>" type="text" />
					</td>
				</tr>
				<tr>
					<td>Landeseite Abbruch</td>
					<td>
						<input name="nk_vrpay_url_abbruch" value="<?php echo $configWerte->gibWertAus('nk_vrpay_url_abbruch'); ?>" type="text" />
					</td>
				</tr>
				<tr>
					<td>Seite zu den AGB's</td>
					<td>
						<input name="nk_vrpay_url_agb" value="<?php echo $configWerte->gibWertAus('nk_vrpay_url_agb'); ?>" type="text" />
					</td>
				</tr>
			</table>
			<input name="nk_vrpay_aufruf" type="hidden" value="1" />
			<input type="submit" value=" Absenden ">
		</form>
	</body>
</html>