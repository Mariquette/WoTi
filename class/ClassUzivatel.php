<?php

class Uzivatel
{
  private $id;
  private $prijmeni;
  private $jmeno;
  private $login;
  private $heslo;
  private $role;
  
  public function __construct($array = false)
  {
    if((is_array($array)) AND (count($array)==6))
    {
      $this->set_id($array["id"]);
      $this->set_prijmeni($array["prijmeni"]);
      $this->set_jmeno($array["jmeno"]);
      $this->set_login($array["login"]);
      $this->set_heslo($array["heslo"]);
      $this->set_role($array["role"]);
    }
    else
    {
      $this->id = 0;
      $this->prijmeni = "";
      $this->jmeno = "";
      $this->login = "";
      $this->heslo = "";
      $this->role = 1;
    }
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
  
//------------------------------ private functions -------------------------------  

}//konec tridy

?>
