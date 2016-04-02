<?php

class LideViews
{
  
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
    
  static function get_list($list,$script)
  /*
  zobrazeni seznamu lidi, oznost razeni
  */
  {
    $html = '<table><tr>';
    //$html .= '<th>id(pole)</th><th>id</th>';
    $html .= '<th><a href="'.$script.'&order=prijmeni">Příjmení</a></th>
              <th><a href="'.$script.'&order=jmeno">Jméno</a></th>
              <th><a href="'.$script.'&order=role">Role</a></th>
             ';
    $html .= '</tr>';
    if(is_array($list))
    {    
      foreach ($list as $id => $obj)
      {
        $html .= '<tr>';
        //$html .= '<td>'.$id.'</td><td>'.$obj->get_id().'</td>';
        $html .= '<td>'.$obj->get_prijmeni().'</td>
                  <td>'.$obj->get_jmeno().'</td>
                  <td>'.$obj->get_role().'</td>
                  <td><a href="./lide.php?detail='.$obj->get_id().'">Detail</a></td>
                 ';
        $html .= '</tr>';
      }
      $html .= '</table>';
    } 
    return $html;
  }
  
  static function detail($obj,$script)
  /*
  zobrazeni detailnich informaci o cloveku
  */
  {
    $html = '<table><tr>';
    $html .= '<tr><td>Příjmení:</td><td>'.$obj->get_prijmeni().'</td></tr>
              <tr><td>Jméno:</td><td>'.$obj->get_jmeno().'</td></tr>
              <tr><td>Role</td><td>'.$obj->get_role().'</td></tr>
              <tr><td><a href="'.$script.'?edit='.$obj->get_id().'">Editovat</a></td><td></td></tr>
             ';
    $html .= '</table>';
    return $html;
  }
  
  static function edit($obj,$script)
  /*
  formular pro zmenu cloveka
  */
  {
    $html = '<form action="'.$script.'" method="post"><table>';
    $html .= '<tr><td>Příjmení:</td><td><input type="text" maxlength="100" size="40" name="prijmeni" value="'.$obj->get_prijmeni().'"></td></tr>
              <tr><td>Jméno:</td><td><input type="text" maxlength="100" size="40" name="jmeno" value="'.$obj->get_jmeno().'"</td></tr>
              <tr><td>Role</td><td><input type="text" maxlength="100" size="40" name="role" value="'.$obj->get_role().'"</td></tr>
              <tr><td class="add"><input type="submit" name="edit" value="Uložit"></td></tr>
             ';
    $html .= '</table><input type="hidden" name="token" value="'.Util::get_token().'">
              <input type="hidden" name="id" value="'.$obj->get_id().'"></form>';
    return $html;
  }
  
  static function add($script)
  {
  /*
  formular pro vlozeni noveho cloveka
  */
    $form = '<div class="add"><form action="'.$script.'" method="post">
    <table>
    <tr><td class="add">Jméno:</td>
        <td class="add"><input type="text" maxlength="100" size="40" name="jmeno" value=""></td></tr>
    <tr><td class="add">Příjmení:</td>
        <td class="add"><input type="text" maxlength="100" size="40" name="prijmeni" value=""></td></tr>
    <tr><td class="add"><input type="submit" name="add" value="Uložit"></td></tr>
    </table>
    <input type="hidden" name="token" value="'.Util::get_token().'">
    </form></div>';
    return $form;  
  }
  
}//konec tridy

?>
