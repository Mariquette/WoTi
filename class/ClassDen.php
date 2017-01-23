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
    
  private $id_err;
  private $zak_err;
  private $uziv_err;
  private $rok_err;
  private $mesic_err;
  private $den_err;
  private $hodiny_err;
  
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
    
    $this->id_err = "";
    $this->zak_err = "";
    $this->uziv_err = "";
    $this->rok_err = "";
    $this->mesic_err = "";
    $this->den_err = "";
    $this->hodiny_err = "";
    
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
  
  public function get_id_err()
  {
    return $this->id_err;
  }
  public function get_zak_err()
  {
    return $this->zak_err;
  }
  public function get_uziv_err()
  {
    return $this->uziv_err;
  }
  public function get_rok_err()
  {
    return $this->rok_err;
  }
  public function get_mesic_err()
  {
    return $this->mesic_err;
  }
  public function get_den_err()
  {
    return $this->den_err;
  }
  public function get_hodiny_err()
  {
    return $this->hodiny_err;
  }
  
  public function to_array()
  {
    $array["id"] = $this->id;
    $array["zakazky_id"] = $this->zak_id;
    $array["uzivatele_id"] = $this->uziv_id;
    $array["rok"] = $this->rok;
    $array["mesic"] = $this->mesic;
    $array["den"] = $this->den;
    $array["hodiny"] = $this->hodiny;
    return $array;
  }

  public function is_valid()
  {
    $return = true;
    /*
    if(!Util::is_number($this->id))
    {
      $return = false;
      $this->id_err = "Povolené znaky pro parametr Id jsou číslice 0-9.";
    }
    */
    if(!Util::is_number($this->zak_id)) //nezjistuje, zda existuje v db!
    {
      $return = false;
      $this->zak_err = "Neplatná zakázka.";
    }
    if(!Util::is_number($this->uziv_id)) //nezjistuje, zda existuje v db!
    {
      $return = false;
      $this->uziv_err = "Neplatný uživatel.";
    }
    if(!Util::is_year($this->rok))
    {
      $return = false;
      $this->rok_err = "Neplatný rok.";
    }
    if(!Util::is_month($this->mesic))
    {
      $return = false;
      $this->mesic_err = "Neplatný měsíc.";
    }
    if(!Util::is_day($this->den,$this->mesic,$this->rok))
    {
      $return = false;
      $this->den_err = "Neplatný den.";
    }
    if(!Util::is_number($this->hodiny))
    {
      $return = false;
      $this->hodiny_err = "Počet hodin musí být číslo.";
    }
    if($this->hodiny<0) 
    {
      $return = false;
      $this->hodiny_err = "Počet hodin nesmí být < 0!";
    }
    if($this->hodiny>24) 
    {
      $return = false;
      $this->hodiny_err = "Počet hodin nesmí být > 24!";
    }
    return $return;
  }
  
  //------------------------------ private functions -------------------------------  
  
     
}// konec tridy
?>
