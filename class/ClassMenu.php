<?php
class Menu
{
  
  private $items;
  private $items_styl;
  private $menu_styl;
                            
  public function __construct($menu_styl="menu")
  {
    $this->menu_styl = $menu_styl;
    $this->items = array();
  }
  
  public function set_active($item_name)
  {
  	foreach($this->items_styl as $name=>$val)
    {
  		if($name == $item_name) 
  		{ 
  			$this->items_styl[$name] = $this->menu_styl."Aktivni";  
  			//return true;
  		}
	  }      	
	 // jinak chyba	
 	  //return false;
  }

  public function add_item($item)
  {
    if(is_array($this->items))
    {
      foreach($this->items as $i)
      {
        if($i->is_empty())continue;
        if($i->get_name() == $item->get_name()) die("Menu->add_item(\$item): odkaz musi mit jedinecny nazev!(".$i->get_name().")"); 
      }
    }
    $this->items[] = $item;
    $this->items_styl[$item->get_name()] = $this->menu_styl;
  } 
    
  public function get_html()
  {  
    $html = '<div class="'.$this->menu_styl.'">';
    foreach($this->items as $item)
    {
      if($item->is_empty())
      {
        $html.='<span class="'.$this->items_styl[$item->get_name()].'">'.$item->get_name().'</span> ';
      }
      else
      {
        $html.='<a class="'.$this->items_styl[$item->get_name()].'" href="'.$item->get_addr().'">'.$item->get_name().'</a> ';      
      }                     
    }
    
    $html.= '</div>';
    
    return $html;
  }
} // end Class
?>
