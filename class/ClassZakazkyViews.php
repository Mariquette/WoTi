<?php

class ZakazkyViews
{
  
  static function get_list($list,$script)
  /*
  zobrazeni seznamu zakazek, moznost razeni
  */
  {
    $html = '<div><table class="list">
    <tr><th class="list"><span class="list"><a class="razeni" href="'.$script.'&order=nazev">Název</a></span></th>
        <th class="list"><span class="list"><a class="razeni" href="'.$script.'&order=popis">Popis</a></span></th>
        <th class="list"><span class="list"><a class="razeni" href="'.$script.'&order=obdobi">Období</a></span></th>
        <th class="list"><span class="list"><a class="razeni" href="'.$script.'&order=kategorie">Kategorie</a></span></th>
        <th class="list"><span class="">Odpovědné osoby</span></th>
        <th class="list"><span class="list"><a class="razeni" href="'.$script.'&order=stav">Stav</a></span></th>
    </tr>';
    if(is_array($list))
    {    
      $i = 0;
      $styl_suda = "";
      foreach ($list as $id => $obj)
      {
      	$i++;
      	if($i%2)
      	{
      	  $styl_suda = "zvyrazneni";
      	}
      	else
      	{
      	  $styl_suda = "";
      	}
        $html .= '<tr class="'.$styl_suda.'"><td class="list"><span class="list"><a class="list" href="./zakazky.php?detail='.$obj->get_id().'">'.$obj->get_nazev().'</a></span></td>
                      <td class="list"><span class="list">'.$obj->get_popis(30).'</span></td>
                      <td class="list"><span class="list">'.$obj->get_obdobi(30).'</span></td>
                      <td class="list"><span class="list">'.$obj->get_kategorie_name($obj->get_kategorie()).'</span></td>';
                      $osoby = "";
                      foreach ($obj->get_odpovedny() as $osoba)
                      {
                        $osoby .= $osoba->get_zkratka().", ";
                      }
                      if($osoby != "") $osoby = substr($osoby,0,-2);
        $html .= '<td class="list"><span class="list">'.$osoby.'</span></td>
                  <td class="list"><span class="list">'.$obj->get_stav_name($obj->get_stav()).'</span></td>
                  </tr>';
      }
    } 
    $html .= '</table></div>';
    return $html;
  }
  
  static function detail($obj,$script,$admin=false)
  /*
  zobrazeni detailnich informaci o zakazce $obj
  $pracovnici,$editori,$odpovedni = seznamy vsech uzivatelu, kteri maji k zakazce dany vztah
  */
  {
    $edit = "";
    if($admin == true) $edit = ': <a class="link" href="'.$script.'?edit='.$obj->get_id().'">Editovat</a>';

    $html = '<fieldset><legend>Detail zakázky'.$edit.'</legend>
    <table>
    <tr><td class="label"><span class="label">Název:</span></td>
        <td class="detail"><span class="detail">'.$obj->get_nazev().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Popis:</span></td>
        <td class="detail"><span class="detail">'.$obj->get_popis().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Období:</span></td>
        <td class="detail"><span class="detail">'.$obj->get_obdobi().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Kategorie:</span></td>
        <td class="detail"><span class="detail">'.$obj->get_kategorie_name($obj->get_kategorie()).'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Stav:</span></td>
        <td class="detail"><span class="detail">'.$obj->get_stav_name($obj->get_stav()).'</span></td>
    </tr>
    </table>
    </fieldset>';
    return $html;
  }
  
  static function detail_lide($obj,$pracovnici,$editori,$odpovedni,$script,$admin=false)
  /*
  zobrazeni detailnich informaci o zakazce $obj
  $pracovnici,$editori,$odpovedni = seznamy vsech uzivatelu, kteri maji k zakazce dany vztah
  */
  {
    $odp_odebrat = "";
    $odp_pridat = "";
    $edt_odebrat = "";
    $edt_pridat = "";
    $prc_odebrat = "";
    $prc_pridat = "";

    $html = '<fieldset><legend>Lidé</legend>
    <table>
    <tr><td class="label"><span class="label">Odpovědné osoby:</span>
        </td>
        <td class="detail">';
        $oddelovac = "";
        foreach ($odpovedni as $id => $uzivatel)
        {
          $html .= '<span class="detail">'.$oddelovac.$uzivatel->get_jmeno().' '.$uzivatel->get_prijmeni().'</span>';
	        if($admin == true) $html.= '<a class="minus" href="'.$script.'?detail='.$obj->get_id().'&minus='.$uzivatel->get_id().'&opravneni=2">[odebrat]</a>';
          $oddelovac = "<br>";
        }
        $html .= $oddelovac;
      	if($admin == true) $html.= '<a class="plus" href="'.$script.'?detail='.$obj->get_id().'&plus&opravneni=2">[přidat]</a>';
      	$html .='</td>
    </tr>
    <tr><td class="label"><span class="label">Editoři zakázky:</span></td>
        <td class="detail">';
        $oddelovac = "";
        foreach ($editori as $id => $uzivatel)
        {
          $html .= '<span class="detail">'.$oddelovac.$uzivatel->get_jmeno().' '.$uzivatel->get_prijmeni().'</span>';
    	    if($admin == true) $html.= '<a class="minus" href="'.$script.'?detail='.$obj->get_id().'&minus='.$uzivatel->get_id().'&opravneni=1">[odebrat]</a>';
          $oddelovac = "<br>";
        }
        $html .= $oddelovac;
      	if($admin == true) $html.='<a class="plus" href="'.$script.'?detail='.$obj->get_id().'&plus&opravneni=1">[přidat]</a>';
      	$html .='</td>
    </tr>
    <tr><td class="label"><span class="label">Pracovníci:</span></td>
        <td class="detail">';
        $oddelovac = "";
        foreach ($pracovnici as $id => $uzivatel)
        {
          $html .= '<span class="detail">'.$oddelovac.$uzivatel->get_jmeno().' '.$uzivatel->get_prijmeni().'</span>';
	        if($admin == true) $html.='<a class="minus" href="'.$script.'?detail='.$obj->get_id().'&minus='.$uzivatel->get_id().'&opravneni=0">[odebrat]</a>';
          $oddelovac = "<br>";
        }
        $html .= $oddelovac;
      	if($admin == true) $html.='<a class="plus" href="'.$script.'?detail='.$obj->get_id().'&plus&opravneni=0">[přidat]</a>';
        $html.='</td>
    </tr>
    </table>
    </fieldset>';
    return $html;
  }
  
  static function edit($obj,$script)
  /*
  formular pro zmenu zakazky $obj
  */
  {
    $html = '<fieldset><legend>Editovat zakázku</legend>
    <form action="'.$script.'" method="post">
    <table>
    <tr><td class="label"><span class="label">Název:</span></td>
        <td class="edit"><span class="edit"><input type="text" maxlength="100" size="40" name="nazev" value="'.$obj->get_nazev().'"></span></td>
        <td class="edit"><span class="err">'.$obj->get_nazev_err().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Popis:</span></td>
        <td class="edit"><span class="edit"><input type="text" maxlength="100" size="40" name="popis" value="'.$obj->get_popis().'"</span></td>
        <td class="edit"><span class="err">'.$obj->get_popis_err().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Období:</span></td>
        <td class="edit"><span class="edit"><input type="text" maxlength="100" size="40" name="obdobi" value="'.$obj->get_obdobi().'"</span></td>
        <td class="edit"><span class="err">'.$obj->get_obdobi_err().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Kategorie:</span></td>
        <td class="edit"><span class="edit"><select name="kategorie">';
            $pole = $obj->get_options_kategorie(); 
        		foreach($pole as $option)
        		{
        			if($option->get_id()==$obj->get_kategorie())
        			{ 
        				$html .= '<option selected value="'.$option->get_id().'">'.$option->get_name().'</option>';
        			}
        			else 
        			{
        				$html .= '<option value="'.$option->get_id().'">'.$option->get_name().'</option>';
        			}
        		}
            $html .= '</select></span></td>
        <td class="edit"><span class="err">'.$obj->get_kategorie_err().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Stav:</td>
        <td class="edit"><span class="edit"><select name="stav">';
            $pole = $obj->get_options_stav(); 
        		foreach($pole as $option)
        		{
        			if($option->get_id()==$obj->get_stav())
        			{ 
        				$html .= '<option selected value="'.$option->get_id().'">'.$option->get_name().'</option>';
        			}
        			else 
        			{
        				$html .= '<option value="'.$option->get_id().'">'.$option->get_name().'</option>';
        			}
        		}
            $html .= '</select></span></td>
        <td class="edit"><span class="err">'.$obj->get_stav_err().'</span></td>
    </tr>
    </table>
    <div class="submit"><input class="submit" type="submit" name="edit" value="Uložit">
                        <a class="submit" href="./zakazky.php?detail='.$obj->get_id().'">Zpět</a>
                        <input type="hidden" name="token" value="'.Util::get_token().'">
                        <input type="hidden" name="id" value="'.$obj->get_id().'">
    </div>
    </form>
    </fieldset>';
    return $html;
  }
  
  static function add($obj,$script)
  {
  /*
  formular pro vlozeni nove zakazky
  */
    $html = '<fieldset><legend>Přidat novou zakázku</legend>
    <form action="'.$script.'" method="post">
    <table>
    <tr><td class="label"><span class="label">Název:</span></td>
        <td class="add"><span class="add"><input type="text" maxlength="100" size="40" name="nazev" value="'.$obj->get_nazev().'"></span></td>
        <td class="add"><span class="err">'.$obj->get_nazev_err().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Popis:</span></td>
        <td class="add"><span class="add"><input type="text" maxlength="100" size="40" name="popis" value="'.$obj->get_popis().'"></span></td>
        <td class="add"><span class="err">'.$obj->get_popis_err().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Období:</span></td>
        <td class="add"><span class="add"><input type="text" maxlength="100" size="40" name="obdobi" value="'.$obj->get_obdobi().'"></span></td>
        <td class="add"><span class="err">'.$obj->get_obdobi_err().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Kategorie:</span></td>
        <td class="add"><span class="add"><select name="kategorie">';
            $pole = $obj->get_options_kategorie(); 
        		foreach($pole as $option)
        		{
        			if($option->get_id()==$obj->get_kategorie())
        			{ 
        				$html .= '<option selected value="'.$option->get_id().'">'.$option->get_name().'</option>';
        			}
        			else 
        			{
        				$html .= '<option value="'.$option->get_id().'">'.$option->get_name().'</option>';
        			}
        		}
            $html .= '</select></span></td>
        <td class="add"><span class="err">'.$obj->get_kategorie_err().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Stav:</span></td>
        <td class="add"><span class="add"><select name="stav">';
            $pole = $obj->get_options_stav(); 
        		foreach($pole as $option)
        		{
        			if($option->get_id()==$obj->get_stav())
        			{ 
        				$html .= '<option selected value="'.$option->get_id().'">'.$option->get_name().'</option>';
        			}
        			else 
        			{
        				$html .= '<option value="'.$option->get_id().'">'.$option->get_name().'</option>';
        			}
        		}
            $html .= '</select></span></td>
        <td class="add"><span class="err">'.$obj->get_stav_err().'</span></td>
    </tr>
    </table>
    <div class="submit"><input class="submit" type="submit" name="add" value="Uložit">
                        <input type="hidden" name="token" value="'.Util::get_token().'">
    </div>
    </form>
    </fieldset>';
    return $html;  
  }
  
  static function add_opravneni($pole,$zakazka,$opravneni,$script)
  {
  /*
  formular pro prirazeni cloveka k zakazce
  */
    $options = "";
    foreach($pole as $id => $obj)
    {
      $options.='<option value="'.$obj->get_id().'">'.$obj->get_jmeno().' '.$obj->get_prijmeni().'</option>';
    }
    $html = '<fieldset><legend>Přidat zaměstnance:</legend>
    <form action="'.$script.'" method="post">
    <div class="add"><select name="uzivatel">'.$options.'</select></div>
    <div class="submit"><input class="submit" type="submit" name="plus" value="Uložit">
                        <a class="submit" href="./zakazky.php?detail='.$zakazka.'">Storno</a>
                        <input type="hidden" name="token" value="'.Util::get_token().'">
                        <input type="hidden" name="zakazka" value="'.$zakazka.'">
                        <input type="hidden" name="opravneni" value="'.$opravneni.'">
    </div>
    </form>
    </fieldset>';
    return $html;  
  }
  
}//konec tridy

?>
