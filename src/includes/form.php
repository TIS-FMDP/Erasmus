<?php

class form
{
  var $errors = array();
  var $success;
  var $show = true;
  
  function addError($error)
  {
    $this->errors[] = $error;
  }

  function success($msg)
  {
    $this->success = $msg;
    $this->show = false;
  } 

  function isError()
  {
    if (count($this->errors) > 0)
    {
      return true;
    }
    else
    {
      return false;
    }
  } 
 
  function showMessage()
  {
   
    if ($this->isError() || !empty($this->success))
    {
      if ($this->isError())
      {
        $msg = implode("<br />\n", $this->errors);
        $class = 'errorbox';
        $heading = 'Chyba';
      }
      else
      {
        $msg = $this->success;
        $class = 'successbox';
        $heading = 'Informácia';        
      }
      $html = '<div class="'.$class.'">
	               <h3>'.$heading.'</h3>
	               <p>'.$msg.'</p>
               </div>';      
    }
    else
    {
      $html = '';
    }            
    return $html;
  }
}
$form = new form;

?>