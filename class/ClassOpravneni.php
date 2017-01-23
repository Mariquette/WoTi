<?php

class Opravneni
{
  private $id;
  private $uzivatel;
  private $zakazka;
  private $opravneni;
    
  const PRACOVNIK = 0;
  const EDITOR = 1;
  const ODPOVEDNY = 2;

  public function __construct($array = false)                     
  {
    if((is_array($array)) AND (count($array)==4))
    {
      $this->set_id($array["id"]);
      $this->set_uzivatel($array["uzivatele_id"]);
      $this->set_zakazka($array["zakazky_id"]);
      $this->set_opravneni($array["opravneni"]);
    }
    else
    {
      $this->id = 0;
      $this->uzivatel = 0;
      $this->zakazka = 0;
      $this->opravneni = 0;
    }    
  }

//------------------------------ public functions -------------------------------  

  public function set_id($id)
  {
    $this->id = $id;
  }
  public function set_uzivatel($uzivatel)
  {
    $this->uzivatel = $uzivatel;
  }
  public function set_zakazka($zakazka)
  {
    $this->zakazka = $zakazka;
  }
  public function set_opravneni($opravneni)
  {
    $this->opravneni = $opravneni;
  }
  
  public function get_id()
  {
    return $this->id;
  }
  public function get_uzivatel()
  {
    return $this->uzivatel;
  }
  public function get_zakazka()
  {
    return $this->zakazka;
  }
  public function get_opravneni()
  {
    return $this->opravneni;
  }
  
  public function get_options_opravneni()
  {
    $data = new Database();
    return $data->get_options_opravneni();
  }  
  public function get_opravneni_name()
  {
  	$data = new Database();
    if($obj = $data->get_opravneni($this->opravneni)) return $obj->get_name();
	  return "";
  }
    
  public function to_array()
  {
    $array["id"] = $this->id;
    $array["uzivatele_id"] = $this->uzivatel;
    $array["zakazky_id"] = $this->zakazka;
    $array["opravneni"] = $this->opravneni;
    return $array;
  }
  
  //------------------------------ private functions -------------------------------  
  
     
}// konec tridy
?>
