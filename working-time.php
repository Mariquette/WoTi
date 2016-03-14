<?php

define("DirRoot", "./");
define("DirName", "");
define("FileName", "working-time.php");
define("LastChange", date("d. m. Y G:i", filemtime("working-time.php")));
define("Title", ""."working-time.php"."(".LastChange.")");      

include_once("_autoload.php");

session_start();  
Util::filtruj_vstup();
Util::check_token();   

$data = new Database();
$mesic = new Mesic("working-time.php");
$dny = new Dny();

$mesic->aktualizuj();
$err = $dny->set_dny_by_uzivatel($mesic->get_dny());

$obsah = "";

if($logged = Util::is_logged())
{
  $submenu = new Menu("submenu");
  $submenu->add_item(new SimpleLink("working time",FileName."?working-time"));
  $submenu->add_item(new SimpleLink("|",""));
  $submenu->add_item(new SimpleLink("nástěnka",FileName."?nastenka"));
  $submenu->add_item(new SimpleLink("|",""));
  $submenu->add_item(new SimpleLink("moje zakázky",FileName."?moje-zakazky"));
  $submenu->add_item(new SimpleLink("|",""));
  $submenu->add_item(new SimpleLink("Původní",FileName."?old"));
 
  if(isset($_GET["nastenka"])) 
  {
    $submenu->set_active("nástěnka");
    $obsah .= 'kalendář na nástěnku<br>';
    $obsah .= 'Tlačítko -tisk kalendáře-<br>';
  }
  elseif(isset($_GET["moje-zakazky"])) 
  {
    $submenu->set_active("moje zakázky");
    $obsah .= 'seznam zakázek, kde jsem editorem<br>';
  }
  elseif(isset($_GET["old"])) 
  {
    $submenu->set_active("Původní");
    $obsah .= $mesic->nabidka(); 
    if($err != "")
    {
      $obsah .=  "<div class=\"err\">".$err."</div>";
      $_GET["vypln"] = "";
    }
    $uziv_data = "";
    foreach ($data->get_zakazky_active() as $id => $obj)
    {
      $uziv_data[] = array("id"=>$id, "jmeno"=>$obj->get_nazev(), "popis"=>$obj->get_popis(), "cinnost"=>$obj->get_cinnost(), "dny"=>$dny->get_list($logged["id"],$obj->get_id(),$mesic->get_rok(),$mesic->get_mesic()));   
    }
    $obsah .= $mesic->view($uziv_data,$logged["id"],$logged["prijmeni"]." ".$logged["jmeno"]);
  }
  else
  {
    $submenu->set_active("working time");
    $obsah .= 'seznam měsíců<br>';
  }
  
  echo Sablona::get_html(DirRoot, FileName, Title, $submenu->get_html().$obsah);
}
else
{
  //$err = $uzivatele->get_err();
  $obsah .= $err["text"];
  echo Sablona::get_html(DirRoot, FileName, Title, $obsah);  
}

?>
