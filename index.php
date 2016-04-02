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
		$obsah .= "<p>Odhlášení se nezdařilo.</p>";	
	}
	else
	{
		$obsah .= "<p>Odhlášení proběhlo úspěšně.</p>";	
	}
	goto OUTPUT;
}
   
// prihlaseni uzivatele
if(isset($_POST["uzivatel"]) AND isset($_POST["heslo"]) AND isset($_POST["prihlasit"]))
{
	if(Util::login($_POST["uzivatel"], $_POST["heslo"]))
	{
		if($logged=Util::is_logged())		
		{
			$obsah .= "<p>Uživatel <b>".$logged->get_prijmeni()." ".$logged->get_jmeno()."</b> byl úspěšně přihlášen.</p>";
			goto OUTPUT;
		}	
	}
	$obsah .="<p>Přihlášení se nezdařilo, zkuste to znovu:</p>";
	$obsah .= LideViews::prihlas_formular($data->get_uzivatele_active(), $_POST["uzivatel"], FileName);
	goto OUTPUT;
}

// prihlaseny uzivatel
if($logged=Util::is_logged())
{
	$obsah .="<p>Aktuálně je přihlášen: ".$logged->get_prijmeni()." ".$logged->get_jmeno()."</p>";
	goto OUTPUT;
}

// konec
$obsah .= LideViews::prihlas_formular($data->get_uzivatele_active(), "", FileName);

OUTPUT:
  echo Sablona::get_html(DirRoot, FileName, Title, $obsah);  

?>
