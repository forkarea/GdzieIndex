<?php

//======================================================================
// Skrypt zamykający sesję użytkownika
//======================================================================

session_start ();

include_once 'conn_cfg.php';

$connect = mysql_connect ( $ADRES, $ADMIN, $ADMINPASS );
$baza = mysql_select_db ( $BAZADANYCH );

$id = $_SESSION ['id'];
$query = mysql_query ( "UPDATE uzytkownik SET status_2='0' WHERE idUzytkownik='$id'" );
mysql_close ();
session_destroy ();
header ( "Location: index.php" );

?>