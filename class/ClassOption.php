<?php

class Option
{
  
  public function __construct($name, $id)
  {
    $this->set_name($name);
    $this->set_id($id);
  }

//------------------------------ public functions -------------------------------  

  public function set_name($name)
  {
    $this->name = $name;
  }
  public function set_id($id)
  {
    $this->id = $id;
  }
  
  public function get_name()
  {
    return $this->name;
  }
  public function get_id()
  {
    return $this->id;
  }

//------------------------------ private functions -------------------------------  

}//konec tridy

?>