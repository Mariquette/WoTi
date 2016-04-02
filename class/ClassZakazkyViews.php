<?php

class ZakazkyViews
{
  
  static function get_list($list,$script)
  /*
  zobrazeni seznamu zakazek, moznost razeni
  */
  {
    $html = '<table><tr>';
    //$html .= '<th>id(pole)</th><th>id</th>';
    $html .= '<th><a href="'.$script.'&order=nazev">Název</a></th>
              <th><a href="'.$script.'&order=popis">Popis</a></th>
              <th><a href="'.$script.'&order=cinnost">Činnost</a></th>
              <th><a href="'.$script.'&order=stav">Stav</a></th>
              <th></th>
             ';
    $html .= '</tr>';
    if(is_array($list))
    {    
      foreach ($list as $id => $obj)
      {
        $html .= '<tr>';
        //$html .= '<td>'.$id.'</td><td>'.$obj->get_id().'</td>';
        $html .= '<td>'.$obj->get_nazev().'</td>
                  <td>'.$obj->get_popis().'</td>
                  <td>'.$obj->get_cinnost().'</td>
                  <td>'.$obj->get_stav().'</td>
                  <td><a href="./zakazky.php?detail='.$obj->get_id().'">Detail</a></td>
                  ';
        $html .= '</tr>';
      }
      $html .= '</table>';
    } 
    return $html;
  }
  
  static function detail($obj,$script)
  /*
  zobrazeni detailnich informaci o zakazce
  */
  {
    $html = '<table><tr>';
    $html .= '<tr><td>Název:</td><td>'.$obj->get_nazev().'</td></tr>
              <tr><td>Popis:</td><td>'.$obj->get_popis().'</td></tr>
              <tr><td>Činnost</td><td>'.$obj->get_cinnost().'</td></tr>
              <tr><td>Stav</td><td>'.$obj->get_stav().'</td></tr>
              <tr><td><a href="'.$script.'?edit='.$obj->get_id().'">Editovat</a></td><td></td></tr>
             ';
    $html .= '</table>';
    return $html;
  }
  
  static function edit($obj,$script)
  /*
  formular pro zmenu zakazky
  */
  {
    $html = '<form action="'.$script.'" method="post"><table>';
    $html .= '<tr><td>Název:</td><td><input type="text" maxlength="100" size="40" name="nazev" value="'.$obj->get_nazev().'"></td></tr>
              <tr><td>Popis:</td><td><input type="text" maxlength="100" size="40" name="popis" value="'.$obj->get_popis().'"</td></tr>
              <tr><td>Činnost</td><td><input type="text" maxlength="100" size="40" name="cinnost" value="'.$obj->get_cinnost().'"</td></tr>
              <tr><td>Stav</td><td><input type="text" maxlength="100" size="40" name="stav" value="'.$obj->get_stav().'"</td></tr>
              <tr><td class="add"><input type="submit" name="edit" value="Uložit"></td></tr>
             ';
    $html .= '</table><input type="hidden" name="token" value="'.Util::get_token().'">
              <input type="hidden" name="id" value="'.$obj->get_id().'"></form>';
    return $html;
  }
  
  static function add($script)
  {
  /*
  formular pro vlozeni nove zakazky
  */
    $html = '<form action="'.$script.'" method="post"><table>';
    $html .= '<tr><td class="add">Název:</td><td class="add"><input type="text" maxlength="100" size="40" name="nazev" value=""></td></tr>
              <tr><td class="add">Popis:</td><td class="add"><input type="text" maxlength="100" size="40" name="popis" value=""></td></tr>
              <tr><td class="add">Činnost:</td><td class="add"><input type="text" maxlength="100" size="40" name="cinnost" value=""></td></tr>
              <tr><td class="add"><input type="submit" name="add" value="Uložit"></td></tr>
             ';
    $html .= '</table><input type="hidden" name="token" value="'.Util::get_token().'"></form>';
    return $html;  
  }
  
}//konec tridy

?>
