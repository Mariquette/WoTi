<?php

class Zakazka
{
private static $data;

  private $id;
  private $nazev;
  private $popis;
  private $obdobi;
  private $kategorie;
  private $stav;
  
  private $id_err;
  private $nazev_err;
  private $popis_err;
  private $obdobi_err;
  private $kategorie_err;
  private $stav_err;
  
  //kategorie
  const RAD = 0;
  const GRANT = 1;
  const SOW = 2;
  const RITE = 3;
  const RC = 4;
  const OTHER = 5;
  const REZIE = 8;
  const OSTATNI = 9;
  
  //stav
  const UKONCENA = 0;
  const AKTIVNI = 1;
  const ZARUKA = 2;
  const TRVALA = 3;

  public function __construct($array = false)
  {
    if(self::$data === NULL)
    {
      self::$data = new Database();
    }
    
    if((is_array($array)) AND (count($array)==6))
    {
      $this->set_id($array["id"]);
      $this->set_nazev($array["nazev"]);
      $this->set_popis($array["popis"]);
      $this->set_obdobi($array["obdobi"]);
      $this->set_kategorie($array["kategorie"]);
      $this->set_stav($array["stav"]);
    }
    else
    {
      $this->id = 0;
      $this->nazev = "";
      $this->popis = "";
      $this->obdobi = "";
      $this->kategorie = 0;
      $this->stav = 1;
    }
    
    $this->id_err = "";
    $this->nazev_err = "";
    $this->popis_err = "";
    $this->obdobi_err = "";
    $this->kategorie_err = "";
    $this->stav_err = "";
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
  public function set_obdobi($obdobi)
  {
    $this->obdobi = $obdobi;
  }
  public function set_kategorie($kategorie)
  {
    $this->kategorie = $kategorie;
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
  public function get_popis($znaku=0)
  {
    $text = $this->popis;
    if($znaku>0)
    {
      if(strlen($text)>$znaku)
      {
        $text = mb_substr($text,0,$znaku-2);
        $text .= "...";
      }
    }
    return $text;
  }
  public function get_obdobi($znaku=0)
  {
    $text = $this->obdobi;
    if($znaku>0)
    {
      if(strlen($text)>$znaku)
      {
        $text = mb_substr($text,0,$znaku-2);
        $text .= "...";
      }
    }
    return $text;
  }
  public function get_kategorie()
  {
    return $this->kategorie;
  }
  public function get_stav()
  {
    return $this->stav;
  }
  
  public function get_id_err()
  {
    return $this->id_err;
  }
  public function get_nazev_err()
  {
    return $this->nazev_err;
  }
  public function get_popis_err()
  {
    return $this->popis_err;
  }
  public function get_obdobi_err()
  {
    return $this->obdobi_err;
  }
  public function get_kategorie_err()
  {
    return $this->kategorie_err;
  }
  public function get_stav_err()
  {
    return $this->stav_err;
  }

  public function get_options_stav()
  {
    $data = self::$data;
    return $data->get_options_stav_zakazka();
  }  
  public function get_stav_name()
  {
  	$data = self::$data;
    if($obj = $data->get_stav_zakazka($this->stav)) return $obj->get_name();
	  return "";
  }
  public function get_options_kategorie()
  {
    $data = self::$data;
    return $data->get_options_kategorie();
  }  
  public function get_kategorie_name()
  {
  	$data = self::$data;
    if($obj = $data->get_kategorie($this->kategorie)) return $obj->get_name();
	  return "";
  }
  
  public function to_array()
  {
    $array["id"] = $this->id;
    $array["nazev"] = $this->nazev;
    $array["popis"] = $this->popis;
    $array["obdobi"] = $this->obdobi;
    $array["kategorie"] = $this->kategorie;
    $array["stav"] = $this->stav;
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
    if(!Util::is_text($this->nazev,50))
    {
      $return = false;
      $this->nazev_err = "Název musí být vyplněn a má omezenou délku na 50 znaků.<br>";
    } 
    if(!Util::is_text($this->popis,200))
    {
      $return = false;
      $this->popis_err = "Popis musí být vyplněn a má omezenou délku na 200 znaků.";
    } 
    if(!Util::is_text_or_empty($this->obdobi,100))
    {
      $return = false;
      $this->obdobi_err = "Období má omezenou délku na 100 znaků.";
    } 
    if(!Util::is_number($this->kategorie))
    {
      $return = false;
      $this->kategorie_err = "Povolené znaky pro kategorii jsou číslice 0-9.";
    }
    if(!Util::is_number($this->stav))
    {
      $return = false;
      $this->stav_err = "Povolené znaky pro stav jsou číslice 0-9.";
    }
    return $return;
  }
  
  public function get_odpovedny()
  {
    $data = self::$data;
    return $data->get_uzivatele_4zakazka($this->id,Opravneni::ODPOVEDNY);
  }

//------------------------------ private functions -------------------------------  


}//konec tridy

?>
