<?php
class SimpleLink
{
  
  public function __construct($name, $addr)
  {
    $this->set_name($name);
    $this->set_addr($addr);
  }
  public function set_name($name)
  {
    if($name == "") die("SimpleLink->set_name(\$name): Parametr \$name nesmi byt prazdny!");
    $this->name = $name;
  }
  public function set_addr($addr)
  {
    $this->addr = $addr;
  }
  
  public function get_name()
  {
    return $this->name;
  }
  public function get_addr()
  {
    return $this->addr;
  }
  
  public function is_valid()
  {
    if(is_file($this->addr)) return true;
    return false;
  }
  public function is_empty()
  {
    if($this->addr=="") return true;
    return false;
  }
  
} // end Class
?>
