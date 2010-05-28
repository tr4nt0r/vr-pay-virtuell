<?php
	include_once ('vrpay.class.php');
	include_once ('config.class.php');

// Daten aus dem Shopsystem auslesen ------------------------ start ------------------------
	$i = 1;
	$anzahlArtikel = 0;
	$gesamtbetrag = 0;
	while (isset($_POST['pos_'.$i]) && !empty($_POST['pos_'.$i]))
	{
		$anzahlArtikel++;
		$gesamtbetrag = $gesamtbetrag + $_POST['gesamt_'.$i];
		$i++;
	}
// Daten aus dem Shopsystem auslesen ------------------------ ende -------------------------


	$vrpayklasse = new NetzkollektivVrpay ();
	$vrpayklasse->dynamischeShopDatenSetzen ( $_POST['bestellnummer'], $anzahlArtikel, $gesamtbetrag );
	$vrpayklasse->verbindeVrpay();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//DE"
		"http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Weiterleitung</title>
	</head>
	<body>
<?php
		echo 'Artikel = '.$anzahlArtikel.'<br />';
		echo 'Gesamtbetrag = '.$gesamtbetrag.'<br />';
		echo '<br />';
		
		echo 'POST<pre>';
		print_r($_POST);
		echo '</pre>';
		
		echo 'vrpayklasse<pre>';
		print_r($vrpayklasse);
		echo '</pre>';
?>
	</body>
</html>