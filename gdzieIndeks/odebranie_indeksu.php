<?php

//======================================================================
// Skrypt zamykający sesję użytkownika
//======================================================================

session_start ();

include_once 'conn_cfg.php';

if(isset ($_SESSION ['login']) && isset ($_GET ['nr'])){
	$connect = mysql_connect ( $ADRES, $ADMIN, $ADMINPASS );
	$baza = mysql_select_db ( $BAZADANYCH );
	$id = $_SESSION ['id'];
	$idIndeks = $_GET ['nr'];
	$data = date ( 'Y-m-d' );
	$query = mysql_query ( "SELECT nrIndeks FROM indeks WHERE idUzytkownik='$id' && idIndeks='$idIndeks'");
	if(mysql_num_rows($query) != 0) {
		$stanIndeks = mysql_query ("SELECT R.idRejestratorZdarzenia FROM rejestratorZdarzenia R 
									WHERE R.idIndeks='$idIndeks' ORDER BY R.dataOtrzymania DESC LIMIT 1;");
		if(mysql_num_rows($stanIndeks) !=0) {
			$idRejestrator = mysql_result ($stanIndeks,0,'idRejestratorZdarzenia');
			$query = mysql_query ("UPDATE rejestratorzdarzenia SET dataZwrotu = '$data' WHERE idRejestratorZdarzenia ='$idRejestrator';");
		}
	}
	mysql_close ();
}
header ( "Location: indeks.php" );
?>