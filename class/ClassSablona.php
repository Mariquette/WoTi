<?php
class Sablona
{
	static public function get_html($dir_root, $file_name, $title, $obsah = "")
  {
	  // hlavni menu
    $menu = '';  
		$menu .= '<a class="menu'.Util::jeAktivni("index.php").'" href="./index.php">home</a>';
    //$menu .= '|';
		$menu .= '<a class="menu'.Util::jeAktivni("lide.php").'" href="./lide.php">lidé</a>';
		$menu .= '<a class="menu'.Util::jeAktivni("zakazky.php").'" href="./zakazky.php">zakázky</a>';
		$menu .= '<a class="menu'.Util::jeAktivni("working-time.php").'" href="./working-time.php">working time</a>';
		$menu .= '<a class="menu'.Util::jeAktivni("info.php").'" href="./info.php">info</a>';
    
		$odhlasit = '';	
		if($logged = Util::is_logged())
		{
		  $odhlasit .= '<a class="odhlasit" href="index.php?logout">Odhlásit</a>';
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
  			<body class="vse">
  				<h1>Working Time v2.3</h1>
  				<div class="menu">'.$menu.'</div>
  				<br>
  				<div class="obsah">'.$obsah.'</div>
  				<div class="odhlasit">'.$odhlasit.'</div>         
  			</body>
  		</html>
	    ';
	}  
} // end Class
?>
