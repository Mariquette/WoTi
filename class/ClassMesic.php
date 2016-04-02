<?php

class Mesic
{
  private $rok;
  private $mesic;
    
  public function __construct()                     
  {
    $this->rok = date("Y");
    $this->mesic = date("n");
  }

//------------------------------ public functions -------------------------------  

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
  
  public function get_rok()
  {
    return $this->rok;
  }
  public function get_mesic()
  {
    return $this->mesic;
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
  public function get_p_rok()
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
  public function get_d_rok()
  {
    $d_rok = $this->rok;
    if($this->get_d_mesic()==1)
    {
      $d_rok = $this->rok+1;
    }
    return $d_rok;
  }

  public function pocet_dnu()
  /*
  vrati pocet dnu v aktualnim mesici
  */
  {
    return cal_days_in_month(CAL_GREGORIAN, $this->mesic, $this->rok);
  }
  
  //------------------------------ private functions -------------------------------  
  
     
}// konec tridy
?>
