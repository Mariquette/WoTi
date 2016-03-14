<?php

define("DirRoot", "./");
define("DirName", "");
define("FileName", "info.php");
define("LastChange", date("d. m. Y G:i", filemtime("info.php")));
define("Title", ""."info.php"."(".LastChange.")");      		  

include_once("_autoload.php");

session_start();  
Util::filtruj_vstup();
Util::check_token();   
  
$obsah = "";
$obsah .= '<div class="vysvetlivky">
	  <ul>
	    <li>zobrazí úvodní informace, aktuality</li>
	  </ul>
	</div>
	<br>
	Vyplňování v tomto systému od 2.4.2012 !
	<br>';
$obsah .= 'info<br>';
$obsah .= 'návod k aplikaci<br>';

echo Sablona::get_html(DirRoot, FileName, Title, $obsah);  

?>
