<?php

class Dny
{
  private $data = false;
  

  public function __construct()
  {
    $this->data = new Database();
  }
  
  //public functions  
  
  public function get($uziv_id="", $zak_id="", $rok="", $mesic="")
  {    
    return $this->data->get_dny($uziv_id, $zak_id, $rok, $mesic);               
  }

  public function get_list($uziv_id="", $zak_id="", $rok="", $mesic="")   
  //vraci seznam, kde key = cislo dne, value = pocet hodin
  {
    if(is_array($this->get($uziv_id, $zak_id, $rok, $mesic))==false) return array();
    foreach ($this->get($uziv_id, $zak_id, $rok, $mesic) as $key => $values)
    {
      $list[$values["den"]] = $values["hodiny"];
    }
    return $list;
  }
  
  public function set_dny_by_uzivatel($dny) //ulozi vsechny dny z pole dny
  {
    $err = "";
    if(is_array($dny))
    {
      foreach ($dny as $key => $value)
      {
        if(is_valid_hodiny($value["hodiny"]))
        {
          $this->data->set_den($value["pole_id"],$value["id"],$value["rok"],$value["mesic"],$value["den"],$value["hodiny"]);
        }
        else 
        {
          $err = "Neplatné zadání hodin! (počet hodin <0,24>)";
        }           
      }  
    }
    return $err;
  }  
                                                        
  public function set_dny_by_zakazka($dny) //zatim nepouzivana (pouzit v pripade, ze budu chtit editovat tabulku pro zakazku)
  {
    if(is_array($dny))
    {
      foreach ($dny as $key => $value)
      {
        $this->$data->set_den($value);
      }  
    }
  }
    
}//konec tridy

?>
