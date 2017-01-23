<?php

class Mesic
{
  private $id;
  private $rok;
  private $mesic;
  private $uzivatel;
  private $stav;
  
  const OTEVRENO = 0;
  const UZAVRENO = 1;
  const ZAMCENO = 2;
    
  public function __construct($array = false)                     
  {
    if((is_array($array)) AND (count($array)==5))
    {
      $this->set_id($array["id"]);
      $this->set_rok($array["rok"]);
      $this->set_mesic($array["mesic"]);
      $this->set_uzivatel($array["uzivatele_id"]);
      $this->set_stav($array["stav"]);
    }
    else
    {
      $this->id = NULL;
      $this->rok = date("Y");
      $this->mesic = date("n");
      $this->uzivatel = 0;
      $this->stav = 0;
    }    
  }

//------------------------------ public functions -------------------------------  

  public function set_id($id)
  {
    $this->id = $id;
  }
  public function set_mesic($mesic)
  {
    if(($mesic>12)OR($mesic<1)) 
    {
      die("Mesic musi byt od 1 do 12!");
    }
    $this->mesic = $mesic;   
  }
  public function set_rok($rok)
  {
    if(($rok>2112)OR($rok<2012))
    {
      die("Rok musi byt v rozmezi 2012 az 2112!");
    }
    $this->rok = $rok; 
  }  
  public function set_uzivatel($uzivatel)
  {
    $this->uzivatel = $uzivatel;
  }
  public function set_stav($stav)
  {
    if($this->rok < 2017) $this->stav = self::ZAMCENO;
    else $this->stav = $stav;
  }
  
  public function get_id()
  {
    return $this->id;
  }
  public function get_rok()
  {
    return $this->rok;
  }
  public function get_mesic()
  {
    return $this->mesic;
  }
  public function get_uzivatel()
  {
    return $this->uzivatel;
  }
  public function get_stav()
  {
    return $this->stav;
  }
  
  public function get_options_stav()
  {
    $data = new Database();
    return $data->get_options_stav_mesic();
  }  
  public function get_stav_name()
  {
  	$data = new Database();
    if($obj = $data->get_stav_mesic($this->stav)) return $obj->get_name();
	  return "";
  }
  static function stav_2_name($stav)
  {
  	$data = new Database();
    if($obj = $data->get_stav_mesic($stav)) return $obj->get_name();
	  return "";
  }
  
  public function get_p_rok()
  {
    return $this->rok-1;
  }
  public function get_d_rok()
  {
    return $this->rok+1;
  }
  
  public function get_p_mesic()
  {
    $p_mesic = $this->mesic-1;
    if($p_mesic<1)
    {
      $p_mesic = 12;
    }
    return $p_mesic;
  }
  public function get_p_mesic_rok()
  {
    $p_rok = $this->rok;
    if($this->get_p_mesic()==12)
    {
      $p_rok = $this->rok-1;
    }
    return $p_rok;
  }
  public function get_d_mesic()
  {
    $d_mesic = $this->mesic+1;
    if($d_mesic>12)
    {
      $d_mesic = 1;
    }
    return $d_mesic;
  }
  public function get_d_mesic_rok()
  {
    $d_rok = $this->rok;
    if($this->get_d_mesic()==1)
    {
      $d_rok = $this->rok+1;
    }
    return $d_rok;
  }
  
  public function pocet_dnu() //pocet dnu v mesici
  {
    return cal_days_in_month(CAL_GREGORIAN, $this->mesic, $this->rok);
  }
  public function prvni_den() //poradove cislo dne v tydnu pro prvni den v mesici
  {
    return $this->den_v_tydnu(1);
  }
  public function den_v_tydnu($den) //poradove cislo dne v tydnu
  {
    return date("N",mktime(0,0,0,$this->mesic,$den,$this->rok));
  }
  public function pocet_tydnu() //pocet tydnu v mesici
  {
    return date("W",mktime(0,0,0,$this->mesic,$this->pocet_dnu()-7,$this->rok))-date("W",mktime(0,0,0,$this->mesic,1+7,$this->rok))+3;
  }
  
  public function to_array()
  {
    $array["id"] = $this->id;
    $array["rok"] = $this->rok;
    $array["mesic"] = $this->mesic;
    $array["uzivatele_id"] = $this->uzivatel;
    $array["stav"] = $this->stav;
    return $array;
  }
  
  //------------------------------ private functions -------------------------------  
  
     
}// konec tridy
?>
