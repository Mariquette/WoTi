<?php

class Den
{
  private $id;
  private $zak_id;
  private $uziv_id;
  private $rok;
  private $mesic;
  private $den;
  private $hodiny;
    
  public function __construct($array = false)                     
  {
    if((is_array($array)) AND (count($array)==7))
    {
      $this->set_id($array["id"]);
      $this->set_zak_id($array["zakazky_id"]);
      $this->set_uziv_id($array["uzivatele_id"]);
      $this->set_rok($array["rok"]);
      $this->set_mesic($array["mesic"]);
      $this->set_den($array["den"]);
      $this->set_hodiny($array["hodiny"]);
    }
    else
    {
      $this->id = 0;
      $this->zak_id = 0;
      $this->uziv_id = 0;
      $this->rok = date("Y");
      $this->mesic = date("n");
      $this->den = date("d");
      $this->hodiny = 0;
    }
  }

//------------------------------ public functions -------------------------------  

  public function set_id($id)
  {
    $this->id = $id;
  }
  public function set_zak_id($id)
  {
    $this->zak_id = $id;
  }
  public function set_uziv_id($id)
  {
    $this->uziv_id = $id;
  }
  public function set_rok($rok)
  {
    $this->rok = $rok; 
  }
  public function set_mesic($mesic)
  {
    $this->mesic = $mesic;   
  }
  public function set_den($den)
  {
    $this->den = $den;
  }
  public function set_hodiny($hodiny)
  {
    $this->hodiny = $hodiny;
  }
  
  public function get_id()
  {
    return $this->id;
  }
  public function get_zak_id()
  {
    return $this->zak_id;
  }
  public function get_uziv_id()
  {
    return $this->uziv_id;
  }
  public function get_rok()
  {
    return $this->rok;
  }
  public function get_mesic()
  {
    return $this->mesic;
  }
  public function get_den()
  {
    return $this->den;
  }
  public function get_hodiny()
  {
    return $this->hodiny;
  }
  
  //------------------------------ private functions -------------------------------  
  
     
}// konec tridy
?>
