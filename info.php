<?php

ini_set("log_errors", 1);
ini_set("error_log", "/home/daniela/woti/php-error.log");
error_log( "Hello, errors!" );

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
        
if(!($logged = Util::is_logged()))
{
	$obsah.='Pro zobrazení je nutné se <a href="./index.php">přihlásit</a>.';
	goto OUTPUT;
}

$submenu = new Menu("submenu");
$submenu->add_item(new SimpleLink("manuál",FileName."?manual"));
$submenu->add_item(new SimpleLink("|",""));
$submenu->add_item(new SimpleLink("směrnice",FileName."?smernice"));

// smernice
if(isset($_GET["smernice"])) 
{
  $submenu->set_active("směrnice");
  $obsah .= 'interní směrnice RITE';
  goto OUTPUT;
}

// default 
$submenu->set_active("manuál");
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

OUTPUT:
  if($logged = Util::is_logged())
  {
  	echo Sablona::get_html(DirRoot, FileName, Title, $submenu->get_html().$obsah);
  }
  else
  {
  	echo Sablona::get_html(DirRoot, FileName, Title, $obsah);  
  }

?>
