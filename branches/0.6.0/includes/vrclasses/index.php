<?php
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//DE"
		"http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Warenkorb</title>
	</head>
	<body>
		<form action="vrpay.php" method="post">
			<p>Warenkorb</p>
			<p>Produkte</p>
			<table width="500">
				<tr>
					<td>Pos</td>
					<td>Produktname</td>
					<td>Preis</td>
					<td>Anzahl</td>
					<td>Gesamtpreis</td>
				</tr>
				<tr>
					<td colspan="10"><hr /></td>
				</tr>
				<tr>
					<td>z.B.:</td>
					<td>Tisch_Rot</td>
					<td>123.12</td>
					<td>10</td>
					<td>1231.20</td>
				</tr>
				<tr>
					<td>1</td>
					<td><input type="text" name="pos_1" value="Tisch_Rot"></td>
					<td><input type="text" name="preis_1" value="123.12"></td>
					<td><input type="text" name="anzahl_1" value="10"></td>
					<td><input type="text" name="gesamt_1" value="1231.20"></td>
				</tr>
				<tr>
					<td>2</td>
					<td><input type="text" name="pos_2" value=""></td>
					<td><input type="text" name="preis_2" value=""></td>
					<td><input type="text" name="anzahl_2" value=""></td>
					<td><input type="text" name="gesamt_2" value=""></td>
				</tr>
				<tr>
					<td>3</td>
					<td><input type="text" name="pos_3" value=""></td>
					<td><input type="text" name="preis_3" value=""></td>
					<td><input type="text" name="anzahl_3" value=""></td>
					<td><input type="text" name="gesamt_3" value=""></td>
				</tr>
				<tr>
					<td colspan="10"><hr /></td>
				</tr>
			</table>
			<p>Bestellnummer</p>
			<input type="text" name="bestellnummer" value="100">
			<input type="submit" value=" Absenden ">
		</form>
	</body>
</html>