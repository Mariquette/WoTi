<?php

class Uzivatel
{
  private static $data;

  private $id;
  private $prijmeni;
  private $jmeno;
  private $login;
  private $heslo;
  private $role;
  private $zkratka;
  private $dovolena;
  private $uvazek;
  
  private $id_err;
  private $prijmeni_err;
  private $jmeno_err;
  private $login_err;
  private $heslo_err;
  private $role_err;
  private $zkratka_err;
  private $dovolena_err;
  private $uvazek_err;
  
  const NEAKTIVNI = 0;
  const ZAKLADNI = 1;
  const ADMIN = 2;
  const SUPERUSER = 3;

  public function __construct($array = false)
  {
    if(self::$data === NULL)
    {
      self::$data = new Database();
    }
    
    if((is_array($array)) AND (count($array)==9))
    {
      $this->set_id($array["id"]);
      $this->set_prijmeni($array["prijmeni"]);
      $this->set_jmeno($array["jmeno"]);
      $this->set_login($array["login"]);
      $this->set_heslo($array["heslo"]);
      $this->set_role($array["role"]);
      $this->set_zkratka($array["zkratka"]);
      $this->set_dovolena($array["dovolena"]);
      $this->set_uvazek($array["uvazek"]);
    }
    else
    {
      $this->id = 0;
      $this->prijmeni = "";
      $this->jmeno = "";
      $this->login = "";
      $this->heslo = "";
      $this->role = 1;
      $this->zkratka = "";
      $this->dovolena = 0;
      $this->uvazek = "";
    }
    $this->id_err = "";
    $this->prijmeni_err = "";
    $this->jmeno_err = "";
    $this->login_err = "";
    $this->heslo_err = "";
    $this->role_err = "";  
    $this->zkratka_err = "";
    $this->dovolena_err = "";
    $this->uvazek_err = "";
     }
  
//------------------------------ public functions -------------------------------  
  
  public function set_id($id)
  {
    $this->id = $id;
  }
  public function set_prijmeni($prijmeni)
  {
    $this->prijmeni = $prijmeni;
  }
  public function set_jmeno($jmeno)
  {
    $this->jmeno = $jmeno;
  }
  public function set_login($login)
  {
    $this->login = $login;
  }
  public function set_heslo($heslo)
  {
    $this->heslo = $heslo;
  }
  public function set_role($role)
  {
    $this->role = $role;
  }
  public function set_zkratka($zkratka)
  {
    $this->zkratka = $zkratka;
  }
  public function set_dovolena($dovolena)
  {
    $this->dovolena = $dovolena;
  }
  public function set_uvazek($uvazek)
  {
    $this->uvazek = $uvazek;
  }
  
  public function get_id()
  {
    return $this->id;
  }
  public function get_prijmeni()
  {
    return $this->prijmeni;
  }
  public function get_jmeno()
  {
    return $this->jmeno;
  }
  public function get_login()
  {
    return $this->login;
  }
  public function get_heslo()
  {
    return $this->heslo;
  }
  public function get_role()
  {
    return $this->role;
  }
  public function get_zkratka()
  {
    return $this->zkratka;
  }
  public function get_dovolena()
  {
    return $this->dovolena;
  }
  public function get_uvazek()
  {
    return $this->uvazek;
  }
  
  public function get_id_err()
  {
    return $this->id_err;
  }
  public function get_prijmeni_err()
  {
    return $this->prijmeni_err;
  }
  public function get_jmeno_err()
  {
    return $this->jmeno_err;
  }
  public function get_login_err()
  {
    return $this->login_err;
  }
  public function get_heslo_err()
  {
    return $this->heslo_err;
  }
  public function get_role_err()
  {
    return $this->role_err;
  }
  public function get_zkratka_err()
  {
    return $this->zkratka_err;
  }
  public function get_dovolena_err()
  {
    return $this->dovolena_err;
  }
  public function get_uvazek_err()
  {
    return $this->uvazek_err;
  }
  
  public function get_options_role()
  {
    $data = self::$data;
    return $data->get_options_role();
  }  
  public function get_role_name()
  {
  	$data = self::$data;
    if($obj = $data->get_role($this->role)) return $obj->get_name();
	  return "";
  }
  
  public function to_array()
  {
    $array["id"] = $this->id;
    $array["prijmeni"] = $this->prijmeni;
    $array["jmeno"] = $this->jmeno;
    $array["login"] = $this->login;
    $array["heslo"] = $this->heslo;
    $array["role"] = $this->role;
    $array["zkratka"] = $this->zkratka;
    $array["dovolena"] = $this->dovolena;
    $array["uvazek"] = $this->uvazek;
    return $array;
  }
  
  public function is_valid()
  {
    $return = true;
    /*
    if(!Util::is_number($this->id))
    {
      $return = false;
      $this->id_err = "Povolené znaky pro parametr id jsou číslice 0-9.";
    }
    */
    if(!Util::is_text($this->prijmeni,50))
    {
      $return = false;
      $this->prijmeni_err = "Příjmení musí být vyplněno a má omezenou délku na 50 znaků.";
    } 
    if(!Util::is_text($this->jmeno,50))
    {
      $return = false;
      $this->jmeno_err = "Jméno musí být vyplněno a má omezenou délku na 50 znaků.";
    } 
    /*
    if(!Util::is_text($this->login,50))
    {
      $return = false;
      $this->login_err = "Login musí být vyplněn a má omezenou délku na 50 znaků.";
    } 
    if(!Util::is_text($this->heslo,50))
    {
      $return = false;
      $this->heslo_err = "Heslo musí být vyplněno a má omezenou délku na 50 znaků.";
    } 
    */
    if(!Util::is_number($this->role))
    {
      $return = false;
      $this->role_err = "Povolené znaky pro parametr role jsou číslice 0-9.";
    }
    if(!Util::is_text($this->zkratka,6))
    {
      $return = false;
      $this->zkratka_err = "Zkratka musí být vyplněna a má omezenou délku na 6 znaků.";
    } 
    if(!Util::is_number($this->dovolena))
    {
      $return = false;
      $this->dovolena_err = "Povolené znaky pro parametr dovolená jsou číslice 0-9.";
    }
    if(!Util::is_text_or_empty($this->uvazek,10))
    {
      $return = false;
      $this->uvazek_err = "=Úvazek má omezenou délku na 10 znaků.";
    } 
    
    return $return;
  }

public function is_editor($zak_id="")
{
	if(Util::is_admin($this->get_role())) return true;
  //$data = self::$data;
  $data = new Database;
  if($zak_id=="")
	{
		if(count($data->get_zakazky_4uzivatel($this->get_id(), Opravneni::EDITOR))>0) return true;
	}
	else
	{
		foreach($data->get_uzivatele_4zakazka($zak_id, Opravneni::EDITOR) as $uziv)
		{
			if($uziv->get_id()==$this->get_id()) return true;
		}
	}
	return false;
}
  
//------------------------------ private functions -------------------------------  

}//konec tridy

?>
