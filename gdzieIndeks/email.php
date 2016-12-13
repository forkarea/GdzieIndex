<?php
session_start ();
include_once 'header.php';
?>
<div id="body-bot">
	<h2><span><strong></strong> Wysyłanie wiadomosci</span></h2>
	<div id="items">
<?php

$connect = mysql_connect ( $ADRES, $ADMIN, $ADMINPASS );
$baza = mysql_select_db ( $BAZADANYCH );
if (isset ( $_SESSION ['login'] ))
	$login = $_SESSION ['login'];

if (count ( $_POST )) {
	// //////// USTAWIENIA //////////
	$email = $_POST ['Odbiorca']; // Adres e-mail adresata
	$subject = $_POST ['Temat']; // Temat listu
	$message = 'Formularz został wysłany'; // Komunikat
	$error = 'Wystąpił błąd podczas wysyłania formularza'; // Komunikat błędu
	$charset = 'UTF-8'; // Strona kodowa
	                    // ////////////////////////////
	
	$head = "MIME-Version: 1.0\r\n" . "Content-Type: text/plain; charset=$charset\r\n" . "Content-Transfer-Encoding: 8bit";
	$body = '';
	foreach ( $_POST as $name => $value ) {
		if (is_array ( $value )) {
			for($i = 0; $i < count ( $value ); $i ++) {
				$body .= "$name = " . (get_magic_quotes_gpc () ? stripslashes ( $value [$i] ) : $value [$i]) . "\r\n";
			}
		} else
			$body .= "$name = " . (get_magic_quotes_gpc () ? stripslashes ( $value ) : $value) . "\r\n";
	}
	echo mail ( $email, "=?$charset?B?" . base64_encode ( $subject ) . "?=", $body, $head ) ? $message : $error;
} else {
	
	$id_odbiorcy = $_GET ['id'];
	$pobierz_mail_odbiorcy = mysql_query ( "SELECT mail FROM uzytkownik WHERE idUzytkownik=$id_odbiorcy" );
	
	echo '<center><table border=0>
<form action="?" method="post">';
	if (isset ( $_SESSION ['id'] )) {
		$id_nadawcy = $_SESSION ['id'];
		$pobierz_mail_nadawcy = mysql_query ( "SELECT mail FROM uzytkownik WHERE idUzytkownik=$id_nadawcy" );
		echo '<tr><td>Nadawca: </td><td><input type="text" name="Nadawca" size="70" value="' . mysql_result ( $pobierz_mail_nadawcy, 0, "mail" ) . '"></tr></td>';
	} else
		echo '<tr><td></td><td>Podaj mail rejestracyjny na forum w celu weryfikacji:</td></tr>
 			<tr><td>Nadawca: </td><td><input type="text" name="Nadawca" size="70"></tr></td>';
		echo '<tr><td><input type="hidden" name="Odbiorca" value="' . mysql_result ( $pobierz_mail_odbiorcy, 0, "mail" ) . '" /></tr></td>
			<tr><td>Temat: </td><td><input type="text" size="70" name="Temat" /></td></tr>   
			<tr><td>Treść: </td><td><textarea rows="20" cols="54" name="Tresc"></textarea></td></tr>
			<tr><td></td><td align=center><input type="submit" value="Wyślij wiadomość użytkownikowi" /></tr></td>
		</form></table></center>';
}
mysql_close ();

		?>
</div>


<?php include_once 'footer.php'; ?>