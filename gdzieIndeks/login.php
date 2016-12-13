<?php
// ======================================================================
// Skrypt logowania do platformy
// ======================================================================
	include_once 'conn_cfg.php'; 
	session_start ();
	unset ( $_SESSION ['login'] );
	unset ( $_SESSION ['id'] );
	unset ( $_SESSION ['grupa'] );
	unset ( $_SESSION ['email'] );
					  
	$data = date ( 'Y-m-d H:i:s' );
	$uzytkownik = $_POST ['login'];
	$haslo = $_POST ['haslo'];
	
	$connect = mysql_connect ( $ADRES, $ADMIN, $ADMINPASS );
	$baza = mysql_select_db ( $BAZADANYCH );
	// ======================================================================
	// Zabezpieczenie SQL Injection
	// ======================================================================
	$uzytkownik=htmlentities($uzytkownik, ENT_QUOTES, "UTF-8");
	$haslo=htmlentities($haslo, ENT_QUOTES, "UTF-8");
	$zapytanie = sprintf("SELECT idUzytkownik, idGrupyUzytkownik, mail, potwierdzenie FROM uzytkownik WHERE login='%s' AND haslo=MD5('%s')", mysql_real_escape_string($uzytkownik),mysql_real_escape_string($haslo));
	
	$query = mysql_query ( $zapytanie );
	// ======================================================================
	// Sprawdzenie czy uzytkownik istnieje i czy jest dostepny
	// ======================================================================
	if (! mysql_num_rows ( $query )) {
		header ( "Location: index.php?lg=0" );
	} else if (mysql_result ( $query, 0, 'potwierdzenie' ) == 0)  {
		header ( "Location: index.php?lg=1" );
	} else if (mysql_result ( $query, 0, 'idGrupyUzytkownik' ) == 5) {
		header ( "Location: index.php?lg=2" );
	} 	
	// ======================================================================
	// Utworzenie nowej sesji zalogowanego uzytkownika
	// ======================================================================
	else {
		$_SESSION ['login'] = $uzytkownik;
		$_SESSION ['id'] = mysql_result ( $query, 0, 'idUzytkownik' );
		$_SESSION ['grupa'] = mysql_result ( $query, 0, 'idGrupyUzytkownik' );
		$_SESSION ['email'] = mysql_result ( $query, 0, 'mail' );
		$id_uz = $_SESSION ['id'];
		
		$query = mysql_query ( "UPDATE uzytkownik SET logowanie='$data', status_2='1' WHERE idUzytkownik='$id_uz'" );
		header ( "Location: index.php" );
	}
	mysql_close (); 
?>
