<?php

function __autoload($class_name) 
{
    if(is_file("./class/Class".$class_name.'.php'))
    {
      include "./class/Class".$class_name.'.php';
    }else{
      die("Nelze načíst třídu $class_name! (./class/Class$class_name.php)");
    }
}

?>
