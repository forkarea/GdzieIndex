<?php

// ======================================================================
// Formularze i Implementacje opcji zarządzania forum
// ======================================================================
session_start ();
include_once 'header.php';
?>
<div id="body-bot">
	<h2><span><strong>PANEL ADMIN</strong> edycja danych</span></h2>
	<div id="items">
		<?php
		if(isset($_SESSION['login']) && isset($_SESSION['id']) && ($_SESSION['grupa']==2)) {
			$connect = mysql_connect ( $ADRES, $ADMIN, $ADMINPASS );
			$baza = mysql_select_db ( $BAZADANYCH );
			$login = $_SESSION ['login'];
			$id = $_SESSION ['id'];
			$grupa = $_SESSION ['grupa'];
			$tryb = $_GET ['mode'];
			if (isset ( $_GET ['option'] ))
				$opcja = $_GET ['option'];
				
			// ======================================================================
			// Funkcje pobrania potrzebnych danych z bazy do skryptu
			// ======================================================================
			function pobierz_miejsca() {
				$miejsca = mysql_query ( "SELECT idMiejsce, nazwaMiejsce FROM miejsce 
											ORDER BY nazwaMiejsce ASC");
				return $miejsca;
			}
			
			function pobierz_zestawienie_uzytkownikow() {
				$zestawienie_uzytkownikow = mysql_query ( "SELECT U.idUzytkownik, U.login, U.idGrupyUzytkownik, G.nazwaGrupa FROM uzytkownik U 
															INNER JOIN grupyUzytkownik G ON U.idGrupyUzytkownik=G.idGrupyUzytkownik ORDER BY nazwaGrupa, login  ASC" );
				return $zestawienie_uzytkownikow;
			}
			
			function pobierz_zestawienie_pracownikow() {
				$zestawienie_pracownikow = mysql_query ( "SELECT U.idUzytkownik, P.idPracownik, U.login, U.idGrupyUzytkownik, G.nazwaGrupa FROM uzytkownik U
															INNER JOIN grupyUzytkownik G ON U.idGrupyUzytkownik=G.idGrupyUzytkownik
															INNER JOIN pracownik P ON U.idUzytkownik=P.idUzytkownik
															ORDER BY login ASC");
				return $zestawienie_pracownikow;
			}
			
			function pobierz_dolaczenie_pracownikow() {
				$dolaczenie_pracownikow = mysql_query ( "SELECT U.idUzytkownik, P.idUzytkownik, U.login, U.idGrupyUzytkownik, G.nazwaGrupa FROM uzytkownik U
															INNER JOIN grupyUzytkownik G ON U.idGrupyUzytkownik=G.idGrupyUzytkownik
															LEFT JOIN pracownik P ON U.idUzytkownik=P.idUzytkownik WHERE U.idGrupyUzytkownik IN (2,3,4) 
															AND P.idUzytkownik IS NULL ORDER BY login ASC");
				
				return $dolaczenie_pracownikow;
			}
			
			function pobierz_wydzialy() {
				$wydzialy = mysql_query ( "SELECT U.nazwaUczelnia, W.nazwaWydzial, W.idWydzial FROM wydzial W 
											INNER JOIN uczelnia U ON U.idUczelnia=W.idUczelnia ORDER BY U.nazwaUczelnia, W.nazwaWydzial ASC");
				return $wydzialy;
			}
			
			function pobierz_grupy_uzytkownicy() {
				$grupy_uzytkownicy = mysql_query ( "SELECT * FROM grupyUzytkownik" );
				return $grupy_uzytkownicy;
			}
			
			function pobierz_akceptacja_uzytkownikow() {
				$akceptacja_uzytkownikow = mysql_query ( "SELECT U.idUzytkownik, U.login, U.idGrupyUzytkownik, U.potwierdzenie, G.nazwaGrupa FROM uzytkownik U 
															INNER JOIN grupyUzytkownik G ON U.idGrupyUzytkownik=G.idGrupyUzytkownik 
															WHERE U.potwierdzenie=0 ORDER BY login ASC" );
				return $akceptacja_uzytkownikow;
			}
			
			// ======================================================================
			// Funkcja zarzadzanie miejsca edytuj
			// ======================================================================
			function zm_edytuj() {
				$miejsca = pobierz_miejsca();
				echo "<p id='title'>Edytuj nazwę miejsca: </p>";
				if (mysql_num_rows ( $miejsca ) != 0) {
					echo "<form action='edytuj_admin.php?zm=true&mode=zm&option=edycja' method='POST'>
							<table border=0>
								<tr align=center>
									<td>Wybierz edytowane miejsce: <select style='width: 150px' name='miejsce'>
										<option value='" . mysql_result ( $miejsca, 0, 'idMiejsce' ) . "' selected='selected'>" . mysql_result ( $miejsca, 0, 'nazwaMiejsce' ) . "</option>";
					for($i = 1; $i < mysql_num_rows ( $miejsca ); $i ++) {
						echo "<option value='" . mysql_result ( $miejsca, $i, 'idMiejsce' ) . "'>" . mysql_result ( $miejsca, $i, 'nazwaMiejsce' ) . "</option>";
					}
					echo "</select></td>
								<td> Nowa nazwa miejsca: <input style='width: 200px' type='text' name='zmiana_nazwy'/></td>
							<tr>
								<td colspan=2 align=center><input type='submit' name='ok' value='ZAPISZ' /></td>
							</tr>
								</table>
							</form>";
				} else
					echo "Brak miejsc - dodaj nowe miejsca <br/>";
			}
			
			// ======================================================================
			// Funkcja grupy uzytkownikow dodaj
			// ======================================================================
			function zm_dodaj() {
				echo "<p id='title'>Dodaj nowe miejsce: <p>";
				echo "<form action='edytuj_admin.php?zm=true&mode=zm&option=dodaj' method='POST'>
						<table border=0>
							<tr>
								<td>Nazwa miejsca: <input style='width: 200px' type='text' name='miejsce' /></td>
							</tr>
							<tr>
								<td colspan=2 align=center><input type='submit' name='ok' value='DODAJ' /></td>
							</tr>
						</table>
					   </form>";
			}

			// ======================================================================
			// Funkcja zarzadzanie uzytkownikiem edytuj
			// ======================================================================
			function zu_edytuj() {
				$zestawienie_uzytkownikow = pobierz_zestawienie_uzytkownikow();
				$grupy_uzytkownicy = pobierz_grupy_uzytkownicy();
				echo "<p id='title'>Edytuj przydział użytkowników:</p>";
				if (mysql_num_rows ( $zestawienie_uzytkownikow ) != 0) {
					echo "<form action='edytuj_admin.php?zm=true&mode=zu&option=edycja' method='POST'>
							<table border=0>
								<tr align=center>
									<td>Wybierz użytkowników: <select style='width: 200px' id=uzytkownicy name='uzytkownicy[]' multiple='multiple' size='5'>";
					for($i = 0; $i < mysql_num_rows ( $zestawienie_uzytkownikow ); $i ++) {
						echo "<option value='" . mysql_result ( $zestawienie_uzytkownikow, $i, 'idUzytkownik' ) . "' selected='selected'>" . mysql_result ( $zestawienie_uzytkownikow, $i, 'login' ) . " :: " . mysql_result ( $zestawienie_uzytkownikow, $i, 'nazwaGrupa' ) . "</option>";
					}
					echo "</select></td>
								<td>Dodaj uzytkownika do grupy: <select name='grupy_uzytkownicy'>
										<option value='" . mysql_result ( $grupy_uzytkownicy, 0, 'idGrupyUzytkownik' ) . "' selected='selected'>" . mysql_result ( $grupy_uzytkownicy, 0, 'nazwaGrupa' ) . "</option>";
					for($i = 1; $i < mysql_num_rows ( $grupy_uzytkownicy ); $i ++) {
						echo "<option value='" . mysql_result ( $grupy_uzytkownicy, $i, 'idGrupyUzytkownik' ) . "'>" . mysql_result ( $grupy_uzytkownicy, $i, 'nazwaGrupa' ) . "</option>";
					}
					echo "</select></td></tr>
							<tr>
								<td colspan=2 align=center><input type='submit' name='ok' value='DODAJ UŻYTKOWNIKA' /></td>
							</tr>
								</table>
							</form>";
				} else
					echo "Brak użytkowników<br/>";
			}
			
			// ======================================================================
			// Funkcja zarzadzanie pracownikami dodaj
			// ======================================================================
			function zp_dodaj() {
				$dolaczenie_pracownikow = pobierz_dolaczenie_pracownikow();
				$wydzialy = pobierz_wydzialy();
				echo "<p id='title'>Dodaj użytkowników jako pracownicy:</p>";
				if (mysql_num_rows ( $dolaczenie_pracownikow ) != 0) {
					echo "<form action='edytuj_admin.php?zm=true&mode=zp&option=dodaj' method='POST'>
							<table border=0>
								<tr align=center>
									<td>Wybierz użytkowników: <select style='width: 200px' id=pracownicy name='pracownicy[]' multiple='multiple' size='5'>";
					for($i = 0; $i < mysql_num_rows ( $dolaczenie_pracownikow ); $i ++) {
						echo "<option value='" . mysql_result ( $dolaczenie_pracownikow, $i, 'idUzytkownik' ) . "' selected='selected'>" . mysql_result ( $dolaczenie_pracownikow, $i, 'login' ) . " :: " . mysql_result ( $dolaczenie_pracownikow, $i, 'nazwaGrupa' ) . "</option>";
					}
					echo "</select></td>
								<td>Dodaj pracownika do wydzialu: <select style='width: 300px' name='wydzial'>
										<option value='" . mysql_result ( $wydzialy, 0, 'idWydzial' ) . "' selected='selected'>" . mysql_result ( $wydzialy, 0, 'nazwaUczelnia' ) . " :: " . mysql_result ( $wydzialy, 0, 'nazwaWydzial' ) . "</option>";
					for($i = 1; $i < mysql_num_rows ( $wydzialy ); $i ++) {
						echo "<option value='" . mysql_result ( $wydzialy, $i, 'idWydzial' ) . "'>" . mysql_result ( $wydzialy, $i, 'nazwaUczelnia' ) . " :: " . mysql_result ( $wydzialy, $i, 'nazwaWydzial' ) . "</option>";
					}
					echo "</select></td></tr>
							<tr>
								<td colspan=2 align=center><input type='submit' name='ok' value='DODAJ PRACOWNIKA' /></td>
							</tr>
								</table>
							</form>";
				} else
					echo "Brak użytkowników do przydzielenia<br/>";
			}
			
			// ======================================================================
			// Funkcja zarzadzanie pracownikami wypisz
			// ======================================================================
			function zp_usun() {
				$zestawienie_pracownikow = pobierz_zestawienie_pracownikow();
				echo "<p id='title'>Wypisz pracownika z grupy:</p>";
				if (mysql_num_rows ( $zestawienie_pracownikow) != 0) {
					echo "<form action='edytuj_admin.php?zm=true&mode=zp&option=usun' method='POST'>
							<table border=0>
								<tr>
									<td>Wybierz pracowanika: <select style='width: 200px' name='pracownik' >";
					for($i = 0; $i < mysql_num_rows ( $zestawienie_pracownikow ); $i ++) {
						echo "<option value='" . mysql_result ( $zestawienie_pracownikow, $i, 'idPracownik' ) . "' selected='selected'>" . mysql_result ( $zestawienie_pracownikow, $i, 'login' ) . " :: " . mysql_result ( $zestawienie_pracownikow, $i, 'nazwaGrupa' ) . "</option>";
					}
					echo "</select></td>";
					echo "<tr>
										<td colspan=2 align=center><input type='submit' name='ok' value='WYPISZ' /></td>
									</tr>
								</table>
							</form>";
				} else
					echo "Brak pracowników	";
			}
			
			// ======================================================================
			// Funkcja akceptacja uzytkownikow
			// ======================================================================
			function au_dodaj() {
				$akceptacja_uzytkownikow = pobierz_akceptacja_uzytkownikow();
				echo "<p id='title'>Akceptacja nowych użytkowników:</p>";
				if (mysql_num_rows ( $akceptacja_uzytkownikow ) != 0) {
					echo "<form action='edytuj_admin.php?zm=true&mode=au&option=dodaj' method='POST'>
							<table border=0>
								<tr align=center>
									<td>Wybierz użytkowników: <select style='width: 150px' id=uzytkownicy name='uzytkownicy[]' multiple='multiple' size='5'>";
					for($i = 0; $i < mysql_num_rows ( $akceptacja_uzytkownikow ); $i ++) {
						echo "<option value='" . mysql_result ( $akceptacja_uzytkownikow, $i, 'idUzytkownik' ) . "' selected='selected'>" . mysql_result ( $akceptacja_uzytkownikow, $i, 'login' ) . " :: " . mysql_result ( $akceptacja_uzytkownikow, $i, 'nazwaGrupa' ) . "</option>";
					}
					echo "</select></td>
							<tr>
								<td colspan=2 align=center><input type='submit' name='ok' value='ZAAKCEPTUJ UŻYTKOWNIKA' /></td>
							</tr>
								</table>
							</form>";
				} else
					echo "Brak użytkowników<br/>";
			}

			if (isset ( $_GET ['zm'] )) {
				if (isset ( $_GET ['mode'] )) {
					if ($tryb == 'zu') {
						echo '<div>Zmiany przydziału users</div><center>';
						
						//======================================================================
						// Tryb zarzadzanie uzytkownikiem edycja
						//======================================================================
						if ($opcja == 'edycja') {
							if (isset ( $_POST ['uzytkownicy'] )) {
								$uzytkownicy = $_POST ['uzytkownicy'];
								$id_grupy = $_POST ['grupy_uzytkownicy'];
								for($i = 0; ! empty ( $uzytkownicy [$i] ); $i ++)
									if ($uzytkownicy [$i] != $id) {
										$query [$i] = mysql_query ( "UPDATE uzytkownik SET idGrupyUzytkownik='$id_grupy' WHERE idUzytkownik='$uzytkownicy[$i]'" );
										if ($query) {
											echo "<p>Przeniesienie użytkowników ukończone</p>";
										} else {
											echo "<p><b>Błąd! Pozostały stare dane</b></p>";
											zu_edytuj ();
										}
									} else {
										echo "<p><b>Swojego konta administratora nie można zmienić</b></p>";
										zu_edytuj ();
									}
							} else {
								echo "<p><b>Nie wybrano użytkowników</b></p>";
								zu_edytuj ();
							}
						}
						echo '<p><a href="panel_admin.php">Powrót do panelu admina</a>&emsp;::&emsp;<a href="edytuj_admin.php?mode=zu">Zarządzanie USERS</a></p>';
					} else if ($tryb == 'au') {
						if (isset ( $_POST ['uzytkownicy'] )) {
								$uzytkownicy = $_POST ['uzytkownicy'];
								for($i = 0; ! empty ( $uzytkownicy [$i] ); $i ++)
										$query [$i] = mysql_query ( "UPDATE uzytkownik SET potwierdzenie=1 WHERE idUzytkownik='$uzytkownicy[$i]'" );
										if ($query) {
											echo "<p>Użytkownicy zostali zaakceptowani</p>";
										} else {
											echo "<p><b>Błąd! Pozostały stare dane</b></p>";
											zu_edytuj ();
										}
							} else {
								echo "<p><b>Nie wybrano użytkowników</b></p>";
								zu_edytuj ();
							}
						echo '<p><a href="panel_admin.php">Powrót do panelu admina</a>&emsp;::&emsp;<a href="edytuj_admin.php?mode=zu">Zarządzanie USERS</a></p>';
					} else if ($tryb == 'zp') {
						//======================================================================
						// Tryb zarzadzanie pracownikiem dodaj
						//======================================================================
						if ($opcja == 'dodaj') {
							if (isset ( $_POST ['pracownicy'] )) {
								$pracownicy = $_POST ['pracownicy'];
								$id_wydzialy = $_POST ['wydzial'];
								for($i = 0; ! empty ( $pracownicy [$i] ); $i ++)
									$query [$i] = mysql_query ( "INSERT INTO pracownik(idUzytkownik, idWydzial) VALUES ('$pracownicy[$i]','$id_wydzialy')" );
									if ($query) {
										echo "<p>Dodanie pracowników ukończone</p>";
									} else {
										echo "<p><b>Błąd! Pozostały stare dane</b></p>";
										zp_dodaj();
									}
							} else {
								echo "<p><b>Nie wybrano pracowników</b></p>";
								zu_dodaj ();
							}
							//======================================================================
							// Tryb zarzadzanie uzytkownikiem usun
							//======================================================================
						} else if ($opcja == 'usun') {
							$pracownik = $_POST ['pracownik'];
							$query = mysql_query ( "DELETE FROM pracownik WHERE idPracownik='$pracownik'" );
							if ($query)
								echo "<p>Wypisanie pracowanika zakończone</p>";
						}
						echo '<p><a href="panel_admin.php">Powrót do panelu admina</a>&emsp;::&emsp;<a href="edytuj_admin.php?mode=zu">Zarządzanie USERS</a></p>';
						
					} else if ($tryb == 'zm') { 
						//======================================================================
						// Tryb zarzadzanie miejsca edycja
						//======================================================================
						if ($opcja == 'edycja') {
							$id_miejsce = $_POST ['miejsce'];
							$nazwa = $_POST ['zmiana_nazwy'];
							$query = mysql_query ( "SELECT nazwaMiejsce FROM miejsce WHERE nazwaMiejsce='$nazwa'" );
							if (mysql_num_rows ( $query ) != 0 || empty ( $nazwa )) {
								echo "<p><b>Nazwa miejsca już istnieje lub nie wpisano nazwy</b></p>";
								zm_edytuj ();
							} else {
								$query = mysql_query ( "UPDATE miejsce SET nazwaMiejsce='$nazwa' WHERE idMiejsce='$id_miejsce'" );
								if ($query) {
									echo "<p>Edycja nazwy miejsca ukończona</p>";
								} else
									echo "<p><b>Błąd! Pozostały stare dane</b></p>";
							}
						}
						//======================================================================
						// Tryb zarzadzanie miejsca dodaj
						//======================================================================
						if ($opcja == 'dodaj') {
							$nazwa = $_POST ['miejsce'];
							$query = mysql_query ( "SELECT nazwaMiejsce FROM miejsce WHERE nazwaMiejsce='$nazwa' " );
							if (mysql_num_rows ( $query ) != 0 || empty ( $nazwa )) {
								echo "<p><b>Nazwa miejsca już istnieje lub nie wpisano nazwy</b></p>";
								zm_dodaj ();
							} else {
								$query = mysql_query ( "INSERT INTO miejsce (nazwaMiejsce) VALUES('$nazwa')" );
								echo "Dodawanie miejsca ukończone<br/>";
							}
						}		
						echo '<p><a href="panel_admin.php">Powrót do panelu admina</a>&emsp;::&emsp;<a href="edytuj_admin.php?mode=zm">ZARZĄDZANIE MIEJSCE</a></p>';						
					}
				}
				
				//======================================================================
				// Wstepna inicjalizacja formularzy opcji zarzadzania
				//======================================================================
			} else {
				if (isset ( $_GET ['mode'] )) {
					if ($tryb == 'zu' || $tryb == 'zp') {
						echo '<div>Zmiany przydziału users</div><center>';
						zu_edytuj ();
						echo "<hr/>";
						au_dodaj();
						echo "<hr/>";
						zp_dodaj();
						echo "<hr/>";
						zp_usun();
						echo "<hr/>";
						echo '<p><a href="panel_admin.php">Powrót do panelu</a></p>';
					}
					
					if ($tryb == 'zm') {
						echo '<div>Zmiany miejsca przebywania indeksow</div><center>';
						zm_edytuj ();
						echo "<hr/>";
						zm_dodaj();
						echo "<hr/>";
						echo '<p><a href="panel_admin.php">Powrót do panelu</a></p>';
					}
				}
			}
			mysql_close ();
		}
		?>
</div>
<?php include_once 'footer.php'; ?>