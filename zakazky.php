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

$obsah = "";

if(!($logged = Util::is_logged()))
{
	$obsah.='Pro zobrazení je nutné se <a href="./index.php">přihlásit</a>.';
	goto OUTPUT;
}

$data = new Database();
$mesic = new Mesic();

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

// aktualizace mesice
if(isset($_GET["rok"]))
{
  $mesic->set_rok($_GET["rok"]);
}
if(isset($_GET["mesic"]))
{
  $mesic->set_mesic($_GET["mesic"]);
}
if(isset($_POST["rok"]))
{
  $mesic->set_rok($_POST["rok"]);
}
if(isset($_POST["mesic"]))
{
  $mesic->set_mesic($_POST["mesic"]);
}

//razeni
$order = "cinnost";
if(isset($_GET["order"]))
{	
  //if($_GET["order"]=="model") $order = Computer::get_model_index();
  $order = $_GET["order"];
}

// detail zakazky
if(isset($_GET["detail"])) 
{
  $obsah .= ZakazkyViews::detail($data->get_zakazka($_GET["detail"]),"zakazky.php");
  goto OUTPUT;
}

// editacni formular
if(isset($_GET["edit"])) 
{
  $obsah .= ZakazkyViews::edit($data->get_zakazka($_GET["edit"]),"zakazky.php");
  goto OUTPUT;
}

// zmena zakazky
if(isset($_POST["edit"])) 
{
  if($data->edit_zakazka($_POST["id"],$_POST["nazev"],$_POST["popis"],$_POST["cinnost"],$_POST["stav"]))
  {
  	$obsah .= "Změny byly uloženy.<br>";    	
  }
  else
  {
  	$obsah .="Při ukládání změn došlo k chybě!";	
  }
  $obsah .= ZakazkyViews::detail($data->get_zakazka($_POST["id"]),"zakazky.php");
  goto OUTPUT;
}

// seznam ukoncenych zakazek  
if(isset($_GET["ukoncene"])) 
{
  $submenu->set_active("ukončené");
  $obsah .= ZakazkyViews::get_list($data->get_zakazky_inactive($order),"zakazky.php?ukoncene"); 
	goto OUTPUT;
}

// seznam vsech zakazek
if(isset($_GET["vse"])) 
{
  $submenu->set_active("seznam všech zakázek");
  $obsah .= ZakazkyViews::get_list($data->get_zakazky_all($order),"zakazky.php?vse"); 
	goto OUTPUT;
}

// pridani nove zakazky
if(isset($_GET["pridat"])) 
{
  $submenu->set_active("přidat");
  if(isset($_POST["add"]) AND isset($_POST["nazev"]) AND isset($_POST["popis"]) AND isset($_POST["cinnost"])) 
  {
    if($data->add_zakazka($_POST["nazev"],$_POST["popis"],$_POST["cinnost"]))
    {
    	$obsah .= "Zakázka byla přidána.<br>";    	
    	$obsah .= "Přidejte další zakázku:";
    }
    else
    {
    	$obsah .="Při vytváření nového záznamu došlo k chybě!";	
    }
  }
  $obsah .= ZakazkyViews::add("zakazky.php?pridat");
	goto OUTPUT;
}

// working time vsech lidi po zakazkach
if(isset($_GET["old"])) 
{
	$submenu->set_active("Původní");
  $obsah .= "<h2>Přehled zakázek</h2>";
  $obsah .= Views::nabidka2($mesic,"zakazky.php?old");
  foreach($data->get_zakazky_active() as $id => $obj)
  {
    $zak_data = "";
    foreach ($data->get_uzivatele_active() as $uziv_id => $uziv_obj)
    {
      $pole = $data->get_dny($uziv_obj->get_id(),$obj->get_id(),$mesic->get_rok(),$mesic->get_mesic());
      $hodiny = array();
      if(is_array($pole)) 
      {
        foreach ($pole as $id => $den)
        {
          $hodiny[$den->get_den()] = $den->get_hodiny();
        }
      }
      $zak_data[] = array("id"=>$uziv_obj->get_id(), "jmeno"=>$uziv_obj->get_prijmeni(), "popis"=>"", "cinnost"=>"", "dny"=>$hodiny);    
    }
    $obsah .= Views::zobraz_mesic($zak_data,$id,substr($obj->get_cinnost(),2).": ".$obj->get_nazev(),$mesic->pocet_dnu());
  }
	goto OUTPUT;
}

// default - seznam aktivnich zakazek
$submenu->set_active("přehled zakázek");
$obsah .= 'Tlačítko -tisk přehledu-<br>';
$obsah .= '<br>';
$obsah .= ZakazkyViews::get_list($data->get_zakazky_active($order),"zakazky.php?seznam");
  
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
