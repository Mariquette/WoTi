<?php

class Outputs
{
  
  static function mesic2csv($radky,$nazev,$cinnost,$pocet_dnu)
  {
    //$echo = substr($cinnost,2).";".$nazev.";";
    $echo = $cinnost.";".$nazev.";";
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
  
  static function zakazky2pdf($list)
  {
    header('Content-Type','text/html; charset=windows-1250');
    // Instanciation of inherited class
    $pdf = new PDF();
    $pdf->LoadFonts();
    $pdf->AliasNbPages();
    $pdf->AddPage('P','A4');
    // nadpis
    $pdf->SetFont('calibri','B',12);
    $pdf->Cell(0,10,iconv('UTF-8','WINDOWS-1250',"Přehled zakázek"),0,1,'C');
    $pdf->ln(5);
    // tabulka - hlavicka
    $pdf->SetFont('calibri','B',9);
    $widths = array(30,20,120,20);
    $aligns = array('L','L','L','L');
    $txt[0] = iconv('UTF-8','WINDOWS-1250','Název');
    $txt[1] = iconv('UTF-8','WINDOWS-1250','Kategorie');
    $txt[2] = iconv('UTF-8','WINDOWS-1250','Popis');
    $txt[3] = iconv('UTF-8','WINDOWS-1250','Odpovědný');
    //$txt[3] = iconv('UTF-8','WINDOWS-1250','Stav');
    $pdf->Row($txt,10,$widths,array('C','C','C','C'),true);
    // tabulka - vnitrek
    $pdf->SetFont('calibri','',9);
    $pdf->SetFillColor(220);
    $fill = true;
    foreach ($list as $id => $obj)
    {
      $txt[0] = iconv('UTF-8','WINDOWS-1250',$obj->get_nazev());
      $txt[1] = iconv('UTF-8','WINDOWS-1250',$obj->get_kategorie_name());
      $txt[2] = iconv('UTF-8','WINDOWS-1250',$obj->get_popis());
      $osoby = "";
      foreach ($obj->get_odpovedny() as $osoba)
      {
        $osoby .= $osoba->get_zkratka().", ";
      }
      if($osoby != "") $osoby = substr($osoby,0,-2);
      $txt[3] = iconv('UTF-8','WINDOWS-1250',$osoby);
      //$txt[3] = iconv('UTF-8','WINDOWS-1250',$obj->get_stav_name());
      $pdf->Row($txt,5,$widths,$aligns,array($fill,$fill,$fill,$fill));
      //zvyrazneni kazdeho 2. radku
      $fill = !$fill;
    }
    // vystup
    $pdf->Output();    
  }
    
  static function kalendar2pdf($mesic,$list)
  {
    header('Content-Type','text/html; charset=windows-1250');
    // Instanciation of inherited class
    $pdf = new PDF();
    $pdf->LoadFonts();
    $pdf->AliasNbPages();
    $pdf->AddPage('L','A4');
    // nadpis
    $pdf->SetFont('calibri','B',12);
    $mesice = Database::get_names_mesic();
    $pdf->Cell(0,5,iconv('UTF-8','WINDOWS-1250',mb_strtoupper($mesice[$mesic->get_mesic()],'UTF-8').' '.$mesic->get_rok()),0,1,'C');
    $pdf->ln(5);
    // tabulka - hlavicka
    $pdf->SetFont('calibri','B',9);
    $pdf->SetFillColor(250,180,140);
    $svatky = Database::get_svatky($mesic->get_rok());
    $txt[0] = iconv('UTF-8','WINDOWS-1250','');
    $widths[0] = 30;
    $aligns[0] = 'L';
    $fills[0] = false;
    $txt[1] = iconv('UTF-8','WINDOWS-1250','Dovolená');
    $widths[1] = 20;
    $aligns[1] = 'C';
    $fills[1] = false;
    for ($i=1;$i<=$mesic->pocet_dnu();$i++)
    {
      $txt[$i+1] = iconv('UTF-8','WINDOWS-1250',$i);
      $widths[$i+1] = 7;
      $aligns[$i+1] = 'C';
      $fills[$i+1] = false;
      //zvyrazneni vikendu
      if ($mesic->den_v_tydnu($i)==6) $fills[$i+1] = true;
      if ($mesic->den_v_tydnu($i)==7) $fills[$i+1] = true;
      //zvyrazneni svatku
      foreach($svatky as $svatek)
      {
        if ( ($i==$svatek[0]) AND ($mesic->get_mesic()==$svatek[1]) ) $fills[$i+1] = true;
      }
    }
    $pdf->Row($txt,10,$widths,$aligns,$fills);
    // tabulka - vnitrek (widths,aligns zustavaji stejne jako v hlavicce)
    $pdf->SetFont('calibri','',9);
    $fill = true;
    foreach ($list as $id => $obj)
    {
      $txt[0] = iconv('UTF-8','WINDOWS-1250',$obj->get_prijmeni());
      $txt[1] = '';
      for ($i=1;$i<=$mesic->pocet_dnu();$i++)
      {
        $txt[$i+1] = '';
        //zvyrazneni vikendu
        if ($mesic->den_v_tydnu($i)>5) $txt[$i+1] = 'X';
        //zvyrazneni svatku
        foreach($svatky as $svatek)
        {
          if ( ($i==$svatek[0]) AND ($mesic->get_mesic()==$svatek[1]) ) $txt[$i+1] = 'X';
        }
      }
      //zvyrazneni kazdeho 2. radku
      $x = $pdf->GetX();
      $pdf->SetFillColor(220);
      $pdf->Cell(array_sum($widths),6,'',0,0,'L',$fill);
      $fill = !$fill;
      //vykresleni radku
      $pdf->SetX($x);
      $pdf->SetFillColor(250,180,140);
      $pdf->Row($txt,6,$widths,$aligns,$fills);
    }
    // vysvetlivky
    $pdf->SetFont('Calibri','B',8);
    $pdf->Ln();
    $pdf->Cell(40,5,iconv('UTF-8','WINDOWS-1250','Dovolená'),0,0,'L');
    $pdf->Cell(30,5,iconv('UTF-8','WINDOWS-1250','D'),0,0,'L');
    $pdf->Cell(40,5,iconv('UTF-8','WINDOWS-1250','Sick Day'),0,0,'L');
    $pdf->Cell(30,5,iconv('UTF-8','WINDOWS-1250','SD'),0,0,'L');
    $pdf->Ln();
    $pdf->Cell(40,5,iconv('UTF-8','WINDOWS-1250','Služební cesta'),0,0,'L');
    $pdf->Cell(30,5,iconv('UTF-8','WINDOWS-1250','SC'),0,0,'L');
    $pdf->Cell(40,5,iconv('UTF-8','WINDOWS-1250','Lékař'),0,0,'L');
    $pdf->Cell(30,5,iconv('UTF-8','WINDOWS-1250','L'),0,0,'L');
    $pdf->Ln();
    $pdf->Cell(40,5,iconv('UTF-8','WINDOWS-1250','Náhradní volno'),0,0,'L');
    $pdf->Cell(30,5,iconv('UTF-8','WINDOWS-1250','NV'),0,0,'L');
    $pdf->Cell(40,5,iconv('UTF-8','WINDOWS-1250','Nemoc'),0,0,'L');
    $pdf->Cell(30,5,iconv('UTF-8','WINDOWS-1250','N'),0,0,'L');
    $pdf->Ln();
    $pdf->Cell(40,5,iconv('UTF-8','WINDOWS-1250','Práce z domu'),0,0,'L');
    $pdf->Cell(30,5,iconv('UTF-8','WINDOWS-1250','PD'),0,0,'L');
    $pdf->Cell(40,5,iconv('UTF-8','WINDOWS-1250','Neplacené volno'),0,0,'L');
    $pdf->Cell(30,5,iconv('UTF-8','WINDOWS-1250','NV'),0,0,'L');
    $pdf->Ln();
        
    // test vicestrankoveho dokumentu
//    for($i=1;$i<=40;$i++) $pdf->Cell(0,10,'Printing line number '.$i,0,1);    
    // test fontu
/*
    $pdf->SetFont('arial','',10);
    $pdf->Ln();
    $pdf->Write(10,iconv('UTF-8','WINDOWS-1250','Ó, náhlý déšť již zvířil prach a čilá laň teď běží s houfcem gazel k úkrytům.'));
    $pdf->Write(10,iconv('UTF-8','WINDOWS-1250','ó, NÁHLÝ DÉŠŤ JIŽ ZVÍŘIL PRACH A ČILÁ LAŇ TEĎ BĚŽÍ S HOUFCEM GAZEL K ÚKRYTŮM.'));
    $pdf->Ln();
    $pdf->Write(10,iconv('UTF-8','WINDOWS-1250','příšerně žluťoučký kůň úpěl ďábelské ódy'));
    $pdf->Write(10,iconv('UTF-8','WINDOWS-1250','PŘÍŠERNĚ ŽLUŤOUČKÝ KŮŇ ÚPĚL ĎÁBELSKÉ ÓDY'));
*/
    
    // vystup
    $pdf->Output();    
  }

}//konec tridy

?>
