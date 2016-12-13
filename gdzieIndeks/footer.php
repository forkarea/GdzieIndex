<?php

//======================================================================
// Stopka forum 
//======================================================================
	//======================================================================
	// Informacja o aktualnie aktywnych
	//======================================================================
?>
						<div id="banner">
							<div id="banner-text">
								<?php
									if(isset($_SESSION['login'])){
										$connect = mysql_connect($ADRES, $ADMIN, $ADMINPASS);
										$baza = mysql_select_db($BAZADANYCH);
										$queryAktywni = mysql_query("SELECT login, idUzytkownik  FROM uzytkownik WHERE status_2='1'");
										echo "<br>Aktualnie aktywni: ";
										for($i=0; $i<mysql_num_rows($queryAktywni);$i++)
											echo "<a href='user.php?id=".mysql_result($queryAktywni, $i,'idUzytkownik')."'>".mysql_result($queryAktywni,$i,'login')."&emsp;</a>"; 
										mysql_close(); 
									}
								?>
							</div>
							<div class="clear"></div>
						</div>
						<div id="footer">
							<div id="footloose"><span class="logo"><span class="top">Gdzie jest</span><span class="gadgets">INDEKS?</span></span></div>
							<p>Platforma "Gdzie jest Indeks?" zrealizowana w ramach projektu<br />Programowanie Aplikacji Internetowych 
								<br />&copy; Copyright 2016 Dawid Sobczyk &amp; Paweł Szulc<br />Politechnika Śląska w Gliwicach</p>
						</div>					
				</div>
			</div>
		</div>
	</div>

</body>
</html>

