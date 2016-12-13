<?php
// ======================================================================
// Panel administracyjny
// ======================================================================
session_start ();
include_once 'header.php';
?>

<div id="body-bot">
	<h2><span><strong>PANEL ADMINISTRATORA</strong></span></h2>
	<div id="items">
		<?php
			if (isset ( $_SESSION ['login']) && isset ( $_SESSION ['grupa']) && ($_SESSION['grupa']==2 || $_SESSION['grupa']==4)) {
				
				$id_grupy = $_SESSION ['grupa'];
				$connect = mysql_connect ( $ADRES, $ADMIN, $ADMINPASS );
				$baza = mysql_select_db ( $BAZADANYCH );
				// ======================================================================
				// Wyswietlenie linkow do opcji zarzadzania serwisem
				// ======================================================================
				echo "<center>ZARZĄDZANIE<br/>
						::&emsp;<a href='edytuj_mod.php?mode=str'>Struktura</a>&emsp;::&emsp;
						<a href='edytuj_mod.php?mode=uk'>Student-Kierunek</a>&emsp;";
				if ($id_grupy == 2){
						echo "::&emsp;<a href='edytuj_admin.php?mode=zu'>Users</a>&emsp;::&emsp;
							<a href='edytuj_admin.php?mode=zm'>Miejsca</a>&emsp;::
							<hr /><br />
								===&emsp;OKNO ZAPYTAŃ <b>SQL</b>&emsp;===
							<br/><br/><hr /></center>";
					
					// ======================================================================
					// Konsola SQL
					// ======================================================================
					if (isset ( $_GET ['action'] )) {
						$zapytanie = $_POST ['tresc'];
						// ======================================================================
						// Wykonanie zapytania i wyswietlenie wyniku
						// ======================================================================
						echo "Twoje zapytanie:&emsp;<b>" . $zapytanie . "</b><br/><br/>";
						$query = mysql_query ( ( string ) $zapytanie );
						$rows = mysql_num_rows ( $query );
						echo "<center><table border=1>";
						for($i = 0; $i < mysql_num_rows ( $query ); $i ++) {
							if ($i == 0) {
								echo "<tr>";
								for($j = 0; $j < mysql_num_fields ( $query ); $j ++)
									echo "<td>" . mysql_field_name ( $query, $j ) . "</td>";
								echo "</tr>";
							}
							echo "<tr>";
							for($j = 0; $j < mysql_num_fields ( $query ); $j ++)
								echo "<td>" . mysql_result ( $query, $i, $j ) . "</td>";
							echo "</tr>";
						}
						echo "</table></center><br/><br/>";
						echo "<a href='panel_admin.php'>Powrót do wpisywania zapytań</a>";
					} else {
						// ======================================================================
						// Inicjalizacja konsoli
						// ======================================================================
						echo "<center><table border=0>
										<form action='panel_admin.php?action=true' method='post'> 
										<tr><td>Treść:</td><td><textarea rows='10' cols='60' name='tresc'></textarea></td></tr>
										<tr><td></td><td align=center><input type='submit' name='Dodaj' value='Wykonaj zapytanie' /></td></tr>
										</form>
									 </table></center>";
					}
				}
				mysql_close ();
			}
			?>
	</div>

<?php include_once 'footer.php';?>