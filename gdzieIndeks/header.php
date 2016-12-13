<?php 

//======================================================================
// Pobranie danych logowania do bazy
// Nagłówek forum z paskiem nawigacji
//======================================================================

include_once 'conn_cfg.php'; 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
		<title>Gdzie Indeks?</title>
		<meta name="Description" content="Aplikacja internetowa wpomagajaca poszukiwanie indeksow podczas zbierania wpisow" />
		<meta name="Author" content="Dawid Sobczyk & Paweł Szulc" />
		<meta name="robots" content="index,follow" />
		<meta name="Language" content="pl" />
		<meta http-equiv="content-type" content="text/html; charset=ISO 8859-2" />
		<link rel="stylesheet" type="text/css" href="style.css" />

	</head>
	
	<body>
	
		<div id="outer">
			<div id="wrapper">
				<div id="nav">
					<div id="nav-left">
						<div id="nav-right">
							<ul>
								<li><a href="index.php">HOME</a></li>
								<li><a href="onas.php">O SERWISIE</a></li>
								<li><a href="regulamin.php">POMOC I REGULAMIN</a></li>
								<?php
				
									//======================================================================
									// Link do Moje Konto i Panel Admin
									//======================================================================	
									
									if(!isset($_SESSION['login']))
									{
										echo '<li><a href="rejestracja.php">REJESTRACJA</a></li>';
									}
									else {
										//echo '<li><a href="index.php">SZUKAJ</a></li>';
										echo '<li><a href="users.php?sort=login">UŻYTKOWNICY</a></li>';
										if($_SESSION['grupa'] == 2){
											echo '<li><a href="panel_admin.php">PANEL ADMIN</a></li>';
										}
									}
									
								?>
							</ul>
						</div>
					</div>
					<div class="clear"></div>
				</div>
				
				<div id="head">
					<div id="head-left"></div>
					<div id="head-right"></div>
					<div id="head-1"></div>
					<h1><span class="logo"><span class="top">Gdzie jest</span><span class="gadgets">INDEKS?</span></span></h1>
					<?php
						if(isset($_SESSION['login']) && isset($_SESSION['id'])){
							echo '<div id="navb">
								  <ul>
										<li><a href="user.php?id='.$_SESSION["id"].'">MOJE KONTO</a></li>
										<li><a href="indeks.php">INDEKS</a></li>';
										if($_SESSION['grupa'] == 2 || $_SESSION['grupa'] == 4){
											echo '<li><a href="panel_admin.php">PANEL ADMIN</a></li>';
										}
							echo '</ul>
								</div>';
						}
					?>
				</div>

				
				<div id="login">
					<div id="login-bot">
						<div id="login-box">
							<?php

								//======================================================================
								// Formularz logowania i link wyloguj
								//======================================================================
							
								if(!isset($_SESSION['login'])){
								   echo "<h3 class='login'>Nie jesteś zalogowany.</h3>";
								   echo '<form action="login.php?lg=TRUE" method="POST">
											<div id="login-username">
												<div><label for="username">login</label>: <input type="text" name="login"/></div>
												<div><label for="password">password</label>: <input type="password" name="haslo"/></div>
											</div>
											<div id="login-button">
												<input type="image" src="images/btn_login.gif" name="loguj"/>
											</div>
											<div class="clear">
												<div class="reg">';
													if (isset ( $_GET ['lg'] )) {
														$lg = $_GET ['lg'];
														if ($lg==0) {
															echo "Błędny login lub hasło !<br/>";
														} else if ($lg==1)  {
															echo 'Twoje konto nie zostalo jeszcze aktywowane Skontaktuj się z <a href="email.php?id=1">Administratorem</a><br/><br/>';
														} else if ($lg==2) {
															echo 'Twoje konto zostało zablokowane. Skontaktuj się z <a href="email.php?id=1">Administratorem</a><br/><br/>';
														}
													}
									echo 			'<a href="odzyskiwanie.php" />Zapomniałem hasła</a><br/>
													Jesteś nowy? <a href="rejestracja.php">Zarejestruj się</a>
												</div>
											</div>
										</form>';
								}
								else {
									echo "<h3 class='login'><a href='wyloguj.php'> Wyloguj ".$_SESSION['login'].".</a></h3>";
								}
							
							?>
						</div>
						<div id="login-welcome">
							<div class="date">
								<?php

									//======================================================================
									// Data na stronie
									//======================================================================
									
									$dzien = date('d');
									$dzien_tyg = date('l');
									$miesiac = date('n');
									$rok = date('Y');

									$miesiac_pl = array(1 => 'stycznia', 'lutego', 'marca', 'kwietnia', 'maja', 'czerwca', 'lipca', 'sierpnia', 'września', 'października', 'listopada', 'grudnia');

									$dzien_tyg_pl = array('Monday' => 'Poniedziałek', 'Tuesday' => 'Wtorek', 'Wednesday' => 'Środa', 'Thursday' => 'Czwartek', 'Friday' => 'Piątek', 'Saturday' => 'Sobota', 'Sunday' => 'Niedziela');

									echo $dzien_tyg_pl[$dzien_tyg].", ".$dzien." ".$miesiac_pl[$miesiac]." ".$rok."r.";
									
									//======================================================================
									// Aktualne stany indeksów
									//======================================================================
									if(isset ($_SESSION ['id']) && isset ($_SESSION ['grupa'])){
										if($_SESSION ['grupa'] == 1){
											$connect = mysql_connect ( $ADRES, $ADMIN, $ADMINPASS );
											$baza = mysql_select_db ( $BAZADANYCH );
											$id_grupy = $_SESSION ['grupa'];
											$id_uzytkownik = $_SESSION ['id'];
											$indeks = mysql_query ("SELECT I.idIndeks, I.nrIndeks FROM indeks I WHERE I.idUzytkownik='$id_uzytkownik' && I.status_2=0");
											if(mysql_num_rows($indeks) != 0){
												echo "<br/><br/>";
												for($i = 0; $i < mysql_num_rows ( $indeks); $i ++) {
													$wybranyIndeks = mysql_result( $indeks, $i, 'idIndeks');
													$stanIndeks = mysql_query ("SELECT M.nazwaMiejsce, R.idUzytkownik, R.dataZwrotu FROM rejestratorzdarzenia R 
																				INNER JOIN miejsce M ON M.idMiejsce=R.idMiejsce 
																				WHERE R.idIndeks='$wybranyIndeks'
																				ORDER BY R.dataOtrzymania DESC LIMIT 1;");
													echo "Indeks nr " . mysql_result( $indeks, $i, 'nrIndeks') . ": "; 
													if (mysql_num_rows($stanIndeks) == 0)
														echo "Brak danych <br/>";
													else {
														$spr = mysql_result( $stanIndeks,  0, 'dataZwrotu');
														if (!empty($spr))
															echo "Odebrany <br/>";
														else 
															echo "<a href='user.php?id=".mysql_result($stanIndeks, 0,'idUzytkownik')."'>".mysql_result($stanIndeks,0,'nazwaMiejsce')."</a><br/>";	
													}													
												}
											}
											mysql_close ();
										}
									}
					
								?>
							</div>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div id="body">