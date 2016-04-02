<?php

class Views
{
  
  static function nabidka($mesic,$script,$vypln)
  /*
  nabidka odkazu predchozi, tento, dalsi, vypln
  */
  {
    $html = '<div class = "nabidka">'; 
    $html .= '<h2>'.$mesic->get_mesic().' / '.$mesic->get_rok().'</h2>
              <a class="nabidka" href = "'.$script.'&mesic='.$mesic->get_p_mesic().'&rok='.$mesic->get_p_rok().$vypln.'">Předchozí</a> |  
              <a class="nabidka" href = "'.$script.$vypln.'">Tento měsíc</a> | 
              <a class="nabidka" href = "'.$script.'&mesic='.$mesic->get_d_mesic().'&rok='.$mesic->get_d_rok().$vypln.'">Další</a> | 
              <a class="vypln" href = "'.$script.'&mesic='.$mesic->get_mesic().'&rok='.$mesic->get_rok().'&vypln">Vyplň</a>';
    $html .=  '</div>';
    return $html;
  }
  
  static function nabidka2($mesic,$script)
  /*
  nabidka odkazu predchozi, tento, dalsi
  */
  {
    $html = '<div class = "nabidka">'; 
    $html .= '<h2>'.$mesic->get_mesic().' / '.$mesic->get_rok().'</h2>
              <a class="nabidka" href = "'.$script.'&mesic='.$mesic->get_p_mesic().'&rok='.$mesic->get_p_rok().'">Předchozí</a> |  
              <a class="nabidka" href = "'.$script.'">Tento měsíc</a> | 
              <a class="nabidka" href = "'.$script.'&mesic='.$mesic->get_d_mesic().'&rok='.$mesic->get_d_rok().'">Další</a>'; 
    $html .=  '</div>';
    return $html;
  }

  static function zobraz_mesic($radky,$id,$nadpis,$pocet_dnu)
  /*
  tabulka mesic
  */
  {
    $html = '<table><tr><th class="nazev">'.$nadpis.'</th>';
    for($i=1;$i<=$pocet_dnu;$i++)
    {
      $html .= '<th class="den"><div class="den">'.$i.'</div></th>';
    }                                         
    $html .= '<th class="soucet"><div class="soucet"></div></th></tr>';
    for($i=1;$i<=$pocet_dnu;$i++)
    {
      $soucet_sloupec[$i] = 0;
    }     
    if(is_array($radky))
    {  
      foreach ($radky as $key=>$value)
      {
        $soucet_radek = 0; 
        $cinnost = "";
        if($value["cinnost"]!="")
        {
          $cinnost = substr($value["cinnost"],2).": ";
        }
        if($key%2) /*lichy radek*/
        {
          $html .= '<tr class="lichy"><td class="nazev" title="'.$value["popis"].'"><div class="nazev">'.$cinnost.$value["jmeno"].'</div></td>';
        }
        else /*sudy radek*/
        {
          $html .= '<tr class="sudy"><td class="nazev" title="'.$value["popis"].'"><div class="nazev">'.$cinnost.$value["jmeno"].'</div></td>'; 
        }
        for($i=1;$i<=$pocet_dnu;$i++)
        {                 
          if(isset($value["dny"][$i]))
          {  
            $html .= '<td class="den">'.$value["dny"][$i].'</td>';
            $soucet_sloupec[$i] = $soucet_sloupec[$i] + $value["dny"][$i];
            $soucet_radek = $soucet_radek + $value["dny"][$i];  
          }
          else
          {
            $html .= '<td class="den">-</td>';         
          }          
        }        
        $html .= '<td class="soucet">'.$soucet_radek.'</td></tr>';
      }
    }  
    $html .= '<tr><td></td>';
    for($i=1;$i<=$pocet_dnu;$i++)
    {
      $html .= '<td class="soucet">'.$soucet_sloupec[$i].'</td>';
    }                                         
    $html .= '<td class="soucetsoucet">'.array_sum($soucet_sloupec).'</td></tr></table>';
    return $html;                                                                                                             
  }
  
  static function vypln_mesic($mesic,$radky,$id,$nadpis,$pocet_dnu,$script)         
  /*
  formular mesic pro vyplneni
  */
  {
    $html = '<form action="'.$script.'" method="post"><table><tr><th class="nazev">'.$nadpis.'</th>';
    for($i=1;$i<=$pocet_dnu;$i++)
    {
      $html .= '<th class="den"><div class="den">'.$i.'</div></th>';
    } 
    $html .= '<th class="soucet"><div class="soucet"></div></th></tr>';
    for($i=1;$i<=$pocet_dnu;$i++)
    {
      $soucet_sloupec[$i] = 0;
    } 
    if(is_array($radky))
    { 
      foreach ($radky as $key=>$value)
      {
        $soucet_radek = 0; 
        $cinnost = "";
        if($value["cinnost"]!="")
        {
          $cinnost = substr($value["cinnost"],2).": ";
        }
        if($key%2) /*lichy radek*/
        {
          $html .= '<tr class="lichy_vypln"><td class="nazev" title="'.$value["popis"].'"><div class="nazev">'.$cinnost.$value["jmeno"].'</div></td>';
        }
        else /*sudy radek*/
        {
          $html .= '<tr class="sudy_vypln"><td class="nazev" title="'.$value["popis"].'"><div class="nazev">'.$cinnost.$value["jmeno"].'</div></td>'; 
        }
        for($i=1;$i<=$pocet_dnu;$i++)
        {                 
          if(isset($value["dny"][$i]))
          {  
            if($key%2) /*lichy radek*/
            {
              $html .= '<td class="den"><input class="lichy" type="text" maxlength="2" name="'.self::uprav_nazev($value["id"]).'['.$i.']" value="'.$value["dny"][$i].'"></td>'; 
            }
            else /*sudy radek*/
            {
              $html .= '<td class="den"><input class="sudy" type="text" maxlength="2" name="'.self::uprav_nazev($value["id"]).'['.$i.']" value="'.$value["dny"][$i].'"></td>';   
            }
            $soucet_sloupec[$i] = $soucet_sloupec[$i] + $value["dny"][$i];
            $soucet_radek = $soucet_radek + $value["dny"][$i]; 
          }
          else
          {
            if($key%2) /*lichy radek*/
            {
              $html .= '<td><input class="lichy" type="text" maxlength="2" name="'.self::uprav_nazev($value["id"]).'['.$i.']" value=""></td>';         
            }
            else /*sudy radek*/
            {
              $html .= '<td><input class="sudy" type="text" maxlength="2" name="'.self::uprav_nazev($value["id"]).'['.$i.']" value=""></td>';         
            }
          }
        }
        $html .= '<td class="soucet">'.$soucet_radek.'</td></tr>';
      }  
    }
    $html .= '<tr><td></td>';
    for($i=1;$i<=$pocet_dnu;$i++)
    {
      $html .= '<td class="soucet">'.$soucet_sloupec[$i].'</td>';
    } 
    $html .= '<td class="soucetsoucet">'.array_sum($soucet_sloupec).'</td></tr></table>
      <input type="hidden" name="mesic" value="'.$mesic->get_mesic().'">
      <input type="hidden" name="rok" value="'.$mesic->get_rok().'">
      <input type="hidden" name="id" value="'.$id.'">
      <input type="hidden" name="token" value="'.Util::get_token().'">
      <input type="submit" name="odeslano" value="odeslat"></form>';
    $html .= '<a href = "'.$script.'?mesic='.$mesic->get_mesic().'&rok='.$mesic->get_rok().'">Zpět</a>';  
    return $html;
  }

  static function uprav_nazev($jmeno)
  /*
  vlozi pred retezec "x_"
  */
  {
    $jmeno = str_replace(" ","_",$jmeno);
    $jmeno = str_replace("\"","_",$jmeno);
    $jmeno = str_replace("/","_",$jmeno);
    return "x_".$jmeno;
  }
    
}//konec tridy

?>
