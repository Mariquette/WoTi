<?php
  
class Util
{
  //private $err = false;

  //------------------------------ public functions -------------------------------  
  
  static function jeAktivni($odkaz)
  {
  /*
  vstup: jmeno aktualniho souboru i s priponou
  fce: rozhodne zda je jmeno stejne s jmenem aktualniho souboru(zjisteni pomoci konstanty definovane v souboru)
  vystup: Aktivni, nebo ""
  */ 
    $return=  substr($odkaz, 0, (count($odkaz)-5)) == DirName ? "Aktivni"  : $odkaz==FileName ? "Aktivni" : "";
    return $return;
  }
  
  static function filtruj_vstup()
  {
    foreach ($_GET as $key => $val)
    {
      $_GET[$key] = Util::filtr_input($val);
    } 
  
    foreach ($_POST as $key => $val)
    {
      $_POST[$key] = Util::filtr_input($val);
    } 
  }
  
  static function check_token()
  {
    if(isset($_POST["token"]))
    {
      if(!Util::check_token_2($_POST["token"]))
      {
        Util::create_token();
        die ("<h2>Opakované odeslání formuláře!</h2>");
      }
    }
    Util::create_token();
  }
  
  static function get_token()
  {
    if(!isset($_SESSION["token"]))
    {
      return Util::create_token();
    }    
    return $_SESSION["token"];
  }
   
  static function is_logged()
  /*
  zjisti, zda je uzivatel prihlasen
  */
  {
    if(isset($_SESSION["logged"]))
    {
      $logged = $_SESSION["logged"];
//	print_r($logged);
    }
    else
    {
      $logged = false;
    }
    return $logged;
  }
  
  //------------------------------ private functions -------------------------------

  
  private static function filtr_input($input)
  {
      $input = str_replace("|","",$input);
      $input = str_replace("\\","",$input);
      $input = str_replace("\"","",$input);
      $input = str_replace("'","",$input);
      $input = str_replace("$","",$input);
      $input = str_replace("~","",$input);
      //$input = str_replace(">","(větší než)",$input);
      //$input = str_replace("<","(menší než)",$input);
      return $input;
  }
  
  private static function check_token_2($token)
  {
    if(!isset($_SESSION["token"]))
    {
      return false;
    }    
    if($_SESSION["token"]==$token)
    {
      return true;
    }
    return false;
  }       
  
  private static function create_token()
  {
    $token = rand ( 1000000 , 9999999 );
    $_SESSION["token"]= $token;
    return $token;
  } 
  
  public static function login($uziv_id, $heslo)
  /*
  pokud je uzivatel overen, prihlasi ho
  */
  {
    $data = new Database();
    if($uzivatel = $data->over_uzivatel($uziv_id, $heslo))
    {
      //$_SESSION["logged"] = array("id"=>$uziv_id,"prijmeni"=>$uzivatel["prijmeni"],"jmeno"=>$uzivatel["jmeno"],"role"=>$uzivatel["role"]);
	$_SESSION["logged"] = $uzivatel;
	return true;
    }
    else $_SESSION["logged"] = false;
	return false;
  }
  
  public static function ch_passwd($uziv_id, $heslo, $nove_heslo, $nove_heslo2)
  /*
  zmena hesla
  */
  {
    $data = new Database();
    if($uzivatel = $data->over_uzivatel($uziv_id, $heslo))
    {
      if($nove_heslo==$nove_heslo2)
      {
        if((strlen($nove_heslo)>5) AND (strlen($nove_heslo)<=20))
        {
          if($data->set_heslo($uziv_id, $nove_heslo))
          {
            return ""; //"<h2>Heslo bylo úspěšně změněno.</h2>";
            //$this->err["text"] = "<h2>Heslo bylo úspěšně změněno.</h2>";
            //$this->err["num"] = 6;
          }
          else
          {
            return "<h2>Nepodařilo se uložit heslo do databáze!</h2>";
            //$this->err["num"] = 5;
          }
        }
        else
        {
          return "<h2>Heslo musí splňovat: min. 6 znaků, max. 20 znaků!</h2>";
          //$this->err["num"] = 4;
        }
      }
      else
      {
        return "<h2>Hesla se neshodují!</h2>";
        //$this->err["num"] = 3;
      }
    }
    else
    {
      return "<h2>Neplatné heslo!</h2>";
      //$this->err["num"] = 2;
    }
	return "super chyba";
  }
  
  //-------------NEPOUZIVANE-----------------
  
  
  static function is_valid_hodiny($hodiny)
  {
    if($hodiny<0) return false;
    if($hodiny>24) return false;
    if(!Util::is_cele_cislo($hodiny)) return false;
    return true;
  }
  
  static function is_cele_cislo($cislo)
  {
    $cislo = str_replace(" ","",$cislo);
    $atom = '/^[0-9]*$/';
    return preg_match($atom, $cislo);
  }

}//konec tridy

?>
