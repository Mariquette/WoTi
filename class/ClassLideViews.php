<?php

class LideViews
{
    
  //------------------------------ public functions -------------------------------  
  
  static function prihlas_formular($pole, $uzivatel, $script)
  /*
  formular pro prihlasovani uzivatelu - zobrazeni pouze aktivni
  $pole = seznam objektu k zobrazeni
  $uzivatel = id v objektu uzivatel
  */
  {  
    $option = "";
    foreach($pole as $id => $obj)
    {
      $selected = "";
      if($uzivatel==$obj->get_id()) $selected = " selected";
      $option.='<option value="'.$obj->get_id().'"'.$selected.'>'.$obj->get_prijmeni().'</option>';
    }
    $form = '<div class="prihlas_formular"><form action="'.$script.'" method="post"><table>
    <tr><td class="prihlas_formular">Jméno:</td>
      <td class="prihlas_formular">Heslo:</td>
      <td class="prihlas_formular"></td></tr>
    <tr><td class="prihlas_formular"><select name="uzivatel">'.$option.'</select></td>
      <td class="prihlas_formular"><input type="password" maxlength="20" name="heslo"><input class="none" type="password"></td>
      <td class="prihlas_formular"><input type="submit" name="prihlasit" value="Přihlásit"></td></tr>
    </table>
    <input type="hidden" name="token" value="'.Util::get_token().'">
    </form></div>';
    return $form;
  }
  
  static function zmena_hesla_formular($uzivatel, $script)
  /*
  formular pro zmenu hesla
  $uzivatel = id v objektu uzivatel
  */
  {  
    $option="";
    $form = '<div class="zmena_hesla_formular"><form action="'.$script.'" method="post"><table>
    <tr><td class="zmena_hesla_formular">Jméno:</td>
      <td class="zmena_hesla_formular">'.$uzivatel->get_prijmeni()." ".$uzivatel->get_jmeno().'</td></tr>
    <tr><td class="zmena_hesla_formular">Staré heslo:</td>
      <td class="zmena_hesla_formular"><input type="password" maxlength="20" name="heslo"></td></tr>
    <tr><td class="zmena_hesla_formular">Nové heslo:</td>
      <td class="zmena_hesla_formular"><input type="password" maxlength="20" name="nove_heslo"></tr>
    <tr><td class="zmena_hesla_formular">Nové heslo podruhé:</td>
      <td class="zmena_hesla_formular"><input type="password" maxlength="20" name="nove_heslo2"></td></tr>
    <tr><td class="zmena_hesla_formular"><input type="submit" name="zmena_hesla" value="Změň heslo"></td><td class="none"></td></tr>
    </table>
    <input type="hidden" name="token" value="'.Util::get_token().'">
    <input type="hidden" name="uzivatel" value="'.$uzivatel->get_id().'">
    </form></div>';
    return $form;
  }
    
  static function get_list($list)
  {
    $html = "";
    if(is_array($list))
      {    
        $html .= '<table>';
        //foreach ($uzivatele->get_all_2list() as $uziv_id => $uziv_name)
        foreach ($list as $id => $obj)
        {
          $html .= '<tr><td>'.$obj->get_prijmeni().'</td><td>'.$obj->get_jmeno().'</td><td>'.$obj->get_role().'</td></tr>';
        }
        $html .= '</table>';
      } 
      return $html;
  }
  
  //------------------------------ private functions -------------------------------
  
  
}//konec tridy

?>
