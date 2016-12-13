<?php

// ======================================================================
// Formularze i Implementacje opcji zarządzania forum
// ======================================================================
session_start ();
include_once 'header.php';
?>
<div id="body-bot">
	<h2><span><strong>PANEL MODERATOR</strong> edycja indeksów</span></h2>
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
			// Funkcje pobrania potrzebnych danych z bazy do skryptu
			// ======================================================================
			function pobierz_indeksy_szczegoly() {
				$indeks = mysql_query ("SELECT I.idIndeks, I.idUzytkownik, I.nrIndeks, I.utworzenie, I.status_2, I.zakonczenie, K.nazwaKierunek, K.dataRozpoczecia, K.dataZakonczenia, W.nazwaWydzial, U.nazwaUczelnia FROM indeks I 
										INNER JOIN kierunek K ON K.idKierunek=I.idKierunek 
										INNER JOIN wydzial W ON W.idWydzial=K.idWydzial 
										INNER JOIN uczelnia U ON U.idUczelnia=W.idUczelnia 
										WHERE I.status_2=0 ORDER BY nazwaUczelnia, nazwaWydzial, nazwaKierunek, dataRozpoczecia ASC");
				return $indeks;
			}
			
			function pobierz_indeksy() {
				$indeks = mysql_query ("SELECT I.idIndeks, I.nrIndeks FROM indeks I WHERE I.status_2=0 ORDER BY nrIndeks ASC");
				return $indeks;
			}
			
			function pobierz_kierunek() {
				$kierunek = mysql_query ( "SELECT K.idKierunek, K.idWydzial, K.nazwaKierunek, K.dataRozpoczecia, K.dataZakonczenia, K.opis, W.nazwaWydzial, U.nazwaUczelnia FROM kierunek K 
											INNER JOIN wydzial W ON W.idWydzial=K.idWydzial 
											INNER JOIN uczelnia U ON U.idUczelnia=W.idUczelnia 
											ORDER BY nazwaUczelnia, nazwaWydzial, nazwaKierunek, dataRozpoczecia ASC" );
				return $kierunek;
			}
			
			function pobierz_zestawienie_studentow() {
				$zestawienie_studentow = mysql_query ( "SELECT U.idUzytkownik, U.idGrupyKierunek, U.login, U.idGrupyUzytkownik, G.nazwaGrupa FROM uzytkownik U 
											INNER JOIN grupyUzytkownik G ON U.idGrupyUzytkownik=G.idGrupyUzytkownik WHERE U.idGrupyUzytkownik = 1 ORDER BY login ASC" );
				return $zestawienie_studentow;
			}
			
			function pobierz_zmianaStanu() {
				global $id;
				$zmianaStanu = mysql_query ("SELECT R.idRejestratorZdarzenia, R.idIndeks, I.nrIndeks FROM rejestratorZdarzenia R 
												INNER JOIN indeks I ON I.idIndeks=R.idIndeks 
												WHERE R.idUzytkownik='$id' && stasus=0 ORDER BY dataOtrzymania ASC");
				return $zmianaStanu;
			}
			
			function pobierz_miejsce() {
				$miejsce = mysql_query ("SELECT M.idMiejsce, M.nazwaMiejsce FROM miejsce M ORDER BY M.nazwaMiejsce ASC");
				return $miejsce;
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
// Funkcja dla trybu Zarządzania indeksami ZI
//======================================================================	
			// ======================================================================
			// Funkcja indeks dodanie
			// ======================================================================
			function zi_dodaj() {
				$kierunek = pobierz_kierunek();
				$zestawienie_studentow = pobierz_zestawienie_studentow();
				$data = date ( 'Y-m-d' );
				echo "<p id='title'>Dodaj nowy indeks:</p>";
				echo "<form action='edytuj_indeks.php?zm=true&mode=zi&option=dodaj' method='POST'>
						<table border=0>
							<tr><td id='fnazwa_pola'>Numer indeksu: </td><td><input style='width: 100px' type='text' name='numerIndeks' /></td></tr>
							<tr><td id='fnazwa_pola'>Utworzenie indeksu: </td><td><input style='width: 100px' type='text' name='utworzenie' value='$data' placeholder='yyyy-mm-dd'/></td></tr>
							<tr><td id='fnazwa_pola'>Wybierz studenta: </td><td><select  style='width: 100px' name='student' >";
					for($i = 0; $i < mysql_num_rows ( $zestawienie_studentow ); $i ++) {
						echo "<option value='" . mysql_result ( $zestawienie_studentow, $i, 'idUzytkownik' ) . "' selected='selected'>" . mysql_result ( $zestawienie_studentow, $i, 'login' ) . "</option>";
					}
					echo "</select></td></tr>
							<tr><td id='fnazwa_pola'>Dodaj kierunek studiów: </td><td><select  style='width: 300px' name='kierunek'>
										<option value='" . mysql_result ( $kierunek, 0, 'idKierunek' ) . "' selected='selected'>" . mysql_result ( $kierunek, 0, 'nazwaKierunek' ) . "::" . mysql_result ( $kierunek, 0, 'dataRozpoczecia' ) . "::" . mysql_result ( $kierunek, 0, 'nazwaWydzial' ) . "::" . mysql_result ( $kierunek, 0, 'nazwaUczelnia' ) . "</option>";
								for($i = 1; $i < mysql_num_rows ( $kierunek ); $i ++) {
									echo "<option value='" . mysql_result ( $kierunek, $i, 'idKierunek' ) . "' selected='selected'>" . mysql_result ( $kierunek, $i, 'nazwaKierunek' ) . "::" . mysql_result ( $kierunek, $i, 'dataRozpoczecia' ) . "::" . mysql_result ( $kierunek, $i, 'nazwaWydzial' ) . "::" . mysql_result ( $kierunek, $i, 'nazwaUczelnia' ) . "</option>";
								}
								echo "</select></td></tr>
							<tr><td colspan=2 align=center><input type='submit' name='ok' value='DODAJ' /></td></tr>
						</table>
					   </form>";
			}
			
			// ======================================================================
			// Funkcja indeks edycja
			// ======================================================================
			function zi_edytuj() {
				$indeks = pobierz_indeksy_szczegoly();
				$kierunek = pobierz_kierunek();
				$zestawienie_studentow = pobierz_zestawienie_studentow();
				echo "<p id='title'>Edytuj indeks:</p>";
				if (mysql_num_rows ( $indeks ) != 0) {
					echo "<form action='edytuj_indeks.php?zm=true&mode=zi&option=edycja' method='POST'>
							<table border=0>
								<tr>
									<td id='fnazwa_pola'>Wybierz indeks: </td><td><select style='width: 100px' name='numerIndeks'>
										<option value='" . mysql_result ( $indeks, 0, 'idIndeks' ) . "' selected='selected'>" . mysql_result ( $indeks, 0, 'nrIndeks' ) . "</option>";
										for($i = 1; $i < mysql_num_rows ( $indeks ); $i ++) {
											echo "<option value='" . mysql_result ( $indeks, $i, 'idIndeks' ) . "' selected='selected'>" . mysql_result ( $indeks, $i, 'nrIndeks' ) . "</option>";
										}
									echo "</select></td></tr>
									<tr><td id='fnazwa_pola'>Wybierz studenta: </td><td><select style='width: 100px' name='student' >";
										for($i = 0; $i < mysql_num_rows ( $zestawienie_studentow ); $i ++) {
											echo "<option value='" . mysql_result ( $zestawienie_studentow, $i, 'idUzytkownik' ) . "' selected='selected'>" . mysql_result ( $zestawienie_studentow, $i, 'login' ) . "</option>";
										}
									echo "</select></td></tr>
									<tr><td id='fnazwa_pola'> Nowy numer indeksu: </td><td><input style='width: 100px' type='text' name='zmiana_numeru'/></td></tr>
									<tr><td id='fnazwa_pola'> Nowa data utworzenia: </td><td><input style='width: 100px' type='text' name='zmiana_utworzenie' placeholder='yyyy-mm-dd'/></td></tr>
									<tr><td id='fnazwa_pola'> Nowa data zakończenia: </td><td><input style='width: 100px' type='text' name='zmiana_zakonczenie' placeholder='yyyy-mm-dd'/></td></tr>
									<tr><td id='fnazwa_pola'> Edytuj kierunek: </td><td><select  style='width: 350px' name='kierunek'>
										<option value='0' selected>Wybierz kierunek</option>";
								for($i = 0; $i < mysql_num_rows ( $kierunek ); $i ++) {
									echo "<option value='" . mysql_result ( $kierunek, $i, 'idKierunek' ) . "'>" . mysql_result ( $kierunek, $i, 'nazwaKierunek' ) . "::" . mysql_result ( $kierunek, $i, 'dataRozpoczecia' ) . "::" . mysql_result ( $kierunek, $i, 'nazwaWydzial' ) . "::" . mysql_result ( $kierunek, $i, 'nazwaUczelnia' ) . "</option>";
								}
								echo "</select></td></tr>
									<tr><td align='center' colspan=2><input type='checkbox' name='status' value='status' />Czy indeks zakończony</a></td></tr>
									<tr><td colspan=2 align=center><input type='submit' name='ok' value='ZAPISZ' /></td></tr>
								</table>
							</form>";
				} else
					echo "Brak indeksów - dodaj nowe indeksy<br/>";
			}
			
//======================================================================
// Funkcja dla trybu Stany Indeksu SI
//======================================================================
			// ======================================================================
			// Funkcja rejestr zdarzenia dodaj
			// ======================================================================
			function si_dodaj() {
				$indeks = pobierz_indeksy();
				$miejsce = pobierz_miejsce();
				$data = date ( 'Y-m-d' );
				echo "<p id='title'>Dodaj nowe zdarzenie:</p>";
				if (mysql_num_rows ( $indeks ) != 0 && mysql_num_rows ( $miejsce ) != 0) {
					echo "<form action='edytuj_indeks.php?zm=true&mode=si&option=dodaj' method='POST'>
							<table border=0>
								<tr><td id='fnazwa_pola'>Wybierz indeks: </td><td><select style='width: 100px' name='numerIndeks'>
									<option value='" . mysql_result ( $indeks, 0, 'idIndeks' ) . "' selected='selected'>" . mysql_result ( $indeks, 0, 'nrIndeks' ) . "</option>";
									for($i = 1; $i < mysql_num_rows ( $indeks ); $i ++) {
										echo "<option value='" . mysql_result ( $indeks, $i, 'idIndeks' ) . "' selected='selected'>" . mysql_result ( $indeks, $i, 'nrIndeks' ) . "</option>";
									}
								echo "</select></td></tr>
								<tr>
								<td id='fnazwa_pola'>Wybierz miejsce: </td><td><select style='width: 100px' name='miejsce'>
									<option value='" . mysql_result ( $miejsce, 0, 'idMiejsce' ) . "' selected='selected'>" . mysql_result ( $miejsce, 0, 'nazwaMiejsce' ) . "</option>";
									for($i = 1; $i < mysql_num_rows ( $miejsce ); $i ++) {
										echo "<option value='" . mysql_result ( $miejsce, $i, 'idMiejsce' ) . "' selected='selected'>" . mysql_result ( $miejsce, $i, 'nazwaMiejsce' ) . "</option>";
									}
								echo "</select></td></tr>
								<tr><td id='fnazwa_pola'>Data utworzenia: </td><td><input style='width: 100px' type='text' name='utworzenie' value='$data' placeholder='yyyy-mm-dd'/></td></tr>
								<tr><td id='fnazwa_pola'>Opis: </td><td><input style='width: 200px' type='text' name='opis'/></td></tr>
								<tr><td colspan=2 align=center><input type='submit' name='ok' value='ZAPISZ' /></td></tr>
							</table>
						</form>";
				} else
					echo "Brak indeksów lub miejsc<br/>";
			}
			
			// ======================================================================
			// Funkcja rejestr zdarzenia stan indeksu
			// ======================================================================
			function si_stan() {
				$zmianaStanu = pobierz_zmianaStanu();
				echo "<p id='title'>Zmień stan indeksu:</p>";
				if (mysql_num_rows ( $zmianaStanu ) != 0) {
					echo "<form action='edytuj_indeks.php?zm=true&mode=si&option=stan' method='POST'>
							<table border=0>
								<tr>
									<td id='fnazwa_pola'>Wybierz indeks: </td><td><select style='width: 100px' name='zdarzenie'>
										<option value='" . mysql_result ( $zmianaStanu, 0, 'idRejestratorZdarzenia' ) . "' selected='selected'>" . mysql_result ( $zmianaStanu, 0, 'nrIndeks' ) . "</option>";
										for($i = 1; $i < mysql_num_rows ( $zmianaStanu ); $i ++) {
											echo "<option value='" . mysql_result ( $zmianaStanu, $i, 'idRejestratorZdarzenia' ) . "' selected='selected'>" . mysql_result ( $zmianaStanu, $i, 'nrIndeks' ) . "</option>";
										}
									echo "</select></td></tr>
									<tr><td align='center' colspan=2><input type='checkbox' name='status' value='status' />Czy zdarzenie zakończone</a></td></tr>
									<tr><td colspan=2 align=center><input type='submit' name='ok' value='ZAPISZ' /></td></tr>
								</table>
							</form>";
				} else
					echo "Brak indeksów do zmiany statusu<br/>";
			}
			
			// ======================================================================
			// Funkcja rejestr zdarzenia stan indeksu
			// ======================================================================
			function si_stanwiele($query) {
				echo "<p id='title'>Zmień stan wybranych indeksów:</p>";
				if (mysql_num_rows ( $query ) != 0) {
					echo "<form action='edytuj_indeks.php?zm=true&mode=si&option=stanwiele' method='POST'>
							<table border=0 align=center>
								<tr><td>Wybierz indeksy: <select style='width: 150px' id=rejestrator name='rejestrator[]' multiple='multiple' size='10'>";
								for($i = 0; $i < mysql_num_rows ( $query ); $i ++) {
									echo "<option value='" . mysql_result ( $query, $i, 'idRejestratorZdarzenia' ) . "' selected='selected'>" . mysql_result ( $query, $i, 'nrIndeks' ) . "</option>";
								}
								echo "</select></td></tr>
								<tr><td align='center' colspan=2><input type='checkbox' name='status' value='status' />Czy zdarzenie zakończone</a></td></tr>
								<tr><td colspan=2 align=center><input type='submit' name='ok' value='ZAPISZ' /></td></tr>
							</table>
						</form>";
				} else
					echo "Brak indeksów<br/>";
			}
			
			// ======================================================================
			// Funkcja rejestr zdarzenia dodaj wiele
			// ======================================================================
			function si_dodajwiele($query) {
				$miejsce = pobierz_miejsce();
				$data = date ( 'Y-m-d' );
				echo "<p id='title'>Dodaj zdarzenia do indeksów:</p>";
				if (mysql_num_rows ( $query ) != 0) {
					echo "<form action='edytuj_indeks.php?zm=true&mode=si&option=dodajwiele' method='POST'>
							<table border=0 align=center>
								<tr><td id='fnazwa_pola'>Wybierz indeksy: </td><td><select style='width: 150px' id=indeksy name='indeksy[]' multiple='multiple' size='10'>";
								for($i = 0; $i < mysql_num_rows ( $query ); $i ++) {
									echo "<option value='" . mysql_result ( $query, $i, 'idIndeks' ) . "' selected='selected'>" . mysql_result ( $query, $i, 'nrIndeks' ) . "</option>";
								}
								echo "</select></td></tr>
								<tr><td id='fnazwa_pola'>Wybierz miejsce: </td><td><select style='width: 100px' name='miejsce'>
										<option value='" . mysql_result ( $miejsce, 0, 'idMiejsce' ) . "' selected='selected'>" . mysql_result ( $miejsce, 0, 'nazwaMiejsce' ) . "</option>";
										for($i = 1; $i < mysql_num_rows ( $miejsce ); $i ++) {
											echo "<option value='" . mysql_result ( $miejsce, $i, 'idMiejsce' ) . "' selected='selected'>" . mysql_result ( $miejsce, $i, 'nazwaMiejsce' ) . "</option>";
										}
									echo "</select></td></tr>
									<tr><td id='fnazwa_pola'>Data utworzenia: </td><td><input style='width: 100px' type='text' name='utworzenie' value='$data' placeholder='yyyy-mm-dd'/></td></tr>
									<tr><td id='fnazwa_pola'>Opis: </td><td><input style='width: 200px' type='text' name='opis'/></td></tr>
									<tr><td colspan=2 align=center><input type='submit' name='ok' value='ZAPISZ' /></td></tr>
							</table>
						</form>";
				} else
					echo "Brak indeksów<br/>";
			}
			
			// ======================================================================
			// Funkcja rejestr zdarzenia selekcja indeksów wg kierunku
			// ======================================================================
			function si_selekcja() {
				$kierunek = pobierz_kierunek();
				echo "<p id='title'>Wybierz parametr selekcji indeksów:</p>";
				echo "<form action='edytuj_indeks.php?zm=true&mode=si&option=selekcja' method='POST'>
						<table border=0>
							<tr><td id='fnazwa_pola'>Wybierz kierunek studiów: </td><td><select  style='width: 350px' name='kierunek'>
										<option value='" . mysql_result ( $kierunek, 0, 'idKierunek' ) . "' selected='selected'>" . mysql_result ( $kierunek, 0, 'nazwaKierunek' ) . "::" . mysql_result ( $kierunek, 0, 'dataRozpoczecia' ) . "::" . mysql_result ( $kierunek, 0, 'nazwaWydzial' ) . "::" . mysql_result ( $kierunek, 0, 'nazwaUczelnia' ) . "</option>";
								for($i = 1; $i < mysql_num_rows ( $kierunek ); $i ++) {
									echo "<option value='" . mysql_result ( $kierunek, $i, 'idKierunek' ) . "' selected='selected'>" . mysql_result ( $kierunek, $i, 'nazwaKierunek' ) . "::" . mysql_result ( $kierunek, $i, 'dataRozpoczecia' ) . "::" . mysql_result ( $kierunek, $i, 'nazwaWydzial' ) . "::" . mysql_result ( $kierunek, $i, 'nazwaUczelnia' ) . "</option>";
								}
								echo "</select></td></tr>
							<tr><td colspan=2 align=center><input type='submit' name='ok' value='WYBIERZ' /></td></tr>
						</table>
					   </form>";
			}
				
			
			

			//======================================================================
			// Tryb zatwierdzania zmian w indeksach
			//======================================================================
			if (isset ( $_GET ['zm'] )) {
				if (isset ( $_GET ['mode'] )) {
					$data = date ( 'Y-m-d' );
					if ($tryb == 'zi') {
						echo '<div>Zarządzanie Indeksami</div><center>';
						//======================================================================
						// Tryb indeks dodaj
						//======================================================================
						if ($opcja == 'dodaj') {
							$numer = $_POST ['numerIndeks'];
							$idKierunek = $_POST ['kierunek'];
							$idStudent = $_POST ['student'];
							$utworzenie = $_POST ['utworzenie'];
							$query = mysql_query ( "SELECT idIndeks FROM indeks WHERE nrIndeks='$numer'" );
							$query1 = mysql_query ( "SELECT idIndeks FROM indeks WHERE idKierunek='$idKierunek' && idUzytkownik='$idStudent'" );
							if (mysql_num_rows ( $query ) != 0 || empty ( $numer )) {
								echo "<p>Indeks już istnieje lub nie podano numeru indeksu</p>";
								zi_dodaj ();
							} else if (mysql_num_rows ( $query1 ) != 0) {
								echo "<p>Student już posiada indeks na tym kierunku</p>";
								zi_dodaj ();
							} else {
								if(sprData($utworzenie)){ 
									$spr = $data - $utworzenie;
									if($spr >= 0) {
										$query = mysql_query ( "INSERT INTO indeks(nrIndeks, idUzytkownik, idKierunek, utworzenie, status_2) VALUES('$numer','$idStudent','$idKierunek','$utworzenie',0)" );
										$query = mysql_query ( "SELECT idIndeks FROM indeks WHERE nrIndeks='$numer'");
										$idIndeks = mysql_result ($query,0,'idIndeks');
										$query = mysql_query ( "INSERT INTO rejestratorZdarzenia(idIndeks, idMiejsce, idUzytkownik, dataOtrzymania, stasus, opis) VALUES('$idIndeks',1,'$id','$data',1,'Wykonanie indeksu')" );
										echo "Dodawanie indeksu ukończone<br/>";
									} else {
										echo "Data się nie zgadza";
										zi_dodaj ();
									}
								} else {
									echo "Zły format daty";
									zi_dodaj ();
								}
							}
						//======================================================================
						// Tryb indeks edytuj
						//======================================================================
						} else if ($opcja == 'edycja') {
								$idStudent = $_POST ['student'];
								$idKierunek = $_POST ['kierunek'];
								$idIndeks = $_POST ['numerIndeks'];
								$numer = $_POST ['zmiana_numeru'];
								$utworzenie = $_POST ['zmiana_utworzenie'];
								$zakonczenie = $_POST ['zmiana_zakonczenie'];
								$query = mysql_query ( "SELECT idIndeks FROM indeks WHERE nrIndeks='$numer'" );
								$query1 = mysql_query ( "SELECT idIndeks FROM indeks WHERE idKierunek='$idKierunek' && idUzytkownik='$idStudent'" );
								if (mysql_num_rows ( $query ) != 0) {
									echo "<p>Indeks już istnieje</p>";
								} else if (mysql_num_rows ( $query1 ) != 0) {
									echo "<p>Student już posiada indeks na tym kierunku</p>";
								} else {
									if(!empty($numer))
										$query = mysql_query ( "UPDATE indeks SET nrIndeks='$numer', idUzytkownik='$idStudent' WHERE idIndeks='$idIndeks'" );
									if($idKierunek!=0)
										$query = mysql_query ( "UPDATE indeks SET idUzytkownik='$idStudent', idKierunek='$idKierunek' WHERE idIndeks='$idIndeks'" );
									if((empty($utworzenie) || sprData($utworzenie)) && (empty($zakonczenie) || sprData($zakonczenie))){ 
										$spr1 = $zakonczenie - $utworzenie;
										if($spr1 >= 0 || empty($utworzenie) || empty($zakonczenie)) {
											$spr1 = $data - $utworzenie;
											$spr2 = $data - $zakonczenie;
											if ( !empty ( $utworzenie) && $spr1 >=0)
												$query = mysql_query ( "UPDATE indeks SET utworzenie='$utworzenie' WHERE idIndeks='$idIndeks'" );
											if ( !empty ( $zakonczenie) && $spr2 >=0)
												$query = mysql_query ( "UPDATE indeks SET zakonczenie='$zakonczenie', status_2=1 WHERE idIndeks='$idIndeks'" );
										} else {
											echo "Daty się nie zgadzają";
										}
									} else {
										echo "Zły format daty";
									}
									if ( isset ($_POST ['status']))
										$query = mysql_query ( "UPDATE indeks SET status_2=1, zakonczenie='$data' WHERE idIndeks='$idIndeks'" );
									//else
										//$query = mysql_query ( "UPDATE indeks SET status_2=0, zakonczenie=NULL WHERE idIndeks='$idIndeks'" );
									if ($query) 
										echo "<p>Edycja indeksu ukończona</p>";
									 else
										echo "<p>Błąd! Pozostały stare dane</p>";
								}
						}
						echo '<p><a href="indeks.php">Powrót do panelu</a>&emsp;::&emsp;<a href="edytuj_indeks.php?mode=zi">Zarządzanie indeksami</a></p>';
					} else if ($tryb == 'si') {
						//======================================================================
						// Tryb zdarzenie dodaj
						//======================================================================
						if ($opcja == 'dodaj') {
							$miejsce = $_POST ['miejsce'];
							$idIndeks = $_POST ['numerIndeks'];
							$utworzenie = $_POST ['utworzenie'];
							$opis = $_POST ['opis'];
								if(sprData($utworzenie)){ 
									$spr1 = $data - $utworzenie;
									if($spr1 >= 0) {
											$query = mysql_query ("SELECT R.idRejestratorZdarzenia FROM rejestratorzdarzenia R WHERE R.idIndeks='$idIndeks' ORDER BY R.dataOtrzymania DESC LIMIT 1");
											if(mysql_num_rows($query) !=0 ) {
												$idRejestrator = mysql_result($query,0,'idRejestratorZdarzenia');
												mysql_query ("UPDATE rejestratorZdarzenia SET dataZwrotu='$data', stasus=1 WHERE idRejestratorZdarzenia='$idRejestrator'");
											}
											$query = mysql_query ( "INSERT INTO rejestratorZdarzenia(idIndeks, idMiejsce, idUzytkownik, dataOtrzymania, stasus, opis) VALUES('$idIndeks','$miejsce','$id','$utworzenie',0,'$opis')" );
											if ($query) {
												echo "<p>Dodanie zdarzenia ukończone</p>";
											} else
												echo "<p>Błąd! Pozostały stare dane</p>";
									} else {
										echo "Daty się nie zgadzają";
									}
								} else {
									echo "Zły format daty";
								}			
						//======================================================================
						// Tryb zdarzenie stan
						//======================================================================
						} else if ($opcja == 'stan') {
							$idRejestrator = $_POST ['zdarzenie'];
							$query = mysql_query ( "SELECT idRejestratorZdarzenia FROM rejestratorZdarzenia WHERE idRejestratorZdarzenia='$idRejestrator'" );
							if ( $query && isset ($_POST ['status']))
								$query = mysql_query ( "UPDATE rejestratorZdarzenia SET stasus=1 WHERE idRejestratorZdarzenia='$idRejestrator'" );
							if ($query) {
								echo "<p>Zmiana statusu zakończona</p>";
							} else
								echo "<p>Błąd! Status indeksu niezmieniony</p>";	
						//======================================================================
						// Tryb selekcja indeksów
						//======================================================================
						} else if ($opcja == 'selekcja') {
							$idKierunek = $_POST ['kierunek'];
								// ======================================================================
								// Pobranie danych z bazy do skryptu
								// ======================================================================
								$selekcjaIndeks = mysql_query ("SELECT I.idIndeks, I.nrIndeks, I.utworzenie, I.status_2, I.zakonczenie FROM indeks I 
										WHERE I.status_2=0 && idKierunek='$idKierunek' ORDER BY nrIndeks ASC");
								$selekcjaZmianaStanu = mysql_query ("SELECT R.idRejestratorZdarzenia, R.idIndeks, I.nrIndeks, I.idKierunek FROM rejestratorZdarzenia R 
										INNER JOIN indeks I ON I.idIndeks=R.idIndeks 
										WHERE R.idUzytkownik='$id' && stasus=0 && idKierunek='$idKierunek' ORDER BY dataOtrzymania ASC");
							if(mysql_num_rows($selekcjaIndeks) !=0 ) {
								si_dodajwiele($selekcjaIndeks);
								echo "<hr/>";
								si_stanwiele($selekcjaZmianaStanu);
								echo "<hr/>";
							} else
								echo "<p>Brak indeksów</p>";
						//======================================================================
						// Tryb zdarzenie stan wiele
						//======================================================================
						} else if ($opcja == 'stanwiele') {
							if (isset ( $_POST ['rejestrator'] ) && isset ($_POST ['status'])) {
								$rejestrator = $_POST ['rejestrator'];
								for($i = 0; ! empty ( $rejestrator [$i] ); $i ++)
									$query = mysql_query ( "UPDATE rejestratorZdarzenia SET stasus=1 WHERE idRejestratorZdarzenia='$rejestrator[$i]'" );
								if ($query) {
									echo "<p>Zmiana statusu zakończona</p>";
								} else
									echo "<p>Błąd! Status indeksu niezmieniony</p>";	
							} else 
								echo "<p><b>Nie wybrano indeksów lub nie zaznaczono pola</b></p>";
						
						//======================================================================
						// Tryb zdarzenie dodaj
						//======================================================================
						} else if ($opcja == 'dodajwiele') {
							if (isset ( $_POST ['indeksy'] )) {
								$idIndeks = $_POST ['indeksy'];
								$miejsce = $_POST ['miejsce'];
								$utworzenie = $_POST ['utworzenie'];
								$opis = $_POST ['opis'];
								if(sprData($utworzenie)){ 
									$spr1 = $data - $utworzenie;
									if($spr1 >= 0) {
										for($i = 0; ! empty ( $idIndeks [$i] ); $i ++){
											$query = mysql_query ("SELECT R.idRejestratorZdarzenia FROM rejestratorzdarzenia R WHERE R.idIndeks='$idIndeks[$i]' ORDER BY R.dataOtrzymania DESC LIMIT 1");
											if(mysql_num_rows($query) !=0 ) {
												$idRejestr = mysql_result($query,0,'idRejestratorZdarzenia');
												mysql_query ("UPDATE rejestratorZdarzenia SET dataZwrotu='$data', stasus=1 WHERE idRejestratorZdarzenia='$idRejestr'");
											}
											$query = mysql_query ( "INSERT INTO rejestratorZdarzenia(idIndeks, idMiejsce, idUzytkownik, dataOtrzymania, stasus, opis) VALUES('$idIndeks[$i]','$miejsce','$id','$utworzenie',0,'$opis')" );
										}
										if ($query) {
											echo "<p>Dodanie zdarzeń ukończone</p>";
										} else
											echo "<p>Błąd! Pozostały stare dane</p>";
									} else {
										echo "Daty się nie zgadzają";
									}
								} else {
									echo "Zły format daty";
								}
							} else {
								echo "<p><b>Nie wybrano indeksów</b></p>";

							}
						}
						echo '<p><a href="indeks.php">Powrót do panelu</a>&emsp;::&emsp;<a href="edytuj_indeks.php?mode=si">Zarządzanie zdarzeniami</a></p>';
					}
				}
					
			

			
			//======================================================================
			// Wstepna inicjalizacja formularzy opcji zarzadzania
			//======================================================================
			} else {
				if (isset ( $_GET ['mode'] )) {
					if ($tryb == 'zi') {
						echo '<div>Zarządzanie Indeksami</div><center>';
						zi_dodaj();
						echo "<hr/>";
						zi_edytuj();
						echo "<hr/>";
						echo '<p><a href="indeks.php">Powrót do panelu</a></p></center>';
					}

					if ($tryb == 'si') {
						echo '<div>Zarządzanie rejestrami zdarzeń</div><center>';
						si_dodaj();
						echo "<hr/>";
						si_stan();
						echo "<hr/>";
						si_selekcja();
						echo "<hr/>";
						echo '<p><a href="indeks.php">Powrót do panelu</a></p>';
					}
				}
			}
			mysql_close ();
		}
		?>
</div>
<?php include_once 'footer.php'; ?>