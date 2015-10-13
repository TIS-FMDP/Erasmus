<?PHP
include 'config.php';
include 'db.php';
include 'control_logic.php';
include 'forms.php';
$link = db_connect();
session_start();
$id = $_GET['id'];
determine_user_credentials();
if (strlen($userid) == 0) echo "<html><body>unauthorized access</body></html>";
else
{
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Internal storage for Erasmus FMFI UK: travel
<?PHP
print $id;
?>
</title>
<style>
a {text-decoration: none; }
a:link { color: #FF0000; }
a:visited { color: #C00000; }
a:hover { color: #5000FF; }
a:active { color:#FF8000; }
table,th,td { border: 0px solid white; border-collapse: collapse; }
th, td { padding: 3px; }
</style>
</head>
<body style="font-family: Arial, Helvetica, sans-serif;">
<table width="1200px"><tbody><tr><td><img src="images/logo_uk.jpg" width="150px" /></td>
<td style=" text-align: center; "><b>UNIVERZITA KOMENSKÉHO V BRATISLAVE</b><br />
       Fakulta matematiky, fyziky a informatiky<br />
       Mlynská dolina, 84148 Bratislava<br />
       Študijné oddelenie<br />
       tel.: 02/65427086, 65426720, fax: 02/65425882</td>
<td style=" text-align: right; "><img src="images/logo_fmfi.jpg" width="150px" /></td></tr></tbody></table><br />
<table width="1200px"><tbody><tr><td>Meno studenta: 
<?PHP
$tv = db_retrieve_travel_byID($id, TRUE);
$id_student = $tv[5];
$id_program = $tv[6];
logmsg("s: $id_student p: $id_program");
$stsp = db_retrieve_study_prog_and_student_details($id_program, $id_student);
$agr = db_retrieve_agreement_byID($tv[2]);
$courses = $tv[14];
print $stsp[1];
print '</td><td>Ročník a štúdijný program: ';
print $tv[1] . ' - ' . $stsp[0];
print '</td></tr><tr><td>Partnerská univerzita: ';
print $agr[1][1] . ' (' . $agr[1][2] . ')';
print '</td><td>Obdobie: ';
if ($tv[3] !== '0000-00-00') print $tv[3]; else print 'neznámy termín nástupu';
print ' - ';
if ($tv[4] !== '0000-00-00') print $tv[4]; else print 'neznámy termín návratu';
print '</td></tr></table><br /><br />';
print "\n";
print '<table width="1200px" style=" border: 2px solid black; border-collapse: collapse; "><tbody>';

$headers = array('Uznaný predmet na FMFI UK:', 
                 'Absolvovaný predmet na zahraničnej univerzite', 
                 'Počet<br />kreditov', 
                 'Hodnotenie', 
                 'Typ'); 
$td1 = '<td style=" border: 2px solid black; border-collapse: collapse; ">';
print '<tr><td><b>Predmet absolvovaný na zahraničnej univerzite</b></td>';
print '<td><b>Počet kreditov</b></td>';
print '<td><b>Hodnotenie</b></td>';
print '<td><b>Typ</b></td>';
print "</tr>\n";
$td2 = '</td>' . "\n";
//"Courses taken at foreign institution:<br />" . $tv[14][0] . '<br />Courses taken at FMFI:<br />' . $tv[14][1];
foreach($courses[0] as $c)
{
  print '<tr>' . $td1 . $c[1] . ': ' . $c[2] . $td2;
  print $td1 . $c[3] . $td2;
  print $td1 . $c[4] . $td2;
  print $td1 . course_type_sk($c[5][0]) . $td2;
  print '</tr>'; 
} 
print '</tbody></table><br /><br />' . "\n";

print '<table width="1200px" style=" border: 2px solid black; border-collapse: collapse; "><tbody>';
print '<tr><td><b>Uznaný predmet na FMFI UK</b></td><td><b>Počet kreditov</b></td><td><b>Hodnotenie<b></td></tr>';
print "\n";
foreach($courses[1] as $c)
{
  print '<tr>' . $td1 . $c[1][1] . ': ' . $c[1][2] . $td2;
  print $td1 . $c[1][3] . $td2;
  print $td1 . $c[2] . $td2 . '</tr>' . "\n"; 
} 
?>
</tbody></table>
<br /><br /><br />
<table width="1200px"><tbody>
<tr><td>_________________________</td><td>____________________</td><td>_______________________________</td></tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;&nbsp;podpis garanta</td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;podpis študenta</td>
<?PHP
print '<td>&nbsp;&nbsp;&nbsp;&nbsp; ' . $dekan . '<br />';
for ($i = 0; $i < 23; $i++) print '&nbsp;';
print 'dekan</td></tr>';
$link->close();
print "</tbody></table>";
show_footers();
}
?>
