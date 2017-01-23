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
	$obsah.='<p><span class="infoerr">Pro zobrazení je nutné se <a href="./index.php">přihlásit</a>.</span><p>';
	goto OUTPUT;
}

// autentifikace
$role = $logged->get_role();

$data = new Database();
$mesic = new Mesic();

$submenu = new Menu("submenu");
$submenu->add_item(new SimpleLink("přehled zakázek",FileName."?seznam"));
$submenu->add_item(new SimpleLink("|",""));
$submenu->add_item(new SimpleLink("v záruce",FileName."?zaruka"));
$submenu->add_item(new SimpleLink("|",""));
$submenu->add_item(new SimpleLink("ukončené",FileName."?ukoncene"));
$submenu->add_item(new SimpleLink("|",""));
$submenu->add_item(new SimpleLink("seznam všech zakázek",FileName."?vse"));
if((Util::is_admin($role)))
{
  $submenu->add_item(new SimpleLink("|",""));
  $submenu->add_item(new SimpleLink("přidat",FileName."?pridat"));
}

// aktualizace mesice
if(isset($_GET["rok"])) $mesic->set_rok($_GET["rok"]);
if(isset($_GET["mesic"])) $mesic->set_mesic($_GET["mesic"]);
if(isset($_POST["rok"])) $mesic->set_rok($_POST["rok"]);
if(isset($_POST["mesic"])) $mesic->set_mesic($_POST["mesic"]);

//razeni
$order = "kategorie";
if(isset($_GET["order"])) $order = $_GET["order"];

// detail zakazky
if(isset($_GET["detail"])) 
{
  if(isset($_GET["plus"]) AND isset($_GET["opravneni"]))
  {
    $obsah .= ZakazkyViews::detail($data->get_zakazka($_GET["detail"]),"zakazky.php");
    $obsah .= ZakazkyViews::add_opravneni($data->get_uzivatele_active(),$_GET["detail"],$_GET["opravneni"],"zakazky.php");
    goto OUTPUT;
  }
  if(isset($_GET["minus"]) AND isset($_GET["opravneni"]))
  {
    if($data->remove_opravneni($_GET["detail"],$_GET["minus"],$_GET["opravneni"])) 
      $obsah .= '<p><span class="info">Uživatel byl odebrán.</span></p>';
    else $obsah .= '<p><span class="infoerr">Odebrání uživatele se nezdařilo.</span></p>';    	
  }
  $obsah .= ZakazkyViews::detail($data->get_zakazka($_GET["detail"]),"zakazky.php",$logged->is_editor($_GET["detail"]));
  $obsah .= ZakazkyViews::detail_lide($data->get_zakazka($_GET["detail"]),$data->get_uzivatele_4zakazka($_GET["detail"],Opravneni::PRACOVNIK),$data->get_uzivatele_4zakazka($_GET["detail"],Opravneni::EDITOR),$data->get_uzivatele_4zakazka($_GET["detail"],Opravneni::ODPOVEDNY),"zakazky.php",$logged->is_editor($_GET["detail"]));
  goto OUTPUT;
}

// pridani cloveka k zakazce
if(isset($_POST["plus"]) AND isset($_POST["uzivatel"]) AND isset($_POST["zakazka"]) AND isset($_POST["opravneni"]))
{
  if($logged->is_editor($_POST["zakazka"]) == false)
  { 
    $obsah .= LideViews::admin_only(); 
    goto OUTPUT;  
  }
$opravneni = new Opravneni(array("id"=>NULL,"uzivatele_id"=>$_POST["uzivatel"],"zakazky_id"=>$_POST["zakazka"],"opravneni"=>$_POST["opravneni"]));
  if($autoid = $data->add_opravneni($opravneni)) 
    $obsah .= '<p><span class="info">Uživatel byl přidán.</span></p>';    	
  else $obsah .= '<p><span class="infoerr">Při vytváření nového záznamu došlo k chybě!</span></p>';	
  $obsah .= ZakazkyViews::detail($data->get_zakazka($_POST["zakazka"]),"zakazky.php",$logged->is_editor($_POST["zakazka"]));
  $obsah .= ZakazkyViews::detail_lide($data->get_zakazka($_POST["zakazka"]),$data->get_uzivatele_4zakazka($_POST["zakazka"],Opravneni::PRACOVNIK),$data->get_uzivatele_4zakazka($_POST["zakazka"],Opravneni::EDITOR),$data->get_uzivatele_4zakazka($_POST["zakazka"],Opravneni::ODPOVEDNY),"zakazky.php",$logged->is_editor($_POST["zakazka"]));
  goto OUTPUT;
}

// editacni formular
if(isset($_GET["edit"])) 
{
  if($logged->is_editor($_GET["edit"]) == false)
  { 
    $obsah .= LideViews::admin_only(); 
    goto OUTPUT;  
  }
$obsah .= ZakazkyViews::edit($data->get_zakazka($_GET["edit"]),"zakazky.php");
  goto OUTPUT;
}

// zmena zakazky
//if(isset($_POST["edit"])) 
if(isset($_POST["edit"]) AND isset($_POST["id"]) AND isset($_POST["nazev"]) AND isset($_POST["popis"]) AND isset($_POST["obdobi"]) AND isset($_POST["kategorie"]) AND isset($_POST["stav"])) 
{
  $zakazka = new Zakazka(array("id"=>$_POST["id"],"nazev"=>$_POST["nazev"],"popis"=>$_POST["popis"],"obdobi"=>$_POST["obdobi"],"kategorie"=>$_POST["kategorie"],"stav"=>$_POST["stav"]));
  if($zakazka->is_valid())
  {
    if($logged->is_editor($zakazka->get_id()) == false)
    { 
      $obsah .= LideViews::admin_only(); 
      goto OUTPUT;  
    }
    if($data->edit_zakazka($zakazka)) $obsah .= "Změny byly uloženy.<br>";    	
    else $obsah .='<p><span class="infoerr">Při ukládání změn došlo k chybě!</span></p>';	
    $obsah .= ZakazkyViews::detail($data->get_zakazka($_POST["id"]),"zakazky.php",$logged->is_editor($zakazka->get_id()));
    $obsah .= ZakazkyViews::detail_lide($data->get_zakazka($_POST["id"]),$data->get_uzivatele_4zakazka($_POST["id"],Opravneni::PRACOVNIK),$data->get_uzivatele_4zakazka($_POST["id"],Opravneni::EDITOR),$data->get_uzivatele_4zakazka($_POST["id"],Opravneni::ODPOVEDNY),"zakazky.php",$logged->is_editor($zakazka->get_id()));
  }
  else $obsah .= ZakazkyViews::edit($zakazka,"zakazky.php");
  goto OUTPUT;
}

// seznam ukoncenych zakazek  
if(isset($_GET["ukoncene"])) 
{
  $submenu->set_active("ukončené");
  $obsah .= ZakazkyViews::get_list($data->get_zakazky_inactive($order),"zakazky.php?ukoncene"); 
	goto OUTPUT;
}

// seznam zakazek v zaruce 
if(isset($_GET["zaruka"])) 
{
  if(isset($_GET["tisk"]))
  {
    Outputs::zakazky2pdf($data->get_zakazky_zaruka($order));
    goto OUTPUT;
  }  
  $submenu->set_active("v záruce");
  $obsah .= '<div><a class="link" href="zakazky.php?tisk&zaruka" target="_blank">Vytisknout přehled zakázek v záruce</a></div>';
  $obsah .= ZakazkyViews::get_list($data->get_zakazky_zaruka($order),"zakazky.php?zaruka"); 
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
  if(Util::is_admin($role) == false)
  { 
    $obsah .= LideViews::admin_only(); 
    goto OUTPUT;  
  }

  $submenu->set_active("přidat");
  if(isset($_POST["add"]) AND isset($_POST["nazev"]) AND isset($_POST["popis"]) AND isset($_POST["obdobi"]) AND isset($_POST["kategorie"]) AND isset($_POST["stav"])) 
  {
    $zakazka = new Zakazka(array("id"=>"NULL","nazev"=>$_POST["nazev"],"popis"=>$_POST["popis"],"obdobi"=>$_POST["obdobi"],"kategorie"=>$_POST["kategorie"],"stav"=>$_POST["stav"]));
    if($zakazka->is_valid())
    {
      if($autoid = $data->add_zakazka($zakazka))
      {	
      	$obsah .= '<p><span class="info">Zakázka byla přidána.</span></p>';    	
      	$obsah .= ZakazkyViews::detail($data->get_zakazka($autoid),"zakazky.php",Util::is_admin($role));
        $obsah .= ZakazkyViews::detail_lide($data->get_zakazka($autoid),$data->get_uzivatele_4zakazka($autoid,Opravneni::PRACOVNIK),$data->get_uzivatele_4zakazka($autoid,Opravneni::EDITOR),$data->get_uzivatele_4zakazka($autoid,Opravneni::ODPOVEDNY),"zakazky.php",Util::is_admin($role));
	    }
      else $obsah .= '<p><span class="infoerr">Při vytváření nového záznamu došlo k chybě!</span></p>';	
    }
    else $obsah .= ZakazkyViews::add($zakazka,"zakazky.php?pridat");
  }
  else $obsah .= ZakazkyViews::add(new Zakazka(),"zakazky.php?pridat");
	goto OUTPUT;
}

// tisk prehledu zakazek
if(isset($_GET["tisk"]))
{
  Outputs::zakazky2pdf($data->get_zakazky_active($order));
  goto OUTPUT;
}  

// default - seznam aktivnich zakazek
$submenu->set_active("přehled zakázek");
$obsah .= '<div><a class="link" href="zakazky.php?tisk" target="_blank">Vytisknout přehled aktivních zakázek</a></div>';
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
