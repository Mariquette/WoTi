<?php
require('fpdf.php');

class PDF extends FPDF
{
  // Load fonts from ./font directory 
  function LoadFonts()
  {
    $this->AddFont('times','','times.php');
    $this->AddFont('times','B','timesbd.php');
    $this->AddFont('times','I','timesi.php');
    $this->AddFont('times','BI','timesbi.php');
    
    $this->AddFont('courier','','cour.php');
    $this->AddFont('courier','B','courbd.php');
    $this->AddFont('courier','I','couri.php');
    $this->AddFont('courier','BI','courbi.php');
    
    $this->AddFont('arial','','arial.php');
    $this->AddFont('arial','B','arialbd.php');
    $this->AddFont('arial','I','ariali.php');
    $this->AddFont('arial','BI','arialbi.php');
  
    $this->AddFont('calibri','','calibri.php');
    $this->AddFont('calibri','B','calibrib.php');
    $this->AddFont('calibri','I','calibrii.php');
  }
  
  // Page header
  function Header()
  {
  	// Logo
  	$this->SetDrawColor(0,116,188);
    $this->SetLineWidth(1);
    $this->Line(30,15,$this->GetPageWidth()-30-40,15);
    $this->Image('logo.png',$this->GetPageWidth()-30-40+2,12,40,0);
  	// Font
  	$this->SetFont('arial','',6);
  	// Title
  	$this->Ln(5);
  	$this->Cell(20);
    $this->Cell(0,10,'Rigaku Innovative Technologies Europe s.r.o.',0,0,'L');
  	// Line break
  	$this->Ln(10);
  }
  
  // Page footer
  function Footer()
  {
  	// Position at 1.5 cm from bottom
  	$this->SetY(-15);
  	// Line
    $this->SetDrawColor(0,116,188);
    $this->SetLineWidth(1);
    $this->Line(30,$this->GetPageHeight()-15,$this->GetPageWidth()-30,$this->GetPageHeight()-15);
  	// Font
  	$this->SetFont('arial','',6);
    // Date
  	$this->Cell(20);
    $this->Cell(0,10,date('j.n.Y G:i'),0,0,'L');
  	// Page number
    $this->SetX(-45);
  	$this->Cell(0,10,'Strana '.$this->PageNo().'/{nb}',0,0,'P');
  }
  
  // Cell with horizontal scaling if text is too wide
  function CellFit($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $scale=false, $force=false)
  /*
  scale false: character spacing
        true: horizontal scaling
  force false: only space/scale if necessary (not when text is short enough to fit)
        true: always space/scale 
  */
  {
    // Get string width
    $str_width=$this->GetStringWidth($txt);
    // Calculate ratio to fit cell
    if($w==0)$w = $this->w-$this->rMargin-$this->x;
    $ratio = ($w-$this->cMargin*2)/$str_width;
    $fit = ($ratio < 1 || ($ratio > 1 && $force));
    if ($fit)
    {
        if ($scale)
        {
            // Calculate horizontal scaling
            $horiz_scale=$ratio*100.0;
            // Set horizontal scaling
            $this->_out(sprintf('BT %.2F Tz ET', $horiz_scale));
        }
        else
        {
            // Calculate character spacing in points
            $char_space=($w-$this->cMargin*2-$str_width)/max($this->MBGetStringLength($txt)-1, 1)*$this->k;
            // Set character spacing
            $this->_out(sprintf('BT %.2F Tc ET', $char_space));
        }
        // Override user alignment (since text will fill up cell)
        $align='';
      }
    // Pass on to Cell method
    $this->Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
    // Reset character spacing/horizontal scaling
    if ($fit) $this->_out('BT '.($scale ? '100 Tz' : '0 Tc').' ET');
  }
  // Patch to also work with CJK double-byte text
  function MBGetStringLength($s)
  {
    if($this->CurrentFont['type']=='Type0')
    {
      $len = 0;
      $nbbytes = strlen($s);
      for ($i = 0; $i < $nbbytes; $i++)
      {
          if (ord($s[$i])<128) $len++;
          else
          {
              $len++;
              $i++;
          }
      }
      return $len;
    }
    else return strlen($s);
  }
  
  // Row in table
  function Row($txt,$height,$widths,$aligns,$fills = false) // $height = vyska jednoho radku
  {
    // Calculate the height of the row
    $nb = 0;
    for($i=0;$i<count($txt);$i++) $nb = max($nb,$this->NbLines($widths[$i],$txt[$i]));
    $h = $height*$nb;
    // If the height would cause an overflow, add a new page immediately    
    if($this->GetY()+$h > $this->PageBreakTrigger) $this->AddPage($this->CurOrientation);
    // Draw the cells of the row
    for($i=0;$i<count($txt);$i++)
    {
        // Save the current position
        $x = $this->GetX();
        $y = $this->GetY();
        // Print the text
        $this->MultiCell($widths[$i],$height,$txt[$i],0,$aligns[$i],$fills[$i]);
        // Draw the border
        $this->Rect($x,$y,$widths[$i],$h);
        // Put the position to the right of the cell
        $this->SetXY($x+$widths[$i],$y);
    }
    // Go to the next line
    $this->Ln($h);
  }
  // Computes the number of lines a MultiCell of width will take
  function NbLines($width,$txt)
  {
    $cw = &$this->CurrentFont['cw'];
    if($width==0) $width = $this->width-$this->rMargin-$this->x;
    $wmax = ($width-2*$this->cMargin)*1000/$this->FontSize;
    $s = str_replace("\r",'',$txt);
    $nb = strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
  }
    
}

?>
