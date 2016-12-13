<?php

// ======================================================================
// Formularze i Implementacje opcji zarządzania forum
// ======================================================================
session_start ();
include_once 'header.php';
?>
<div id="body-bot">
	<h2><span><strong>PANEL MODERATOR</strong> edycja danych</span></h2>
	<div id="items">
		<?php
		if(isset($_SESSION['login']) && isset($_SESSION['id']) && ($_SESSION['grupa']==2 || $_SESSION['grupa']==4)) {
			$connect = mysql_connect ( $ADRES, $ADMIN, $ADMINPASS );
			$baza = mysql_select_db ( $BAZADANYCH );
			$login = $_SESSION ['login'];
			$id = $_SESSION ['id'];
			$grupa = $_SESSION ['grupa'];
			$tryb = $_GET ['mode'];
			if (isset ( $_GET ['option'] ))
				$opcja = $_GET ['option'];
				
			// ======================================================================
			// Pobranie potrzebnych danych z bazy do skryptu
			// ======================================================================
			function pobierz_uczelnia() {
				$uczelnia = mysql_query ( "SELECT * FROM uczelnia ORDER BY nazwaUczelnia ASC" );
				return $uczelnia;
			}
			
			function pobierz_wydzial() {
				$wydzial = mysql_query ( "SELECT W.idWydzial, W.idUczelnia, W.nazwaWydzial, U.nazwaUczelnia, W.adres FROM wydzial W 
											INNER JOIN uczelnia U ON u.idUczelnia=W.idUczelnia ORDER BY nazwaUczelnia, nazwaWydzial ASC" );
				return $wydzial;
			}
			
			function pobierz_kierunek() {
				$kierunek = mysql_query ( "SELECT K.idKierunek, K.idWydzial, K.nazwaKierunek, K.dataRozpoczecia, K.dataZakonczenia, K.opis, W.nazwaWydzial, U.nazwaUczelnia FROM kierunek K 
											INNER JOIN wydzial W ON W.idWydzial=K.idWydzial 
											INNER JOIN uczelnia U ON U.idUczelnia=W.idUczelnia 
											ORDER BY nazwaUczelnia, nazwaWydzial, nazwaKierunek, dataRozpoczecia ASC" );
				return $kierunek;
			}
			
			function pobierz_grupa() {
				$grupa = mysql_query ( "SELECT G.idGrupyKierunek,G.idKierunek, G.nazwaGrupa, K.nazwaKierunek, K.dataRozpoczecia, W.nazwaWydzial, U.nazwaUczelnia FROM grupykierunek G
											INNER JOIN kierunek K ON K.idKierunek=G.idKierunek
											INNER JOIN wydzial W ON W.idWydzial=K.idWydzial 
											INNER JOIN uczelnia U ON U.idUczelnia=W.idUczelnia 
											ORDER BY nazwaUczelnia, nazwaWydzial, nazwaKierunek, dataRozpoczecia ASC" );
				
				return $grupa;
			}
			
			function pobierz_zestawienie_studentow() {
				$zestawienie_studentow = mysql_query ( "SELECT U.idUzytkownik, U.idGrupyKierunek, U.login, U.idGrupyUzytkownik, G.nazwaGrupa FROM uzytkownik U 
						INNER JOIN grupyUzytkownik G ON U.idGrupyUzytkownik=G.idGrupyUzytkownik
						WHERE U.idGrupyUzytkownik = 1 ORDER BY login ASC" );
				return $zestawienie_studentow;
			}
			
			function pobierz_zestawienie_studentow_bez_kierunku() {
				$zestawienie_studentow_bez_kierunku = mysql_query ( "SELECT U.idUzytkownik, U.idGrupyKierunek, U.login, U.idGrupyUzytkownik, G.nazwaGrupa FROM uzytkownik U 
						INNER JOIN grupyUzytkownik G ON U.idGrupyUzytkownik=G.idGrupyUzytkownik
						WHERE U.idGrupyUzytkownik = 1 && idGrupyKierunek IS NULL ORDER BY login ASC" );
				return $zestawienie_studentow_bez_kierunku;
			}
			
			function pobierz_zestawienie_studentow_z_kierunkiem() {
				$zestawienie_studentow_z_kierunkiem = mysql_query ( "SELECT U.idUzytkownik, U.idGrupyKierunek, U.login, U.idGrupyUzytkownik, G.nazwaGrupa FROM uzytkownik U 
						INNER JOIN grupyUzytkownik G ON U.idGrupyUzytkownik=G.idGrupyUzytkownik
						WHERE U.idGrupyUzytkownik = 1 && idGrupyKierunek IS NOT NULL ORDER BY login ASC" );
				return $zestawienie_studentow_z_kierunkiem;
			}
			
			function pobierz_grupy_uzytkownicy() {
				$grupy_uzytkownicy = mysql_query ( "SELECT * FROM grupy_uzytkownicy" );
				return $grupy_uzytkownicy;
			}


			//======================================================================
			// Funkcja sprawdzenia poprawnosci daty
			//======================================================================
			function sprData($date) {
				$sprawdz = '/^[1-2]{1}+[0-9]{1}+[0-9]{1}+[0-9]{1}+\-[0-1]{1}+[0-9]{1}+\-[0-3]{1}+[0-9]{1}$/';
				if (preg_match ( $sprawdz, $date ))
					return true;
				else
					return false;
			}

//======================================================================
// Funkcja dla trybu UK
//======================================================================	
			// ======================================================================
			// Funkcja uzytkownicy - kierunek dodanie
			// ======================================================================
			function uk_dodaj() {
				$grupa = pobierz_grupa();
				$zestawienie_studentow_bez_kierunku = pobierz_zestawienie_studentow_bez_kierunku();
				echo "<p id='title'>Wybierz użytkowników do przydzielenia:</p>";
				if (mysql_num_rows ( $zestawienie_studentow_bez_kierunku ) != 0) {
					echo "<form action='edytuj_mod.php?zm=true&mode=uk&option=dodajUK' method='POST'>
							<table border=0>
								<tr align=center>
									<td>Wybierz użytkowników: <select id=uzytkownicy style='width: 150px' name='uzytkownicy[]' multiple='multiple' size='5'>";
					for($i = 0; $i < mysql_num_rows ( $zestawienie_studentow_bez_kierunku ); $i ++) {
						echo "<option value='" . mysql_result ( $zestawienie_studentow_bez_kierunku, $i, 'idUzytkownik' ) . "' selected='selected'>" . mysql_result ( $zestawienie_studentow_bez_kierunku, $i, 'login' ) . " :: " . mysql_result ( $zestawienie_studentow_bez_kierunku, $i, 'nazwaGrupa' ) . "</option>";
					}
					echo "</select></td>
					<td>Wybierz grupę: <select style='width: 300px' name='grupa'>
										<option value='" . mysql_result ( $grupa, 0, 'idGrupyKierunek' ) . "' selected='selected'>" . mysql_result ( $grupa, 0, 'nazwaGrupa' ) . "::" . mysql_result ( $grupa, 0, 'nazwaKierunek' ) . "::" . mysql_result ( $grupa, 0, 'dataRozpoczecia' ) . "::" . mysql_result ( $grupa, 0, 'nazwaWydzial' ) . "::" . mysql_result ( $grupa, 0, 'nazwaUczelnia' ) . "</option>";
					for($i = 1; $i < mysql_num_rows ( $grupa ); $i ++) {
						echo "<option value='" . mysql_result ( $grupa, $i, 'idGrupyKierunek' ) . "' selected='selected'>" . mysql_result ( $grupa, $i, 'nazwaGrupa' ) . "::" . mysql_result ( $grupa, $i, 'nazwaKierunek' ) . "::" . mysql_result ( $grupa, $i, 'dataRozpoczecia' ) . "::" . mysql_result ( $grupa, $i, 'nazwaWydzial' ) . "::" . mysql_result ( $grupa, $i, 'nazwaUczelnia' ) .  "</option>";
					}
					echo "</select></td>
							<tr>
								<td colspan=2 align=center><input type='submit' name='ok' value='DODAJ' /></td>
							</tr>
								</table>
							</form>";
				} else
					echo "Brak użytkowników<br/>";
			}
			
			// ======================================================================
			// Funkcja uzytkownicy - kierunek edycja
			// ======================================================================
			function uk_edytuj() {
				$grupa = pobierz_grupa();
				$zestawienie_studentow_z_kierunkiem = pobierz_zestawienie_studentow_z_kierunkiem();
				echo "<p id='title'>Edytuj grupę kierunkową użytkownika:</p>";
				if (mysql_num_rows ( $zestawienie_studentow_z_kierunkiem) != 0) {
					echo "<form action='edytuj_mod.php?zm=true&mode=uk&option=edytujUK' method='POST'>
							<table border=0>
								<tr align=center>
									<td>Wybierz studenta: <select style='width: 150px' name='student' >";
					for($i = 0; $i < mysql_num_rows ( $zestawienie_studentow_z_kierunkiem ); $i ++) {
						echo "<option value='" . mysql_result ( $zestawienie_studentow_z_kierunkiem, $i, 'idUzytkownik' ) . "' selected='selected'>" . mysql_result ( $zestawienie_studentow_z_kierunkiem, $i, 'login' ) . "</option>";
					}
					echo "</select></td>
					<td>Wybierz grupę: <select style='width: 300px' name='grupa'>
										<option value='" . mysql_result ( $grupa, 0, 'idGrupyKierunek' ) . "' selected='selected'>" . mysql_result ( $grupa, 0, 'nazwaGrupa' ) . "::" . mysql_result ( $grupa, 0, 'nazwaKierunek' ) . "::" . mysql_result ( $grupa, 0, 'dataRozpoczecia' ) . "::" . mysql_result ( $grupa, 0, 'nazwaWydzial' ) . "::" . mysql_result ( $grupa, 0, 'nazwaUczelnia' ) . "</option>";
					for($i = 1; $i < mysql_num_rows ( $grupa ); $i ++) {
						echo "<option value='" . mysql_result ( $grupa, $i, 'idGrupyKierunek' ) . "' selected='selected'>" . mysql_result ( $grupa, $i, 'nazwaGrupa' ) . "::" . mysql_result ( $grupa, $i, 'nazwaKierunek' ) . "::" . mysql_result ( $grupa, $i, 'dataRozpoczecia' ) . "::" . mysql_result ( $grupa, $i, 'nazwaWydzial' ) . "::" . mysql_result ( $grupa, $i, 'nazwaUczelnia' ) .  "</option>";
					}
					echo "</select></td>
						<tr>
										<td colspan=2 align=center><input type='submit' name='ok' value='ZAPISZ' /></td>
									</tr>
								</table>
							</form>";
				} else
					echo "Brak pracowników	";
			}
			
//======================================================================
// Funkcja dla trybu STR
//======================================================================
		//======================================================================
		// Zarządzanie UCZELNIA
		//======================================================================
			// ======================================================================
			// Funkcja uczelnia dodaj
			// ======================================================================
			function str_dodajU() {
				echo "<p id='title'>Dodaj nową uczelnię:</p>";
				echo "<form action='edytuj_mod.php?zm=true&mode=str&option=dodajU' method='POST'>
						<table border=0>
							<tr>
								<td id='fnazwa_pola'>Nazwa uczelni: </td><td><input style='width: 300px' type='text' name='nazwaUczelnia' /></td>
							</tr>
							<tr>
								<td id='fnazwa_pola'>Adres uczelni: </td><td><input style='width: 300px' type='text' name='adres' /></td>
							</tr>
							<tr>
								<td colspan=2 align=center><input type='submit' name='ok' value='DODAJ' /></td>
							</tr>
						</table>
					   </form>";
			}
			
			// ======================================================================
			// Funkcja uczelnia edytuj
			// ======================================================================
			function str_edytujU() {
				$uczelnia = pobierz_uczelnia();
				echo "<p id='title'>Edytuj uczelnię:</p>";
				if (mysql_num_rows ( $uczelnia ) != 0) {
					echo "<form action='edytuj_mod.php?zm=true&mode=str&option=edycjaU' method='POST'>
							<table border=0>
								<tr>
									<td id='fnazwa_pola'>Wybierz uczelnię: </td><td><select style='width: 300px' name='uczelnia'>
										<option value='" . mysql_result ( $uczelnia, 0, 'idUczelnia' ) . "' selected='selected'>" . mysql_result ( $uczelnia, 0, 'nazwaUczelnia' ) . "</option>";
					for($i = 1; $i < mysql_num_rows ( $uczelnia ); $i ++) {
						echo "<option value='" . mysql_result ( $uczelnia, $i, 'idUczelnia' ) . "'>" . mysql_result ( $uczelnia, $i, 'nazwaUczelnia' ) . "</option>";
					}
					echo "</select></td>
							<tr><td id='fnazwa_pola'> Nowa nazwa uczelni: </td><td><input style='width: 300px' type='text' name='zmiana_nazwy'/></td></tr>
							<tr><td id='fnazwa_pola'> Nowy adres uczelni: </td><td><input style='width: 300px' type='text' name='zmiana_adresu'/></td></tr>
							<tr>
								<td colspan=2 align=center><input type='submit' name='ok' value='ZAPISZ' /></td>
							</tr>
								</table>
							</form>";
				} else
					echo "Brak uczelni. Dodaj nowe uczelnie.<br/>";
			}
		//======================================================================
		// Zarządzanie WYDZIAŁ
		//======================================================================	
			// ======================================================================
			// Funkcja wydział dodaj
			// ======================================================================
			function str_dodajW() {
				$uczelnia = pobierz_uczelnia();
				echo "<p id='title'>Dodaj nowy wydział:</p>";
				if (mysql_num_rows ( $uczelnia) != 0) {
					echo "<form action='edytuj_mod.php?zm=true&mode=str&option=dodajW' method='POST'>
						<table border=0>
							<tr>
								<td id='fnazwa_pola'>Nazwa nowego wydziału: </td><td><input style='width: 300px' type='text' name='wydzial' /></td>
							</tr>
							<tr>
								<td id='fnazwa_pola'>Adres wydzialu: </td><td><input style='width: 300px' type='text' name='adres' /></td>
							</tr>
							<tr>
									<td id='fnazwa_pola'>Dodaj wydział do uczelni: </td><td><select  style='width: 300px' name='uczelnia'>
										<option value='" . mysql_result ( $uczelnia, 0, 'idUczelnia' ) . "' selected='selected'>" . mysql_result ( $uczelnia, 0, 'nazwaUczelnia' ) . "</option>";
					for($i = 1; $i < mysql_num_rows ( $uczelnia ); $i ++) {
						echo "<option value='" . mysql_result ( $uczelnia, $i, 'idUczelnia' ) . "'>" . mysql_result ( $uczelnia, $i, 'nazwaUczelnia' ) . "</option>";
					}
					echo "</select></td></tr>
							<tr>
								<td colspan=2 align=center><input type='submit' name='ok' value='DODAJ' /></td>
							</tr>
						</table>
					   </form>";
				} else
					echo "Brak uczelni - dodaj nowe uczelnie<br/>";
			}

			// ======================================================================
			// Funkcja wydzialy edytuj
			// ======================================================================
			function str_edytujW() {
				$uczelnia = pobierz_uczelnia();
				$wydzial = pobierz_wydzial();
				echo "<p id='title'>Edytuj wydzial:</p>";
				if (mysql_num_rows ( $wydzial ) != 0) {
					echo "<form action='edytuj_mod.php?zm=true&mode=str&option=edycjaW' method='POST'>
							<table border=0>
								<tr>
									<td id='fnazwa_pola'>Wybierz edytowany wydział: </td><td><select style='width: 300px' name='wydzial'>
										<option value='" . mysql_result ( $wydzial, 0, 'idWydzial' ) . "' selected='selected'>" . mysql_result ( $wydzial, 0, 'nazwaWydzial' ) . "::" . mysql_result ( $wydzial, 0, 'nazwaUczelnia' ) . "</option>";
					for($i = 1; $i < mysql_num_rows ( $wydzial ); $i ++) {
						echo "<option value='" . mysql_result ( $wydzial, $i, 'idWydzial' ) . "'>" . mysql_result ( $wydzial, $i, 'nazwaWydzial' ) . "::" . mysql_result ( $wydzial, $i, 'nazwaUczelnia' ) . "</option>";
					}
					echo "</select></td></tr>
								<tr><td id='fnazwa_pola'>Dodaj wydział do uczelni: </td><td><select style='width: 300px' name='uczelnia'>
										<option value='" . mysql_result ( $uczelnia, 0, 'idUczelnia' ) . "' selected='selected'>" . mysql_result ( $uczelnia, 0, 'nazwaUczelnia' ) . "</option>";
					for($i = 1; $i < mysql_num_rows ( $uczelnia ); $i ++) {
						echo "<option value='" . mysql_result ( $uczelnia, $i, 'idUczelnia' ) . "'>" . mysql_result ( $uczelnia, $i, 'nazwaUczelnia' ) . "</option>";
					}
					echo "</select></td></tr>
							<tr>
								<td colspan=2 align=center><input type='submit' name='ok' value='DODAJ' /></td>
							</tr>
								</table>
							</form>";
				} else
					echo "Brak wydziałów - dodaj nowe wydziały<br/>";
				echo "<p id='title'>Edytuj nazwę wydziału:</p>";
				if ((mysql_num_rows ( $wydzial ) != 0 && mysql_num_rows ( $uczelnia ) != 0)) {
					echo "<form action='edytuj_mod.php?zm=true&mode=str&option=edycjaW' method='POST'>
							<table border=0>
								<tr>
									<td id='fnazwa_pola'>Wybierz wydział: </td><td><select  style='width: 300px' name='wydzial'>
										<option value='" . mysql_result ( $wydzial, 0, 'idWydzial' ) . "' selected='selected'>" . mysql_result ( $wydzial, 0, 'nazwaWydzial' ) . "::" . mysql_result ( $wydzial, 0, 'nazwaUczelnia' ) . "</option>";
					for($i = 1; $i < mysql_num_rows ( $wydzial ); $i ++) {
						echo "<option value='" . mysql_result ( $wydzial, $i, 'idWydzial' ) . "'>" . mysql_result ( $wydzial, $i, 'nazwaWydzial' ) . "::" . mysql_result ( $wydzial, $i, 'nazwaUczelnia' ) . "</option>";
					}
					echo "</select></td></tr>
							<tr><td id='fnazwa_pola'> Nowa nazwa wydziału: </td><td><input style='width: 300px' type='text' name='zmiana_nazwy'/></td></tr>
							<tr><td id='fnazwa_pola'> Nowy adres wydziału: </td><td><input style='width: 300px' type='text' name='zmiana_adresu'/></td></tr>
							<tr>
								<td colspan=2 align=center><input type='submit' name='ok' value='ZAPISZ' /></td>
							</tr>
								</table>
							</form>";
				} else
					echo "Brak uczelni lub wydziałów<br/>";
			}
		//======================================================================
		// Zarządzanie KIERUNEK
		//======================================================================	
			// ======================================================================
			// Funkcja kierunek dodaj
			// ======================================================================
			function str_dodajK() {
				$wydzial = pobierz_wydzial();
				echo "<p id='title'>Dodaj nowy kierunek:</p>";
				if (mysql_num_rows ( $wydzial) != 0) {
					echo "<form action='edytuj_mod.php?zm=true&mode=str&option=dodajK' method='POST'>
						<table border=0>
							<tr>
								<td id='fnazwa_pola'>Nazwa nowego kierunku: </td><td><input style='width: 300px' type='text' name='kierunek' /></td>
							</tr>
							<tr>
								<td id='fnazwa_pola'>Rozpoczęcie: </td><td><input style='width: 300px' type='text' name='rozpoczecie' placeholder='yyyy-mm-dd'/></td>
							</tr>
							<tr>
								<td id='fnazwa_pola'>Zakończenie: </td><td><input style='width: 300px' type='text' name='zakonczenie' placeholder='yyyy-mm-dd'/></td>
							</tr>
							<tr>
								<td id='fnazwa_pola'>Opis kierunku: </td><td><input style='width: 300px' type='text' name='opis' /></td>
							</tr>
							<tr>
									<td id='fnazwa_pola'>Dodaj kierunek do wydzialu: </td><td><select  style='width: 300px' name='wydzial'>
										<option value='" . mysql_result ( $wydzial, 0, 'idWydzial' ) . "' selected='selected'>" . mysql_result ( $wydzial, 0, 'nazwaUczelnia' ) . "::" . mysql_result ( $wydzial, 0, 'nazwaWydzial' ) . "</option>";
											for($i = 1; $i < mysql_num_rows ( $wydzial ); $i ++) {
												echo "<option value='" . mysql_result ( $wydzial, $i, 'idWydzial' ) . "'>" . mysql_result ( $wydzial, $i, 'nazwaUczelnia' ) . "::" . mysql_result ( $wydzial, $i, 'nazwaWydzial' ) . "</option>";
											}
					echo "</select></td></tr>
							<tr>
								<td colspan=2 align=center><input type='submit' name='ok' value='DODAJ' /></td>
							</tr>
						</table>
					   </form>";
				} else
					echo "Brak wydziałów - dodaj nowe wydziały<br/>";
			}
			
			// ======================================================================
			// Funkcja kierunki edytuj
			// ======================================================================
			function str_edytujK() {
				$wydzial = pobierz_wydzial();
				$kierunek = pobierz_kierunek();
				echo "<p id='title'>Edytuj kierunek:</p>";
				if (mysql_num_rows ( $kierunek ) != 0) {
					echo "<form action='edytuj_mod.php?zm=true&mode=str&option=edycjaK' method='POST'>
							<table border=0>
								<tr>
									<td id='fnazwa_pola'>Wybierz edytowany kierunek: </td><td><select style='width: 300px' name='kierunek'>
										<option value='" . mysql_result ( $kierunek, 0, 'idKierunek' ) . "' selected='selected'>" . mysql_result ( $kierunek, 0, 'nazwaKierunek' ) . "::" . mysql_result ( $kierunek, 0, 'nazwaWydzial' ) . "::" . mysql_result ( $kierunek, 0, 'nazwaUczelnia' ) . "::" . mysql_result ( $kierunek, 0, 'dataRozpoczecia' ) . "</option>";
					for($i = 1; $i < mysql_num_rows ( $kierunek ); $i ++) {
						echo "<option value='" . mysql_result ( $kierunek, $i, 'idKierunek' ) . "' selected='selected'>" . mysql_result ( $kierunek, $i, 'nazwaKierunek' ) . "::" . mysql_result ( $kierunek, $i, 'nazwaWydzial' ) . "::" . mysql_result ( $kierunek, $i, 'nazwaUczelnia' ) . "::" . mysql_result ( $kierunek, $i, 'dataRozpoczecia' ) . "</option>";
					}
					echo "</select></td></tr>
								<tr><td id='fnazwa_pola'>Dodaj kierunek do wydzialu: </td><td><select style='width: 300px' name='wydzial'>
										<option value='" . mysql_result ( $wydzial, 0, 'idWydzial' ) . "' selected='selected'>" . mysql_result ( $wydzial, 0, 'nazwaWydzial' ) . "::" . mysql_result ( $wydzial, 0, 'nazwaUczelnia' ) . "</option>";
					for($i = 1; $i < mysql_num_rows ( $wydzial ); $i ++) {
						echo "<option value='" . mysql_result ( $wydzial, $i, 'idWydzial' ) . "' selected='selected'>" . mysql_result ( $wydzial, $i, 'nazwaWydzial' ) . "::" . mysql_result ( $wydzial, $i, 'nazwaUczelnia' ) .  "</option>";
					}
					echo "</select></td></tr>
							<tr>
								<td colspan=2 align=center><input type='submit' name='ok' value='DODAJ' /></td>
							</tr>
								</table>
							</form>";
				} else
					echo "Brak kierunków - dodaj nowe kierunki<br/>";
				echo "<p id='title'>Edytuj nazwę kierunku:</p>";
				if ((mysql_num_rows ( $kierunek ) != 0 && mysql_num_rows ( $wydzial ) != 0)) {
					echo "<form action='edytuj_mod.php?zm=true&mode=str&option=edycjaK' method='POST'>
							<table border=0>
								<tr>
									<td id='fnazwa_pola'>Wybierz edytowany kierunek: </td><td><select  style='width: 300px' name='kierunek'>
										<option value='" . mysql_result ( $kierunek, 0, 'idKierunek' ) . "' selected='selected'>" . mysql_result ( $kierunek, 0, 'nazwaKierunek' ) . "::" . mysql_result ( $kierunek, 0, 'nazwaWydzial' ) . "::" . mysql_result ( $kierunek, 0, 'nazwaUczelnia' ) . "::" . mysql_result ( $kierunek, 0, 'dataRozpoczecia' ) . "</option>";
					for($i = 1; $i < mysql_num_rows ( $kierunek ); $i ++) {
						echo "<option value='" . mysql_result ( $kierunek, $i, 'idKierunek' ) . "' selected='selected'>" . mysql_result ( $kierunek, $i, 'nazwaKierunek' ) . "::" . mysql_result ( $kierunek, $i, 'nazwaWydzial' ) . "::" . mysql_result ( $kierunek, $i, 'nazwaUczelnia' ) . "::" . mysql_result ( $kierunek, $i, 'dataRozpoczecia' ) . "</option>";
					}
					echo "</select></td></tr>
							<tr><td id='fnazwa_pola'> Nowa nazwa kierunku: </td><td><input style='width: 300px' type='text' name='zmiana_nazwy'/></td></tr>
							<tr><td id='fnazwa_pola'> Nowa data rozpoczęcia: </td><td><input style='width: 300px' type='text' name='zmiana_rozpoczecie' placeholder='yyyy-mm-dd'/></td></tr>
							<tr><td id='fnazwa_pola'> Nowa data zakończenia: </td><td><input style='width: 300px' type='text' name='zmiana_zakonczenie' placeholder='yyyy-mm-dd'/></td></tr>
							<tr><td id='fnazwa_pola'> Nowy opis kierunku: </td><td><input style='width: 300px' type='text' name='zmiana_opisu'/></td></tr>
							<tr>
								<td colspan=2 align=center><input type='submit' name='ok' value='ZAPISZ' /></td>
							</tr>
								</table>
							</form>";
				} else
					echo "Brak kierunków lub innej struktury<br/>";
			}
		//======================================================================
		// Zarządzanie GRUPA KIERUNKOWA
		//======================================================================	
			// ======================================================================
			// Funkcja grupa kierunkowa dodaj
			// ======================================================================
			function str_dodajG() {
				$kierunek = pobierz_kierunek();
				echo "<p id='title'>Dodaj nową grupę kierunkową:</p>";
				echo "<form action='edytuj_mod.php?zm=true&mode=str&option=dodajG' method='POST'>
						<table border=0>
							<tr>
								<td id='fnazwa_pola'>Nazwa grupy: </td><td><input style='width: 300px' type='text' name='nazwaGrupa' /></td>
							</tr>
							<tr>
									<td id='fnazwa_pola'>Dodaj grupę do kierunku: </td><td><select  style='width: 300px' name='kierunek'>
										<option value='" . mysql_result ( $kierunek, 0, 'idKierunek' ) . "' selected='selected'>" . mysql_result ( $kierunek, 0, 'nazwaKierunek' ) . "::" . mysql_result ( $kierunek, 0, 'dataRozpoczecia' ) . "::" . mysql_result ( $kierunek, 0, 'nazwaWydzial' ) . "::" . mysql_result ( $kierunek, 0, 'nazwaUczelnia' ) . "</option>";
								for($i = 1; $i < mysql_num_rows ( $kierunek ); $i ++) {
									echo "<option value='" . mysql_result ( $kierunek, $i, 'idKierunek' ) . "' selected='selected'>" . mysql_result ( $kierunek, $i, 'nazwaKierunek' ) . "::" . mysql_result ( $kierunek, $i, 'dataRozpoczecia' ) . "::" . mysql_result ( $kierunek, $i, 'nazwaWydzial' ) . "::" . mysql_result ( $kierunek, $i, 'nazwaUczelnia' ) . "</option>";
								}
								echo "</select></td></tr>
							<tr>
								<td colspan=2 align=center><input type='submit' name='ok' value='DODAJ' /></td>
							</tr>
						</table>
					   </form>";
			}
			
			// ======================================================================
			// Funkcja grupa kierunkowa edytuj
			// ======================================================================
			function str_edytujG() {
				$kierunek = pobierz_kierunek();
				$grupa = pobierz_grupa();
				echo "<p id='title'>Edytuj grupę kierunkową:</p>";
				if (mysql_num_rows ( $grupa ) != 0) {
					echo "<form action='edytuj_mod.php?zm=true&mode=str&option=edycjaG' method='POST'>
							<table border=0>
								<tr>
									<td id='fnazwa_pola'>Wybierz edytowaną grupę: </td><td><select style='width: 300px' name='grupa'>
										<option value='" . mysql_result ( $grupa, 0, 'idGrupyKierunek' ) . "' selected='selected'>" . mysql_result ( $grupa, 0, 'nazwaGrupa' ) . "::" . mysql_result ( $grupa, 0, 'nazwaKierunek' ) . "::" . mysql_result ( $grupa, 0, 'dataRozpoczecia' ) . "::" . mysql_result ( $grupa, 0, 'nazwaWydzial' ) . "::" . mysql_result ( $grupa, 0, 'nazwaUczelnia' ) . "</option>";
					for($i = 1; $i < mysql_num_rows ( $grupa ); $i ++) {
						echo "<option value='" . mysql_result ( $grupa, $i, 'idGrupyKierunek' ) . "' selected='selected'>" . mysql_result ( $grupa, $i, 'nazwaGrupa' ) . "::" . mysql_result ( $grupa, $i, 'nazwaKierunek' ) . "::" . mysql_result ( $grupa, $i, 'dataRozpoczecia' ) . "::" . mysql_result ( $grupa, $i, 'nazwaWydzial' ) . "::" . mysql_result ( $grupa, $i, 'nazwaUczelnia' ) .  "</option>";
					}
					echo "</select></td></tr>
								<td id='fnazwa_pola'>Dodaj grupę do kierunku: </td><td><select  style='width: 300px' name='kierunek'>
										<option value='" . mysql_result ( $kierunek, 0, 'idKierunek' ) . "' selected='selected'>" . mysql_result ( $kierunek, 0, 'nazwaKierunek' ) . "::" . mysql_result ( $kierunek, 0, 'dataRozpoczecia' ) . "::" . mysql_result ( $kierunek, 0, 'nazwaWydzial' ) . "::" . mysql_result ( $kierunek, 0, 'nazwaUczelnia' ) . "</option>";
								for($i = 1; $i < mysql_num_rows ( $kierunek ); $i ++) {
									echo "<option value='" . mysql_result ( $kierunek, $i, 'idKierunek' ) . "' selected='selected'>" . mysql_result ( $kierunek, $i, 'nazwaKierunek' ) . "::" . mysql_result ( $kierunek, $i, 'dataRozpoczecia' ) . "::" . mysql_result ( $kierunek, $i, 'nazwaWydzial' ) . "::" . mysql_result ( $kierunek, $i, 'nazwaUczelnia' ) . "</option>";
								}
						echo "</select></td></tr>
							<tr>
								<td colspan=2 align=center><input type='submit' name='ok' value='DODAJ' /></td>
							</tr>
								</table>
							</form>";
				} else
					echo "Brak grup - dodaj nowe grupy kierunkowe<br/>";
				echo "<p id='title'>Edytuj nazwę grupy kierunkowej:</p>";
				if ((mysql_num_rows ( $grupa ) != 0 && mysql_num_rows ( $kierunek ) != 0)) {
					echo "<form action='edytuj_mod.php?zm=true&mode=str&option=edycjaG' method='POST'>
							<table border=0>
								<tr>
									<td id='fnazwa_pola'>Wybierz edytowaną grupę:</td><td><select style='width: 300px' name='grupa'>
										<option value='" . mysql_result ( $grupa, 0, 'idGrupyKierunek' ) . "' selected='selected'>" . mysql_result ( $grupa, 0, 'nazwaGrupa' ) . "::" . mysql_result ( $grupa, 0, 'nazwaKierunek' ) . "::" . mysql_result ( $grupa, 0, 'dataRozpoczecia' ) . "::" . mysql_result ( $grupa, 0, 'nazwaWydzial' ) . "::" . mysql_result ( $grupa, 0, 'nazwaUczelnia' ) . "</option>";
					for($i = 1; $i < mysql_num_rows ( $grupa ); $i ++) {
						echo "<option value='" . mysql_result ( $grupa, $i, 'idGrupyKierunek' ) . "' selected='selected'>" . mysql_result ( $grupa, $i, 'nazwaGrupa' ) . "::" . mysql_result ( $grupa, $i, 'nazwaKierunek' ) . "::" . mysql_result ( $grupa, $i, 'dataRozpoczecia' ) . "::" . mysql_result ( $grupa, $i, 'nazwaWydzial' ) . "::" . mysql_result ( $grupa, $i, 'nazwaUczelnia' ) .  "</option>";
					}
					echo "</select></td>
							<tr><td id='fnazwa_pola'> Nowa nazwa grupy: </td><td><input style='width: 300px' type='text' name='zmiana_nazwy'/></td></tr>
							<tr><td align='center' colspan=2><input type='checkbox' name='status' value='status' />Czy grupa zamknięta</a></td></tr>
							<tr>
								<td colspan=2 align=center><input type='submit' name='ok' value='ZAPISZ' /></td>
							</tr>
								</table>
							</form>";
				} else
					echo "Brak kierunków lub innych struktur<br/>";
			}
			
			
			
			//======================================================================
			// Tryb zatwierdzania zmian w zarządzaniu systemem
			//======================================================================
			if (isset ( $_GET ['zm'] )) {
				if (isset ( $_GET ['mode'] )) {
//======================================================================
// TRYB STR
//======================================================================
					if ($tryb == 'str') {
						echo '<div>Zmiany struktur oświaty wyższej</div><center>';
		//======================================================================
		// Zarządzanie UCZELNIA
		//======================================================================
						//======================================================================
						// Tryb uczelnia dodaj
						//======================================================================
						if ($opcja == 'dodajU') {
							$nazwa = $_POST ['nazwaUczelnia'];
							$adres = $_POST ['adres'];
							$query = mysql_query ( "SELECT nazwaUczelnia FROM uczelnia WHERE nazwaUczelnia='$nazwa' " );
							if (mysql_num_rows ( $query ) != 0 || empty ( $nazwa )) {
								echo "<p>Nazwa uczelni już istnieje lub nie wpisano nazwy</p>";
								str_dodajU ();
							} else {
								$query = mysql_query ( "INSERT INTO uczelnia(nazwaUczelnia, adres) VALUES('$nazwa','$adres')" );
								echo "Dodawanie uczelni ukończone<br/>";
							}
						//======================================================================
						// Tryb uczelnia edycja
						//======================================================================
						} else if ($opcja == 'edycjaU') {
							$idUczelnia = $_POST ['uczelnia'];
							$nazwa = $_POST ['zmiana_nazwy'];
							$adres = $_POST ['zmiana_adresu'];
							$query = mysql_query ( "SELECT nazwaUczelnia FROM uczelnia WHERE nazwaUczelnia='$nazwa'" );
							if (mysql_num_rows ( $query ) != 0) {
								echo "<p>Nazwa uczelni już istnieje</p>";
								str_edytujU ();
							} else {
								$update = 0;
								if( !empty ( $nazwa ) )
									$update = mysql_query ( "UPDATE uczelnia SET nazwaUczelnia='$nazwa' WHERE idUczelnia='$idUczelnia'" );
								if ( !empty ( $adres ) )
									$update = mysql_query ( "UPDATE uczelnia SET adres='$adres' WHERE idUczelnia='$idUczelnia'" );
								if ($update) {
									echo "<p>Edycja nazwy uczelni ukończona</p>";
								} else 
									echo "<p>Błąd! Pozostały stare dane</p>";
							}
		//======================================================================
		// Zarządzanie WYDZIAŁ
		//======================================================================
						//======================================================================
						// Tryb wydzialy dodaj
						//======================================================================
						} else if ($opcja == 'dodajW') {
							$nazwa = $_POST ['wydzial'];
							$idUczelnia = $_POST ['uczelnia'];
							$adres = $_POST ['adres'];
							$query = mysql_query ( "SELECT nazwaWydzial FROM wydzial WHERE nazwaWydzial='$nazwa' && idUczelnia='$idUczelnia'" );
							if (mysql_num_rows ( $query ) != 0 || empty ( $nazwa )) {
								echo "<p>Nazwa wydziału już istnieje na tej uczelni lub nie wpisano nazwy</p>";
								str_dodajW ();
							} else {
								$query = mysql_query ( "INSERT INTO wydzial(nazwaWydzial, idUczelnia, adres) VALUES('$nazwa','$idUczelnia','$adres')" );
								echo "Dodawanie wydzialu ukończone<br/>";
							}
							
						//======================================================================
						// Tryb wydzialy edycja
						//======================================================================
						} else if ($opcja == 'edycjaW') {
							if (! isset ( $_POST ['uczelnia'] )) {
								$idWydzial = $_POST ['wydzial'];
								$nazwa = $_POST ['zmiana_nazwy'];
								$adres = $_POST ['zmiana_adresu'];
								$query = mysql_query ( "SELECT idUczelnia FROM wydzial WHERE idWydzial='$idWydzial'");
								$idUczelnia = mysql_result ($query,0,'idUczelnia');
								$query = mysql_query ( "SELECT nazwaWydzial FROM wydzial WHERE nazwaWydzial='$nazwa' && idUczelnia='$idUczelnia'");
								if (mysql_num_rows ( $query ) != 0 ) {
									echo "<p>Nazwa grupy już istnieje</p>";
									str_edytujW ();
								} else {
									$update = 0;
									if( !empty ( $nazwa ) )
										$update = mysql_query ( "UPDATE wydzial SET nazwaWydzial='$nazwa' WHERE idWydzial='$idWydzial'" );
									if ( !empty ( $adres ))
										$update = mysql_query ( "UPDATE wydzial SET adres='$adres' WHERE idWydzial='$idWydzial'" );
									if ($update) {
										echo "<p>Edycja nazwy wydziału i adresu ukończona</p>";
									} else
										echo "<p>Błąd! Pozostały stare dane</p>";
								}
							} else {
								$idWydzial = $_POST ['wydzial'];
								$idUczelnia = $_POST ['uczelnia'];
								$query = mysql_query ( "UPDATE wydzial SET idUczelnia='$idUczelnia' WHERE idWydzial='$idWydzial'" );
								if ($query) {
									echo "<p>Edycja przypisania wydziału ukończona</p>";
								} else
									echo "<p>Błąd! Pozostały stare dane</p>";
							}
		//======================================================================
		// Zarządzanie KIERUNEK
		//======================================================================
							//======================================================================
							// Tryb kierunek dodaj
							//======================================================================
						} else if ($opcja == 'dodajK') {
							$nazwa = $_POST ['kierunek'];
							$idWydzial = $_POST ['wydzial'];
							$rozpoczecie = $_POST ['rozpoczecie'];
							$zakonczenie = $_POST ['zakonczenie'];
							$opis = $_POST ['opis'];
							$query = mysql_query ( "SELECT nazwaKierunek FROM kierunek WHERE nazwaKierunek='$nazwa' && idWydzial='$idWydzial' && dataRozpoczecia='$rozpoczecie'" );
							if (mysql_num_rows ( $query ) != 0 || empty ( $nazwa )) {
								echo "<p>Nazwa kierunku już istnieje na tym wydziale lub nie wpisano nazwy</p>";
								str_dodajK ();
							} else {
								
								if(sprData($rozpoczecie) && (empty($zakonczenie) || sprData($zakonczenie))){ 
									$spr = $zakonczenie - $rozpoczecie;
									if($spr > 0 || empty($zakonczenie)) {
										$query = mysql_query ( "INSERT INTO kierunek(nazwaKierunek, idWydzial, dataRozpoczecia, dataZakonczenia, opis) VALUES('$nazwa','$idWydzial','$rozpoczecie','$zakonczenie','$opis')" );
										echo "Dodawanie kierunku ukończone<br/>";
									} else {
										echo "Daty się nie zgadzają";
										str_dodajK ();
									}
								} else {
									echo "Zły format daty";
									str_dodajK ();
								}
									
							}
							//======================================================================
							// Tryb kierunek edycja
							//======================================================================
						} else if ($opcja == 'edycjaK') {
							if (! isset ( $_POST ['wydzial'] )) {
								$idKierunek = $_POST ['kierunek'];
								$nazwa = $_POST ['zmiana_nazwy'];
								$rozpoczecie = $_POST ['zmiana_rozpoczecie'];
								$zakonczenie = $_POST ['zmiana_zakonczenie'];
								$opis = $_POST ['zmiana_opisu'];
								$query = mysql_query ( "SELECT idWydzial, dataRozpoczecia FROM kierunek WHERE idKierunek='$idKierunek'");
								if(empty($rozpoczecie))
									$rozpoczecie = mysql_result ($query,0,'dataRozpoczecia');
								$idWydzial = mysql_result ($query,0,'idWydzial');
								$query = mysql_query ( "SELECT nazwaKierunek FROM kierunek WHERE nazwaKierunek='$nazwa' && idWydzial='$idWydzial' && dataRozpoczecia='$rozpoczecie'" );
								if (mysql_num_rows ( $query ) != 0 ) {
									echo "<p>Nazwa kierunku z ta data rozpoczęcia już istnieje na tym wydziale</p>";
									str_dodajK ();
								} else {
									$update=0;
									if( !empty ( $nazwa ) )
										$update = mysql_query ( "UPDATE kierunek SET nazwaKierunek='$nazwa' WHERE idKierunek='$idKierunek'" );
									if ( !empty ( $opis) )
										$update = mysql_query ( "UPDATE kierunek SET opis='$opis' WHERE idKierunek='$idKierunek'" );
									if(sprData($rozpoczecie) && (empty($zakonczenie) || sprData($zakonczenie))){ 
										$spr1 = $zakonczenie - $rozpoczecie;
										if($spr1 >= 0 || empty($zakonczenie)) {
											if ( !empty ( $rozpoczecie))
												$update = mysql_query ( "UPDATE kierunek SET dataRozpoczecia='$rozpoczecie' WHERE idKierunek='$idKierunek'" );
											if ( !empty ( $zakonczenie))
												$update = mysql_query ( "UPDATE kierunek SET dataZakonczenia='$zakonczenie' WHERE idKierunek='$idKierunek'" );
										} else {
											echo "Daty się nie zgadzają";
										}
									} else {
										echo "Zły format daty";
									}
									if ($update) {
										echo "<p>Edycja danych kierunku ukończona</p>";
									} else
										echo "<p>Błąd! Pozostały stare dane</p>";
								}
							} else {
								$idKierunek = $_POST ['kierunek'];
								$idWydzial = $_POST ['wydzial'];
								$query = mysql_query ( "UPDATE kierunek SET idWydzial='$idWydzial' WHERE idKierunek='$idKierunek'" );
								if ($query) {
									echo "<p>Edycja przypisania kierunku ukończona</p>";
								} else
									echo "<p>Błąd! Pozostały stare dane</p>";
							}
		//======================================================================
		// Zarządzanie GRUPA KIERUNKOWA
		//======================================================================
						//======================================================================
						// Tryb grupy kierunkowe dodaj
						//======================================================================
						} else if ($opcja == 'dodajG') {
							$nazwa = $_POST ['nazwaGrupa'];
							$idKierunek = $_POST ['kierunek'];
							$query = mysql_query ( "SELECT nazwaGrupa FROM grupykierunek WHERE nazwaGrupa='$nazwa' && idKierunek='$idKierunek'" );
							if (mysql_num_rows ( $query ) != 0 || empty ( $nazwa )) {
								echo "<p>Nazwa grupy już istnieje na tym kierunku lub nie wpisano nazwy</p>";
								str_dodajG ();
							} else {
								$query = mysql_query ( "INSERT INTO grupykierunek(nazwaGrupa, idKierunek, status_2) VALUES('$nazwa','$idKierunek',0)" );
								echo "Dodawanie wydzialu ukończone<br/>";
							}
							
							//======================================================================
							// Tryb grupy kierunkowe edycja
							//======================================================================
						} else if ($opcja == 'edycjaG') {
							if (! isset ( $_POST ['kierunek'] )) {
								$idGrupy = $_POST ['grupa'];
								$nazwa = $_POST ['zmiana_nazwy'];
								$query = mysql_query ( "SELECT idKierunek FROM grupykierunek WHERE idGrupyKierunek='$idGrupy'");
								$idKierunek = mysql_result ($query,0,'idKierunek');
								$query = mysql_query ( "SELECT nazwaGrupa FROM grupykierunek WHERE nazwaGrupa='$nazwa' && idKierunek='$idKierunek'");
								if (mysql_num_rows ( $query ) != 0 ) {
									echo "<p>Nazwa grupy już istnieje</p>";
									str_edytujG ();
								} else {
									$update=0;
									if( !empty ( $nazwa ) )
										$update = mysql_query ( "UPDATE grupykierunek SET nazwaGrupa='$nazwa' WHERE idGrupyKierunek='$idGrupy'" );
									if ( isset ($_POST ['status']))
										$update = mysql_query ( "UPDATE grupykierunek SET status_2=1 WHERE idGrupyKierunek='$idGrupy'" );
									if ($update) {
										echo "<p>Edycja nazwy grupy ukończona</p>";
									} else
										echo "<p>Błąd! Pozostały stare dane</p>";
								}
							} else {
								$idGrupy = $_POST ['grupa'];
								$idKierunek = $_POST ['kierunek'];
								$query = mysql_query ( "UPDATE grupykierunek SET idKierunek='$idKierunek' WHERE idGrupyKierunek='$idGrupy'" );
								if ($query) {
									echo "<p>Edycja przypisania grupy ukończona</p>";
								} else
									echo "<p>Błąd! Pozostały stare dane</p>";
							}
						}
					echo '<p><a href="panel_admin.php">Powrót do panelu admina</a>&emsp;::&emsp;<a href="edytuj_mod.php?mode=str">Zarządzanie strukturą</a></p>';	
//======================================================================
// TRYB UK
//======================================================================
					} else if ($tryb == 'uk') {
						//======================================================================
						// Tryb uzytkownicy - kierunek dodaj
						//======================================================================
						if ($opcja == 'dodajUK') {
							if (isset ( $_POST ['uzytkownicy'] )) {
								$studenci = $_POST ['uzytkownicy'];
								$idGrupy = $_POST ['grupa'];
								for($i = 0; ! empty ( $studenci [$i] ); $i ++)
									$query [$i] = mysql_query ( "UPDATE uzytkownik SET idGrupyKierunek='$idGrupy' WHERE idUzytkownik='$studenci[$i]'" );
									if ($query) {
										echo "<p>Dodanie studentów ukończone</p>";
									} else {
										echo "<p><b>Błąd! Pozostały stare dane</b></p>";
										uk_dodaj();
									}
							} else {
								echo "<p><b>Nie wybrano studentów</b></p>";
								uk_dodaj ();
							}
							//======================================================================
							// Tryb uzytkownicy - kierunek edycja
							//======================================================================
						} else if ($opcja == 'edytujUK') {
							$idStudent = $_POST ['student'];
							$idGrupa = $_POST ['grupa'];
							$query = mysql_query ( "UPDATE uzytkownik SET idGrupyKierunek='$idGrupa' WHERE idUzytkownik='$idStudent'" );
							if ($query) {
								echo "<p>Edycja przypisania studenta ukończona</p>";
							} else
								echo "<p>Błąd! Pozostały stare dane</p>";
						}
						echo '<p><a href="panel_admin.php">Powrót do panelu admina</a>&emsp;::&emsp;<a href="edytuj_mod.php?mode=str">Zarządzanie strukturą</a></p>';	
					}
				}
				//======================================================================
				// Wstepna inicjalizacja formularzy opcji zarzadzania
				//======================================================================
			} else {
				if (isset ( $_GET ['mode'] )) {
					if ($tryb == 'str') {
						echo '<div>Zarzadzanie struktura oświaty wyższej</div><center>';
						str_dodajG();
						echo "<hr/>";
						str_edytujG();
						echo "<hr/>";
						str_dodajK();
						echo "<hr/>";
						str_edytujK();
						echo "<hr/>";
						str_dodajW();
						echo "<hr/>";
						str_edytujW();
						echo "<hr/>";
						str_dodajU ();
						echo "<hr/>";
						str_edytujU ();
						echo "<hr/>";
						echo '<p><a href="panel_admin.php">Powrót do panelu</a></p></center>';
					}

					if ($tryb == 'uk') {
						echo '<div>Zarządzanie student - kierunek</div><center>';
						uk_dodaj();
						echo "<hr/>";
						uk_edytuj ();
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