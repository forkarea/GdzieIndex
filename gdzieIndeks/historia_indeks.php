<?php
// ======================================================================
// Zestawienie uzytkownikow wraz z sortowaniem po kategoriach
// ======================================================================
session_start ();
include_once 'header.php';
?>

<div id="body-bot">
	<h2><span><strong>HISTORIA</strong> indeksu</span></h2>
	<div id="items">
		<?php
			if (isset ( $_SESSION ['login'] ) && isset ( $_GET ['nr'])) {
				$connect = mysql_connect ( $ADRES, $ADMIN, $ADMINPASS );
				$baza = mysql_select_db ( $BAZADANYCH );
				$nrIndeks = $_GET ['nr'];
				$id = $_SESSION ['id'];
				$sprawdzenie = mysql_query ("SELECT idIndeks FROM indeks WHERE idUzytkownik='$id'");
				if(mysql_num_rows($sprawdzenie) !=0) {
					$pobierz_rejestr_indeks = mysql_query ("SELECT R.idUzytkownik, R.dataOtrzymania, R.dataZwrotu, R.stasus, R.opis, M.nazwaMiejsce, R.idUzytkownik FROM rejestratorzdarzenia R 
															INNER JOIN miejsce M ON M.idMiejsce=R.idMiejsce 
															WHERE R.idIndeks='$nrIndeks'
															ORDER BY R.dataOtrzymania DESC;");
					if ( mysql_num_rows ( $pobierz_rejestr_indeks )==0)
						echo "<p><b>Brak danych</b></p>";
					else {
						echo '<table style="width: 550px" align="center" border="1" cellpadding="5" rules="all" bgcolor="#e5e5e5">
							<tr align="center"><td>LP</td>
							<td>Miejsce</td>
							<td>Otrzymanie</td>
							<td>Status</td>
							<td>Zwrot</a></td>
							<td>Opis</td>
							</tr>';
						for($i = 0; $i < mysql_num_rows ( $pobierz_rejestr_indeks); $i ++) {
							$lp = $i + 1;
							echo '<tr align="center"><td>' . $lp . '</td>
								<td><a href="user.php?id=' . mysql_result ( $pobierz_rejestr_indeks, $i, "idUzytkownik" ) . '">' . mysql_result ( $pobierz_rejestr_indeks, $i, "nazwaMiejsce" ) . '</a></td>
								<td >' . mysql_result ( $pobierz_rejestr_indeks, $i, "dataOtrzymania" ) . '</td>
								<td>' . mysql_result ( $pobierz_rejestr_indeks, $i, "stasus" ) . '</td>
								<td>' . mysql_result ( $pobierz_rejestr_indeks, $i, "dataZwrotu" ) . '</td>
								<td>' . mysql_result ( $pobierz_rejestr_indeks, $i, "opis" ) . '</td></tr>';
						}
						echo "</table>";
					}
				}
				mysql_close();
			}
			?>
	</div>

<?php include_once 'footer.php';?>