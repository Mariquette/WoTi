<?php

ini_set("log_errors", 1);
ini_set("error_log", "/home/daniela/woti/php-error.log");
error_log( "Hello, errors!" );

define("DirRoot", "./");
define("DirName", "");
define("FileName", "lide.php");
define("LastChange", date("d. m. Y G:i", filemtime("lide.php")));
define("Title", ""."lide.php"."(".LastChange.")");      

include_once("_autoload.php");

session_start();  
Util::filtruj_vstup();
Util::check_token();   

$data = new Database();
$mesic = new Mesic("lide.php");
$dny = new Dny();

$obsah = "";
        
if(!($logged = Util::is_logged()))
{
	$obsah.='Pro zobrazení je nutné se <a href="./index.php">přihlásit</a>.';
	goto OUTPUT;
}

$submenu = new Menu("submenu");
$submenu->add_item(new SimpleLink("seznam",FileName."?seznam"));
$submenu->add_item(new SimpleLink("|",""));
$submenu->add_item(new SimpleLink("přidat",FileName."?pridat"));
$submenu->add_item(new SimpleLink("|",""));
$submenu->add_item(new SimpleLink("změna hesla",FileName."?edit"));
$submenu->add_item(new SimpleLink("|",""));
$submenu->add_item(new SimpleLink("Původní",FileName."?old"));
 
  if(isset($_GET["pridat"])) 
  {
    $submenu->set_active("přidat");
    $obsah .= 'formulář pro přidání nového člověka<br>';
//    $obsah .= 
    goto OUTPUT;
  }
  
  if(isset($_GET["edit"])) 
  {
  	$submenu->set_active("změna hesla");
        $obsah .= LideViews::zmena_hesla_formular($logged, FileName);		
	goto OUTPUT;
  }


// zmena hesla
if(isset($_POST["uzivatel"]) AND isset($_POST["heslo"]) AND isset($_POST["nove_heslo"]) AND isset($_POST["nove_heslo2"]) AND isset($_POST["zmena_hesla"]) AND isset($_POST["token"]))
{
	$submenu->set_active("změna hesla");

	$err=Util::ch_passwd($_POST["uzivatel"], $_POST["heslo"], $_POST["nove_heslo"], $_POST["nove_heslo2"]);
	if($err!="")
	{
		$obsah .= "<p> $err </p>";
		$obsah .= LideViews::zmena_hesla_formular($logged, FileName);		
		goto OUTPUT;
	}

	$obsah .="<p>zmena hesla uspesne provedena</p>";
	goto OUTPUT;
}


// old
  if(isset($_GET["old"]))
  {
    $submenu->set_active("Původní");
  	$obsah .= "<br>PŘEHLED PO ZAMĚSTNANCÍCH:<br>";   
    $obsah .= $mesic->nabidka_zakazky()."<br>";
    foreach($data->get_uzivatele_active() as $uziv_id => $uziv_obj)
    {
      $uziv_data = "";
      foreach ($data->get_zakazky_active() as $id => $obj)
      {
        $uziv_data[] = array("id"=>$id, "jmeno"=>$obj->get_nazev(), "popis"=>$obj->get_popis(), "cinnost"=>$obj->get_cinnost(), "dny"=>$dny->get_list($uziv_obj->get_id(),$obj->get_id(),$mesic->get_rok(),$mesic->get_mesic()));   
      }
      $obsah .= $mesic->zobraz_mesic($uziv_data,$uziv_id,$uziv_obj->get_prijmeni().$uziv_obj->get_jmeno());
      $uziv_data = "";
    }
	goto OUTPUT;   
  }


// konec

$submenu->set_active("seznam");
$obsah .= LideViews::get_list($data->get_uzivatele_all()); 

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
