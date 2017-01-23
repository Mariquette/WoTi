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
	$obsah.='<p><span class="infoerr">Pro zobrazení je nutné se <a href="./index.php">přihlásit</a>.</span><p>';
	goto OUTPUT;
}

// autentifikace
$role = $logged->get_role();

$submenu = new Menu("submenu");
$submenu->add_item(new SimpleLink("working time",FileName."?working-time"));
$submenu->add_item(new SimpleLink("|",""));
$submenu->add_item(new SimpleLink("kalendář",FileName."?kalendar"));
if($logged->is_editor())
{
	$submenu->add_item(new SimpleLink("|",""));
	$submenu->add_item(new SimpleLink("moje zakázky",FileName."?moje-zakazky"));
}
if((Util::is_admin($role)))
{
	$submenu->add_item(new SimpleLink("|",""));
	$submenu->add_item(new SimpleLink("kontrola",FileName."?kontrola"));
}

$data = new Database();
$mesic = new Mesic();

// aktualizace mesice
if(isset($_GET["rok"])) $mesic->set_rok($_GET["rok"]);
if(isset($_GET["mesic"])) $mesic->set_mesic($_GET["mesic"]);
if(isset($_POST["rok"])) $mesic->set_rok($_POST["rok"]);
if(isset($_POST["mesic"])) $mesic->set_mesic($_POST["mesic"]);
$mesic->set_uzivatel($logged->get_id());
$mesic->set_stav(Mesic::OTEVRENO);


// ulozeni zmen v tabulce working time
if((isset($_POST["ulozit"]))AND(isset($_POST["mesic"]))AND(isset($_POST["rok"]))AND(isset($_POST["uzivatel"])))
{ 
  $chyby = 0;
  foreach($_POST as $key => $value) // $key = id zakazky, $value = pole
  {
    if(substr($key,0,2)=="x_")
    {
      foreach($value as $den => $hodiny)
      {
        if($hodiny != "") 
        {
          $zaznam = new Den(array("id"=>"NULL","zakazky_id"=>str_replace("x_","",$key),"uzivatele_id"=>$_POST["uzivatel"],"rok"=>$_POST["rok"],"mesic"=>$_POST["mesic"],"den"=>$den,"hodiny"=>$hodiny));
          if($zaznam->is_valid()) $data->set_den($zaznam);
          else 
          {
            $chyby++;
            $obsah .= '<p><span class="infoerr">'.$zaznam->get_den().'/'.$zaznam->get_mesic().'/'.$zaznam->get_rok().': '.$zaznam->get_hodiny_err().'</span><p>';
          }
        } 
      }
    }
  }
  $hodiny = array();
  $_zaka = $data->get_zakazky_4uzivatel_a_mesic($logged->get_id(),Opravneni::PRACOVNIK,$mesic);
  //foreach ($data->get_zakazky_4uzivatel($logged->get_id(),Opravneni::PRACOVNIK) as $obj)
  foreach ($_zaka as $obj)
  {
    $pole = $data->get_dny($logged->get_id(),$obj->get_id(),$mesic->get_rok(),$mesic->get_mesic());
    foreach ($pole as $den)
    {
      $hodiny[$den->get_den()][$obj->get_id()] = $den->get_hodiny();
    }
  }
  if($chyby==0) $obsah .= Views::zobraz_mesic_4uzivatel($mesic,NULL,$_zaka,$hodiny,"working-time.php?working-time",true);
  else $obsah .= Views::vypln_mesic($mesic,$logged,$data->get_zakazky_4uzivatel($logged->get_id(),Opravneni::PRACOVNIK),$hodiny,"working-time.php?working-time");
  goto OUTPUT;         
}

// kalendar    
if(isset($_GET["kalendar"])) 
{
  $submenu->set_active("kalendář");
  $obsah .= '<div><a class="link" href="working-time.php?mesic='.$mesic->get_mesic().'&rok='.$mesic->get_rok().'&tisk" target="_blank">Vytisknout kalendář na nástěnku</a></div>';
  $obsah .= Views::kalendar($mesic,"working-time.php?kalendar");
  goto OUTPUT;
}

// tisk kalendare
if((isset($_GET["rok"]))AND(isset($_GET["mesic"]))AND(isset($_GET["tisk"])))
{
  Outputs::kalendar2pdf($mesic,$data->get_uzivatele_active());
  goto OUTPUT;
}  

// moje zakazky
if(isset($_GET["moje-zakazky"])) 
{
  if(!$logged->is_editor())
  { 
    $obsah .= LideViews::admin_only(); 
    goto OUTPUT;  
  }
  $submenu->set_active("moje zakázky");
  $obsah .= Views::nabidka_mesic($mesic,"working-time.php?moje-zakazky");
  $_zakazky = Util::is_admin($role) ? $data->get_zakazky_active() : $data->get_zakazky_4uzivatel($logged->get_id(),Opravneni::EDITOR);
  foreach ($_zakazky as $zak)
  {
    $hodiny = array();
 		$_uzivatele = $data->get_uzivatele_4zakazka_a_mesic($zak->get_id(), Opravneni::PRACOVNIK, $mesic);
    foreach ($_uzivatele as $obj)
    {
      $pole = $data->get_dny($obj->get_id(),$zak->get_id(),$mesic->get_rok(),$mesic->get_mesic());
      foreach ($pole as $den)
      {
        $hodiny[$den->get_den()][$obj->get_id()] = $den->get_hodiny();
      }
    }
    $obsah .= Views::zobraz_mesic_4zakazka($mesic,$zak,$_uzivatele,$hodiny,$data->celkem_hodiny_4zakazka($zak->get_id()),"working-time.php?moje-zakazky");
  }
  goto OUTPUT;
}

// kontrola
if(isset($_GET["kontrola"])) 
{
  if(!Util::is_admin($role))
  { 
    $obsah .= LideViews::admin_only(); 
    goto OUTPUT;  
  }
  $submenu->set_active("kontrola");
  /*
  //----------------------------------------------------------------------------------------------
  // export do csv
  $soubor = "./uzaverky/uzaverka_".$mesic->get_rok()."-".$mesic->get_mesic().".csv";   
  //if (!File_Exists($soubor))    
  $text = "";
  if(isset($_GET["uzaverka"]))
  {
    foreach ($data->get_zakazky_active() as $id => $obj)
    {
      $zak_data = "";
      foreach($data->get_uzivatele_active() as $uziv_id => $uziv_obj)
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
        $zak_data[] = array("id"=>$uziv_obj->get_id(), "jmeno"=>$uziv_obj->get_prijmeni(), "popis"=>"", "kategorie"=>"", "dny"=>$hodiny);
      }
      $text .= Outputs::mesic2csv($zak_data,$obj->get_nazev(),$obj->get_kategorie(),$mesic->pocet_dnu());  
      $zak_data = "";     
    }
    if($soubor_link = fopen("$soubor","w"))
    {
      if(fwrite($soubor_link,$text)) $obsah .= '<p><span class="info">Soubor '.$soubor.' byl úspěšně vytvořen.</span></p>';
      else $obsah .= '<p><span class="infoerr">Zápis do souboru se nezdařil.</span></p>';
      fclose($soubor_link);
    }
    else $obsah .= '<p><span class="infoerr">Soubor se nepodařilo vytvořit.</span></p>';
  }
  else $obsah .= '<p><a href="lide.php?old&uzaverka&rok='.$mesic->get_rok().'&mesic='.$mesic->get_mesic().'">Exportuj</a></p>';
  if (File_Exists($soubor))
  {
    $obsah .= '<p><span class="info">Soubor ke stažení: <a href="'.$soubor.'">'.$soubor.'</a> (vytvořeno '.date("d. m. Y, G:i", filemtime($soubor)).')</p>';    
  } 
  //----------------------------------------------------------------------------------------------
  */
  
  if ((isset($_GET["rok"]))AND(isset($_GET["mesic"]))AND(isset($_GET["uzivatel"])))
  {
    //zamceni mesice
    if(isset($_GET["zamknout"])) 
    {
      if(isset($_POST["ok"]))
      {
        if($mesic_tmp = $data->get_mesic($_GET["rok"],$_GET["mesic"],$_GET["uzivatel"])) //zaznam existuje
        {
          $mesic = $mesic_tmp;
          $mesic->set_stav(Mesic::ZAMCENO);
          $data->edit_mesic($mesic);
        }
        else //zaznam neexistuje - nemuze nastat
        {
          $mesic->set_uzivatel($logged->get_id());
          $mesic->set_stav(Mesic::ZAMCENO);
          $data->add_mesic($mesic);
        }
      }
      elseif(!isset($_POST["storno"]))
      {
        $obsah .= Views::potvrzeni_zamceni($data->get_uzivatel($_GET["uzivatel"]),$mesic,"working-time.php?kontrola");
        goto OUTPUT;
      }
    }
    //otevreni mesice
    if(isset($_GET["otevrit"])) 
    {
      if(isset($_POST["ok"]))
      {
        if($mesic_tmp = $data->get_mesic($_GET["rok"],$_GET["mesic"],$_GET["uzivatel"])) //zaznam existuje
        {
          $mesic = $mesic_tmp;
          $mesic->set_stav(Mesic::OTEVRENO);
          $data->edit_mesic($mesic);
        }
        else //zaznam neexistuje - nemuze nastat
        {
          $mesic->set_uzivatel($logged->get_id());
          $mesic->set_stav(Mesic::OTEVRENO);
          $data->add_mesic($mesic);
        }
      }
      elseif(!isset($_POST["storno"]))
      {
        $obsah .= Views::potvrzeni_otevreni($data->get_uzivatel($_GET["uzivatel"]),$mesic,"working-time.php?kontrola");
        goto OUTPUT;
      }
    }
    //tabulka mesice pro daneho uzivatele
    $hodiny = array();
		$_zakazk = $data->get_zakazky_4uzivatel_a_mesic($_GET["uzivatel"],Opravneni::PRACOVNIK,$mesic); 
    foreach ($_zakazk as $obj)
    {      
      $pole = $data->get_dny($_GET["uzivatel"],$obj->get_id(),$_GET["rok"],$_GET["mesic"]);
      foreach ($pole as $den)
      {    
        $hodiny[$den->get_den()][$obj->get_id()] = $den->get_hodiny();
      }    
    }      
    if($mesic_tmp = $data->get_mesic($mesic->get_rok(),$mesic->get_mesic(),$_GET["uzivatel"])) //zaznam existuje
    {
      $mesic = $mesic_tmp;
    }
    else //zaznam neexistuje
    {
      $mesic->set_uzivatel($_GET["uzivatel"]);
      $mesic->set_stav(Mesic::OTEVRENO);
    }
    $obsah .= Views::zobraz_mesic_4uzivatel($mesic,$data->get_uzivatel($_GET["uzivatel"]),$_zakazk,$hodiny,"working-time.php?kontrola",false);
    goto OUTPUT;
  }
  //seznam uzivatelu s indikaci stavu pro dany mesic a rok
  $obsah .= Views::get_list_uzivatele($data->get_mesice($mesic->get_rok(),$mesic->get_mesic()),$mesic,$data->get_uzivatele_active(),"working-time.php?kontrola"); 
  goto OUTPUT;
}

// working time - vyplnovani
if((isset($_GET["working-time"]))AND(isset($_GET["vypln"]))) 
{
  $submenu->set_active("working time");
  if($mesic_tmp = $data->get_mesic($mesic->get_rok(),$mesic->get_mesic(),$logged->get_id()))
  {
    $mesic = $mesic_tmp;
  }
  if($mesic->get_stav() != Mesic::OTEVRENO) goto OUTPUT;
  $hodiny = array();
	$_zakaz = $data->get_zakazky_4uzivatel_a_mesic($logged->get_id(),Opravneni::PRACOVNIK,$mesic); 
  foreach ($_zakaz as $obj)
  {      
    $pole = $data->get_dny($logged->get_id(),$obj->get_id(),$mesic->get_rok(),$mesic->get_mesic());
    foreach ($pole as $den)
    {    
      $hodiny[$den->get_den()][$obj->get_id()] = $den->get_hodiny();
    }    
  }      
  $obsah .= Views::vypln_mesic($mesic,$logged,$_zakaz,$hodiny,"working-time.php?working-time");
  goto OUTPUT;
}

// working time - uzavreni
if((isset($_GET["working-time"]))AND(isset($_GET["zavrit"]))) 
{
  $submenu->set_active("working time");
  if(isset($_POST["ok"]))
  {
    if($mesic_tmp = $data->get_mesic($mesic->get_rok(),$mesic->get_mesic(),$logged->get_id())) //zaznam existuje
    {
      $mesic = $mesic_tmp;
      $mesic->set_stav(Mesic::UZAVRENO);
      $data->edit_mesic($mesic);
    }
    else //zaznam neexistuje
    {
      $mesic->set_uzivatel($logged->get_id());
      $mesic->set_stav(Mesic::UZAVRENO);
      $data->add_mesic($mesic);
    }    
  }
  elseif(!isset($_POST["storno"]))
  {
    $obsah .= Views::potvrzeni_zavreni($mesic,"working-time.php?working-time");
    goto OUTPUT;
  }
}

// default - wt tabulka pro vybrany mesic
$submenu->set_active("working time");
$hodiny = array();
$_zaka = $data->get_zakazky_4uzivatel_a_mesic($logged->get_id(),Opravneni::PRACOVNIK,$mesic);
//foreach ($data->get_zakazky_4uzivatel($logged->get_id(),Opravneni::PRACOVNIK) as $obj)
foreach ($_zaka as $obj)
{
  $pole = $data->get_dny($logged->get_id(),$obj->get_id(),$mesic->get_rok(),$mesic->get_mesic());
  foreach ($pole as $den)
  {
    $hodiny[$den->get_den()][$obj->get_id()] = $den->get_hodiny();
  }
}
if($mesic_tmp = $data->get_mesic($mesic->get_rok(),$mesic->get_mesic(),$logged->get_id())) //zaznam existuje
{
  $mesic = $mesic_tmp;
}
$obsah .= Views::zobraz_mesic_4uzivatel($mesic,NULL,$_zaka,$hodiny,"working-time.php?working-time",true);

//---------------------------------------
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
