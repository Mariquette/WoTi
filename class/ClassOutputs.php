<?php

class Outputs
{
  
  static function mesic2csv($radky,$nazev,$cinnost,$pocet_dnu)
  {
    //$echo = substr($cinnost,2).";".$nazev.";";
    $echo = $cinnost.";".$nazev.";";
    for($i=1;$i<=$pocet_dnu;$i++)
    {
      $soucet_sloupec[$i] = 0;
    }
    if(is_array($radky))
    {  
      foreach ($radky as $key=>$value)
      {
        $cinnost = "";
        if($value["cinnost"]!="")
        {
          $cinnost = substr($value["cinnost"],2).": ";
        }
        for($i=1;$i<=$pocet_dnu;$i++)
        {                 
          if(isset($value["dny"][$i]))
          {  
            $soucet_sloupec[$i] = $soucet_sloupec[$i] + $value["dny"][$i];  
          }
        }        
      }
    }  
    $echo .= "".array_sum($soucet_sloupec).";\n";
    return $echo;                                                                                                             
  }
    
}//konec tridy

?>
