<?php

ini_set("log_errors", 1);
ini_set("error_log", "/home/daniela/woti/php-error.log");
error_log( "Hello, errors!" );

define("DirRoot", "./");
define("DirName", "");
define("FileName", "working-time.php");
define("LastChange", date("d. m. Y G:i", filemtime("working-time.php")));
define("Title", ""."working-time.php"."(".LastChange.")");      

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
$submenu->add_item(new SimpleLink("working time",FileName."?working-time"));
$submenu->add_item(new SimpleLink("|",""));
$submenu->add_item(new SimpleLink("nástěnka",FileName."?nastenka"));
$submenu->add_item(new SimpleLink("|",""));
$submenu->add_item(new SimpleLink("moje zakázky",FileName."?moje-zakazky"));
$submenu->add_item(new SimpleLink("|",""));
$submenu->add_item(new SimpleLink("Původní",FileName."?old"));

$data = new Database();
$mesic = new Mesic();

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

// ulozeni zmen v tabulce
if((isset($_POST["odeslano"]))AND(isset($_POST["mesic"]))AND(isset($_POST["rok"]))AND(isset($_POST["id"])))
{ 
  foreach($_POST as $key => $value) // $key = id zakazky, $value = pole
  {
    if(substr($key,0,2)=="x_")
    {
      foreach($value as $den => $hodiny)
      {
        if($hodiny != "") 
        {
          if(Util::is_valid_hodiny($hodiny))
          {
            $data->set_den(str_replace("x_","",$key),$_POST["id"],$_POST["rok"],$_POST["mesic"],$den,$hodiny);
          }
          else 
          {
            $obsah .= "Neplatné zadání hodin! (počet hodin <0,24>)<br>";
          } 
        } 
      }
    }
  }         
}

// generovani kalendare na nastenku    
if(isset($_GET["nastenka"])) 
{
  $submenu->set_active("nástěnka");
  $obsah .= 'kalendář na nástěnku<br>';
  $obsah .= 'Tlačítko -tisk kalendáře-<br>';
	goto OUTPUT;
}

// moje zakazky
if(isset($_GET["moje-zakazky"])) 
{
  $submenu->set_active("moje zakázky");
  $obsah .= 'seznam zakázek, kde jsem editorem<br>';
	goto OUTPUT;
}

// working time - vyplnovani
if(isset($_GET["old"])) 
{
  $submenu->set_active("Původní");
  $vypln = "";
  if(isset($_GET["vypln"]))
  {
    $vypln = "&vypln";
  }
  $obsah .= Views::nabidka($mesic,"working-time.php?old",$vypln);
  $uziv_data = ""; 
  foreach ($data->get_zakazky_active() as $id => $obj)
  {
    $pole = $data->get_dny($logged->get_id(),$obj->get_id(),$mesic->get_rok(),$mesic->get_mesic());
    $hodiny = array();
    if(is_array($pole)) 
    {
      //foreach ($pole as $key => $values)
      foreach ($pole as $id => $den)
      {
        $hodiny[$den->get_den()] = $den->get_hodiny();
      }
    }
    $uziv_data[] = array("id"=>$obj->get_id(), "jmeno"=>$obj->get_nazev(), "popis"=>$obj->get_popis(), "cinnost"=>$obj->get_cinnost(), "dny"=>$hodiny);   
  }
  if(isset($_GET["vypln"]))
  {
    $obsah .= Views::vypln_mesic($mesic,$uziv_data,$logged->get_id(),$logged->get_prijmeni()." ".$logged->get_jmeno(),$mesic->pocet_dnu(),'working-time.php?old');
  }
  else
  {  
    $obsah .= Views::zobraz_mesic($uziv_data,$logged->get_id(),$logged->get_prijmeni()." ".$logged->get_jmeno(),$mesic->pocet_dnu());
  }
  goto OUTPUT;
}

// default - seznam mesicu v tomto roce
$submenu->set_active("working time");
$obsah .= 'seznam měsíců<br>';

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
