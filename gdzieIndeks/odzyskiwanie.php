<?php
// ======================================================================
// Skrypt odzyskiwania hasła użytkownika
// ======================================================================
session_start ();
include_once 'header.php';
?>

<div id="body-bot">
	<h2><span><strong>ODZYSKIWANIE HASŁA</strong></span></h2>
	<div id="items">
		<?php
			function formularz() {
				echo '<center>
					<form action="odzyskiwanie.php?newpass=true" method="POST">
					<table border="0">
					<tr>
						<td>Login</td><td><input type="text" name="login"></td>
					</tr>
					<tr>
						<td>Mail</td><td><input type="text" name="mail"></td>
					</tr>
					<tr>
						<td>Nowe hasło</td><td><input type="password" name="pass"></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2"><input type="submit" name="zmien" value="Zmien haslo"></td>
					</tr>
					</table>
					</form>
					</center>';
			}

			if (isset ( $_GET ['newpass'] )) {
				$data = date ( 'Y-m-d H:i:s' );
				$uzytkownik = $_POST ['login'];
				$mail = $_POST ['mail'];
				$haslo = $_POST ['pass'];
				
				$connect = mysql_connect ( $ADRES, $ADMIN, $ADMINPASS );
				$baza = mysql_select_db ( $BAZADANYCH );
				
				// ======================================================================
				// Sprawdzenie czy użytkownik podal dobre dane werufikujace mail i login
				// ======================================================================
				$query = mysql_query ( "SELECT login, mail, potwierdzenie FROM uzytkownik WHERE login='$uzytkownik' AND mail='$mail'" );
				if (! mysql_num_rows ( $query )) {
					echo '<p>Błędny login lub mail</p>';
					formularz();
				} else if (mysql_result ( $query, 0, 'potwierdzenie' ) == 0) {
					echo '<p>Twoje konto nie zostalo jeszcze aktywowane</p>';
				} else {
					$query = mysql_query ( "UPDATE uzytkownik SET haslo=MD5('$haslo') WHERE login='$uzytkownik' AND mail='$mail'" );
					if ($query)
						echo "<p>Hasło zostało zmienione. Możesz się zalogować</p>";
					else
						echo mysql_error ();
				}
				mysql_close ();
			} else {
				// ======================================================================
				// Inicjalizacja formularza odzyskiwania
				// ======================================================================
				formularz();
			}
			?>
	</div>

<?php include_once 'footer.php'; ?>