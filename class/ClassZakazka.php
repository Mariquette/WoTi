<?php

class Zakazka
{
  private $id;
  private $nazev;
  private $popis;
  private $cinnost;
  private $stav;
  
  public function __construct($array = false)
  {
    if((is_array($array)) AND (count($array)==5))
    {
      $this->set_id($array["id"]);
      $this->set_nazev($array["nazev"]);
      $this->set_popis($array["popis"]);
      $this->set_cinnost($array["cinnost"]);
      $this->set_stav($array["stav"]);
    }
    else
    {
      $this->id = 0;
      $this->nazev = "";
      $this->popis = "";
      $this->cinnost = "";
      $this->stav = 1;
    }
  }
  
//------------------------------ public functions -------------------------------  

  public function set_id($id)
  {
    $this->id = $id;
  }
  public function set_nazev($nazev)
  {
    $this->nazev = $nazev;
  }
  public function set_popis($popis)
  {
    $this->popis = $popis;
  }
  public function set_cinnost($cinnost)
  {
    $this->cinnost = $cinnost;
  }
  public function set_stav($stav)
  {
    $this->stav = $stav;
  }
  
  public function get_id()
  {
    return $this->id;
  }
  public function get_nazev()
  {
    return $this->nazev;
  }
  public function get_popis()
  {
    return $this->popis;
  }
  public function get_cinnost()
  {
    return $this->cinnost;
  }
  public function get_stav()
  {
    return $this->stav;
  }
  
  
//------------------------------ private functions -------------------------------  


}//konec tridy

?>
