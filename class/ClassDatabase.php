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
  
  public function get_zakazky_all($order = "cinnost")
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
  
  public function get_zakazky_active($order = "cinnost")
  /*
  vrati aktivni zakazky
  serazene podle promenne $order
  */
  {    
    $vysledek = array();
    if($db_id = $this->connect())
    {   
      //$dotaz = mysql_query("SELECT * FROM zakazky WHERE stav <> 0", $db_id);
      $dotaz = mysql_query("SELECT * FROM zakazky WHERE stav <> 0 ORDER BY zakazky.$order", $db_id);
      while ($row = mysql_fetch_array($dotaz, MYSQL_ASSOC)) 
      {
        $vysledek[] = new Zakazka($row);
      }
      $this->close($db_id);
    }          
    return $vysledek;              
  }
  
  public function get_zakazky_inactive($order = "cinnost")
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
  
  public function get_uzivatele_all($order = "prijmeni")
  /*
  vrati vsechny uzivatele serazene podle promenne $order 
  */
  {
    $vysledek = false;
    if($db_id = $this->connect())
    {   
      $dotaz = mysql_query("SELECT * FROM uzivatele ORDER BY uzivatele.$order", $db_id);
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
      $dotaz = mysql_query("SELECT * FROM uzivatele WHERE role > 0 ORDER BY uzivatele.$order", $db_id);
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
      $dotaz = mysql_query("SELECT id, prijmeni, jmeno, role FROM uzivatele WHERE id = $uziv_id AND role > 0 AND heslo LIKE '$heslo'", $db_id);
      $row = mysql_fetch_array($dotaz, MYSQL_ASSOC);
      if(is_array($row))
      {
        $vysledek = new Uzivatel(array("id"=>$row["id"],"prijmeni"=>$row["prijmeni"],"jmeno"=>$row["jmeno"],"login"=>"","heslo"=>"","role"=>$row["role"]));
      }  
      $this->close($db_id);
    }          
    return $vysledek;  
  }
  
  public function edit_zakazka($id, $nazev, $popis, $cinnost, $stav)
  /*
  edituje zakazku
  */
  {
    $vysledek = false;
    if($db_id = $this->connect())
    {
      $sql_dotaz = "UPDATE zakazky SET nazev='$nazev',popis='$popis',cinnost='$cinnost',stav='$stav' WHERE id=$id";
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
  
  public function add_zakazka($nazev, $popis, $cinnost, $stav = 1)
  /*
  vlozi novou zakazku
  */
  {
    $vysledek = false;
    if($db_id = $this->connect())
    {
      $sql_dotaz = "INSERT INTO zakazky (nazev,popis,cinnost,stav) VALUES ('$nazev','$popis','$cinnost','$stav')";
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
  
  public function edit_uzivatel($id, $prijmeni, $jmeno, $role)
  /*
  edituje uzivatele
  */
  {
    $vysledek = false;
    if($db_id = $this->connect())
    {
      $sql_dotaz = "UPDATE uzivatele SET prijmeni='$prijmeni',jmeno='$jmeno',role='$role' WHERE id=$id";
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
  
  public function add_uzivatel($prijmeni, $jmeno, $role = 1)
  /*
  vlozi noveho uzivatele
  */
  {
    $vysledek = false;
    if($db_id = $this->connect())
    {
      $login = strtolower(Util::remove_diacritics($jmeno.".".$prijmeni));
      $heslo = strtolower(Util::remove_diacritics($jmeno.".".$prijmeni));
      $sql_dotaz = "INSERT INTO uzivatele (prijmeni,jmeno,login,heslo,role) VALUES ('$prijmeni','$jmeno','$login','$heslo','$role')";
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
   
  public function get_dny($uziv_id="", $zak_id="", $rok="", $mesic="")
  /*
  vrati dny - lze urcit libovolny pocet parametru uzivatel, zakazka, rok, mesic 
  */
  {
    $vysledek = false;
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
  
  public function set_den($zakazky_id,$uzivatele_id,$rok,$mesic,$den,$hodiny)
  /*
  zmeni/vlozi hodiny do tabulky dny pro danou kombinaci zakazka-uzivatel-rok-mesic-den
  */
  {
    $vysledek = false;
    if($db_id = $this->connect())
    {
      $dotaz = mysql_query("SELECT id FROM dny WHERE zakazky_id = $zakazky_id AND uzivatele_id = $uzivatele_id AND rok = $rok AND mesic = $mesic AND den = $den", $db_id);
      if(mysql_num_rows($dotaz)>0)
      {
        $row = mysql_fetch_array($dotaz, MYSQL_ASSOC);
        $den_id = $row["id"];
        $sql_dotaz = "UPDATE dny SET hodiny = $hodiny WHERE id = $den_id";
      }  
      else
      {
        $sql_dotaz = "INSERT INTO dny (zakazky_id,uzivatele_id,rok,mesic,den,hodiny) VALUES ($zakazky_id,$uzivatele_id,$rok,$mesic,$den,$hodiny)";
      }
      if (mysql_query($sql_dotaz, $db_id)!=false)
      {
        if (mysql_query("COMMIT;", $db_id)!=false)       
        {
          $vysledek = true;
        }      
      }
      else die ("Nemam pristup pro zapis do db!<br>");
      $this->close($db_id);
    }
    return $vysledek;
  }
  
     
//------------------------------ private functions -------------------------------  

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
    mysql_close($db_id);
  }

}//konec tridy
?>
