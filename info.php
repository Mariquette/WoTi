<?php

ini_set("log_errors", 1);
ini_set("error_log", "/home/daniela/woti/php-error.log");
error_log( "Hello, errors!" );

define("DirRoot", "./");
define("DirName", "");
define("FileName", "info.php");
define("LastChange", date("d. m. Y G:i", filemtime("info.php")));
define("Title", ""."info.php"."(".LastChange.")");      

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
$submenu->add_item(new SimpleLink("manuál",FileName."?manual"));
$submenu->add_item(new SimpleLink("|",""));
$submenu->add_item(new SimpleLink("co dělat, když ...",FileName."?help"));
$submenu->add_item(new SimpleLink("|",""));
$submenu->add_item(new SimpleLink("směrnice",FileName."?smernice"));

$data = new Database();

// co delat, když ... 
if(isset($_GET["help"])) 
{
  $submenu->set_active("co dělat, když ...");
  
  $admini = "";
  if(is_array($list = $data->get_uzivatele(Uzivatel::ADMIN)))
  {
    foreach($list as $osoba)
    {
        $admini .= $osoba->get_jmeno().' '.$osoba->get_prijmeni().", ";
    }
  }
  if($admini != "") $admini = substr($admini,0,-2);
  
  if(Util::is_admin($role)) 
  {
    $obsah .= '<br>>PRO ADMINISTRÁTORY<
      <p class="nadpis">Potřebuji upravit working time jiného zaměstnance.</p>
      <p class="text">Otevřete mu daný měsíc v sekci kontrola a poproste ho, aby udělal požadované změny.</p>
    
      <p class="nadpis">Potřebuji upravit working time jiného zaměstnance, který není k dispozici.</p>
      <p class="text">Resetujte mu heslo (Detail zaměstnance -> Změna hesla), poté se odhlašte a přihlašte jako on a vyplňte jeho working time.<br>
        Zaměstnance informujte o změně (pokud se tak nestane, nebude se moci přihlásit)!</p>
      
      <br>>PRO VŠECHNY UŽIVATELE<';
  }
  $obsah .= '
    <p class="nadpis">Potřebuji založit novou zakázku.</p>
    <p class="text">Požádajte některého z administrátorů o založení. Aktuální výčet administrátorů: '.$admini.'.</p>
    
    <p class="nadpis">Zavřel jsem měsíc a potřebuji dělat další změny.</p>
    <p class="text">Požádajte některého z administrátorů o otevření měsíce. Aktuální výčet administrátorů: '.$admini.'.</p>
    
    <p class="nadpis">Mám si napsat hodiny na nějakou zakázku, ta ale není v mé tabulce vidět.</p>
    <p class="text">Požádejte editora zakázky o zařazení do týmu pracovníků. Editora zakázky zjistíte rozkliknutím detailu zakázky v seznamu zakázek.</p>
    
    <p class="nadpis">Mám jiný problém.</p>
    <p class="text">Kontaktujte Danielu Doubravovou.</p>
    ';
  goto OUTPUT;
}

// smernice
if(isset($_GET["smernice"])) 
{
  $submenu->set_active("směrnice");
  $obsah .= 'interní směrnice RITE';
  goto OUTPUT;
}

// default 
$submenu->set_active("manuál");
$obsah .= '
  <p class="nadpis">Verze 1.3</p>
  <p class="text">spuštěna 2.4.2012</p>
  
  <p class="nadpis">Verze 2.3</p>
  <p class="text">od ledna 2017</p>
  <p class="text">novinky:
  <ul>
  <li>vkládání nových zaměstnanců a zakázek</li>
  <li>editace zaměstnanců a zakázek</li>
  <li>uzavírání a zamykání working time pro daný měsíc (zavřené měsíce již nelze editovat)</li>
  <li>přiřazení pracovníků k zakázkám -> člověk vidí v tabulce working time pouze přirazené zakázky</li>
  <li>editor zakázky může nahlížet, kdo si na ni píše hodiny</li>
  <li>celkový součet odpracovaných hodin na zakázce</li>
  <li>rozdělení zakázek na aktivní, v záruce, ukončené, trvalé</li>
  <li>možnost tisku přehledu zakázek do pdf</li>
  <li>možnost tisku kalendáře na nástěnku do pdf</li>
  <li>barevné vyznačení víkendů a svátků</li>
  </ul>
  </p>
  
  <p class="nadpis">Pojmy</p>
  
  <p class="text">Editor zakázky
  <ul>
  <li>může měnit údaje u zakázky včetně přiřazení zaměstnanců k zakázce</li>
  <li>v části WORKING TIME má navíc stránku <b>moje zakázky</b></li>
  </ul>
  </p>
  
  <p class="text">Administrátor
  <ul>
  <li>vkládá nové zakázky, zaměstnance</li>
  <li>může měnit údaje u zakázky včetně přiřazení zaměstnanců k zakázce</li>
  <li>v části WORKING TIME má navíc stránku <b>moje zakázky</b> a <b>kontrola</b></li>
  <li>má možnost změnit komukoli heslo</li>
  </ul>
  </p>
  ';

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
