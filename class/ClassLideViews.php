<?php

class LideViews
{
  
  static function prihlas_su($script)
  /*
  formular pro prihlaseni superusera
  */
  {  
    $html = '<fieldset><legend>Přihlášení</legend>
    <form action="'.$script.'" method="post">
    <table>
    <tr><td class="label"><span class="label">Heslo:</span></td>
        <td class="prihlaseni"><span class="prihlaseni"><input type="password" maxlength="20" name="heslo"></span></td>
    </tr>
    </table>
    <div class="submit"><input class="submit" type="submit" name="prihlasit_su" value="OK">
                        <input type="hidden" name="token" value="'.Util::get_token().'">
    </div>
    </form>
    </fieldset>';
    return $html;
  }
  
  static function prihlas_formular($pole, $uzivatel, $script)
  /*
  formular pro prihlasovani uzivatelu - zobrazeni pouze aktivni
  $pole = seznam objektu k zobrazeni
  $uzivatel = id v objektu uzivatel
  */
  {  
    $options = "";
    foreach($pole as $id => $obj)
    {
      $selected = "";
      if($uzivatel==$obj->get_id()) $selected = " selected";
      $options.='<option value="'.$obj->get_id().'"'.$selected.'>'.$obj->get_jmeno()." ".$obj->get_prijmeni().'</option>';
    }
    $html = '<fieldset><legend>Přihlášení</legend>
    <form action="'.$script.'" method="post">
    <table>
    <tr><td class="label"><span class="label">Jméno:</span></td>
        <td class="prihlaseni"><span class="prihlaseni"><select name="uzivatel">'.$options.'</select></span></td>
    </tr>
    <tr><td class="label"><span class="label">Heslo:</span></td>
        <td class="prihlaseni"><span class="prihlaseni"><input type="password" maxlength="20" name="heslo"></span></td>
    </tr>
    </table>
    <div class="submit"><input class="submit" type="submit" name="prihlasit" value="OK">
                        <input type="hidden" name="token" value="'.Util::get_token().'">
    </div>
    </form>
    </fieldset>';
    return $html;
  }
  
  static function zmena_hesla_formular($uzivatel, $script, $err, $admin=false)
  /*
  formular pro zmenu hesla
  $uzivatel = id v objektu uzivatel
  */
  {  
	  $input = $admin ? '<input type="hidden" name="heslo" value="">' : "";
    $option="";
    $html = '<fieldset><legend>Změna hesla</legend>
    <form action="'.$script.'" method="post">
    <table>
    <tr><span class="err">'.$err.'</span>
    </tr>
    <tr><td class="label"><span class="label">Jméno:</span></td>
        <td class="zmenahesla"><span class="zmenahesla">'.$uzivatel->get_jmeno()." ".$uzivatel->get_prijmeni().'</span></td>
    </tr>';
		if($admin == false)
		{
			$html.='
				<tr><td class="label"><span class="label">Staré heslo:</span></td>
				    <td class="zmenahesla"><span class="zmenahesla"><input type="password" maxlength="20" name="heslo"></span></td>
				</tr>';
		}
		$html.='
    <tr><td class="label"><span class="label">Nové heslo:</span></td>
        <td class="zmenahesla"><span class="zmenahesla"><input type="password" maxlength="20" name="nove_heslo"></span></td>
    </tr>
    <tr><td class="label"><span class="label">Nové heslo podruhé:</span></td>
        <td class="zmenahesla"><span class="zmenahesla"><input type="password" maxlength="20" name="nove_heslo2"></span></td>
    </tr>
    </table>
    <div class="submit"><input class="submit" type="submit" name="zmena_hesla" value="Uložit">
                        <input type="hidden" name="token" value="'.Util::get_token().'">
                        <input type="hidden" name="uzivatel" value="'.$uzivatel->get_id().'">
												'.$input.'
    </div>
    </form>
    </fieldset>';
    return $html;
  }
    
  static function get_list($list,$script)
  /*
  zobrazeni seznamu lidi, oznost razeni
  */
  {
    $html = '<div><table class="list">
    <tr><th class="list"><span class="list"><a class="razeni" href="'.$script.'&order=prijmeni">Příjmení</a></span></th>
        <th class="list"><span class="list"><a class="razeni" href="'.$script.'&order=jmeno">Jméno</a></span></th>
        <th class="list"><span class="list"><a class="razeni" href="'.$script.'&order=zkratka">Zkratka</a></span></th>
        <th class="list"><span class="list"><a class="razeni" href="'.$script.'&order=role">Role</a></span></th>
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

        $html .= '<tr class="'.$styl_suda.'"><td class="list"><span class="list"><a class="list" href="./lide.php?detail='.$obj->get_id().'">'.$obj->get_prijmeni().'</a></span></td>
                      <td class="list"><span class="list">'.$obj->get_jmeno().'</span></td>
                      <td class="list"><span class="list">'.$obj->get_zkratka().'</span></td>
                      <td class="list"><span class="list">'.$obj->get_role_name($obj->get_role()).'</span></td>
                  </tr>';
      }
    } 
    $html .= '</div></table>';
    return $html;
  }
  
  static function detail($obj,$script,$admin=false)
  /*
  zobrazeni detailnich informaci o cloveku
  */
  {
    $edit = "";
    if($admin == true) $edit = ': <a class="link" href="'.$script.'?edit='.$obj->get_id().'">Editovat</a> | <a class="link" href="'.$script.'?passwd&id='.$obj->get_id().'">Změna hesla</a>';
    //if($admin == true) $edit = ': <a class="link" href="'.$script.'?edit='.$obj->get_id().'">Editovat</a> | <a class="link" href="'.$script.'?passwd='.$obj->get_id().'">Změna hesla</a>';
    
    $html = '<fieldset><legend>Detail zaměstnance'.$edit.'
                       </legend>
    <table>
    <tr><td class="label"><span class="label">Příjmení:</span></td>
        <td class="detail><span class="detail">'.$obj->get_prijmeni().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Jméno:</span></td>
        <td class="detail><span class="detail">'.$obj->get_jmeno().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Role:</span></td>
        <td class="detail><span class="detail">'.$obj->get_role_name($obj->get_role()).'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Zkratka:</span></td>
        <td class="detail><span class="detail">'.$obj->get_zkratka().'</span></td>
    </tr>
    ';/*
    <tr><td class="label"><span class="label">Dovolená:</span></td>
        <td class="detail><span class="detail">'.$obj->get_dovolena().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Úvazek:</span></td>
        <td class="detail><span class="detail">'.$obj->get_uvazek().'</span></td>
    </tr>
    */$html .= '
    </table>
    </fieldset>';
    return $html;
  }
  
  static function edit($obj,$script)
  /*
  formular pro zmenu cloveka
  */
  {
    $html = '<fieldset><legend>Editovat údaje o zaměstnanci</legend>
    <form action="'.$script.'" method="post">
    <table>
    <tr><td class="label"><span class="label">Příjmení:</span></td>
        <td class="edit"><span class="edit"><input type="text" maxlength="100" size="40" name="prijmeni" value="'.$obj->get_prijmeni().'"></span></td>
        <td class="edit"><span class="err">'.$obj->get_prijmeni_err().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Jméno:</span></td>
        <td class="edit"><span class="edit"><input type="text" maxlength="100" size="40" name="jmeno" value="'.$obj->get_jmeno().'"</span></td>
        <td class="edit"><span class="err">'.$obj->get_jmeno_err().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Role:</span></td>
        <td class="edit"><span class="edit"><select name="role">';
            $pole = $obj->get_options_role(); 
        		foreach($pole as $option)
        		{
        			if($option->get_id()==$obj->get_role())
        			{ 
        				$html .= '<option selected value="'.$option->get_id().'">'.$option->get_name().'</option>';
        			}
        			else 
        			{
        				$html .= '<option value="'.$option->get_id().'">'.$option->get_name().'</option>';
        			}
        		}
            $html .= '</select></span></td>
        <td class="edit"><span class="err">'.$obj->get_role_err().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Zkratka:</span></td>
        <td class="edit"><span class="edit"><input type="text" maxlength="100" size="40" name="zkratka" value="'.$obj->get_zkratka().'"</span></td>
        <td class="edit"><span class="err">'.$obj->get_zkratka_err().'</span></td>
    </tr>
    <input type="hidden" name="dovolena" value="'.$obj->get_dovolena().'">
    <input type="hidden" name="uvazek" value="'.$obj->get_uvazek().'">
    ';/*
    <tr><td class="label"><span class="label">Dovolená:</span></td>
        <td class="edit"><span class="edit"><input type="text" maxlength="100" size="40" name="dovolena" value="'.$obj->get_dovolena().'"</span></td>
        <td class="edit"><span class="err">'.$obj->get_dovolena_err().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Úvazek:</span></td>
        <td class="edit"><span class="edit"><input type="text" maxlength="100" size="40" name="uvazek" value="'.$obj->get_uvazek().'"</span></td>
        <td class="edit"><span class="err">'.$obj->get_uvazek_err().'</span></td>
    </tr>
    */$html .= '
    </table>
    <div class="submit"><a class="submit" href="./lide.php?detail='.$obj->get_id().'">Zpět</a>
                        <input class="submit" type="submit" name="edit" value="Uložit">
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
  formular pro vlozeni noveho cloveka
  */
    $html = '<fieldset><legend>Vložit nového zaměstnance</legend>
    <form action="'.$script.'" method="post">
    <table>
    <tr><td class="label"><span class="label">Příjmení:</span></td>
        <td class="add"><span class="add"><input type="text" maxlength="100" size="40" name="prijmeni" value="'.$obj->get_prijmeni().'"></span></td>
        <td class="add"><span class="err">'.$obj->get_prijmeni_err().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Jméno:</span></td>
        <td class="add"><span class="add"><input type="text" maxlength="100" size="40" name="jmeno" value="'.$obj->get_jmeno().'"></span></td>
        <td class="add"><span class="err">'.$obj->get_jmeno_err().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Role:</span></td>
        <td class="add"><span class="add"><select name="role">';
            $pole = $obj->get_options_role(); 
        		foreach($pole as $option)
        		{
        				if($option->get_id()==$obj->get_role())
        			{ 
        				$html .= '<option selected value="'.$option->get_id().'">'.$option->get_name().'</option>';
        			}
        			else 
        			{
        				$html .= '<option value="'.$option->get_id().'">'.$option->get_name().'</option>';
        			}
        		}
            $html .= '</select></span></td>
        <td class="add"><span class="err">'.$obj->get_role_err().'
    </tr>
    <tr><td class="label"><span class="label">Zkratka:</span></td>
        <td class="add"><span class="add"><input type="text" maxlength="100" size="40" name="zkratka" value="'.$obj->get_zkratka().'"</span></td>
        <td class="add"><span class="err">'.$obj->get_zkratka_err().'</span></td>
    </tr>
    <input type="hidden" name="dovolena" value="'.$obj->get_dovolena().'">
    <input type="hidden" name="uvazek" value="'.$obj->get_uvazek().'">
    ';/*
    <tr><td class="label"><span class="label">Dovolená:</span></td>
        <td class="add"><span class="add"><input type="text" maxlength="100" size="40" name="dovolena" value="'.$obj->get_dovolena().'"</span></td>
        <td class="add"><span class="err">'.$obj->get_dovolena_err().'</span></td>
    </tr>
    <tr><td class="label"><span class="label">Úvazek:</span></td>
        <td class="add"><span class="add"><input type="text" maxlength="100" size="40" name="uvazek" value="'.$obj->get_uvazek().'"</span></td>
        <td class="add"><span class="err">'.$obj->get_uvazek_err().'</span></td>
    </tr>
    */$html .= '
    </table>
    <div class="submit"><input class="submit" type="submit" name="add" value="Uložit">
                        <input type="hidden" name="token" value="'.Util::get_token().'">
    </div>
    </form>
    </fieldset>';
    return $html;  
  }

  static function admin_only()
  {
    return '<div class=""><p><span class="infoerr">Access denied! (admin only)</span></p></div>';
  }
  
	static function uzivatel_nenalezen()
  {
    return '<div class=""><p><span class="infoerr">Uživatel nenalezen!</span></p></div>';
  }
}//konec tridy

?>
