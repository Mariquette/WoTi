<?php

class ZakazkyViews
{
    
  //------------------------------ public functions -------------------------------  
  
  static function add($script)
  {
  /*
  formular pro vlozeni nove zakazky
  */
    $form = '<div class="prihlas_formular"><form action="'.$script.'" method="post">
    <table>
    <tr><td class="prihlas_formular">Název:</td>
        <td class="prihlas_formular"><input type="text" maxlength="100" size="40" name="nazev" value=""></td></tr>
    <tr><td class="prihlas_formular"><input type="submit" name="add" value="Uložit"></td></tr>
    </table>
    <input type="hidden" name="token" value="'.Util::get_token().'">
    </form></div>';
    return $form;  
  }
    
  static function zmena_hesla_formular($pole, $uzivatel, $script)
  /*
  formular pro zmenu hesla
  $pole = seznam objektu k zobrazeni
  $uzivatel = id v objektu uzivatel
  */
  {  
    $option="";
    foreach($pole as $id => $obj)
    {
      $selected = "";
      if($uzivatel==$obj->get_id()) $selected = "selected";
      $option.="<option value=\"".$obj->get_id()."\" $selected>".$obj->get_prijmeni()."</option>";
    }
    $form = "<div class=\"zmena_hesla_formular\"><form action=\"".$script."\" method=\"post\"><table>
    <tr><td class=\"zmena_hesla_formular\">Jméno:</td>
      <td class=\"zmena_hesla_formular\"><select name=\"uzivatel\">$option</select></td></tr>
    <tr><td class=\"zmena_hesla_formular\">Staré heslo:</td>
      <td class=\"zmena_hesla_formular\"><input type=\"password\" maxlength=\"20\" name=\"heslo\"></td></tr>
    <tr><td class=\"zmena_hesla_formular\">Nové heslo:</td>
      <td class=\"zmena_hesla_formular\"><input type=\"password\" maxlength=\"20\" name=\"nove_heslo\"></tr>
    <tr><td class=\"zmena_hesla_formular\">Nové heslo podruhé:</td>
      <td class=\"zmena_hesla_formular\"><input type=\"password\" maxlength=\"20\" name=\"nove_heslo2\"></td></tr>
    <tr><td class=\"zmena_hesla_formular\"><input type=\"submit\" name=\"zmena_hesla\" value=\"Změň heslo\"></td><td class=\"none\"></td></tr>
    </table>
    <input type=\"hidden\" name=\"token\" value=\"".Util::get_token()."\">
    </form></div>";
    return $form;
  }
    
  static function get_list($list)
  {
    $html = "";
    if(is_array($list))
      {    
        $html .= '<table>';
        foreach ($list as $id => $obj)
        {
          $html .= '<tr><td>'.$id.'</td><td>'.$obj->get_id().'</td><td>'.$obj->get_nazev().'</td><td>'.$obj->get_popis().'</td><td>'.$obj->get_cinnost().'</td><td>'.$obj->get_stav().'</td></tr>';
        }
        $html .= '</table>';
      } 
      return $html;
  }
  
  //------------------------------ private functions -------------------------------
  
  
}//konec tridy

?>
