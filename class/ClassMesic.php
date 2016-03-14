<?php
/*
  - zobrazi tabulku pro dany mesic v roce
  - zobrazi formular pro dany mesic
*/
class Mesic
{
  private $script;
  private $rok;
  private $mesic;
    
  public function __construct($script)                     
  {
    $this->script = $script;
    $this->rok = date("Y");
    $this->mesic = date("n");
  }

// public functions

  public function get_rok()
  /*vrati tridni promennou $rok*/
  {
    return $this->rok;
  }
  public function get_mesic()
  /*vrati tridni promennou $mesic*/
  {
    return $this->mesic;
  }
  
  public function view($radky="",$id="",$nadpis="")
  // doplnit!!!!!
  /**
   * id je id uzivatele nebo zakazky
   * nadpis je jmeno uzivatele nebo zakazky
   * radky je pole, kde klic je poradove cislo a hodnota je objekt typu clovek nebo zakazka + pole dny
   * na zaklade parametru GET vypln zobrazi prehled mesice nebo editacni formular
   */   
  {
    if(isset($_GET["vypln"]))
    {
      return $this->vypln_mesic($radky,$id,$nadpis);
    }  
    return $this->zobraz_mesic($radky,$id,$nadpis);
  }
  
  public function nabidka()
  /**
   *  vraci html string: nabidku odkazu predchozi, tento, dalsi, vypln
   *  pouziva stranka working-time*/
  {
    $vypln = "";
    $vypln2 = "";
    if(isset($_GET["vypln"]))
    {
      $vypln = "&vypln";
      $vypln2 = "?vypln";
    }
    
    $echo = "<div class = \"nabidka\">"; /* pridano */
    $echo .= "<h2>Měsíc: ".$this->mesic." / ".$this->rok."</h2>";
    $echo .= "<a class=\"nabidka\" href = \"".$this->script."?old&mesic=".$this->get_p_mesic()."&rok=".$this->get_p_rok()."$vypln\">Předchozí</a> | "; 
    $echo .= "<a class=\"nabidka\" href = \"".$this->script."?old"."$vypln2\">Tento měsíc</a> | ";
    $echo .= "<a class=\"nabidka\" href = \"".$this->script."?old&mesic=".$this->get_d_mesic()."&rok=".$this->get_d_rok()."$vypln\">Další</a> | ";
    $echo .= "<a class=\"vypln\" href = \"".$this->script."?old&mesic=".$this->mesic."&rok=".$this->rok."&vypln\">Vyplň</a>";
    $echo .= "</div>"; /* pridano */
      
    return $echo;
  }

  public function nabidka_zakazky()
  /**
   *  vraci html string: nabidku odkazu predchozi, tento, dalsi
   *  pouziva stranka zakazky   */
  {
    $echo = "<h2>Měsíc: ".$this->mesic." / ".$this->rok."</h2>";
    $echo .= "<a class=\"nabidka\" href = \"".$this->script."?old&mesic=".$this->get_p_mesic()."&rok=".$this->get_p_rok()."\">Předchozí</a> | "; 
    $echo .= "<a class=\"nabidka\" href = \"".$this->script."?old"."\">Tento měsíc</a> | ";
    $echo .= "<a class=\"nabidka\" href = \"".$this->script."?old&mesic=".$this->get_d_mesic()."&rok=".$this->get_d_rok()."\">Další</a>";
    return $echo;
  }

  public function aktualizuj()
  /*aktualizuje stranku pro hodnoty ulozene v GET nebo POST*/
  {
    if(isset($_GET["rok"]))
    {
      $this->set_rok($_GET["rok"]);
    }
    if(isset($_GET["mesic"]))
    {
      $this->set_mesic($_GET["mesic"]);
    }
    if(isset($_POST["rok"]))
    {
      $this->set_rok($_POST["rok"]);
    }
    if(isset($_POST["mesic"]))
    {
      $this->set_mesic($_POST["mesic"]);
    }
  } 

  public function get_dny()
  /**
   *vraci pole $dny, kde na klici nezalezi a hodnota jsou id */  
  {
    $dny = "";
    if((isset($_POST["odeslano"]))AND(isset($_POST["mesic"]))AND(isset($_POST["rok"]))AND(isset($_POST["id"])))
    { 
      foreach($_POST as $key => $value)
      {
        if(substr($key,0,2)=="x_")
        {
          foreach($value as $den => $hodiny)
          {
            if($hodiny != "")
            {
              $dny[] = array("id"=>$_POST["id"],"pole_id"=>str_replace("x_","",$key),"rok"=>$_POST["rok"],"mesic"=>$_POST["mesic"],"den"=>$den,"hodiny"=>$hodiny); 
            }
          }
        }
      }
    }
    return $dny;
  }

  public function zobraz_mesic($radky,$id,$nadpis)
  {
    $pocet_dnu = $this->pocet_dnu();
    $echo = "<table><tr><th class=\"nazev\">$nadpis</th>";
    for($i=1;$i<=$pocet_dnu;$i++)
    {
      $echo .= "<th class=\"den\"><div class=\"den\">$i.</div></th>";
    }                                         
    $echo .= "<th class=\"soucet\"><div class=\"soucet\"></div></th></tr>";
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
        if($key%2) /*lichy sloupec*/
        {
          $echo .= "<tr class=\"lichy\"><td class=\"nazev\" title=\"".$value["popis"]."\"><div class=\"nazev\">".$cinnost.$value["jmeno"]."</div></td>";
        }
        else /*sudy sloupec*/
        {
          $echo .= "<tr class=\"sudy\"><td class=\"nazev\" title=\"".$value["popis"]."\"><div class=\"nazev\">".$cinnost.$value["jmeno"]."</div></td>"; /* class="nazev-sudy" => class="nazev"*/
        }
        for($i=1;$i<=$pocet_dnu;$i++)
        {                 
          if(isset($value["dny"][$i]))
          {  
            $echo .= "<td class=\"den\">".$value["dny"][$i]."</td>";
            $soucet_sloupec[$i] = $soucet_sloupec[$i] + $value["dny"][$i];
            $soucet_radek = $soucet_radek + $value["dny"][$i];  
          }
          else
          {
            $echo .= "<td class=\"den\">-</td>";         
          }          
        }        
        $echo .= "<td class=\"soucet\">".$soucet_radek."</td></tr>";
      }
    }  
    $echo .= "<tr><td></td>";
    for($i=1;$i<=$pocet_dnu;$i++)
    {
      $echo .= "<td class=\"soucet\">$soucet_sloupec[$i]</td>";
    }                                         
    $echo .= "<td class=\"soucetsoucet\">".array_sum($soucet_sloupec)."</td></tr></table>";
    return $echo;                                                                                                             
  }

  public function mesic2csv($radky,$id,$nadpis)
  {
    $pocet_dnu = $this->pocet_dnu();
    //$echo .= "".$cinnost.$value["jmeno"].";";
    $echo = substr($nadpis["zak_cinnost"],2).";".$nadpis["zak_nazev"].";";
    for($i=1;$i<=$pocet_dnu;$i++)
    {
      $soucet_sloupec[$i] = 0;
    }
    if(is_array($radky))
    {  
      foreach ($radky as $key=>$value)
      {
        $cinnost = "";
        if($value["cinnost"]!="")
        {
          $cinnost = substr($value["cinnost"],2).": ";
        }
        for($i=1;$i<=$pocet_dnu;$i++)
        {                 
          if(isset($value["dny"][$i]))
          {  
            $soucet_sloupec[$i] = $soucet_sloupec[$i] + $value["dny"][$i];  
          }
        }        
      }
    }  
    $echo .= "".array_sum($soucet_sloupec).";\n";
    return $echo;                                                                                                             
  }
  
  //depricated
  public function mesic_2_csv($radky,$id,$nadpis)
  {
    $pocet_dnu = $this->pocet_dnu();
    $echo = "$nadpis;";
    for($i=1;$i<=$pocet_dnu;$i++)
    {
      $echo .= "$i;";
    }                                         
    $echo .= "součet po zakázkách;\n";
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
        $echo .= "".$cinnost.$value["jmeno"].";";
        for($i=1;$i<=$pocet_dnu;$i++)
        {                 
          if(isset($value["dny"][$i]))
          {  
            $echo .= "".$value["dny"][$i].";";
            $soucet_sloupec[$i] = $soucet_sloupec[$i] + $value["dny"][$i];
            $soucet_radek = $soucet_radek + $value["dny"][$i];  
          }
          else
          {
            $echo .= "0;";         
          }          
        }        
        $echo .= "".$soucet_radek.";\n";
      }
    }  
    $echo .= "součet po dnech;";
    for($i=1;$i<=$pocet_dnu;$i++)
    {
      $echo .= "$soucet_sloupec[$i];";
    }                                         
    $echo .= "".array_sum($soucet_sloupec).";\n\n";
    return $echo;                                                                                                             
  }

// private functions
  
  private function vypln_mesic($radky,$id,$nadpis)         //pridat soucty
  {
    $pocet_dnu = $this->pocet_dnu();
    $echo = "<form action=\"".$this->script."\" method=\"post\"><table><tr><th class=\"nazev\">$nadpis</th>";
    for($i=1;$i<=$pocet_dnu;$i++)
    {
      $echo .= "<th class=\"den\"><div class=\"den\">$i.</div></th>";
    } 
    $echo .= "<th class=\"soucet\"><div class=\"soucet\"></div></th></tr>";
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
          $echo .= "<tr class=\"lichy_vypln\"><td class=\"nazev\" title=\"".$value["popis"]."\"><div class=\"nazev\">".$cinnost.$value["jmeno"]."</div></td>"; /* <tr class="lichy"=> class="lichy_vypln" */
        }
        else /*sudy radek*/
        {
          $echo .= "<tr class=\"sudy_vypln\"><td class=\"nazev\" title=\"".$value["popis"]."\"><div class=\"nazev\">".$cinnost.$value["jmeno"]."</div></td>"; /* <tr class="sudy"=> class="sudy_vypln" <td class="nazev-sudy" => class="nazev" */
        }
        //$echo .= "<tr><td class=\"nazev\" title=\"".$value["popis"]."\">".$cinnost.$value["jmeno"]."</td>";
        for($i=1;$i<=$pocet_dnu;$i++)
        {                 
          if(isset($value["dny"][$i]))
          {  
            if($key%2) /*lichy radek*/
            {
              $echo .= "<td class=\"den\"><input class=\"lichy\" type=\"text\" maxlength=\"2\" name=\"".$this->uprav_nazev($value["id"])."[$i]\" value=\"".$value["dny"][$i]."\"></td>"; /* pridano <td class="den"> */
            }
            else /*sudy radek*/
            {
              $echo .= "<td class=\"den\"><input class=\"sudy\" type=\"text\" maxlength=\"2\" name=\"".$this->uprav_nazev($value["id"])."[$i]\" value=\"".$value["dny"][$i]."\"></td>"; /* pridano <td class="den"> */  
            }
            $soucet_sloupec[$i] = $soucet_sloupec[$i] + $value["dny"][$i];
            $soucet_radek = $soucet_radek + $value["dny"][$i]; 
          }
          else
          {
            if($key%2) /*lichy radek*/
            {
              $echo .= "<td><input class=\"lichy\" type=\"text\" maxlength=\"2\" name=\"".$this->uprav_nazev($value["id"])."[$i]\" value=\"\"></td>";         
            }
            else /*sudy radek*/
            {
              $echo .= "<td><input class=\"sudy\" type=\"text\" maxlength=\"2\" name=\"".$this->uprav_nazev($value["id"])."[$i]\" value=\"\"></td>";         
            }
          }
        }
        $echo .= "<td class=\"soucet\">".$soucet_radek."</td></tr>";
      }  
    }
    $echo .= "<tr><td></td>";
    for($i=1;$i<=$pocet_dnu;$i++)
    {
      $echo .= "<td class=\"soucet\">$soucet_sloupec[$i]</td>";
    } 
    $echo .= "<td class=\"soucetsoucet\">".array_sum($soucet_sloupec)."</td></tr></table>
      <input type=\"hidden\" name=\"mesic\" value=\"".$this->mesic."\">
      <input type=\"hidden\" name=\"rok\" value=\"".$this->rok."\">
      <input type=\"hidden\" name=\"id\" value=\"".$id."\">
      <input type=\"hidden\" name=\"token\" value=\"".Util::get_token()."\">
      <input type=\"submit\" name=\"odeslano\" value=\"odeslat\"></form>";
    $echo .= "<a href = \"".$this->script."?mesic=".$this->mesic."&rok=".$this->rok."\">Zpět</a>";  
    return $echo;
  }

  private function set_mesic($mesic)
  /**/
  {
    if(($mesic>12)OR($mesic<1)) 
    {
      die("Mesic musi byt od 1 do 12!");
    }
    $this->mesic = $mesic;   
  }
  private function set_rok($rok)
  {
    if(($rok>2112)OR($rok<2012))
    {
      die("Rok musi byt v rozmezi 2012 az 2112!");
    }
    $this->rok = $rok; 
  }
  
  private function get_p_mesic()
  {
    $p_mesic = $this->mesic-1;
    if($p_mesic<1)
    {
      $p_mesic = 12;
    }
    return $p_mesic;
  }
  private function get_p_rok()
  {
    $p_rok = $this->rok;
    if($this->get_p_mesic()==12)
    {
      $p_rok = $this->rok-1;
    }
    return $p_rok;
  }
  private function get_d_mesic()
  {
    $d_mesic = $this->mesic+1;
    if($d_mesic>12)
    {
      $d_mesic = 1;
    }
    return $d_mesic;
  }
  private function get_d_rok()
  {
    $d_rok = $this->rok;
    if($this->get_d_mesic()==1)
    {
      $d_rok = $this->rok+1;
    }
    return $d_rok;
  }

  private function pocet_dnu()
  /*vrati pocet dnu v aktualnim mesici*/
  {
    return cal_days_in_month(CAL_GREGORIAN, $this->mesic, $this->rok);
  }
  
  private function uprav_nazev($jmeno)
  /*vlozi pred retezec "x_"
  */
  {
    $jmeno = str_replace(" ","_",$jmeno);
    $jmeno = str_replace("\"","_",$jmeno);
    $jmeno = str_replace("/","_",$jmeno);
    return "x_".$jmeno;
  }
  
//depricated
  private function is_modif_nazev($zakazky,$jmeno_ref)
  {
    foreach ($zakazky as $jmeno=>$hodiny)
    {
      if($this->uprav_nazev($jmeno)==$jmeno_ref)
      {
        return $jmeno;
      }  
    }
    return false;
  }
  
}// konec tridy
?>
