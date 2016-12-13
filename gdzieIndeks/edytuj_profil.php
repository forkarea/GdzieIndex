<?php

//======================================================================
// Formularze i Implementacje opcji zarządzania profilem
//======================================================================

session_start ();
include_once 'header.php';
?>

<div id="body-bot">
	<h2><span><strong>PROFIL UŻYTKOWNIKA</strong> edycja danych</span></h2>
	<div id="items">
		<?php
		if(isset($_SESSION['login']) && isset($_SESSION['id'])) {
			$connect = mysql_connect ( $ADRES, $ADMIN, $ADMINPASS );
			$baza = mysql_select_db ( $BAZADANYCH );
			$login = $_SESSION ['login'];
			$id = $_SESSION ['id'];
			$tryb = $_GET ['mode'];
			if (isset ( $_GET ['option'] ))
				$opcja = $_GET ['option'];
				
			// ======================================================================
			// Pobranie potrzebnych danych z bazy do skryptu
			// ======================================================================
			if ($tryb == 'edytuj') {
				$staredane = mysql_query ( "SELECT U.avatar, Info.imie, Info.nazwisko, Info.plec, Info.dataUrodzenia, Info.zainteresowania, Info.lokalizacja, Info.telefon
						FROM uzytkownik U INNER JOIN uzytkownikInfo Info ON Info.idUzytkownik=U.idUzytkownik
						WHERE U.login='$login'" );
				$starepracownik = mysql_query ( "SELECT nrPokoju FROM pracownik WHERE idUzytkownik='$id'" );
				if(mysql_num_rows($starepracownik))
					$pokoj = mysql_result ($starepracownik,0,'nrPokoju');
				$sygnatura = mysql_result ( $staredane, 0, 'avatar' );
				$imie = mysql_result ( $staredane, 0, 'imie' );
				$nazwisko = mysql_result ( $staredane, 0, 'nazwisko' );
				$plec = mysql_result ( $staredane, 0, 'plec' );
				$urodziny = mysql_result ( $staredane, 0, 'dataUrodzenia' );
				$zainteresowania = mysql_result ( $staredane, 0, 'zainteresowania' );
				$lokalizacja = mysql_result ( $staredane, 0, 'lokalizacja' );
				$telefon = mysql_result ( $staredane, 0, 'telefon');
			}

			//======================================================================
			// Funkcja sprawdzenia poprawnosci daty
			//======================================================================
			function urodziny($date) {
				$sprawdz = '/^[1-2]{1}+[0-9]{1}+[0-9]{1}+[0-9]{1}+\-[0-1]{1}+[0-9]{1}+\-[0-3]{1}+[0-9]{1}$/';
				if (preg_match ( $sprawdz, $date ))
					return true;
				else
					return false;
			}

			//======================================================================
			// Funkcja sprawdzenia poprawnosci emaila
			//======================================================================
			function checkEmail($email) {
				$sprawdz = '/^[a-zA-Z0-9.\-_]+@[a-zA-Z0-9\-.]+\.[a-zA-Z]{2,4}$/';
				if (preg_match ( $sprawdz, $email ))
					return true;
				else
					return false;
			}

			//======================================================================
			// Formularz zmiany adresu mail
			//======================================================================
			function formularz_email() {
				global $login;
				$starymail = mysql_query ( "SELECT mail FROM uzytkownik WHERE login='$login'" );
				echo "Dotychczasowy adres email: " . mysql_result ( $starymail, 0, 'mail' ) . "<br /><br />";
				echo "<form action='edytuj_profil.php?zm=true&mode=mail' method='POST'>
						<table border=0>
							<tr>
								<td id='fnazwa_pola'>Nowy mail: </td><td><input type='text' name='email' /></td>
							</tr>
							<tr>
								<td id='fnazwa_pola'>  Powtórz mail: </td><td><input type='text' name='email2' /></td>
							</tr>
							<tr>
								<td colspan=2 align=center><input type='submit' name='ok' value='ZAPISZ' /></td>
							</tr>
						</table>
					</form>";
			}

			//======================================================================
			// Formularz zmiany hasla
			//======================================================================
			function formularz_haslo() {
				echo "<form action='edytuj_profil.php?zm=true&mode=haslo' method='POST'>
						<table border=0>
							<tr>
								<td id='fnazwa_pola'>Stare hasło: </td><td><input type='password' name='sh' /></td>
							</tr>
							<tr>
								<td id='fnazwa_pola'>  Nowe hasło: </td><td><input type='password' name='haslo' /></td>
							</tr>
							<tr>
								<td id='fnazwa_pola'>  Powtórz hasło: </td><td><input type='password' name='haslo2' /></td>
							</tr>
							<tr>
								<td colspan=2 align=center><input type='submit' name='ok' value='ZAPISZ' /></td>
							</tr>
						</table>
					</form>";
			}

			if (isset ( $_GET ['zm'] )) {
				if (isset ( $_GET ['mode'] )) {
					
					//======================================================================
					// Tryb mail
					//======================================================================
					if ($tryb == 'mail') {
						echo '<div>Zmiana adresu email</div>
							<center>';
						$mail = $_POST ['email'];
						$mail2 = $_POST ['email2'];
						$query = mysql_query ( "SELECT mail FROM uzytkownik WHERE mail='$mail'" );
						if (empty ( $mail ) || empty ( $mail2 )) {
							echo "<p><b>Uzupełnij wszystkie pola.</b></p>";
							formularz_email ();
						} else if ($mail != $mail2) {
							echo "<p><b>Maile muszą byc takie same</b></p>";
							formularz_email ();
						} else if (! checkEmail ( $mail )) {
							echo "<p>Adres email ma złą składnie (login@domena)</p><br />";
							formularz_email ();
						} else if (mysql_num_rows ( $query ) != 0) {
							echo "<p><b>Inny użytkownik korzysta z tego maila</b></p>";
							formularz_email ();
						} else {
							$zmiana = mysql_query ( "UPDATE uzytkownik SET mail='$mail' WHERE login='$login'" );
							if ($zmiana) {
								echo "<p>Zmieniono adres email</p>";
							}
						}
						
						//======================================================================
						// Tryb haslo
						//======================================================================
					} else if ($tryb == 'haslo') {
						echo '<div>Zmiana hasła</div>
							<center>';
						$starehaslo = $_POST ['sh'];
						$haslo = $_POST ['haslo'];
						$haslo2 = $_POST ['haslo2'];
						if (empty ( $haslo ) || empty ( $haslo2 ) || empty ( $starehaslo )) {
							echo "<p><b>Uzupełnij wszystkie pola.</b></p>";
							formularz_haslo ();
						} else if ($haslo != $haslo2) {
							echo "<p><b>Hasła muszą byc takie same</b></p>";
							formularz_haslo ();
						} else {
							$query = mysql_query ( "SELECT login FROM uzytkownik WHERE haslo=MD5('$haslo')" );
							if (! $rows = mysql_fetch_row ( $query )) {
								$zmiana = mysql_query ( "UPDATE uzytkownik SET haslo=MD5('$haslo') WHERE login='$login'" );
								if ($zmiana) {
									echo "<p>Zmieniono hasło</p>";
								}
							} else {
								echo "<p><b>Stare hasło jest nie poprawne</b></p>";
								formularz_haslo ();
							}
						}
						
						//======================================================================
						// Tryb edycji danych osobowych
						//======================================================================
					} else if ($tryb == 'edytuj') {
						echo '<div>Edycja profilu</div>
							<center>';
						if ($sygnatura != $_POST ['sygnatura'])
							$sygnatura = $_POST ['sygnatura'];
						if ($imie != $_POST ['imie'])
							$imie = $_POST ['imie'];
						if ($nazwisko != $_POST ['nazwisko'])
							$nazwisko = $_POST ['nazwisko'];
						if ($plec != $_POST ['plec'])
							$plec = $_POST ['plec'];
						if ($urodziny != $_POST ['urodziny'])
							if (urodziny ( $_POST ['urodziny'] ))
								if ($_POST ['urodziny'] < date ( "Y-m-d" ))
									$urodziny = $_POST ['urodziny'];
								else
									echo "<p>Nie zmieniono daty urodzin. Podano date z przyszlości.</p>";
							else
								echo "<p>Nie zmieniono daty urodzin. Data ma złą formę (0000-00-00).</p>";
						if ($lokalizacja != $_POST ['lokalizacja'])
							$lokalizacja = $_POST ['lokalizacja'];
						if ($zainteresowania != $_POST ['zainteresowania'])
							$zainteresowania = $_POST ['zainteresowania'];
						$czyPracownik = mysql_query ("SELECT idPracownik FROM pracownik WHERE idUzytkownik='$id'");
						if (mysql_num_rows( $czyPracownik)!=0) {
							if ($pokoj != $_POST ['pokoj'])
								$pokoj = $_POST ['pokoj'];
						}
						if ($telefon != $_POST ['telefon'])
							$telefon = $_POST ['telefon'];
						$zmiana1 = mysql_query ( "UPDATE uzytkownik SET avatar='$sygnatura' WHERE login='$login'" );
						if ($zmiana1) {
							$zmiana2 = mysql_query ( "UPDATE uzytkownikInfo SET imie='$imie', nazwisko='$nazwisko', plec='$plec', dataUrodzenia='$urodziny', zainteresowania='$zainteresowania', lokalizacja='$lokalizacja', telefon='$telefon' WHERE idUzytkownik='$id'" );
							if (mysql_num_rows( $czyPracownik)!=0) {
								$zmiana3 = mysql_query ( "UPDATE pracownik set nrPokoju='$pokoj' WHERE idUzytkownik='$id'");
							}
							if ($zmiana2) {
								echo "<p>Dane zostały zmienione</p>";
							} else
								echo "<p><b>Błąd! Pozostały stare dane</b></p>";
						} else
							echo "<p><b>Błąd! Pozostały stare dane</b></p>";
					} 
					echo '<p><a href="user.php?id=' . $_SESSION ["id"] . '">Powrót do profilu</a></p>';
				}
				
				
				//======================================================================
				// Inicjalizacja poczatkowa formularzy
				//======================================================================
			} else {
				if (isset ( $_GET ['mode'] )) {
					echo "<center>";
					if ($tryb == 'mail') {
						echo 'Zmiana adresu email<br>';
						formularz_email ();
					} else if ($tryb == 'haslo') {
						echo 'Zmiana hasła';
						formularz_haslo ();
					} else if ($tryb == 'edytuj') {
						echo 'Zmiana profilu';
						echo "<form action='edytuj_profil.php?zm=true&mode=edytuj' method='POST'>
									<table border=0>
										<tr>
											<td id='fnazwa_pola'>Sygnatura: </td><td><input type='text' name='sygnatura' value='$sygnatura'/></td>
										</tr>
										<tr>
											<td id='fnazwa_pola'>Imie: </td><td><input type='text' name='imie' value='$imie'/></td>
										</tr>
										<tr>
											<td id='fnazwa_pola'>Nazwisko: </td><td><input type='text' name='nazwisko' value='$nazwisko'/></td>
										</tr>
										<tr>
											<td id='fnazwa_pola'>Płeć: </td>
												";
						if ($plec) {
							echo "<td><input type='radio' name='plec' value='0'> Kobieta<br>
									<input type='radio' name='plec' value='1' checked>Mężczyzna<br> </td>";
						} else {
							echo "<td><input type='radio' name='plec' value='0' checked> Kobieta<br>
									<input type='radio' name='plec' value='1'>Mężczyzna<br> </td>";
						}
						echo "
										</tr>
										<tr>
											<td id='fnazwa_pola'>Data urodzin: </td><td><input type='text' name='urodziny' placeholder='yyyy-mm-dd' value='$urodziny'></td>
										</tr>
										<tr>
											<td id='fnazwa_pola'>Lokalizacja: </td><td><input type='text' name='lokalizacja' value='$lokalizacja'/></td>
										</tr>";
										if (mysql_num_rows($starepracownik)){
											echo "<tr><td id='fnazwa_pola'>Nr pokoju: </td><td><input type='text' name='pokoj' value='$pokoj'/></td></tr>";
										}
								echo "	<tr>
											<td id='fnazwa_pola'>Telefon: </td><td><input type='text' name='telefon' value='$telefon'/></td>
										</tr>
										<tr>
											<td id='fnazwa_pola'>Zainteresowania: </td><td><input type='text' name='zainteresowania' value='$zainteresowania'/></td>
										</tr>
										<tr>
											<td colspan=2 align=center><input type='submit' name='ok' value='ZAPISZ' /></td>
										</tr>
									</table>
							</form>";
					} 
					echo "</center>";
				}
			}
			mysql_close ();
		}
			?>
	</div>

<?php include_once 'footer.php'; ?>