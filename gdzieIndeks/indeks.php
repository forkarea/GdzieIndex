<?php
// ======================================================================
// Panel użytkownika z opcjami zarządzania kontem
// ======================================================================
session_start ();
include_once 'header.php';
?>

<div id="body-bot">
	<h2><span><strong>INDEKSY</strong></span></h2>
	<div id="items">
		<?php
			if (isset ( $_SESSION ['login']) && isset ( $_SESSION ['id'] )) {
				$connect = mysql_connect ( $ADRES, $ADMIN, $ADMINPASS );
				$baza = mysql_select_db ( $BAZADANYCH );
				$id_grupy = $_SESSION ['grupa'];
				$id_uzytkownik = $_SESSION ['id'];
				
				// ======================================================================
				// Opcje zarzadzania indeksem
				// ======================================================================
				$czyPracownik = mysql_query ("SELECT idPracownik FROM pracownik WHERE idUzytkownik='$id_uzytkownik'");
					if ($id_grupy == 2 || mysql_num_rows( $czyPracownik)!=0) {
					echo "<center>
							::&emsp;<a href='edytuj_indeks.php?mode=si'>Stan indeksu</a>&emsp;";
					if ($id_grupy == 2 || $id_grupy == 4){
							echo "::&emsp;<a href='edytuj_indeks.php?mode=zi'>Zarządzanie indeksami</a>&emsp;::
								<hr /><br />";
					}
				}
				echo "</center>";
				
				// ======================================================================
				// Dane indeksów dla zalogowanego studenta
				// ======================================================================
				if($id_grupy == 1){
					$indeks = mysql_query("SELECT I.idIndeks, I.idUzytkownik, I.nrIndeks, I.utworzenie, I.status_2, I.zakonczenie, K.nazwaKierunek, K.dataRozpoczecia, K.dataZakonczenia, W.nazwaWydzial, Uczelnia.nazwaUczelnia FROM indeks I 
												INNER JOIN kierunek K ON K.idKierunek=I.idKierunek 
												INNER JOIN wydzial W ON W.idWydzial=K.idWydzial 
												INNER JOIN uczelnia Uczelnia ON Uczelnia.idUczelnia=W.idUczelnia 
												WHERE I.idUzytkownik='$id_uzytkownik'" );
					
					if(mysql_num_rows ( $indeks ) != 0){
						for($i = 0; $i < mysql_num_rows ( $indeks); $i ++) {
							echo '<fieldset style="margin-left:20px; margin-right:20px; text-align:left; padding:20px">
									<legend>Informacje o indeksie numer: ' . mysql_result ( $indeks, $i, "nrIndeks" ) . '</legend>
									<table>';
									echo '<tr><td align=right>Uczelnia:&emsp;</td><td>' . mysql_result ( $indeks, $i, "nazwaUczelnia" ) . '<br/>' . mysql_result ( $indeks, $i, "nazwaWydzial" ) . '<br/>' . mysql_result ( $indeks, $i, "nazwaKierunek" ) . '</td></tr>';
									echo '<tr><td align=right>Data utworzenia:&emsp;</td><td>' . mysql_result ( $indeks, $i, "utworzenie" ) . '</td></tr>';
										if(mysql_result( $indeks, $i, "status_2") == 0)
											echo '<tr><td></td><td align=right>Indeks aktywny</td>';
										else
											echo '<tr><td align=right>Data zakończenia:&emsp;</td><td>' . mysql_result ( $indeks, $i, "zakonczenie" ) . '</td></tr>';
									echo "<tr><td></td><td><a href='historia_indeks.php?nr=" . mysql_result ( $indeks, $i, "idIndeks" ) . "'>HISTORIA Indeksu</a></td><tr>";
									$wybranyIndeks = mysql_result( $indeks, $i, 'idIndeks');
									$stanIndeks = mysql_query ("SELECT M.nazwaMiejsce, R.idUzytkownik, R.dataZwrotu FROM rejestratorzdarzenia R 
																INNER JOIN miejsce M ON M.idMiejsce=R.idMiejsce 
																WHERE R.idIndeks='$wybranyIndeks'
																ORDER BY R.dataOtrzymania DESC LIMIT 1;");
									$spr = mysql_result( $stanIndeks,  0, 'dataZwrotu');
									if (empty($spr))
										echo "<tr><td></td><td><a href='odebranie_indeksu.php?nr=" . mysql_result ( $indeks, $i, "idIndeks" ) . "'>Odebrałem ten indeks</a></td><tr>";
								echo '</table></fieldset>';
						}
					} else {
						echo "Brak przypisanych indeksów";
					}
				}
				
				mysql_close ();
			}
		?>
	</div>


<?php include_once 'footer.php';?>