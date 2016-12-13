<?php

//======================================================================
// Skrypt rejestrujący do bazy nowego użytkownika
//======================================================================

session_start ();
include_once 'header.php';

?>
<div id="body-bot">
	<h2><span><strong>REJESTRACJA</strong></span></h2>
	<div id="items">
		<?php
			//======================================================================
			// Formularz rejestracyjny
			//======================================================================
			function formularz() {
				$liczba1 = rand ( 0, 20 );
				$liczba2 = rand ( 0, 20 );
				echo "<center>
					<form action='rejestracja.php?rej=true&l1=" . $liczba1 . "&l2=" . $liczba2 . "' method='POST'>
					<table border=0>
						<tr>
							<td>Login</td><td><input type='text' name='login' /></td>
						</tr>
						<tr>
							<td>Hasło</td><td><input type='password' name='haslo' /></td>
							<td>  Powtórz hasło</td><td><input type='password' name='haslo2' /></td>
						</tr>
						<tr>
							<td>Mail</td><td><input type='text' name='mail' /></td>
							<td>  Powtórz mail</td><td><input type='text' name='mail2' /></td>
						</tr>
						<tr>
							<td colspan=2><input type='checkbox' name='regulamin' value='regulamin' />Akceptuje <a href='regulamin.php'>regulamin platformy</a></td>
						</tr>
					</table>
					<br />Weryfikacja osoby: $liczba1 plus $liczba2 = </td><td><input type='text' name='weryfikacja' /><br/>
					<br /><input type='submit' name='ok' value='Rejestruj' />
				</form>
				</center>";
			}
			//======================================================================
			// Funkcja sprawdzajaca poprawnosc adresu email
			//======================================================================
			function checkEmail($email) {
				$sprawdz = '/^[a-zA-Z0-9.\-_]+@[a-zA-Z0-9\-.]+\.[a-zA-Z]{2,4}$/';
				if (preg_match ( $sprawdz, $email ))
					return true;
				else
					return false;
			}
			?>
								  
			<?php
			if (isset ( $_GET ['rej'] )) {
				//======================================================================
				// Pobranie i sprawdzenie danych z formularza
				//======================================================================
				$login = $_POST ['login'];
				$haslo = $_POST ['haslo'];
				$haslo2 = $_POST ['haslo2'];
				$mail = $_POST ['mail'];
				$mail2 = $_POST ['mail2'];
				$weryfikacja = $_POST ['weryfikacja'];
				$data = date ( 'Y-m-d H:i:s' );
				$liczba1 = $_GET ['l1'];
				$liczba2 = $_GET ['l2'];
				$wynik = $liczba1 + $liczba2;
				if ($wynik != $weryfikacja) {
					echo "<p>Podaj poprawną odpowiedź weryfikującą</p>";
					formularz ();
				} else if (empty ( $login ) || empty ( $haslo ) || empty ( $haslo2 ) || empty ( $mail )) {
					echo "<p>Uzupełnij wszystkie pola.</p>";
					formularz ();
				} else if ($haslo != $haslo2) {
					echo "<p>Hasła muszą byc takie same</p>";
					formularz ();
				} else if ($mail != $mail2) {
					echo "<p>Adresy email muszą byc takie same</p>";
					formularz ();
				} else if (! checkEmail ( $mail )) {
					echo "<p>Adres email ma złą składnie (login@domena)</p>";
					formularz ();
				} else if (! isset ( $_POST ['regulamin'] )) {
					echo "<p>Musisz akceptować regulamin</p>";
					formularz ();
				} else {
					$connect = mysql_connect ( $ADRES, $ADMIN, $ADMINPASS );
					$baza = mysql_select_db ( $BAZADANYCH );
					//======================================================================
					// Sprawdzenie czy uzytkownik nie istnieje
					//======================================================================
					// ======================================================================
					// Zabezpieczenie SQL Injection
					// ======================================================================
					$login=htmlentities($login, ENT_QUOTES, "UTF-8");
					$mail=htmlentities($mail, ENT_QUOTES, "UTF-8");
					$data=htmlentities($data, ENT_QUOTES, "UTF-8");
					$query = mysql_query ( sprintf("SELECT login FROM uzytkownik WHERE login='%s'", mysql_real_escape_string($login) ));
					if (mysql_num_rows ( $query ) == 0) {
						$query = mysql_query ( sprintf("SELECT mail FROM uzytkownik WHERE mail='%s'", mysql_real_escape_string($mail) ));
						if (mysql_num_rows ( $query ) == 0) {
							//======================================================================
							// Dodawanie użytkownika do bazy
							//======================================================================
							$query = mysql_query ( sprintf("INSERT INTO uzytkownik(idGrupyUzytkownik,login,haslo,mail,rejestracja,status_2,potwierdzenie) VALUES('1','%s', '" . md5 ( $haslo ) . "', '%s','%s','0','0')", mysql_real_escape_string($login), mysql_real_escape_string($mail), mysql_real_escape_string($data) ));
							if ($query) {
								$id_uzytkownika = mysql_result ( mysql_query ( "SELECT idUzytkownik FROM uzytkownik ORDER BY idUzytkownik DESC LIMIT 1" ), 0, 0 );
								$query2 = mysql_query ( "INSERT INTO uzytkownikInfo(idUzytkownik) VALUES('$id_uzytkownika')" );
								echo "Rejestracja ukończona. Możesz się teraz zalogować.";
							}
						} else {
							echo "<p>Użytkownik z takim adresem email już istnieje</p>";
							formularz ();
						}
					} else {
						echo "<p>Użytkownik o tej nazwie użytkownika już istnieje</p>";
						formularz ();
					}
				}
			} else {
				formularz ();
			}
		?>
	</div>
<?php include_once 'footer.php'; ?>
