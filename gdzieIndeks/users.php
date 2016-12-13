<?php
// ======================================================================
// Zestawienie uzytkownikow wraz z sortowaniem po kategoriach
// ======================================================================
session_start ();
include_once 'header.php';
?>

<div id="body-bot">
	<h2><span><strong>LISTA UŻYTKOWNIKÓW</strong></span></h2>
	<div id="items">
		<?php
			function sortowanie() {
				global $sortowanie;
				// ======================================================================
				// Funkcja pobierająca i sortująca dane do zestawienia w zależności od parametru
				// ======================================================================
				switch ($sortowanie) {
					case "login" :
						$pobierz_uzytkownikow = mysql_query ( "SELECT U.idUzytkownik, U.login, U.logowanie, U.rejestracja, G.nazwaGrupa
								FROM uzytkownik U
								INNER JOIN grupyUzytkownik G ON G.idGrupyUzytkownik=U.idGrupyUzytkownik
								ORDER BY login ASC" );
						break;
					case "grupa" :
						$pobierz_uzytkownikow = mysql_query ( "SELECT U.idUzytkownik, U.login, U.logowanie, U.rejestracja, G.nazwaGrupa
								FROM uzytkownik U
								INNER JOIN grupyUzytkownik G ON G.idGrupyUzytkownik=U.idGrupyUzytkownik
								ORDER BY nazwaGrupa ASC" );
						break;
					case "rej" :
						$pobierz_uzytkownikow = mysql_query ( "SELECT U.idUzytkownik, U.login, U.logowanie, U.rejestracja, G.nazwaGrupa
								FROM uzytkownik U
								INNER JOIN grupyUzytkownik G ON G.idGrupyUzytkownik=U.idGrupyUzytkownik
								ORDER BY rejestracja DESC" );
						break;
					case "wiz" :
						$pobierz_uzytkownikow = mysql_query ( "SELECT U.idUzytkownik, U.login, U.logowanie, U.rejestracja, G.nazwaGrupa
								FROM uzytkownik U
								INNER JOIN grupyUzytkownik G ON G.idGrupyUzytkownik=U.idGrupyUzytkownik
								ORDER BY logowanie DESC" );
						break;

				}
				$num_row = mysql_num_rows ( $pobierz_uzytkownikow );
				if (! $num_row)
					echo "<p><b>Brak użytkownikow</b></p>";
				for($i = 0; $i < $num_row; $i ++) {
					$lp = $i + 1;
					$id_uzytkownika = mysql_result ( $pobierz_uzytkownikow, $i, "idUzytkownik" );
					echo '<tr><td>' . $lp . '</td>
						<td><a href="user.php?id=' . $id_uzytkownika . '">' . mysql_result ( $pobierz_uzytkownikow, $i, "login" ) . '</a></td>
						<td align="center">' . mysql_result ( $pobierz_uzytkownikow, $i, "nazwaGrupa" ) . '</td>
						<td align="center">' . mysql_result ( $pobierz_uzytkownikow, $i, "rejestracja" ) . '</td>
						<td align="center">' . mysql_result ( $pobierz_uzytkownikow, $i, "logowanie" ) . '</td>';
				}
			}

			if (isset ( $_SESSION ['login'] )) {
				
				$connect = mysql_connect ( $ADRES, $ADMIN, $ADMINPASS );
				$baza = mysql_select_db ( $BAZADANYCH );
				$sortowanie = $_GET ['sort'];
				
				echo '<table align="center" border="1" cellpadding="5" rules="all" bgcolor="#e5e5e5">
						<tr align="center"><td>LP</td>
						<td><a href="users.php?sort=login">Login</a></td>
						<td><a href="users.php?sort=grupa">Grupa</a></td>
						<td><a href="users.php?sort=rej">Dołączył</a></td>
						<td><a href="users.php?sort=wiz">Ostatania wizyta</a></td>
						</tr>';
				// ======================================================================
				// Wywołanie funkcji sortowania
				// ======================================================================
				switch ($sortowanie) {
					case "login" :
						sortowanie ();
						break;
					case "grupa" :
						sortowanie ();
						break;
					case "rej" :
						sortowanie ();
						break;
					case "wiz" :
						sortowanie ();
						break;
				}
				echo "</tr></table>";
			}
			?>
	</div>

<?php include_once 'footer.php';?>