<?PHP
function determine_user_credentials()
{
  global $userid, $username;

  if ($_POST['login'] === 'Login')
     db_try_to_login($_POST['email'], $_POST['passwd']);

  $userid = $_SESSION['userid'];
  if (strlen($userid) === 0)
  {
    show_login_form();
    return FALSE;
  }
  else
    db_users_load($userid);
  return TRUE;
}

function logmsg($msg)
{
  global $log;
  $log = $log . $msg . '<br />';
}

function infomsg($msg)
{
  global $info;
  $info = $info . $msg . '<br />';
}

function headers_for_export($filename)
{
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
        header('Cache-Control: private');
        header('Pragma: private');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
}

 
function selection_of_years()
{
        global $first_year;
        $years = array();
        $curyear = date('Y');
        for ($y = $first_year; $y <= $curyear; $y++)
                $years[] = array($y . '/' . ($y + 1), $y . '/' . ($y + 1));
        return $years;
}

?>
