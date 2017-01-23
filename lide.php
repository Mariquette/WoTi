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
	$obsah.='<p><span class="infoerr">Pro zobrazení je nutné se <a href="./index.php">přihlásit</a>.</span><p>';
	goto OUTPUT;
}

// autentifikace
$role = $logged->get_role();

$submenu = new Menu("submenu");
$submenu->add_item(new SimpleLink("seznam",FileName."?seznam"));
if((Util::is_admin($role)))
{
  $submenu->add_item(new SimpleLink("|",""));
  $submenu->add_item(new SimpleLink("přidat",FileName."?pridat"));
}
$submenu->add_item(new SimpleLink("|",""));
$submenu->add_item(new SimpleLink("změna hesla",FileName."?passwd"));
 
$data = new Database();
$mesic = new Mesic();

// aktualizace mesice
if(isset($_GET["rok"])) $mesic->set_rok($_GET["rok"]);
if(isset($_GET["mesic"])) $mesic->set_mesic($_GET["mesic"]);
if(isset($_POST["rok"])) $mesic->set_rok($_POST["rok"]);
if(isset($_POST["mesic"])) $mesic->set_mesic($_POST["mesic"]);

//razeni
$order = "prijmeni";
if(isset($_GET["order"])) $order = $_GET["order"];

// detail cloveka
if(isset($_GET["detail"])) 
{
  $obsah .= LideViews::detail($data->get_uzivatel($_GET["detail"]),"lide.php",Util::is_admin($role));
  goto OUTPUT;
}

// editacni formular
if(isset($_GET["edit"])) 
{
  if(!Util::is_admin($role)) 
  {
    $obsah .= LideViews::admin_only(); 
    goto OUTPUT;
  }  
  $obsah .= LideViews::edit($data->get_uzivatel($_GET["edit"]),"lide.php");
  goto OUTPUT;
}
// zmena cloveka
if(isset($_POST["edit"]) AND isset($_POST["id"]) AND isset($_POST["prijmeni"]) AND isset($_POST["jmeno"]) AND isset($_POST["role"]) AND isset($_POST["zkratka"]) AND isset($_POST["dovolena"]) AND isset($_POST["uvazek"])) 
{
  if(!Util::is_admin($role)) 
  {
    $obsah .= LideViews::admin_only(); 
    goto OUTPUT;
  } 
  $uzivatel = new Uzivatel(array("id"=>$_POST["id"],"prijmeni"=>$_POST["prijmeni"],"jmeno"=>$_POST["jmeno"],"login"=>"","heslo"=>"","role"=>$_POST["role"],"zkratka"=>$_POST["zkratka"],"dovolena"=>$_POST["dovolena"],"uvazek"=>$_POST["uvazek"]));
  if($uzivatel->is_valid())
  {
    if($data->edit_uzivatel($uzivatel)) $obsah .= '<p><span class="info">Změny byly uloženy.</span></p>';    	
    else $obsah .='<p><span class="infoerr">Při ukládání změn došlo k chybě!</span></p>';	
    $obsah .= LideViews::detail($data->get_uzivatel($_POST["id"]),"lide.php",Util::is_admin($role));
  }
  else $obsah .= LideViews::edit($uzivatel,"lide.php");
  goto OUTPUT;
}

// pridani noveho cloveka
if(isset($_GET["pridat"])) 
{
  if(!Util::is_admin($role)) 
  {
    $obsah .= LideViews::admin_only(); 
    goto OUTPUT;
  }  
  $submenu->set_active("přidat");
  if(isset($_POST["add"]) AND isset($_POST["jmeno"]) AND isset($_POST["prijmeni"]) AND isset($_POST["role"]) AND isset($_POST["zkratka"]) AND isset($_POST["dovolena"]) AND isset($_POST["uvazek"])) 
  {
    $login = strtolower(Util::remove_diacritics($_POST["jmeno"].".".$_POST["prijmeni"]));
    $heslo = substr(md5(rand()), 0, 7);
    $uzivatel = new Uzivatel(array("id"=>"NULL", "prijmeni"=>$_POST["prijmeni"], "jmeno"=>$_POST["jmeno"], "login"=>$login, "heslo"=>$heslo, "role"=>$_POST["role"],"zkratka"=>$_POST["zkratka"],"dovolena"=>$_POST["dovolena"],"uvazek"=>$_POST["uvazek"]));
    if($uzivatel->is_valid())
    {
      if($autoid = $data->add_uzivatel($uzivatel))
      {
      	$novy = $data->get_uzivatel($autoid);
        $obsah .= '<p><span class="info">Uživatel '.$novy->get_jmeno().' '.$novy->get_prijmeni().' byl přidán.</span></p>
          <p><span class="info">Heslo: </span><span class="infoerr">'.$novy->get_heslo().'</span></p>
          <p><span class="info">Heslo si zaznamenejte a předejte.</span></p>';    	
      	$obsah .= LideViews::detail($novy,"lide.php",Util::is_admin($role));  
      }
      else $obsah .= '<p><span class="infoerr">Při vytváření nového záznamu došlo k chybě!</span></p>';	
    }
    else $obsah .= LideViews::add($uzivatel, "lide.php?pridat");
  }
  else $obsah .= LideViews::add(new Uzivatel(), "lide.php?pridat");
  goto OUTPUT;
}

// formular pro zmenu hesla
if(isset($_GET["passwd"])) 
{
	$_uziv = $logged;
	if(isset($_GET["id"]))
  {
    if(Util::is_admin($role))
  	{ 
  		if(Util::is_cele_cislo($_GET["id"]))
  		{
  			if(($_uziv = $data->get_uzivatel($_GET["id"])) == false) 
        { 
          $obsah .= LideViews::uzivatel_nenalezen(); 
          goto OUTPUT;  
        }
  		}
  	}
  }
  /*
  $_uziv = $logged;
	if(Util::is_admin($role))
	{ 
		if(Util::is_cele_cislo($_GET["passwd"]))
		{
			if(($_uziv = $data->get_uzivatel($_GET["passwd"])) == false) 
      { 
        $obsah .= LideViews::uzivatel_nenalezen(); 
        goto OUTPUT;  
      }
		}
	}
  */
	$submenu->set_active("změna hesla");
  $obsah .= LideViews::zmena_hesla_formular($_uziv, FileName, "", Util::is_admin($role));		
  goto OUTPUT;
}
// zmena hesla
if(isset($_POST["uzivatel"]) AND isset($_POST["heslo"]) AND isset($_POST["nove_heslo"]) AND isset($_POST["nove_heslo2"]) AND isset($_POST["zmena_hesla"]) AND isset($_POST["token"]))
{
	$submenu->set_active("změna hesla");
	$_uziv = $logged;
	if(Util::is_admin($role))
	{ 
		if(Util::is_cele_cislo($_POST["uzivatel"]))
		{
			if(($_uziv = $data->get_uzivatel($_POST["uzivatel"])) == false) 
      { 
        $obsah .= LideViews::uzivatel_nenalezen(); 
        goto OUTPUT;  
      }
		}
	}
	$err = Util::ch_passwd($_POST["uzivatel"], $_POST["heslo"], $_POST["nove_heslo"], $_POST["nove_heslo2"], Util::is_admin($role));
	if($err != "")
	{
		$obsah .= LideViews::zmena_hesla_formular($_uziv, FileName, $err, Util::is_admin($role));		
		goto OUTPUT;
	}
	$obsah .='<p><span class="info">Změna hesla byla úspěšně provedena</span></p>';
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
