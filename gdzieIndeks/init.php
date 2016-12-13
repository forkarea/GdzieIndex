<?php

//======================================================================
// Inicjalizacja forum, dodanie podstawowych danych do bazy
// user:admin, grupy: student, administrator, pracownik, moderator i zbanowany
//======================================================================
session_start ();
include_once 'header.php';
?>

<?php
	$connect = mysql_connect ( $ADRES, $ADMIN, $ADMINPASS );
	$baza = mysql_select_db ( $BAZADANYCH );
		$student = mysql_query ( "INSERT INTO grupyUzytkownik(nazwaGrupa) VALUES('Student')" );
		$administrator = mysql_query ( "INSERT INTO grupyUzytkownik(nazwaGrupa) VALUES('Administrator')" );
		$pracownik = mysql_query ( "INSERT INTO grupyUzytkownik(nazwaGrupa) VALUES('Pracownik')" );
		$moderator = mysql_query ( "INSERT INTO grupyUzytkownik(nazwaGrupa) VALUES('Moderator')" );
		$zbanowany = mysql_query ( "INSERT INTO grupyUzytkownik(nazwaGrupa) VALUES('Zbanowany')" );
		$data = date ( 'Y-m-d H:i:s' );
		$admin = mysql_query ( "INSERT INTO uzytkownik(idGrupyUzytkownik,login,haslo,mail,rejestracja,status_2,potwierdzenie) 
				VALUES('2','admin', '" . md5 ( "admin" ) . "', 'admin@admin.com','$data','0','1')" );
		$info = mysql_query ( "INSERT INTO uzytkownikInfo(idUzytkownik) VALUES('1')" );
		echo '<br><br><center>Inicjalizacja <br>';
		if ($student && $administrator && $pracownik && $moderator && $zbanowany && $admin && $info)
			echo "<p>Inicjalizacja zakończona. Login: admin :: hasło: admin</p>
					<p><a href='index.php'>Powrót do strony głównej</a>";
		else
			echo "Błąd inicjalizacji";
		echo "</center>";
	mysql_close ();
?>

<?php include_once 'footer.php'; ?>