<?php

class Zakazka
{
  private $id;
  private $nazev;
  private $popis;
  private $cinnost;
  private $stav;
  
  public static $columns = array(
    "id" => 0,
    "nazev" => 1,
    "popis" => 2,
    "cinnost" => 3,
    "stav" => 4
    );
  
  public function __construct($array = false)
  {
    if($array == false)
    {
      $this->id = 0;
      $this->nazev = "";
      $this->popis = "";
      $this->cinnost = "";
      $this->stav = 1;
    }
    else
    {
      $this->from_array($array);
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
  
  public function from_array($array)
  /*
  naplni promenne z pole
  POCET PRVKU POLE !!
  */
  {
    if((is_array($array)) AND (count($array)==5))
    //if((is_array($array)) AND (count($array)==count(self::$columns)))
    {
      $this->set_id($array["id"]);
      $this->set_nazev($array["nazev"]);
      $this->set_popis($array["popis"]);
      $this->set_cinnost($array["cinnost"]);
      $this->set_stav($array["stav"]);
      /*
      $this->set_id($array[self::$columns["id"]]);
      $this->set_nazev($array[self::$columns["nazev"]]);
      $this->set_popis($array[self::$columns["popis"]]);
      $this->set_cinnost($array[self::$columns["cinnost"]]);
      $this->set_stav($array[self::$columns["stav"]]);
      */
      return true;
    }
    return false;
    //vypis chyby 
  }
  public function to_array()
  /*
  vytvori pole z promennych
  */
  {
    $array[self::$columns["id"]] = $this->id;
    $array[self::$columns["nazev"]] = $this->nazev;
    $array[self::$columns["popis"]] = $this->popis;
    $array[self::$columns["cinnost"]] = $this->cinnost;
    $array[self::$columns["stav"]] = $this->stav;
    return $array;
  }


  
//------------------------------ private functions -------------------------------  


}//konec tridy

?>
