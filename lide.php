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
$submenu->add_item(new SimpleLink("změna hesla",FileName."?passwd"));
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

//razeni
$order = "prijmeni";
if(isset($_GET["order"]))
{	
  $order = $_GET["order"];
}

// detail zakazky
if(isset($_GET["detail"])) 
{
  $obsah .= LideViews::detail($data->get_uzivatel($_GET["detail"]),"lide.php");
  goto OUTPUT;
}

// editacni formular
if(isset($_GET["edit"])) 
{
  $obsah .= LideViews::edit($data->get_uzivatel($_GET["edit"]),"lide.php");
  goto OUTPUT;
}

// zmena cloveka
if(isset($_POST["edit"])) 
{
  if($data->edit_uzivatel($_POST["id"],$_POST["prijmeni"],$_POST["jmeno"],$_POST["role"]))
  {
  	$obsah .= "Změny byly uloženy.<br>";    	
  }
  else
  {
  	$obsah .="Při ukládání změn došlo k chybě!";	
  }
  $obsah .= LideViews::detail($data->get_uzivatel($_POST["id"]),"lide.php");
  goto OUTPUT;
}

// pridani noveho cloveka
if(isset($_GET["pridat"])) 
{
  $submenu->set_active("přidat");
  if(isset($_POST["add"]) AND isset($_POST["jmeno"]) AND isset($_POST["prijmeni"])) 
  {
    if($data->add_uzivatel($_POST["prijmeni"], $_POST["jmeno"]))
    {
    	$obsah .= "Uživatel byl přidán.<br>";    	
    	$obsah .= "Přidejte dalšího uživatele:";
    }
    else
    {
    	$obsah .="Při vytváření nového záznamu došlo k chybě!";	
    }
  }
  $obsah .= LideViews::add("lide.php?pridat");
  goto OUTPUT;
}

// editace cloveka
if(isset($_GET["passwd"])) 
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
	$obsah .="<p>Změna hesla byla úspěšně provedena</p>";
	goto OUTPUT;
}

// working time vsech lidi
  if(isset($_GET["old"]))
  {
    $submenu->set_active("Původní");
  	
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
          $zak_data[] = array("id"=>$uziv_obj->get_id(), "jmeno"=>$uziv_obj->get_prijmeni(), "popis"=>"", "cinnost"=>"", "dny"=>$hodiny);
        }
        $text .= Outputs::mesic2csv($zak_data,$obj->get_nazev(),$obj->get_cinnost(),$mesic->pocet_dnu());  
        $zak_data = "";     
      }
      $soubor_link = fopen("$soubor","w");
      if(fwrite($soubor_link,$text))
      {
        $obsah .= "Soubor ".$soubor." byl úspěšně vytvořen.";
      }
      else
      {
        $obsah .= "Zápis do souboru se nezdařil.";
      }
      fclose($soubor_link);
    }
    else
    {
      $obsah .= "<a href=\"lide.php?old&uzaverka&rok=".$mesic->get_rok()."&mesic=".$mesic->get_mesic()."\">Exportuj</a><br>";
    }
    //else
    if (File_Exists($soubor))
    {
      $obsah .= "<br>Soubor ke stažení: <a href=\"$soubor\">".$soubor."</a> (vytvořeno ".date("d. m. Y, G:i", filemtime($soubor)).")<br>";    
    } 
    
    $obsah .= "<br>PŘEHLED PO ZAMĚSTNANCÍCH:<br>";   
    $obsah .= Views::nabidka2($mesic,"lide.php?old");
    foreach($data->get_uzivatele_active() as $uziv_id => $uziv_obj)
    {
      $uziv_data = "";
      foreach ($data->get_zakazky_active() as $id => $obj)
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
        $uziv_data[] = array("id"=>$obj->get_id(), "jmeno"=>$obj->get_nazev(), "popis"=>$obj->get_popis(), "cinnost"=>$obj->get_cinnost(), "dny"=>$hodiny);
      }
      $obsah .= Views::zobraz_mesic($uziv_data,$uziv_id,$uziv_obj->get_prijmeni().' '.$uziv_obj->get_jmeno(),$mesic->pocet_dnu());
      $uziv_data = "";
    }
    goto OUTPUT;   
  }

// default - seznam vsech lidi
$submenu->set_active("seznam");
$obsah .= LideViews::get_list($data->get_uzivatele_all($order),"lide.php?seznam"); 

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
