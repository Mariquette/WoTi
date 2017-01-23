<?php
class Database
/*
pracuje s databazi
*/
{
  private $dbSrv = "";
  private $dbName = ""; 
  private $dbUserName = "";
  private $dbPasswd = "";

  public function __construct()
  {
    $this->dbSrv = "localhost";
    $this->dbName = "wotidb"; 
    $this->dbUserName = "wotidb_user";
    $this->dbPasswd = "quqy4AqR9DWAVSWJ";
    $db_id = $this->connect();
     if($db_id==false)
     {
      die("Nepodařilo se připojit k databázi!");
     }
     //echo "DBID:".$this->db."<br>";
     $this->close($db_id);
  }

//------------------------------ public functions -------------------------------  
  
  static function get_names_mesic()
  {
    return array(1=>"leden", "únor", "březen", "duben", "květen", "červen", "červenec", "srpen", "září", "říjen", "listopad", "prosinec");
  }
  static function get_names_dny()
  {
    return array(1=>"Po", "Út", "St", "Čt", "Pá", "So", "Ne");
  }
  static function get_svatky($rok)
  {
    return array( array(1,1,'Den obnovy samostatného českého státu'),
                  array(date('j',strtotime('-2 days',easter_date($rok))),date('n',strtotime('-2 days',easter_date($rok))),'Velký pátek'),
                  array(date('j',strtotime('+1 day',easter_date($rok))),date('n',strtotime('+1 day',easter_date($rok))),'Velikonoční pondělí'),
                  array(1,5,'Svátek práce'),
                  array(8,5,'Den vítězství'),
                  array(5,7,'Den slovanských věrozvěstů Cyrila a Metoděje'),
                  array(6,7,'Den upálení mistra Jana Husa'), 
                  array(28,9,'Den české státnosti'),
                  array(28,10,'Den vzniku samostatného československého státu'),
                  array(17,11,'Den boje za svobodu a demokracii'),
                  array(24,12,'Štědrý den'),
                  array(25,12,'1. svátek vánoční'),
                  array(26,12,'2. svátek vánoční')
                );
  }
      
  public function get_options_role()
  /*
  vrati pole moznych roli uzivatele v systemu
  */
  {
  	$options = array();
  	$options[] = new Option("neaktivní",Uzivatel::NEAKTIVNI);
  	$options[] = new Option("základní přístup",Uzivatel::ZAKLADNI);
  	$options[] = new Option("administrátor",Uzivatel::ADMIN);
  	//$options[] = new Option("superuser",Uzivatel::SUPERUSER);
  	return $options;
  }
  public function get_role($id)
  {
  	$options = $this->get_options_role();
  	foreach ($options as $obj)
  	{
  		if($id == $obj->get_id()) return $obj;
  	}
  	return false;
  }    
  
  public function get_options_kategorie()
  /*
  vrati pole moznych kategorii zakazky
  */
  {
  	$options = array();
  	$options[] = new Option("R&D",Zakazka::RAD);
  	$options[] = new Option("Grant",Zakazka::GRANT);
  	$options[] = new Option("SoW",Zakazka::SOW);
  	$options[] = new Option("RITE products",Zakazka::RITE);
  	$options[] = new Option("RC products",Zakazka::RC);
  	$options[] = new Option("Other Sales",Zakazka::OTHER);
  	$options[] = new Option("REŽIE",Zakazka::REZIE);
  	$options[] = new Option("Ostatní",Zakazka::OSTATNI);
  	return $options;
  }    
  public function get_kategorie($id)
  {
  	$options = $this->get_options_kategorie();
  	foreach ($options as $obj)
  	{
  		if($id == $obj->get_id()) return $obj;
  	}
  	return false;
  }
  
  public function get_options_stav_zakazka()
  /*
  vrati pole moznych stavu zakazky
  */
  {
  	$options = array();
  	$options[] = new Option("ukončená",Zakazka::UKONCENA);
  	$options[] = new Option("aktivní",Zakazka::AKTIVNI);
  	$options[] = new Option("v záruce",Zakazka::ZARUKA);
  	$options[] = new Option("trvalá",Zakazka::TRVALA);
  	return $options;
  }    
  public function get_stav_zakazka($id)
  {
  	$options = $this->get_options_stav_zakazka();
  	foreach ($options as $obj)
  	{
  		if($id == $obj->get_id()) return $obj;
  	}
  	return false;
  }
  
  public function get_options_stav_mesic()
  /*
  vrati pole moznych stavu mesice
  */
  {
  	$options = array();
  	$options[] = new Option("otevřeno",Mesic::OTEVRENO);
  	$options[] = new Option("uzavřeno",Mesic::UZAVRENO);
  	$options[] = new Option("zamčeno",Mesic::ZAMCENO);
  	return $options;
  }    
  public function get_stav_mesic($id)
  {
  	$options = $this->get_options_stav_mesic();
  	foreach ($options as $obj)
  	{
  		if($id == $obj->get_id()) return $obj;
  	}
  	return false;
  }
  
  public function get_options_opravneni()
  /*
  vrati pole moznych opravneni
  */
  {
  	$options = array();
  	$options[] = new Option("pracovník",Opravneni::PRACOVNIK);
  	$options[] = new Option("editor",Opravneni::EDITOR);
  	$options[] = new Option("odpovědný",Opravneni::ODPOVEDNY);
  	return $options;
  }    
  public function get_opravneni($id)
  {
  	$options = $this->get_options_opravneni();
  	foreach ($options as $obj)
  	{
  		if($id == $obj->get_id()) return $obj;
  	}
  	return false;
  }

  //------------------------------------------------------------------------------------
  
  /*********/
  /*ZAKAZKY*/
  /*********/
  public function get_zakazky_all($order = "kategorie")
  /*
  vrati vsechny zakazky vcetne neaktivnich
  serazene podle promenne $order
  */
  {    
    $vysledek = array();
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query("SELECT * FROM zakazky ORDER BY zakazky.$order", $db_id);
      while ($row = mysql_fetch_array($dotaz, MYSQL_ASSOC)) 
      {
        $vysledek[] = new Zakazka($row);
      }
      $this->close($db_id);
    }          
    return $vysledek;              
  }  
  
  public function get_zakazky_active($order = "kategorie")
  /*
  vrati aktivni zakazky
  serazene podle promenne $order
  */
  {    
    $vysledek = array();
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query("SELECT * FROM zakazky WHERE stav = 1 OR stav = 3 ORDER BY zakazky.$order", $db_id);
      while ($row = mysql_fetch_array($dotaz, MYSQL_ASSOC)) 
      {
        $vysledek[] = new Zakazka($row);
      }
      $this->close($db_id);
    }          
    return $vysledek;
  }  
  
  public function get_zakazky_zaruka($order = "kategorie")
  /*
  vrati aktivni zakazky
  serazene podle promenne $order
  */
  {    
    $vysledek = array();
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query("SELECT * FROM zakazky WHERE stav = 2 ORDER BY zakazky.$order", $db_id);
      while ($row = mysql_fetch_array($dotaz, MYSQL_ASSOC)) 
      {
        $vysledek[] = new Zakazka($row);
      }
      $this->close($db_id);
    }          
    return $vysledek;
  }  
  
  public function get_zakazky_inactive($order = "kategorie")
  /*
  vrati neaktivni zakazky
  serazene podle promenne $order
  */
  {    
    $vysledek = array();
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query("SELECT * FROM zakazky WHERE stav = 0 ORDER BY zakazky.$order", $db_id);
      while ($row = mysql_fetch_array($dotaz, MYSQL_ASSOC)) 
      {
        $vysledek[] = new Zakazka($row);
      }
      $this->close($db_id);
    }          
    return $vysledek;              
  }
  
  public function get_zakazka($id)
  /*
  vrati objekt zakazka s danym id 
  */
  {
    $vysledek = false;
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query("SELECT * FROM zakazky WHERE id = $id", $db_id);
      $row = mysql_fetch_array($dotaz, MYSQL_ASSOC);
      if(is_array($row))
      {
        $vysledek = new Zakazka($row);
      }  
      $this->close($db_id);
    }          
    return $vysledek;  
  }
  
  public function edit_zakazka($obj)
  /*
  edituje zakazku
  */
  {
    $values = $obj->to_array();
    $sql_values = "";
    foreach($values as $key => $value)
    {
      $sql_values .= $key." = '$value', ";
    }
    $sql_values = substr($sql_values,0,-2);        
    $sql_dotaz = "UPDATE zakazky SET $sql_values WHERE id = ".$obj->get_id()." ";
    return $this->database_query($sql_dotaz);    
  }
  
  public function add_zakazka($obj)
  /*
  vlozi novou zakazku
  */
  {
    $values = $obj->to_array();
    $sql_values = "";
    $sql_columns = "";
    foreach($values as $key => $value)
    {
      $sql_values .= "'$value', ";
      $sql_columns .= "$key, ";
    }
    $sql_values = substr($sql_values,0,-2);        
    $sql_columns = substr($sql_columns,0,-2);        
    $sql_dotaz = "INSERT INTO zakazky ($sql_columns) VALUES (".$sql_values.")";
    return $this->database_query($sql_dotaz);          
  }
  
  /***********/
  /*UZIVATELE*/
  /***********/
  public function get_uzivatele($role)
  {
    $vysledek = false;
    if($db_id = $this->connect())
    {                                                                  
      $dotaz = mysql_query("SELECT * FROM uzivatele WHERE role = $role", $db_id);
      while ($row = mysql_fetch_array($dotaz, MYSQL_ASSOC)) 
      {
        $vysledek[] = new Uzivatel($row);
      }
      $this->close($db_id);
    }          
    return $vysledek;              
  }

  public function get_uzivatele_all($order = "prijmeni")
  /*
  vrati vsechny uzivatele serazene podle promenne $order 
  */
  {
    $vysledek = false;
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query("SELECT * FROM uzivatele WHERE role < 3 ORDER BY uzivatele.$order", $db_id);
      while ($row = mysql_fetch_array($dotaz, MYSQL_ASSOC)) 
      {
        $vysledek[] = new Uzivatel($row);
      }
      $this->close($db_id);
    }          
    return $vysledek;              
  }
  
  public function get_uzivatele_active($order = "prijmeni")
  /*
  vrati aktivni (role>1) uzivatele serazene podle promenne $order 
  */
  {
    $vysledek = false;
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query("SELECT * FROM uzivatele WHERE role > 0 AND role < 3 ORDER BY uzivatele.$order", $db_id);
      while ($row = mysql_fetch_array($dotaz, MYSQL_ASSOC)) 
      {
        $vysledek[] = new Uzivatel($row);
      }
      $this->close($db_id);
    }          
    return $vysledek;              
  }
  
  public function get_uzivatele_inactive($order = "prijmeni")
  /*
  vrati neaktivni (role=0) uzivatele serazene podle promenne $order 
  */
  {
    $vysledek = false;
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query("SELECT * FROM uzivatele WHERE role = 0 ORDER BY uzivatele.$order", $db_id);
      while ($row = mysql_fetch_array($dotaz, MYSQL_ASSOC)) 
      {
        $vysledek[] = new Uzivatel($row);
      }
      $this->close($db_id);
    }          
    return $vysledek;              
  }
  
  public function get_uzivatel($id)
  /*
  vrati objekt uzivatel s danym id
  */
  {
    $vysledek = false;
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query("SELECT * FROM uzivatele WHERE id = $id", $db_id);
      $row = mysql_fetch_array($dotaz, MYSQL_ASSOC);
      if(is_array($row))
      {
        $vysledek = new Uzivatel($row);
      }  
      $this->close($db_id);
    }          
    return $vysledek;  
  }
  
  public function over_uzivatel($uziv_id, $heslo)
  /*
  vrati nektere udaje o uzivateli s danym id a heslem, musi byt aktivni 
  */
  {
    $vysledek = false;
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query("SELECT id, prijmeni, jmeno, role, zkratka, dovolena, uvazek FROM uzivatele WHERE id = $uziv_id AND role > 0 AND heslo LIKE '$heslo'", $db_id);
      $row = mysql_fetch_array($dotaz, MYSQL_ASSOC);
      if(is_array($row))
      {
        $vysledek = new Uzivatel(array("id"=>$row["id"],"prijmeni"=>$row["prijmeni"],"jmeno"=>$row["jmeno"],"login"=>"","heslo"=>"","role"=>$row["role"],
          "zkratka"=>$row["zkratka"],"dovolena"=>$row["dovolena"],"uvazek"=>$row["uvazek"]));
      }  
      $this->close($db_id);
    }          
    return $vysledek;  
  }
  
  public function edit_uzivatel($obj)
  /*
  edituje uzivatele
  */
  {
    $sql_dotaz = "UPDATE uzivatele SET id='".$obj->get_id()."', prijmeni='".$obj->get_prijmeni()."', jmeno='".$obj->get_jmeno()."', role='".$obj->get_role()."', 
      zkratka='".$obj->get_zkratka()."', dovolena='".$obj->get_dovolena()."', uvazek='".$obj->get_uvazek()."' WHERE id = ".$obj->get_id()." ";
    return $this->database_query($sql_dotaz);      
  }
  
  public function add_uzivatel($obj)
  /*
  vlozi noveho uzivatele
  */
  {
    $values = $obj->to_array();
    $sql_values = "";
    $sql_columns = "";
    foreach($values as $key => $value)
    {
      $sql_values .= "'$value', ";
      $sql_columns .= "$key, ";
    }
    $sql_values = substr($sql_values,0,-2);        
    $sql_columns = substr($sql_columns,0,-2);        
    $sql_dotaz = "INSERT INTO uzivatele ($sql_columns) VALUES (".$sql_values.")";
    return $this->database_query($sql_dotaz);          
  }
  
  public function set_heslo($uziv_id, $heslo)
  /*
  zmeni heslo uzivateli s danym id a heslem
  */
  {
    $vysledek = false;
    if($db_id = $this->connect())
    {
      $sql_dotaz = "UPDATE uzivatele SET heslo='$heslo' WHERE id=$uziv_id";
      if (mysql_query($sql_dotaz, $db_id)!=false)
      {
        if (mysql_query("COMMIT;", $db_id)!=false)       
        {
          $vysledek = true;
        }      
      }  
      $this->close($db_id);
    }
    return $vysledek;
  }
  
  /********/
  /*MESICE*/
  /********/
  public function get_mesice($rok = "", $mesic = "", $uzivatel = "", $stav = "", $order = "mesic")
  /*
  vrati mesice - lze urcit libovolny pocet parametru rok, mesic, uzivatel, stav 
  serazene podle promenne $order
  */
  {    
    $vysledek = array();
    $pole["rok"] = $rok;
    $pole["mesic"] = $mesic;
    $pole["uzivatele_id"] = $uzivatel;
    $pole["stav"] = $stav;
    $sql_dotaz = "";
    foreach($pole as $key => $val)
    {
      if($val != "")
      {
        if($sql_dotaz == "") $sql_dotaz = "SELECT * FROM mesice WHERE $key = $val";
        else $sql_dotaz .= " AND $key = $val";
      }
    }
    if($sql_dotaz == "") $sql_dotaz = "SELECT * FROM mesice";
    //$sql_dotaz .= " ORDER BY mesice.$order";
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query($sql_dotaz, $db_id);
      while ($row = mysql_fetch_array($dotaz, MYSQL_ASSOC)) 
      {
        $vysledek[] = new Mesic($row);
      }
      $this->close($db_id);
    }          
    return $vysledek;              
  }

  public function get_mesic($rok, $mesic, $uzivatel)
  /*
  vrati objekt mesic pokud existuje zaznam pro dany rok, mesic, uzivatel 
  */
  {    
    $pole["rok"] = $rok;
    $pole["mesic"] = $mesic;
    $pole["uzivatele_id"] = $uzivatel;
    $sql_dotaz = "";
    foreach($pole as $key => $val)
    {
      if($sql_dotaz == "") $sql_dotaz = "SELECT * FROM mesice WHERE $key = $val";
      else $sql_dotaz .= " AND $key = $val";
    }
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query($sql_dotaz, $db_id);
      $vysledky = array();
      while ($row = mysql_fetch_array($dotaz, MYSQL_ASSOC)) 
      {
        $vysledky[] = new Mesic($row);
      }
      $this->close($db_id);
    }          
    if(count($vysledky) > 1) die("Existuje ".count($vysledky)." zaznamu pro dany rok, mesic a uzivatele!");
    if (isset($vysledky[0])) return $vysledky[0];
	  return false;
  }
  
  public function add_mesic($obj)
  /*
  vlozi noveho zaznamu do tabulky mesice
  */
  {
    $values = $obj->to_array();
    $sql_values = "";
    $sql_columns = "";
    foreach($values as $key => $value)
    {
      $sql_values .= "'$value', ";
      $sql_columns .= "$key, ";
    }
    $sql_values = substr($sql_values,0,-2);        
    $sql_columns = substr($sql_columns,0,-2);        
    $sql_dotaz = "INSERT INTO mesice ($sql_columns) VALUES (".$sql_values.")";
    return $this->database_query($sql_dotaz);          
  }

  public function edit_mesic($obj)
  /*
  edituje mesic
  */
  {
    $values = $obj->to_array();
    $sql_values = "";
    foreach($values as $key => $value)
    {
      $sql_values .= $key." = '$value', ";
    }
    $sql_values = substr($sql_values,0,-2);        
    $sql_dotaz = "UPDATE mesice SET $sql_values WHERE id = ".$obj->get_id()." ";
    return $this->database_query($sql_dotaz);    
  }
  
  /***********/
  /*OPRAVNENI*/
  /***********/
  public function get_opravneni_all()
  /*
  vrati vsechna opravneni
  */
  {    
    $vysledek = array();
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query("SELECT * FROM opravneni", $db_id);
      while ($row = mysql_fetch_array($dotaz, MYSQL_ASSOC)) 
      {
        $vysledek[] = new Opravneni($row);
      }
      $this->close($db_id);
    }          
    return $vysledek;              
  }  
  
  public function add_opravneni($obj)
  /*
  vlozi novy vztah uzivatel - zakazka do tabulky opravneni
  */
  {
    $values = $obj->to_array();
    $sql_values = "";
    $sql_columns = "";
    foreach($values as $key => $value)
    {
      $sql_values .= "'$value', ";
      $sql_columns .= "$key, ";
    }
    $sql_values = substr($sql_values,0,-2);        
    $sql_columns = substr($sql_columns,0,-2);        
    $sql_dotaz = "INSERT INTO opravneni ($sql_columns) VALUES (".$sql_values.")";
    return $this->database_query($sql_dotaz);          
  }

  public function remove_opravneni($zakazka, $uzivatel, $opravneni)
  /*
  smaze zaznam v tabulce opravneni
  */
  {
    $vysledek = false;
    if($db_id = $this->connect())
    {   
      $sql_dotaz = "DELETE FROM opravneni WHERE uzivatele_id = $uzivatel AND zakazky_id = $zakazka AND opravneni = $opravneni";
      if (mysql_query($sql_dotaz, $db_id)!=false)
      {
        $vysledek = true;
      }
      $this->close($db_id);
    }          
    return $vysledek;         
  }  

  public function get_uzivatele_4zakazka($zak_id, $opravneni)
  /*
  vrati vsechny uzivatele, kteri maji k dane zakazce dane opravneni
  */
  {    
    $vysledek = array();
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query("SELECT uzivatele.id, uzivatele.prijmeni, uzivatele.jmeno, uzivatele.role, uzivatele.zkratka, uzivatele.dovolena, uzivatele.uvazek FROM opravneni 
        INNER JOIN uzivatele ON opravneni.uzivatele_id = uzivatele.id WHERE zakazky_id = $zak_id AND opravneni.opravneni = $opravneni AND uzivatele.role > 0", $db_id);
      while ($row = mysql_fetch_array($dotaz, MYSQL_ASSOC)) 
      {
        $vysledek[$row["id"]] = new Uzivatel(array("id"=>$row["id"],"prijmeni"=>$row["prijmeni"],"jmeno"=>$row["jmeno"],"login"=>"","heslo"=>"","role"=>$row["role"],
          "zkratka"=>$row["zkratka"],"dovolena"=>$row["dovolena"],"uvazek"=>$row["uvazek"]));
      }
      $this->close($db_id);
    }          
    return $vysledek;              
  }
  
  public function get_uzivatele_4zakazka_a_mesic($zak_id, $opravneni, $mesic)
  /*
  vrati vsechny uzivatele, kteri maji k dane zakazce dane opravneni, nebo vytvorily zaznam den s zak_id v danem mesici
  */
  {    
    $vysledek = array();
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query("
				SELECT * FROM 
				(
					(	
						SELECT uzivatele.id, uzivatele.prijmeni, uzivatele.jmeno, uzivatele.role, uzivatele.zkratka, uzivatele.dovolena, uzivatele.uvazek FROM opravneni 
		        INNER JOIN uzivatele ON opravneni.uzivatele_id = uzivatele.id 
						WHERE zakazky_id = $zak_id AND opravneni.opravneni = $opravneni AND uzivatele.role > 0
					)
					UNION ALL
					(
						SELECT DISTINCT uzivatele.id, uzivatele.prijmeni, uzivatele.jmeno, uzivatele.role, uzivatele.zkratka, uzivatele.dovolena, uzivatele.uvazek FROM dny 
        		INNER JOIN uzivatele ON dny.uzivatele_id = uzivatele.id 
						WHERE dny.zakazky_id = $zak_id AND dny.mesic = ".$mesic->get_mesic()." AND dny.rok = ".$mesic->get_rok()."
					)
				) x ORDER BY x.prijmeni", $db_id);

      while ($row = mysql_fetch_array($dotaz, MYSQL_ASSOC)) 
      {
        $vysledek[$row["id"]] = new Uzivatel(array("id"=>$row["id"],"prijmeni"=>$row["prijmeni"],"jmeno"=>$row["jmeno"],"login"=>"","heslo"=>"","role"=>$row["role"],
          "zkratka"=>$row["zkratka"],"dovolena"=>$row["dovolena"],"uvazek"=>$row["uvazek"]));
      }
      $this->close($db_id);
    }          
    return $vysledek;              
  }

  public function get_zakazky_4uzivatel($uziv_id, $opravneni)
  /*
  vrati vsechny aktivni a trvale zakazky, ke kterym ma dany uzivatel dane opravneni
  */
  {    
    $vysledek = array();
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query("SELECT zakazky.id, zakazky.nazev, zakazky.popis, zakazky.obdobi, zakazky.kategorie, zakazky.stav FROM opravneni 
        INNER JOIN zakazky ON opravneni.zakazky_id = zakazky.id WHERE (stav = 1 OR stav = 3) AND uzivatele_id = $uziv_id AND opravneni.opravneni = $opravneni", $db_id);
      while ($row = mysql_fetch_array($dotaz, MYSQL_ASSOC)) 
      {
        $vysledek[] = new Zakazka(array("id"=>$row["id"],"nazev"=>$row["nazev"],"popis"=>$row["popis"],"obdobi"=>$row["obdobi"],"kategorie"=>$row["kategorie"],"stav"=>$row["stav"]));
      }
      $this->close($db_id);
    }          
    return $vysledek;              
  }


  public function get_zakazky_4uzivatel_a_mesic($uziv_id, $opravneni, $mesic)
  /*
  vrati vsechny aktivni a trvale zakazky, ke kterym ma dany uzivatel dane opravneni + zakazky ve kterych v danem mesici existuje zaznam den
  */
  {    
    $vysledek = array();
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query("SELECT zakazky.id, zakazky.nazev, zakazky.popis, zakazky.obdobi, zakazky.kategorie, zakazky.stav FROM opravneni 
        INNER JOIN zakazky ON opravneni.zakazky_id = zakazky.id WHERE (stav = 1 OR stav = 3) AND uzivatele_id = $uziv_id AND opravneni.opravneni = $opravneni", $db_id);


     $dotaz = mysql_query("
				SELECT DISTINCT * FROM 
				(
					(	
						SELECT zakazky.id, zakazky.nazev, zakazky.popis, zakazky.obdobi, zakazky.kategorie, zakazky.stav FROM opravneni 
        		INNER JOIN zakazky ON opravneni.zakazky_id = zakazky.id 
						WHERE (stav = 1 OR stav = 3) AND uzivatele_id = $uziv_id AND opravneni.opravneni = $opravneni
					)
					UNION ALL
					(
						SELECT DISTINCT zakazky.id, zakazky.nazev, zakazky.popis, zakazky.obdobi, zakazky.kategorie, zakazky.stav FROM dny 
        		INNER JOIN zakazky ON dny.zakazky_id = zakazky.id  
						WHERE dny.uzivatele_id = $uziv_id AND dny.mesic = ".$mesic->get_mesic()." AND dny.rok = ".$mesic->get_rok()."
					)
				) x ORDER BY x.kategorie", $db_id);
  

      while ($row = mysql_fetch_array($dotaz, MYSQL_ASSOC)) 
      {
        $vysledek[] = new Zakazka(array("id"=>$row["id"],"nazev"=>$row["nazev"],"popis"=>$row["popis"],"obdobi"=>$row["obdobi"],"kategorie"=>$row["kategorie"],"stav"=>$row["stav"]));
      }
      $this->close($db_id);
    }          
    return $vysledek;              
  }

  /*****/
  /*DNY*/ 
  /*****/
  public function get_dny($uziv_id="", $zak_id="", $rok="", $mesic="")
  /*
  vrati dny - lze urcit libovolny pocet parametru uzivatel, zakazka, rok, mesic 
  */
  {
    $vysledek = array();
    $pole["uzivatele_id"] = $uziv_id;
    $pole["zakazky_id"] = $zak_id;
    $pole["rok"] = $rok;
    $pole["mesic"] = $mesic;    
    $sql_dotaz = "";
    foreach($pole as $key => $val)
    {
      if($val != "")
      {
        if($sql_dotaz == "") $sql_dotaz = "SELECT * FROM dny WHERE $key = $val";
        else $sql_dotaz .= " AND $key = $val";
      }
    }
    if($sql_dotaz == "") $sql_dotaz = "SELECT * FROM dny";
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query($sql_dotaz,$db_id);
      while ($row = mysql_fetch_array($dotaz, MYSQL_ASSOC)) 
        {
          $vysledek[] = new Den($row);
        }      
      $this->close($db_id);
    }          
    return $vysledek;              
  }
  
  public function celkem_hodiny_4zakazka($zak_id)
  /*
  vrati soucet hodin pro danou zakazku
  */
  {
    $vysledek = false;
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query("SELECT SUM(hodiny) AS soucet FROM dny WHERE zakazky_id = $zak_id", $db_id);
      $row = mysql_fetch_array($dotaz, MYSQL_ASSOC);
      if(is_array($row))
      {
        $vysledek = $row["soucet"];
      }  
      $this->close($db_id);
    }          
    return $vysledek;  
  }
  
  public function set_den($obj)
  /*
  zmeni/vlozi zaznam do tabulky dny
  */
  {
    $values = $obj->to_array();
    $sql_dotaz = "";
    foreach($values as $key => $value)
    {
      if($value != "")
      {
        if($sql_dotaz == "") $sql_dotaz = "SELECT * FROM dny WHERE $key = $value";
        else $sql_dotaz .= " AND $key = $value";
      }
    }
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query($sql_dotaz,$db_id);
      if(mysql_num_rows($dotaz)>0) //zaznam existuje -> update
      {
        $row = mysql_fetch_array($dotaz, MYSQL_ASSOC);
        $this->close($db_id);
        $den_id = $row["id"];
        $sql_dotaz = "UPDATE dny SET hodiny = $hodiny WHERE id = $den_id";
      }  
      else //zaznam neexistuje -> vlozeni noveho
      {
        $sql_values = "";
        $sql_columns = "";
        foreach($values as $key => $value)
        {
          $sql_values .= "'$value', ";
          $sql_columns .= "$key, ";
        }
        $sql_values = substr($sql_values,0,-2);        
        $sql_columns = substr($sql_columns,0,-2);        
        $sql_dotaz = "INSERT INTO dny ($sql_columns) VALUES (".$sql_values.")";
      }
    }
    return $this->database_query($sql_dotaz);  
  }

//------------------------------ private functions -------------------------------  

  private function database_query($sql_dotaz)
  {
    $vysledek = false;
    $autoinc = false;
    if($db_id = $this->connect())
    {
      if (mysql_query($sql_dotaz, $db_id)!=false)
      {
 	      $autoinc = mysql_insert_id();
        if (mysql_query("COMMIT;", $db_id)!=false) $vysledek = true;
      }
      else die ("Nemam pristup pro zapis do db!<br>");
      $this->close($db_id);
    }
    if($autoinc != false) return $autoinc;
    return $vysledek;
  }

  private function connect()
  {
    if($db_id = mysql_connect($this->dbSrv,$this->dbUserName,$this->dbPasswd))
    {
      //echo "Navázáno spojení se serverem!<br>";
      $db_id2 = mysql_select_db($this->dbName);
      if (!$db_id2) die ("Nelze připojit databázi <b>'$dbName'</b>: ". mysql_error());
      else
      {
        mysql_query("SET NAMES 'utf8'");
        mysql_query("SET CHARACTER SET utf8");
        mysql_query("SET COLLATION_CON­NECTION='utf8_czech_ci'"); 
      }
    }
    else die ("Nepodařilo se navázat spojení se serverem!<br>");
    return $db_id;    
  }

  private function close($db_id)
  {
    /*
    if(isset($db_id) && is_resource($db_id))
    {
      mysql_close($db_id);
    }
    else
    {
      mysql_close();
    }
    */
  }

}//konec tridy
?>
