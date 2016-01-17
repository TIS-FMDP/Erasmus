<?php

class safe
{
  final static function SQLString($s)
    {
        $s = trim($s);
        $s = str_replace("'","''",$s);
        $s = str_replace("\\","\\\\",$s);
        return "'" . $s . "'";
    }
  final static function SQLInt($int)
  {
    if(is_numeric($int))
    {
      return $int;
    }
    else
    {
      return false;
    }
  }
  
  final static function input($s)
  {
    $link = db_connect();
	  $s = trim(strip_tags($s));	
	  if (get_magic_quotes_gpc())
	  {
		  $s = stripslashes($s);
	  }
	  $s = mysqli_real_escape_string($link,$s);	
	  return $s;
  }
  
  final static function html($s)
  {	
	  $s = mysql_real_escape_string(trim($s));	
	  return $s;
  }
     
  final static function output($s)
  {
     return htmlspecialchars(stripslashes(trim($s)));
  }
}
$safe = new safe;
?>