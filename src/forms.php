
<?PHP
function show_login_form()
{
?>
<body>

<form class="form-inline action="index.php" method="POST"">
  <div class="form-group">
    <label class="sr-only" for="exampleInputEmail3">Email address</label>
    <input type="email" class="form-control" id="exampleInputEmail3" placeholder="Email" type="text" name="email"">
  </div>
  <div class="form-group">
    <label class="sr-only" for="exampleInputPassword3">Password</label>
    <input type="password" class="form-control" id="exampleInputPassword3" placeholder="Password" type="password" name="passwd"">
  </div>
  <button type="submit" class="btn btn-default" name="login" value="Login">Sign in</button>
</form>

<?PHP
}

function show_main_menu()
{
global $userid, $userrole;

?>

<a type="button" class="btn btn-default" href="index.php?m=logout">Logout</button></a>

<?PHP
if ($userrole === 'student')
{
?>
<a type="button" class="btn btn-default" href="index.php?m=app_list">Preview</button></a>
<a type="button" class="btn btn-default" href="index.php?m=logs">Edit</button></a>
<a type="button" class="btn btn-default" href="index.php?m=export">Print</button></a>


<?PHP
}
if ($userrole === 'admin')
{
?>
<a type="button" class="btn btn-default" href="index.php?m=app_list">Application list</button></a>
<a type="button" class="btn btn-default" href="index.php?m=logs">Logs</button></a>
<a type="button" class="btn btn-default" href="index.php?m=export">Export</button></a>




<?PHP

if (strlen($userid) === 0) return;
?>

<div class="main">
	<hr>
	<div id="sidebar">
		<ul class="nav nav-pills nav-stacked">
			<li><a href="index.php?m=welcome">Welcome</a></li>
			<li><a href="index.php?m=agreements">Bilateral agreements</a></li>
			<li><a href="index.php?m=travels">Travels</a></li>
			<li><a href="index.php?m=foreigncredits">Travel foreign courses</a></li>
			<li><a href="index.php?m=fmficredits">Travel FMFI courses</a></li>
			<li><a href="index.php?m=universities">Universities</a></li>
			<li><a href="index.php?m=students">Students</a></li>
			<li><a href="index.php?m=studyprogs">Study programs</a></li>
			<li><a href="index.php?m=fmficourses">FMFI courses</a></li>
			<li><a href="index.php?m=subjectareas">Subject areas</a></li>
		</ul>
	</div>
	<div class="tables">
        
        <?PHP
        global $filter_needed;
        if ($filter_needed) {
            show_year_filter();
            } 
        else { 
            print '';
            }
   
   }      
  
elseif($userrole === 'student')
{
?>
<div class="main">
	<hr>
	<div id="sidebar">
		<ul class="nav nav-pills nav-stacked">
			<li><a href="index.php?m=welcome">Welcome</a></li>
			<li><a href="index.php?m=application">Application form</a></li>
			<li><a href="index.php?m=agreements">Bilateral agreements</a></li>
		</ul>
	</div>
	<div class="tables">
<?php

}

}


function show_public_menu()
{
?>
<div class="main">
	<hr>
	<div id="sidebar">
		<ul class="nav nav-pills nav-stacked">
			<li><a href="index.php?m=welcome">Welcome</a></li>
			<li><a href="index.php?m=application">Application form</a></li>
			<li><a href="index.php?m=agreements">Bilateral agreements</a></li>
		</ul>
	</div>
	<div class="tables">
	





<?PHP
}

function show_welcome()
{
global $username, $userid;

print "<b>Welcome $username ";
?>
to the internal evidence of student Erasmus travels!</b><br /><br />
This application stores the details about exchange agreements that FMFI UK has with foreign universities,<br />
and records all student travels that take place on the basis of these agreements. <br /><br />
Details about the student data and the courses they took are available. <br />
All changes on the data are recorded in a log. <br /><br />
Enjoy.
<?PHP
}

function show_select($name, $data, $chosen, $additional_attr="")
{
  print '<select name="' . $name . '"' . $additional_attr . '>';
  print "\n";
  logmsg("chosen=$chosen");
  foreach ($data as $d)
  {
    if ($d[0] == $chosen) $selected=' selected'; else $selected='';
    print '<option value="' . $d[0] . '"' . $selected . '>' . $d[1] . '</option>' . "\n";
  }
  print '</select>' . "\n";
}

function determine_sort_url()
{
  if (isset($_GET['s'])) $sort_url = '&s=' . $_GET['s'];
  else $sort_url = "";
  return $sort_url;
}

function sortby($a, $b)
{
	global $sortby, $sortreverse;

	$sa = strip_tags(strtolower($a[$sortby]));
	$sb = strip_tags(strtolower($b[$sortby]));
	if (is_numeric($sa) && is_numeric($sb)) $rv = ($sa > $sb)?1:(($sa < $sb)?-1:0);
	else $rv = strcoll($sa, $sb);
	return $rv * $sortreverse;
}

function show_table($column_labels, $data, $show_controls = TRUE, $print_button = FALSE)
{
?>
<?PHP
global $filter_url, $sortby, $sortreverse, $selected_year;

if (isset($_GET['s']))
{
	$sortby = $_GET['s'];
	if (isset($_GET['desc'])) $sortreverse = -1;
	else $sortreverse = 1;
	usort($data, 'sortby');
}
$menuitem=$_GET['m'];
$sort_url = determine_sort_url();
if (strlen($selected_year) > 0) $yr='&y=' . $selected_year; else $yr='';
print '<form method="post" action="index.php?m=' . $menuitem . $sort_url . $yr . '&act' . $filter_url . '">';
print '<table class="table table-bordered table-hover"><thead><tr>';
$cnt = 0;
foreach ($column_labels as $label)
{
  $srt = "";
  if (($_GET['s'] == $cnt) && (!isset($_GET['desc']))) $srt = '&desc=1';
  print '<th><a href="index.php?m=' . $menuitem . '&s=' . $cnt . $srt . $yr . $filter_url . '">' . $label . "</a></th>\n";
  $cnt++;
}
print '</tr><tbody>';
$nrecs = 0;
//logmsg("data: $data");
foreach ($data as $row)
{
  print '<tr>';
  $cnt = 0;
  foreach ($row as $cell)
  {
    print '<td>';
    if (($cnt == 0) && $show_controls) print '<input type="checkbox" name="' . $cell . '" />';
    print $cell . "</td>\n";
    $cnt++;
  }
  print "</tr>\n";
  $nrecs++;
}
?>
</tbody></table>


<?PHP
print "<br />$nrecs record(s).<br /><br />";
if ($show_controls)
{
?>
<input type="submit" name="Edit" value="edit" />
<input type="submit" name="Add" value="add" />
<input type="submit" name="Remove" value="remove" />
<?PHP
if ($print_button) print '<input type="submit" name="Print" value="print" />';
print '</form>';
}
}

function show_edit_form($column_labels, $data, $column_types, $selectdata)
{
global $filter_url, $selected_year;

$menuitem=$_GET['m'];
$sort_url = determine_sort_url();
if (strlen($selected_year) > 0) $yr='&y=' . $selected_year; else $yr='';
print '<form method="post" action="index.php?m=' . $menuitem . $sort_url . $yr . '&act' . $filter_url . '" enctype="multipart/form-data">';
print '<table><thead><tr><th colspan="2">Edit the record</td></tr></thead><tbody><tr>';
$cnt = 0;
$sel = 0;
foreach ($column_labels as $label)
{
  print "<tr><td>$label</td><td>";
  if ($column_types[$cnt] === 'RDONLY')
      print '<input name="'. $cnt . '" type="hidden" value="' . $data[$cnt] . '" />' . $data[$cnt];
  else if ($column_types[$cnt] === '*')
  {
    print '<select name="'. $cnt . '">' . "\n";
    $selopt = $data[$cnt];
    if (is_array($selopt)) $selopt = $selopt[0];
    logmsg("selopt=$selopt");
    foreach($selectdata[$sel] as $option)
    {
      print '  <option value="' . $option[0] . '"';
      if ($option[0] == $selopt) print ' selected';
      print '>' . $option[1] . "</option>\n";
    }
    print "</select>\n";
    $sel++;
  }
  else if ($column_types[$cnt] === '+')
  {
    $selopt = $data[$cnt];
    $selcnt = 0;
    $selection = '<br />add: <select name="' . $cnt . '">' . "\n";
    $seloptids = array();
    foreach($selopt as $opt) $seloptids[] = $opt[0];
    foreach($selectdata[$sel] as $option)
    {
	if (in_array($option[0], $seloptids))
	{
          if ($selcnt > 0) print '<br />';
      	  print $option[1];
          print '<input type="hidden" name="listid_' . $cnt . '_' . $selcnt . '" value="' . $option[0] . '" />' . "\n";
      	  $itemname = 'deletelist_' . $cnt . '_' . $selcnt;
          print '<input type="image" src="images/recyclebin.gif" alt="delete item" title="delete item" name="' . $itemname . '" id="' . $itemname . '" />' . "\n";
          $selcnt++;
	}
	else $selection = $selection . ' <option value="' . $option[0] . '">' . $option[1] . "</option>\n";
    }
    if ($selcnt == 0) print "none";
    print $selection;
    print '</select> <input type="submit" name="AddOption" value="add" />';
    $sel++;
  }
  else if ($column_types[$cnt] === 'files')
  {
    $filecnt = 0;
    foreach($data[$cnt] as $file)
    {
      print $file[2] . ': ' . $file[1] . "\n";
      $itemname = 'deletefile_' . $cnt . '_' . $filecnt;
      print '<input type="hidden" name="fileid_' . $cnt . '_' . $filecnt . '" value="' . $file[0] . '" />' . "\n";
      $filecnt++;
      print '<input type="image" src="images/recyclebin.gif" alt="recycle file" title="recycle file" name="' . $itemname . '" id="' . $itemname . '" /><br />' . "\n";
    }
    print 'add: <input type="text" size="40" name="desc' . $cnt . '" value="enter file description" /><br />';
    print "\n";
    print '<input type="file" name="filename' . $cnt . '" id="filename' . $cnt . '" /> <input type="submit" name="upload' . $cnt . '" value="Upload" />';
    print "\n";
  }
  else if ($column_types[$cnt] === 'checkbox')
  {
    print '<input name="' . $cnt . '" type="checkbox"';
    if (count($data) > 0)
      if ($data[$cnt] > 0) print ' checked';
    print ' />';
  }
  else
  {
    $size = ""; $dptr = 0;
    while (ctype_digit($column_types[$cnt][$dptr]))
	$size = $size . $column_types[$cnt][$dptr++];
    $ctype = substr($column_types[$cnt], $dptr);
    if (count($size) === 0) $size = 30;

    print '<input name="' . $cnt . '" type="' . $ctype . '" size="' . $size . '"';
    if (count($data) > 0) print ' value="' . $data[$cnt] . '"';
    print ' />';
  }
  print "</td></tr>\n";
  $cnt++;
}
print '</tbody></table><br />';
print "\n";
print '<input type="submit" name="Save" value="save" /> ';
print '<input type="submit" name="Cancel" value="cancel" /></form>';
print "\n";
}

function show_delete_file_form($fileid, $filename, $filedesc, $fwdvars)
{
  global $filter_url, $selected_year;

  $menuitem=$_GET['m'];
  $sort_url = determine_sort_url();
  if (strlen($selected_year) > 0) $yr='&y=' . $selected_year; else $yr='';
  print '<table><thead><tr><th>Confirm file removal</th></tr></thead><tbody>';
  print "\n";
  print '<tr><td><b>Are you sure you want to remove the file "' . $filedesc . '" (' . $filename . ')?</b></td></tr></tbody></table>';
  print "\n";
  print '<form method="post" action="index.php?m=' . $menuitem . $sort_url . $yr . '&act' . $filter_url . '">';
  print '<input type="hidden" name="fileid" value="' . $fileid . '" />';
  foreach($fwdvars as $var => $val)
    print '<input type="hidden" name="' . $var . '" value="' . $val . '" />';
 ?>
 <br />
 <input type="submit" name="DeleteFile" value="yes" />
 <input type="submit" name="DeleteFile" value="no" />
 </form><br />
 <?PHP
}

function show_remove_form($formatted_records, $record_ids, $fwdvars=array())
{
 global $filter_url, $selected_year;

 $menuitem=$_GET['m'];
 $sort_url = determine_sort_url();
 if (strlen($selected_year) > 0) $yr='&y=' . $selected_year; else $yr='';
 ?>
 <table><thead><tr><th>Confirm record removal</th></tr></thead><tbody>
 <tr><td><b>Are you sure you want to remove the following record(s)?</b></td></tr>
 <?PHP
  foreach($formatted_records as $rec)
  {
    print '<tr><td>';
    print format_item($rec);
    print '</td></tr>';
  }
  print "\n</td></tr></tbody></table>";
  print '<form method="post" action="index.php?m=' . $menuitem . $sort_url . $yr . '&act' . $filter_url . '">';
  print '<input type="hidden" name="ids" value="' . $record_ids . '" />';
  foreach($fwdvars as $var => $val)
    print '<input type="hidden" name="' . $var . '" value="' . $val . '" />';
 ?>
 <br />
 <input type="submit" name="Remove" value="yes" />
 <input type="submit" name="Remove" value="no" />
 </form><br />
</div>
</div>
 <?PHP
}

function format_item($item)
{
  $result = "";
  if (is_array($item))
  {
    $result = $result . '(';
    for ($i = 0; $i < count($item); $i++)
    {
      if ($i > 0) $result = $result . ', ';
      $result = $result . format_item($item[$i]);
    }
    return $result . ')';
  }
  return $item;
}

function show_year_filter()
{
    global $userrole;
    if ($userrole === "admin") {
        
  global $first_year, $selected_year;
  $menuitem = $_GET['m'];
  $sort_url = determine_sort_url();
  print '<form action="index.php?m=' . $menuitem . $sort_url . '" method="POST"><select name="select_year" onchange="this.form.submit()">';
  print "\n" . '<option value="ALL">ALL</option>' . "\n";
  $current_year = date('Y');
  for ($y = $first_year; $y <= $current_year; $y++)
  {
	$yr = $y . '/' . ($y + 1);
	print '<option value="' . $yr . '"';
	if ($y == $selected_year) print ' selected';
	print " >$yr</option>\n";
  }
  print '</select></form><br />' . "\n";
}
else{return;}
  }
?>

<?PHP
function show_headers($year_filter)
{
?>
<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="moj_style.css">
<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Erasmus register FMFI UK</title>
<script src="js/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Readmore.js/2.1.0/readmore.min.js"></script>
</head>
<body>
<div class="container">
  <div class="jumbotron">
  <div class="img_logo1"><img src="images/logo_sk.gif" /></div>
  <div class="img_logo2"><img src="images/rsz_comenius-logo.png" /></div>
    <h3>Erasmus register FMFI UK</h3>  
  </div>
</div>

<style>
a {text-decoration: none; }
a:link { color: #FF0000; }
a:visited { color: #C00000; }
a:hover { color: #5000FF; }
a:active { color:#FF8000; }
table,th,td { border: 1px solid black; border-collapse: collapse; }
th, td { padding: 3px; }
th { background-color: #EEEEEE; }
  #tblTravels td article {
    overflow: hidden;
  }
</style>
</head>

<?PHP
}

function show_footers()
{
  global $log, $logON, $info;

  if (strlen($info) > 0)
    print '<table><tbody><tr><td><pre>' . $info . '</pre></td></tr></tbody></table><br />' . "\n";
  if (($logON) && (strlen($log) > 0))
    print '<table><tbody><tr><td><pre>' . $log . '</pre></td></tr></tbody></table>' . "\n";
?>

<script>

</script>
</body>
</html>
<?PHP
}
?>
