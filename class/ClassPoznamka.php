<?php

class Poznamka
{
  private $id;
  private $tabulka;
  private $tabulka_id;
  private $text;
  private $stav;
  
  public function __construct($array = false)
  {
    if((is_array($array)) AND (count($array)==5))
    {
      $this->set_id($array["id"]);
      $this->set_nazev($array["tabulka"]);
      $this->set_popis($array["tabulka_id"]);
      $this->set_cinnost($array["text"]);
      $this->set_stav($array["stav"]);
    }
    else
    {
      die("Nespravny vstupni prarametr \$array!");  
    }
  }
  
//------------------------------ public functions -------------------------------  

  public function set_id($id)
  {
    $this->id = $id;
  }
  public function set_tabulka($tabulka)
  {
    $this->tabulka = $tabulka;
  }
  public function set_tabulka_id($tabulka_id)
  {
    $this->tabulka_id = $tabulka_id;
  }
  public function set_text($text)
  {
    $this->text = $text;
  }
  public function set_stav($stav)
  {
    $this->stav = $stav;
  }
  
  public function get_id()
  {
    return $this->id;
  }
  public function get_tabulka()
  {
    return $this->tabulka;
  }
  public function get_tabulka_id()
  {
    return $this->tabulka_id;
  }
  public function get_text()
  {
    return $this->text;
  }
  public function get_stav()
  {
    return $this->stav;
  }
  
  
//------------------------------ private functions -------------------------------  


}//konec tridy

?>
