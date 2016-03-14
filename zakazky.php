<?php

ini_set("log_errors", 1);
ini_set("error_log", "/home/daniela/woti/php-error.log");

define("DirRoot", "./");
define("DirName", "");
define("FileName", "zakazky.php");
define("LastChange", date("d. m. Y G:i", filemtime("zakazky.php")));
define("Title", ""."zakazky.php"."(".LastChange.")");      

include_once("_autoload.php");

session_start();  
Util::filtruj_vstup();
Util::check_token();   

$data = new Database();
$mesic = new Mesic("zakazky.php");
$dny = new Dny();

$mesic->aktualizuj();

$obsah = "";

if($logged = Util::is_logged())
{
  $submenu = new Menu("submenu");
  $submenu->add_item(new SimpleLink("přehled zakázek",FileName."?seznam"));
  $submenu->add_item(new SimpleLink("|",""));
  $submenu->add_item(new SimpleLink("ukončené",FileName."?ukoncene"));
  $submenu->add_item(new SimpleLink("|",""));
  $submenu->add_item(new SimpleLink("seznam všech zakázek",FileName."?vse"));
  $submenu->add_item(new SimpleLink("|",""));
  $submenu->add_item(new SimpleLink("přidat",FileName."?pridat"));
  $submenu->add_item(new SimpleLink("|",""));
  $submenu->add_item(new SimpleLink("Původní",FileName."?old"));
 
  if(isset($_GET["ukoncene"])) 
  {
    $submenu->set_active("ukončené");
    $obsah .= ZakazkyViews::get_list($data->get_zakazky_inactive()); 
  }
  elseif(isset($_GET["vse"])) 
  {
    $submenu->set_active("seznam všech zakázek");
    $obsah .= ZakazkyViews::get_list($data->get_zakazky_all()); 
  }
  elseif(isset($_GET["pridat"])) 
  {
    $submenu->set_active("přidat");
    if(isset($_POST["add"]) AND isset($_POST["nazev"])) 
    {
	if($data->add_zakazka($_POST["nazev"]))
	{
		$obsah .= "Zakázka byla přidána.<br>";
		
		$obsah .= "Přidejte další zakázku:";
	}
	else
	{
		$obsah .="chyba pri vkladani zakazky!";	
	}

    }
    $obsah .= ZakazkyViews::add("zakazky.php?pridat");
  }
  elseif(isset($_GET["old"])) 
  {
  	$submenu->set_active("Původní");
    $obsah .= "<h2>Přehled zakázek</h2>";
    $obsah .= $mesic->nabidka_zakazky()."<br>";
    foreach($data->get_zakazky_active() as $id => $obj)
    {
      $zak_data = "";
      foreach ($data->get_uzivatele_active() as $uziv_id => $uziv_obj)
      {
        $zak_data[] = array("id"=>$uziv_obj->get_id(), "jmeno"=>$uziv_obj->get_prijmeni(), "popis"=>"", "cinnost"=>"", "dny"=>$dny->get_list($uziv_obj->get_id(),$obj->get_id(),$mesic->get_rok(),$mesic->get_mesic()));    //predelat
      }
      $obsah .= $mesic->zobraz_mesic($zak_data,$id,substr($obj->get_cinnost(),2).": ".$obj->get_nazev());
    }
  }
  else    
  {
    $submenu->set_active("přehled zakázek");
    $obsah .= 'Tlačítko -tisk přehledu-<br>';
    $obsah .= '<br>';
    $obsah .= ZakazkyViews::get_list($data->get_zakazky_active());
  }
  
  echo Sablona::get_html(DirRoot, FileName, Title, $submenu->get_html().$obsah);  
}
else
{
  //$err = $uzivatele->get_err();
  //$obsah .= $err["text"];
  echo Sablona::get_html(DirRoot, FileName, Title, $obsah);  
}  

?>
