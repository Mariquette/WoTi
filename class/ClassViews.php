<?php

class Views
{
  
  static function nabidka_rok($mesic,$script)
  /*
  nabidka odkazu predchozi, tento, dalsi
  */
  {
    $html = '<div class = "nabidka">
      <a class="nabidka prev" href = "'.$script.'&mesic='.$mesic->get_mesic().'&rok='.$mesic->get_p_rok().'"></a>
      <span class="aktual">'.$mesic->get_rok().'</span>
      <a class="nabidka next" href = "'.$script.'&mesic='.$mesic->get_mesic().'&rok='.$mesic->get_d_rok().'"></a>
    </div>';
    return $html;
  }
  
  static function nabidka_rok_old($mesic,$script)
  /*
  nabidka odkazu predchozi, tento, dalsi
  DEPRECATED
  */
  {
    $html = '<div class = "nabidka">
      <span class="nabidka"><a class="nabidka" href = "'.$script.'&mesic='.$mesic->get_mesic().'&rok='.$mesic->get_p_rok().'">Předchozí</a></span>
      <span class="oddelovac">|</span>  
      <span class="aktual">'.$mesic->get_rok().'</span>
      <span class="oddelovac">|</span>
      <span class="nabidka"><a class="nabidka" href = "'.$script.'&mesic='.$mesic->get_mesic().'&rok='.$mesic->get_d_rok().'">Další</a></span>
      <span class="oddelovac">||</span>
      <span class="nabidka"><a class="nabidka" href = "'.$script.'&mesic='.$mesic->get_mesic().'">Aktuální rok</a></span>
    </div>';
    return $html;
  }
  
  static function nabidka_mesic($mesic,$script)
  /*
  nabidka odkazu predchozi, tento, dalsi, vypln
  */
  {
    $html = '<div class = "nabidka">
      <a class="nabidka prev" href = "'.$script.'&mesic='.$mesic->get_p_mesic().'&rok='.$mesic->get_p_mesic_rok().'"></a>
      <span class="aktual">'.$mesic->get_mesic().' / '.$mesic->get_rok().'</span>
      <a class="nabidka next" href = "'.$script.'&mesic='.$mesic->get_d_mesic().'&rok='.$mesic->get_d_mesic_rok().'"></a>
    </div>'; 
    return $html;
  }
  
  static function nabidka_mesic_old($mesic,$script)
  /*
  nabidka odkazu predchozi, tento, dalsi, vypln
  DEPRECATED
  */
  {
    $html = '<div class = "nabidka">
      <span class="nabidka"><a class="nabidka" href = "'.$script.'&mesic='.$mesic->get_p_mesic().'&rok='.$mesic->get_p_mesic_rok().'">Předchozí</a></span>  
      <span class="oddelovac">|</span>  
      <span class="aktual">'.$mesic->get_mesic().' / '.$mesic->get_rok().'</span>
      <span class="oddelovac">|</span>  
      <span class="nabidka"><a class="nabidka" href = "'.$script.'&mesic='.$mesic->get_d_mesic().'&rok='.$mesic->get_d_mesic_rok().'">Další</a></span> 
      <span class="oddelovac">||</span>  
      <span class="nabidka"><a class="nabidka" href = "'.$script.'">Aktuální měsíc</a></span>
    </div>';
    return $html;
  }
  
  static function zobraz_mesic_4zakazka($mesic,$zakazka,$uzivatele,$hodiny,$soucet,$script)
  /*
  tabulka daneho mesice
  zakazky = seznam zakazek (radky tabulky)
  uzivatel = vybrany uzivatel
  hodiny - pole hodin, id je poradove cislo dne
  */
  {
    $mesice = Database::get_names_mesic();
    $dny = Database::get_names_dny();
    $svatky = Database::get_svatky($mesic->get_rok());
    $class = array();
    for($i=1; $i<=$mesic->pocet_dnu(); $i++)
    {
      $class[$i] = "kalendar";
      if (!(($i+$mesic->prvni_den())%7)) $class[$i] = "sobota";
      elseif (!(($i+$mesic->prvni_den()-1)%7)) $class[$i] = "nedele";
      foreach($svatky as $svatek)
      if ( ($i==$svatek[0]) AND ($mesic->get_mesic()==$svatek[1]) ) $class[$i] = "svatek";
      $soucet_sloupec[$i] = 0;
    }  
//<span class="list"><a class="list" href="./zakazky.php?detail='.$obj->get_id().'">'.$obj->get_nazev().'</a></span>
    $html = '<table class="kalendar">
    <caption><span class="aktual"><a class="list" href="./zakazky.php?detail='.$zakazka->get_id().'">'.$zakazka->get_nazev().'</a></span>
             <span class="aktual">Celkem hodin na zakázce: '.$soucet.'</span>
    </caption>
    <tr><th class="kalendar">Příjmení</th>';
    for($i=1; $i<=$mesic->pocet_dnu(); $i++)
    {
      $html .= '<th class="'.$class[$i].'">'.$i.'</th>';
    }                                         
    $html .= '<th class="kalendar"></th></tr>';
    $zvyrazneni = true;
    foreach ($uzivatele as $id => $obj)
    {
      $soucet_radek = 0; 
      if ($zvyrazneni) $html .= '<tr><th class="kalendar2" title="">'.$obj->get_prijmeni().'</th>';
      else $html .= '<tr><th class="kalendar" title="">'.$obj->get_prijmeni().'</th>';
      for($i=1; $i<=$mesic->pocet_dnu(); $i++)
      {                 
        if(isset($hodiny[$i][$obj->get_id()]))
        {  
          if($zvyrazneni) $html .= '<td class="'.$class[$i].'2">'.$hodiny[$i][$obj->get_id()].'</td>';
          else $html .= '<td class="'.$class[$i].'">'.$hodiny[$i][$obj->get_id()].'</td>'; 
          $soucet_sloupec[$i] = $soucet_sloupec[$i] + $hodiny[$i][$obj->get_id()];
          $soucet_radek = $soucet_radek + $hodiny[$i][$obj->get_id()];  
        }
        else
        {
          if($zvyrazneni) $html .= '<td class="'.$class[$i].'2">-</td>';
          else $html .= '<td class="'.$class[$i].'">-</td>';
        }          
      }        
      $html .= '<th class="kalendar soucet">'.$soucet_radek.'</th></tr>';
      $zvyrazneni = !$zvyrazneni;
    }
    $html .= '<tr><th class="kalendar"></th>';
    for($i=1; $i<=$mesic->pocet_dnu(); $i++)
    {
      $html .= '<th class="kalendar soucet">'.$soucet_sloupec[$i].'</th>';
    }                                         
    $html .= '<th class="kalendar soucet">'.array_sum($soucet_sloupec).'</th>
    </tr>
    </table>';
    return $html;                                                                                                             
  }
  
  static function zobraz_mesic_4uzivatel($mesic,$uzivatel,$zakazky,$hodiny,$script,$ownwt)
  /*
  tabulka daneho mesice
  zakazky = seznam zakazek (radky tabulky)
  uzivatel = vybrany uzivatel - pokud je NULL, nezobrazuje se jmeno v nadpisu
  hodiny - pole hodin, id je poradove cislo dne
  */
  {
    $dny = Database::get_names_dny();
    $svatky = Database::get_svatky($mesic->get_rok());
    $class = array();
    for($i=1; $i<=$mesic->pocet_dnu(); $i++)
    {
      $class[$i] = "kalendar";
      if (!(($i+$mesic->prvni_den())%7)) $class[$i] = "sobota";
      elseif (!(($i+$mesic->prvni_den()-1)%7)) $class[$i] = "nedele";
      foreach($svatky as $svatek)
      if ( ($i==$svatek[0]) AND ($mesic->get_mesic()==$svatek[1]) ) $class[$i] = "svatek";
      $soucet_sloupec[$i] = 0;
    }  
    $html = '<table class="kalendar">
    <caption>';
      if($uzivatel!=NULL) 
      {
        //$html .= '<a class="submit" href = "'.$script.'&mesic='.$mesic->get_mesic().'&rok='.$mesic->get_rok().'">Zpět</a>';
        $html .= '<span class="aktual">'.$uzivatel->get_prijmeni()." ".$uzivatel->get_jmeno().'</span>';
        $html .= '<span class="aktual">'.self::nabidka_mesic($mesic,$script.'&uzivatel='.$uzivatel->get_id()).'</span>';
      }
      else $html .= '<span class="aktual">'.self::nabidka_mesic($mesic,$script).'</span>';
      if($ownwt) 
      {
        if($mesic->get_stav()==Mesic::OTEVRENO) 
        {
          $html .= '<a class="submit1" href = "'.$script.'&mesic='.$mesic->get_mesic().'&rok='.$mesic->get_rok().'&vypln">Vyplnit</a>';
          $html .= '<a class="submitg" href = "'.$script.'&mesic='.$mesic->get_mesic().'&rok='.$mesic->get_rok().'&zavrit">Zavřít</a>';
        }
        if($mesic->get_stav()==Mesic::UZAVRENO) $html .= '<span class="green">Uzavřeno</span>';
        if($mesic->get_stav()==Mesic::ZAMCENO) $html .= '<span class="red">Zamčeno</span>';
      }
      else
      {
        if($mesic->get_stav()==Mesic::UZAVRENO) 
        {
          $html .= '<a class="submitr" href = "'.$script.'&mesic='.$mesic->get_mesic().'&rok='.$mesic->get_rok().'&uzivatel='.$uzivatel->get_id().'&zamknout">Zamknout</a>';
          $html .= '<a class="submit1" href = "'.$script.'&mesic='.$mesic->get_mesic().'&rok='.$mesic->get_rok().'&uzivatel='.$uzivatel->get_id().'&otevrit">Otevřít</a>';
        }
        if($mesic->get_stav()==Mesic::ZAMCENO) $html .= '<span class="red">Zamčeno</span>';
      }
    $html .= '</caption>
    <tr>
    <th class="kalendar">Název</th>
    <th class="kalendar">Kategorie</th>';
    for($i=1; $i<=$mesic->pocet_dnu(); $i++)
    {
      $html .= '<th class="'.$class[$i].'">'.$i.'</th>';
    }                                         
    $html .= '<th class="kalendar"></th></tr>';
    $zvyrazneni = true;
    foreach ($zakazky as $id => $obj)
    {
      $soucet_radek = 0; 
      if ($zvyrazneni) $html .= '<tr><th class="kalendar2" title="'.$obj->get_popis().'">'.$obj->get_nazev().'</th>
        <th class="kalendar2" title="'.$obj->get_popis().'">'.$obj->get_kategorie_name().'</th>';
      else $html .= '<tr><th class="kalendar" title="'.$obj->get_popis().'">'.$obj->get_nazev().'</th>
        <th class="kalendar" title="'.$obj->get_popis().'">'.$obj->get_kategorie_name().'</th>';
      for($i=1; $i<=$mesic->pocet_dnu(); $i++)
      {                 
        if(isset($hodiny[$i][$obj->get_id()]))
        {  
          if($zvyrazneni) $html .= '<td class="'.$class[$i].'2">'.$hodiny[$i][$obj->get_id()].'</td>';
          else $html .= '<td class="'.$class[$i].'">'.$hodiny[$i][$obj->get_id()].'</td>'; 
          $soucet_sloupec[$i] = $soucet_sloupec[$i] + $hodiny[$i][$obj->get_id()];
          $soucet_radek = $soucet_radek + $hodiny[$i][$obj->get_id()];  
        }
        else
        {
          if($zvyrazneni) $html .= '<td class="'.$class[$i].'2">-</td>';
          else $html .= '<td class="'.$class[$i].'">-</td>';
        }          
      }        
      $html .= '<th class="kalendar soucet">'.$soucet_radek.'</th></tr>';
      $zvyrazneni = !$zvyrazneni;
    }
    $html .= '<tr><th class="kalendar"></th><th class="kalendar"></th>';
    for($i=1; $i<=$mesic->pocet_dnu(); $i++)
    {
      $html .= '<th class="kalendar soucet">'.$soucet_sloupec[$i].'</th>';
    }                                         
    $html .= '<th class="kalendar soucet">'.array_sum($soucet_sloupec).'</th>
    </tr>
    </table>';
    return $html;                                                                                                             
  }
  
  static function vypln_mesic($mesic,$uzivatel,$zakazky,$hodiny,$script)         
  /*
  tabulka daneho mesice - formular pro vyplneni
  zakazky = seznam zakazek (radky tabulky)
  uzivatel = vybrany uzivatel
  hodiny - pole hodin, id je poradove cislo dne
  */
  {
    $mesice = Database::get_names_mesic();
    $dny = Database::get_names_dny();
    $svatky = Database::get_svatky($mesic->get_rok());
    $class = array();
    for($i=1; $i<=$mesic->pocet_dnu(); $i++)
    {
      $class[$i] = "kalendar";
      if (!(($i+$mesic->prvni_den())%7)) $class[$i] = "sobota";
      elseif (!(($i+$mesic->prvni_den()-1)%7)) $class[$i] = "nedele";
      foreach($svatky as $svatek)
      if ( ($i==$svatek[0]) AND ($mesic->get_mesic()==$svatek[1]) ) $class[$i] = "svatek";
      $soucet_sloupec[$i] = 0;
    }  
    $html = '<form action="'.$script.'" method="post">
    <table class="kalendar">
    <caption><span class="aktual">'.$mesice[$mesic->get_mesic()].' '.$mesic->get_rok().'</span>
             <input class="submit" type="submit" name="ulozit" value="Uložit">
             <a class="submit" href="'.$script.'&mesic='.$mesic->get_mesic().'&rok='.$mesic->get_rok().'&uzivatel='.$uzivatel->get_id().'">Storno</a>
    </caption>
    <tr><th class="kalendar"></th><th class="kalendar"></th>';
    for($i=1; $i<=$mesic->pocet_dnu(); $i++)
    {
      $html .= '<th class="'.$class[$i].'">'.$i.'</th>';
    }                                         
    $html .= '<th class="kalendar"></th></tr>';
    $zvyrazneni = true;
    foreach ($zakazky as $id => $obj)
    {
      $soucet_radek = 0; 
      if ($zvyrazneni) $html .= '<tr><th class="kalendar2" title="'.$obj->get_popis().'">'.$obj->get_nazev().'</th>
        <th class="kalendar2" title="'.$obj->get_popis().'">'.$obj->get_kategorie_name().'</th>';
      else $html .= '<tr><th class="kalendar" title="'.$obj->get_popis().'">'.$obj->get_nazev().'</th>
        <th class="kalendar" title="'.$obj->get_popis().'">'.$obj->get_kategorie_name().'</th>';
      for($i=1; $i<=$mesic->pocet_dnu(); $i++)
      {                 
        if(isset($hodiny[$i][$obj->get_id()]))
        {  
          if($zvyrazneni) $html .= '<td class="'.$class[$i].'2"><input class="kalendar" type="text" maxlength="2" name="'.Util::uprav_nazev($obj->get_id()).'['.$i.']" value="'.$hodiny[$i][$obj->get_id()].'"></td>';
          else $html .= '<td class="'.$class[$i].'"><input class="kalendar" type="text" maxlength="2" name="'.Util::uprav_nazev($obj->get_id()).'['.$i.']" value="'.$hodiny[$i][$obj->get_id()].'"></td>';
          $soucet_sloupec[$i] = $soucet_sloupec[$i] + $hodiny[$i][$obj->get_id()];
          $soucet_radek = $soucet_radek + $hodiny[$i][$obj->get_id()];  
        }
        else
        {
          if($zvyrazneni) $html .= '<td class="'.$class[$i].'2"><input class="kalendar" type="text" maxlength="2" name="'.Util::uprav_nazev($obj->get_id()).'['.$i.']" value=""></td>';
          else $html .= '<td class='.$class[$i].'><input class="kalendar" type="text" maxlength="2" name="'.Util::uprav_nazev($obj->get_id()).'['.$i.']" value=""></td>';
        }          
      }        
      $html .= '<th class="kalendar soucet">'.$soucet_radek.'</th></tr>';
      $zvyrazneni = !$zvyrazneni;
    }
    $html .= '<tr><th class="kalendar"></th><th class="kalendar"></th>';
    for($i=1; $i<=$mesic->pocet_dnu(); $i++)
    {
      $html .= '<th class="kalendar soucet">'.$soucet_sloupec[$i].'</th>';
    }                                         
    $html .= '<th class="kalendar soucet">'.array_sum($soucet_sloupec).'</th>
    </tr>
    </table>
    <input type="hidden" name="mesic" value="'.$mesic->get_mesic().'">
    <input type="hidden" name="rok" value="'.$mesic->get_rok().'">
    <input type="hidden" name="uzivatel" value="'.$uzivatel->get_id().'">
    <input type="hidden" name="token" value="'.Util::get_token().'">
    </form>';
    return $html;
  }

  static function potvrzeni_zavreni($mesic,$script)
  /*
  formular pro prihlasovani uzivatelu - zobrazeni pouze aktivni
  $pole = seznam objektu k zobrazeni
  $uzivatel = id v objektu uzivatel
  */
  {  
    $html = '<form action="'.$script.'&mesic='.$mesic->get_mesic().'&rok='.$mesic->get_rok().'&zavrit" method="post">
    <div><span class="infoerr">Po uzavření měsíce nebude možné údaje měnit!<span>
    </div>
    <div><span class="aktual">Opravdu chcete uzavřít měsíc '.$mesic->get_mesic().' / '.$mesic->get_rok().'?</span>
    </div>
    <div class="submit"><input class="submit" type="submit" name="ok" value="OK">
                        <input class="submit" type="submit" name="storno" value="Storno">
                        <input type="hidden" name="token" value="'.Util::get_token().'">
    </div>
    </form>';
    return $html;
  }

  static function potvrzeni_otevreni($uzivatel,$mesic,$script)
  /*
  formular pro prihlasovani uzivatelu - zobrazeni pouze aktivni
  $pole = seznam objektu k zobrazeni
  $uzivatel = id v objektu uzivatel
  */
  {  
    $html = '<form action="'.$script.'&mesic='.$mesic->get_mesic().'&rok='.$mesic->get_rok().'&uzivatel='.$uzivatel->get_id().'&otevrit" method="post">
    <div><span class="infoerr">Otevřením daného měsíce umožníte uživateli dělat další změny!<span>
    </div>
    <div><span class="aktual">Opravdu chcete znovu otevřít měsíc '.$mesic->get_mesic().' / '.$mesic->get_rok().' pro uživatele '.$uzivatel->get_jmeno().' '.$uzivatel->get_prijmeni().'?</span>
    </div>
    <div class="submit"><input class="submit" type="submit" name="ok" value="OK">
                        <input class="submit" type="submit" name="storno" value="Storno">
                        <input type="hidden" name="token" value="'.Util::get_token().'">
    </div>
    </form>';
    return $html;
  }

  static function potvrzeni_zamceni($uzivatel,$mesic,$script)
  /*
  formular pro prihlasovani uzivatelu - zobrazeni pouze aktivni
  $pole = seznam objektu k zobrazeni
  $uzivatel = id v objektu uzivatel
  */
  {  
    $html = '<form action="'.$script.'&mesic='.$mesic->get_mesic().'&rok='.$mesic->get_rok().'&uzivatel='.$uzivatel->get_id().'&zamknout" method="post">
    <div><span class="infoerr">Po zamčení již nebude možné otevřít měsíc uživateli pro změny!<span>
    </div>
    <div><span class="aktual">Opravdu chcete trvale zamknout měsíc '.$mesic->get_mesic().' / '.$mesic->get_rok().' pro uživatele '.$uzivatel->get_jmeno().' '.$uzivatel->get_prijmeni().'?</span>
    </div>
    <div class="submit"><input class="submit" type="submit" name="ok" value="OK">
                        <input class="submit" type="submit" name="storno" value="Storno">
                        <input type="hidden" name="token" value="'.Util::get_token().'">
    </div>
    </form>';
    return $html;
  }

  static function kalendar($mesic,$script)
  /*
  vertikalni kalendar - dny v tydnu jsou sloupce
  */
  {
    $mesice = Database::get_names_mesic();
    $dny = Database::get_names_dny();
    $svatky = Database::get_svatky($mesic->get_rok());
    $html = '<div><table>
    <caption>'.self::nabidka_mesic($mesic,$script).'</caption>
    <tr>';
    foreach ($dny as $den)
    {
      $html .= '<th class="kalendar">'.$den.'</th>';
    }
    $html .= '</tr>';
    for ($radek=1; $radek<=$mesic->pocet_tydnu(); $radek++)
    {
      $html .= '<tr>';
      for ($sloupec=1; $sloupec<=count($dny); $sloupec++) 
      {
        $cislo = ($radek-1)*count($dny)+$sloupec-$mesic->prvni_den()+1;
        $class = "kalendar";
        if ($sloupec==6) $class = "sobota";
        elseif ($sloupec==7) $class = "nedele";
        foreach($svatky as $svatek)
        {
          if ( ($cislo==$svatek[0]) AND ($mesic->get_mesic()==$svatek[1]) ) $class = "svatek";
        }
        if ($cislo>=1 && $cislo<=$mesic->pocet_dnu()) $html .= '<td class='.$class.'>'.$cislo.'</td>';
        else $html .= '<td class="kalendar"></td>';  
      }
      $html .= '</tr>';
    }
    $html .= '</table></div>';
    return $html;
  }
  
  static function kalendar1($mesic,$script)
  /*
  horizontalni kalendar - dny v tydnu jsou radky
  */
  {
    $mesice = Database::get_names_mesic();
    $dny = Database::get_names_dny();
    $svatky = Database::get_svatky($mesic->get_rok());
    $html = '<div><table>
    <caption>'.self::nabidka_mesic($mesic,$script).'</caption>';
    for ($radek=1; $radek<=count($dny); $radek++)
    {
      $html .= '<tr>';
      for ($sloupec=1; $sloupec<=$mesic->pocet_tydnu(); $sloupec++) 
      {
        if ($sloupec==1) $html .= '<th class="kalendar">'.$dny[$radek].'</th>';
        $cislo = ($sloupec-1)*count($dny)+$radek-$mesic->prvni_den()+1;
        $class = "kalendar";
        if ($radek==6) $class = "sobota";
        elseif ($radek==7) $class = "nedele";
        foreach($svatky as $svatek)
        {
          if ( ($cislo==$svatek[0]) AND ($mesic->get_mesic()==$svatek[1]) ) $class = "svatek";
        }
        if ($cislo>=1 && $cislo<=$mesic->pocet_dnu()) $html .= '<td class='.$class.'>'.$cislo.'</td>';
        else $html .= '<td class="kalendar"></td>';  
      }
      $html .= '</tr>';
    }
    $html .= '</table></div>';
    return $html;
  }

  static function get_list($list,$script)
  /*
  zobrazeni seznamu mesicu se vsemi info
  */
  {
    $html = '<div><table>
    <tr><th class="list"><span class="razeni">Rok</span></th>
        <th class="list"><span class="razeni">Měsíc</span></th>
        <th class="list"><span class="razeni">Uživatel</span></th>
        <th class="list"><span class="razeni">Stav</span></th>
    </tr>';
    if(is_array($list))
    {    
      $mesice = Database::get_names_mesic();
      foreach ($list as $id => $obj)
      {
        $html .= '<tr><td class="list"><span class="list">'.$obj->get_rok().'</span></td>
                      <td class="list"><span class="list"><a class="list" href="'.$script.'&mesic='.$obj->get_mesic().'&rok='.$obj->get_rok().'">'.$mesice[$obj->get_mesic()].'</a></span></td> 
                      <td class="list"><span class="list">'.$obj->get_uzivatel().'</span></td>
                      <td class="list"><span class="list">'.$obj->get_stav_name($obj->get_stav()).'</span></td>
                  </tr>';
      }
    } 
    $html .= '</table></div>';
    return $html;
  }
  
  static function get_list_4uzivatel($list,$mesic,$script)
  /*
  zobrazeni seznamu mesicu pro daneho uzivatele, v danem roce
  DEPRECATED
  */
  {
    $html = '<fieldset>
    <legend>'.self::nabidka_rok($mesic,$script).'</legend>';
    $mesice = Database::get_names_mesic();
    for($i=1;$i<=12;$i++)
    {    
      if(is_array($list))
      {
        $odkaz = '';
        foreach ($list as $id => $obj)
        {
          if ($obj->get_mesic() == $i)
          {
            $odkaz = '<div class="hlist"><a class="hlist" href="'.$script.'&mesic='.$obj->get_mesic().'&rok='.$obj->get_rok().'">'.$mesice[$obj->get_mesic()].'</a>';
            if ($obj->get_stav() > 0) $odkaz .= '<img src="images/lock-r-l.png">';
            $odkaz .= '</div>';
          }
        }
        if ($odkaz == '') $odkaz .= '<div class="hlist"><a class="hlist" href="'.$script.'&mesic='.$i.'&rok='.$mesic->get_rok().'">'.$mesice[$i].'</a></div>';
        $html .= $odkaz;
      }
    } 
    $html .= '</fieldset>';
    return $html;
  }
  
  static function get_list_4mesic($list,$mesic,$uzivatele,$script)
  /*
  zobrazeni seznamu uzivatelu v danem mesici, roce
  DEPRECATED
  */
  {
    $html = '<fieldset>
    <legend>'.self::nabidka_mesic($mesic,$script).'</legend>';
    foreach ($uzivatele as $id => $uzivatel)
    {    
      if(is_array($list))
      {
        $odkaz = '';
        foreach ($list as $id => $obj)
        {
          if ($obj->get_uzivatel() == $uzivatel->get_id())
          {
            $odkaz = '<div class="vlist"><a class="vlist" href="'.$script.'&mesic='.$obj->get_mesic().'&rok='.$obj->get_rok().'&uzivatel='.$uzivatel->get_id().'">'.$uzivatel->get_prijmeni().'</a>';
            if ($obj->get_stav() == 2) $odkaz .= '<img src = "images/lock-r-l.png">';
            if ($obj->get_stav() == 1) $odkaz .= '<img src = "images/lock-g-l.png">';
            $odkaz .= '</div>';
          }
        }
        if ($odkaz == '') $odkaz .= '<div class="vlist"><a class="vlist" href="'.$script.'&mesic='.$mesic->get_mesic().'&rok='.$mesic->get_rok().'&uzivatel='.$uzivatel->get_id().'">'.$uzivatel->get_prijmeni().'</a></div>';
        $html .= $odkaz;
      }
    } 
    $html .= '</fieldset>';
    $html .= '<div class="vlist">Legenda: <img src="images/lock-r-l.png">'.Mesic::stav_2_name(2).' | <img src="images/lock-g-l.png">'.Mesic::stav_2_name(1).'</div>';    
    return $html;
  }
  
  static function get_list_uzivatele($list,$mesic,$uzivatele,$script)
  /*
  $list ... seznam objektu mesic
  zobrazeni seznamu uzivatelu v danem mesici, roce
  */
  {
    $html = '<fieldset>
    <legend>'.self::nabidka_mesic($mesic,$script).'</legend>';
    $html .= '<table>';
    foreach ($uzivatele as $id => $uzivatel)
    {    
      if(is_array($list))
      {
        $html .= '<tr>';
        $odkaz = '';
        foreach ($list as $id => $obj)
        {
          if ($obj->get_uzivatel() == $uzivatel->get_id())
          {
              if ($obj->get_stav() == 2) $odkaz .= '<td class="vlist"><img src = "images/lock-r-l.png"></td>';
              elseif ($obj->get_stav() == 1) $odkaz .= '<td class="vlist"><img src = "images/lock-g-l.png"></td>';
              else $odkaz .= '<td class="vlist"></td>';
              $odkaz .= '<td class="vlist"><a class="vlist" href="'.$script.'&mesic='.$obj->get_mesic().'&rok='.$obj->get_rok().'&uzivatel='.$uzivatel->get_id().'">'.$uzivatel->get_prijmeni().'</a></td>';
          }
        }
        if ($odkaz == '')
        {
          if ($mesic->get_rok()<2017) $odkaz .= '<td class="vlist"><img src = "images/lock-r-l.png"></td>';
          else $odkaz .= '<td class="vlist"></td>';
          $odkaz .= '<td class="vlist"><a class="vlist" href="'.$script.'&mesic='.$mesic->get_mesic().'&rok='.$mesic->get_rok().'&uzivatel='.$uzivatel->get_id().'">'.$uzivatel->get_prijmeni().'</a></td>';
        }
        $html .= $odkaz.'</tr>';
      }
    } 
    $html .= '</table>
    </fieldset>
    <div class="vlist">Legenda: <img src="images/lock-r-l.png">'.Mesic::stav_2_name(2).' | <img src="images/lock-g-l.png">'.Mesic::stav_2_name(1).'</div>';    
    return $html;
  }
  
  static function get_list_opravneni($list)
  /*
  zobrazeni vsech opravneni
  */
  {
    $html = '<div><table>
    <tr><th class="list"><span class="razeni">Uživatel</span></th>
        <th class="list"><span class="razeni">Zakázka</span></th>
        <th class="list"><span class="razeni">Oprávnění</span></th>
    </tr>';
    if(is_array($list))
    {    
      foreach ($list as $id => $obj)
      {
        $html .= '<tr><td class="list"><span class="list">'.$obj->get_uzivatel().'</span></td>
                      <td class="list"><span class="list">'.$obj->get_zakazka().'</span></td>
                      <td class="list"><span class="list">'.$obj->get_opravneni_name($obj->get_opravneni()).'</span></td>
                  </tr>';
      }
    } 
    $html .= '</table></div>';
    return $html;
  }
      
}//konec tridy

?>
