<?php
// ======================================================================
// Panel użytkownika z opcjami zarządzania kontem
// ======================================================================
session_start ();
include_once 'header.php';
?>

<div id="body-bot">
	<h2><span><strong>PROFIL UŻYTKOWNIKA</strong></span></h2>
	<div id="items">
		<?php
			if (isset ( $_SESSION ['login']) && isset ( $_GET ['id'] )) {
				
				$id_uzytkownik = $_GET ['id'];
				$connect = mysql_connect ( $ADRES, $ADMIN, $ADMINPASS );
				$baza = mysql_select_db ( $BAZADANYCH );
				
				// ======================================================================
				// Pobranie informacji o uzytkowniku i komunikatorach ktore uzywa
				// ======================================================================
				$pobierz_uzytkownika = mysql_query ( "SELECT U.idUzytkownik, U.idGrupyKierunek, U.login, U.mail, U.logowanie, U.rejestracja, U.avatar, G.nazwaGrupa,
							Info.imie, Info.nazwisko, Info.plec, Info.dataUrodzenia, Info.zainteresowania, Info.lokalizacja, Info.telefon
							FROM uzytkownik U
							INNER JOIN uzytkownikInfo Info ON Info.idUzytkownik=U.idUzytkownik
							INNER JOIN grupyUzytkownik G ON G.idGrupyUzytkownik=U.idGrupyUzytkownik
							WHERE U.idUzytkownik=$id_uzytkownik" );
				if (mysql_result($pobierz_uzytkownika,0,'idGrupyKierunek') != NULL)
				$pobierz_studia = mysql_query ( "SELECT U.idUzytkownik, G.nazwaGrupa, K.nazwaKierunek, K.dataRozpoczecia, K.dataZakonczenia, W.nazwaWydzial, Uczelnia.nazwaUczelnia FROM uzytkownik U
							INNER JOIN grupyKierunek G ON G.idGrupyKierunek=U.idGrupyKierunek
							INNER JOIN kierunek K ON K.idKierunek=G.idKierunek
							INNER JOIN wydzial W ON W.idWydzial=K.idWydzial
							INNER JOIN uczelnia Uczelnia ON Uczelnia.idUczelnia=W.idUczelnia
							WHERE U.idUzytkownik=$id_uzytkownik" );
				$pobierz_pracownika = mysql_query ( "SELECT P.idUzytkownik, W.nazwaWydzial, U.nazwaUczelnia, P.nrPokoju FROM pracownik P
							INNER JOIN wydzial W ON P.idWydzial=W.idWydzial
							INNER JOIN uczelnia U ON U.idUczelnia=W.idUczelnia
							WHERE P.idUzytkownik=$id_uzytkownik" );
				if (! $pobierz_uzytkownika)
					echo mysql_error ();
				// ======================================================================
				// Opcje zarzadzania profilem uzytkownika
				// ======================================================================
				if (isset ( $_SESSION ['id'] ) && $id_uzytkownik == $_SESSION ['id']) {
					echo "<center><a href='edytuj_profil.php?mode=edytuj'>Edytuj dane</a>&emsp;::&emsp;
									<a href='edytuj_profil.php?mode=haslo'>Zmień hasło</a>&emsp;::&emsp;
									<a href='edytuj_profil.php?mode=mail'>Zmień adres e-mail</a>&emsp;<br /><hr /><br /></center>";
				}
				// ======================================================================
				// Karta uzytkownika z podzialami na sekcje
				// ======================================================================
				echo '<fieldset style="margin-left:20px; margin-right:20px; text-align:left; padding:20px">
						<legend>Informacje o ' . mysql_result ( $pobierz_uzytkownika, 0, "login" ) . ':</legend>
						<table>
							<tr><td align=right>Tytuł:&emsp;</td><td>' . mysql_result ( $pobierz_uzytkownika, 0, "nazwaGrupa" ) . '</td></tr>';
							if (mysql_result($pobierz_uzytkownika,0,'idGrupyKierunek') != NULL) {
								echo '<tr><td align=right>Studiuje na:&emsp;</td><td>' . mysql_result ( $pobierz_studia, 0, "nazwaUczelnia" ) . '<br/>' . mysql_result ( $pobierz_studia, 0, "nazwaWydzial" ) . '<br/>' . mysql_result ( $pobierz_studia, 0, "nazwaKierunek" ) . '</td></tr>';
								$czyZakonczone=mysql_result($pobierz_studia, 0, "dataZakonczenia")-mysql_result($pobierz_studia, 0, "dataRozpoczecia");
								if($czyZakonczone<=0)
									echo '<tr><td></td><td align=right>Nadal studiuje</td>';
							}
					echo '	<tr><td align=right>Ostatnie logowanie:&emsp;</td><td>' . mysql_result ( $pobierz_uzytkownika, 0, "logowanie" ) . '</td></tr>
							<tr><td align=right>Zarejestrowany od:&emsp;</td><td>' . mysql_result ( $pobierz_uzytkownika, 0, "rejestracja" ) . '</td></tr>
							<tr><td align=right>Sygnatura:&emsp;</td><td>' . mysql_result ( $pobierz_uzytkownika, 0, "avatar" ) . '<br /></td></tr>
						</table>
						</fieldset>';
				echo '<br/><fieldset style="margin-left:20px; margin-right:20px; text-align:left; padding:20px">
					<legend>Dane personalne</legend>
						<table>
							<tr><td align=right>Imię:&emsp;</td><td>' . mysql_result ( $pobierz_uzytkownika, 0, "imie" ) . '</td></tr>
							<tr><td align=right>Nazwisko:&emsp;</td><td>' . mysql_result ( $pobierz_uzytkownika, 0, "nazwisko" ) . '</td></tr>
							<tr><td align=right>Płeć:&emsp;</td><td>';
				if (mysql_result ( $pobierz_uzytkownika, 0, "plec" ))
					echo "Mężczyzna</td></tr>";
				else
					echo "Kobieta</td></tr>";
				echo '<tr><td align=right>Data urodzin:&emsp;</td><td>' . mysql_result ( $pobierz_uzytkownika, 0, "dataUrodzenia" ) . '</td></tr>
						<tr><td align=right>Lokalizacja:&emsp;</td><td>' . mysql_result ( $pobierz_uzytkownika, 0, "lokalizacja" ) . '</td></tr>
						<tr><td align=right>Zainteresowania:&emsp;</td><td>' . mysql_result ( $pobierz_uzytkownika, 0, "zainteresowania" ) . '</td></tr>
					</table>
				</fieldset>';
				echo "<br/><fieldset style='margin-left:20px; margin-right:20px; text-align:left; padding:20px'>
					<legend>Kontakt</legend>
					<table>";
					if (mysql_num_rows($pobierz_pracownika)!=0){
						echo "<tr><td align=right>Pracownik:&emsp;</td><td>" . mysql_result ( $pobierz_pracownika, 0, 'nazwaUczelnia' ) . "<br/>". mysql_result ( $pobierz_pracownika, 0, 'nazwaWydzial' ) . "</td></tr>
								<tr><td align=right>Pokój:&emsp;</td><td>" . mysql_result ( $pobierz_pracownika, 0, 'nrPokoju' ) . "</td></tr>";
					}
						
					echo "<tr><td align=right>Telefon:&emsp;</td><td>" . mysql_result ( $pobierz_uzytkownika, 0, 'telefon' ) . "</td></tr>";
				if ($id_uzytkownik == $_SESSION ['id'])
					echo "<tr><td align=right>Mail:&emsp;</td><td>" . mysql_result ( $pobierz_uzytkownika, 0, 'mail' ) . "</td></tr>";
				else
					echo '<form action="email.php" method="GET">
							<input type="hidden" name="id" value="' . $id_uzytkownik . '" />
							<center><input type="submit" name="ok" value="Wyślij wiadomość" /></center>
						</form>';
				echo "</table></fieldset>";
				mysql_close ();
			}
		?>
	</div>


<?php include_once 'footer.php';?>