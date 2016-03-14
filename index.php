<?php

ini_set("log_errors", 1);
ini_set("error_log", "/home/daniela/woti/php-error.log");
error_log( "Hello, errors!" );

define("DirRoot", "./");
define("DirName", "");
define("FileName", "index.php");
define("LastChange", date("d. m. Y G:i", filemtime("index.php")));
define("Title", ""."index.php"."(".LastChange.")");      		  

include_once("_autoload.php");

session_start();  
Util::filtruj_vstup();
Util::check_token();   

$data = new Database();

$obsah = "";

// odhlaseni 
if(isset($_GET["logout"]))
{
	$logged=Util::is_logged();
	session_unset();
	if(Util::is_logged())
	{
		$obsah .="odhlaseni se nezdarilo";	
	}
	else
	{
		$obsah .=$logged->get_prijmeni()." ".$logged->get_jmeno()." uspesne odhlasen";	
	}
	goto OUTPUT;
}
   
//prihlaseni uzivatele
if(isset($_POST["uzivatel"]) AND isset($_POST["heslo"]) AND isset($_POST["prihlasit"]))
{
	if(Util::login($_POST["uzivatel"], $_POST["heslo"]))
	{
		if($logged=Util::is_logged())		
		{
			//$obsah .= "<h2>Uživatel <b>".$logged["prijmeni"]." ".$logged["jmeno"]."</b> úspěšně přihlášen.</h2>";
			$obsah .= "<h2>Uživatel <b>".$logged->get_prijmeni()." ".$logged->get_jmeno()."</b> úspěšně přihlášen.</h2>";
			goto OUTPUT;
		}	
	}
	$obsah .="<p>prihlaseni se nezdarilo, zkuste to znovu:</p>";
	$obsah .= LideViews::prihlas_formular($data->get_uzivatele_active(), $_POST["uzivatel"], FileName);
	goto OUTPUT;
}

// prihlaseny uzivatel
if($logged=Util::is_logged())
{
	$obsah .="<p>aktualne prihlasen: ".$logged->get_prijmeni()." ".$logged->get_jmeno()."</p>";
	goto OUTPUT;
}

// konec

    $obsah .= LideViews::prihlas_formular($data->get_uzivatele_active(), "", FileName);

  OUTPUT:

    echo Sablona::get_html(DirRoot, FileName, Title, $obsah);  

?>
