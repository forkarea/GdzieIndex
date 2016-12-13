<?php
//======================================================================
// Główne okno portalu
// Rozpoczęcie sesji użytkownika i import nagłówka
//======================================================================
	session_start ();
	include_once 'header.php';
?>
<div id="body-bot">
	<h2><span>strona <strong>GŁÓWNA</strong> serwisu</span></h2>
	<div id="items">
		<?php
			$connect = mysql_connect ( $ADRES, $ADMIN, $ADMINPASS );
			$baza = mysql_select_db ( $BAZADANYCH );
			
			$uzytkownicy = mysql_query ( "SELECT * FROM uzytkownik" );
			if (mysql_num_rows ( $uzytkownicy)){
			} else	
				echo '<p>Zainicjalizowanie nowej bazy <a href="init.php">KLIKNIJ TUTAJ</a></p>';
			
			mysql_close ();
		?>
	</div>
<?php include_once 'footer.php'; ?>

