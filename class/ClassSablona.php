<?php
class Sablona
{
	static public function get_html($dir_root, $file_name, $title, $obsah = "")
  {
	  // hlavni menu
    $menu = '';  
		$menu .= '<span class="menu'.Util::jeAktivni("index.php").'"><a class="menu'.Util::jeAktivni("index.php").'" href="./index.php">home</a></span>';
    $menu .= '<span class="menu'.Util::jeAktivni("index.php").'"><a class="menu'.Util::jeAktivni("lide.php").'" href="./lide.php">lidé</a></span>';
		$menu .= '<span class="menu'.Util::jeAktivni("index.php").'"><a class="menu'.Util::jeAktivni("zakazky.php").'" href="./zakazky.php">zakázky</a></span>';
		$menu .= '<span class="menu'.Util::jeAktivni("index.php").'"><a class="menu'.Util::jeAktivni("working-time.php").'" href="./working-time.php">working time</a></span>';
		$menu .= '<span class="menu'.Util::jeAktivni("index.php").'"><a class="menu'.Util::jeAktivni("info.php").'" href="./info.php">info</a></span>';
    
		$odhlasit = '';	
		if($logged = Util::is_logged())
		{
		  $odhlasit .= '<span class="odhlasit">'.$logged->get_jmeno().' '.$logged->get_prijmeni().'</span>
                    <span class="odhlasit"><a class="odhlasit" href="index.php?logout">Odhlásit</a></span>';
		}
	
		return 
      '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
  		<html>
  			<head>
  			  	<meta http-equiv="Content-Language" content="cs">
  			  	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  			     <link rel="stylesheet" type="text/css" media="all" href="'.$dir_root.'styl.css">
  		  		<title>'.$title.'</title>
  			</head>
  			<body>
  				<div class="aplikace">Working Time v2.3</div>
  				<div class="menu">'.$menu.'</div>
  				<div class="obsah">'.$obsah.'</div>
  				<div class="odhlasit">'.$odhlasit.'</div>         
  			</body>
  		</html>
	    ';
	}  
} // end Class
?>
