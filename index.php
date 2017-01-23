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

// prihlaseni superusera
if(isset($_GET["su"]))
{
  $obsah .= LideViews::prihlas_su(FileName);
  goto OUTPUT;
}
if(isset($_POST["heslo"]) AND isset($_POST["prihlasit_su"]))
{
	if(Util::login(21, $_POST["heslo"]))
	{
		if($logged=Util::is_logged())		
		{
			$obsah .= '<p><span class="info">Superuser přihlášen.</span></p>';
			goto OUTPUT;
		}	
	}
	$obsah .='<p><span class="infoerr">Přihlášení se nezdařilo, zkuste to znovu:</span></p>';
	$obsah .= LideViews::prihlas_su(FileName);
	goto OUTPUT;
}
// odhlaseni 
if(isset($_GET["logout"]))
{
	$logged=Util::is_logged();
	session_unset();
	if(Util::is_logged())
	{
		$obsah .= '<p><span class="infoerr">Odhlášení se nezdařilo.</span></p>';	
	}
	else
	{
		$obsah .= '<p><span class="info">Odhlášení proběhlo úspěšně.</span></p>';	
    $obsah .= LideViews::prihlas_formular($data->get_uzivatele_active(), 0, FileName);
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
			$obsah .= '<p><span class="info">Uživatel <b>'.$logged->get_jmeno().' '.$logged->get_prijmeni().'</b> byl úspěšně přihlášen.</span></p>';
			goto OUTPUT;
		}	
	}
	$obsah .='<p><span class="infoerr">Přihlášení se nezdařilo, zkuste to znovu:</span></p>';
	$obsah .= LideViews::prihlas_formular($data->get_uzivatele_active(), $_POST["uzivatel"], FileName);
	goto OUTPUT;
}

// prihlaseny uzivatel
if($logged=Util::is_logged())
{
	$obsah .='<p><span class="info">Aktuálně je přihlášen: <b>'.$logged->get_jmeno().' '.$logged->get_prijmeni().'</b></span></p>';
	goto OUTPUT;
}

// default
$obsah .= LideViews::prihlas_formular($data->get_uzivatele_active(), 0, FileName);

OUTPUT:
  echo Sablona::get_html(DirRoot, FileName, Title, $obsah);  

?>
