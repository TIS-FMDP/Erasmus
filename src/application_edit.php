<?php  
function application_edit(){  

$id = $_GET['id'];
global $userrole;
include 'includes/form.php';
include 'includes/safe.php';

$link = db_connect();

$submit = (isset($_POST['send'])) ? true : false;

$birthdate = ($submit) ? $safe->input($_POST['birthdate']) : '';
$gender = ($submit) ? $safe->input($_POST['gender']) : '';



//errors catching
$error = false;
$error_log = "";

global $year;

$sql = 'SELECT * FROM STUDENTS S LEFT JOIN STUDENT_STUDY_PROGRAMS SSP ON S.ID=SSP.ID_STUDENT LEFT JOIN STUDY_PROGRAMS SP ON SP.ID=SSP.ID_STUDYPROGRAM LEFT JOIN STUDENT_EXCHANGES SE ON SSP.ID=SE.ID_STUDENT_STUDY_PROGRAM WHERE SE.ID = "'.$id.'"';


$query = mysqli_query($link,$sql) or die(mysqli_error($link));
$row = mysqli_fetch_array($query);
$id_stud = $row[0];
$id_exchange = $row['ID'];
echo('<br>');

$sqll = 'SELECT * 
FROM AGREEMENTS_PRIORITY AP
WHERE id_student="'. $row['0'] .'"';
$queryy = mysqli_query($link,$sqll) or die(mysqli_error($link)); 
$bilateral_array = array();
$languages_array = array();  

while($row_priority = mysqli_fetch_array($queryy))   {

   $bilateral_array[] = $row_priority['id_university'];
   $languages_array[] = $row_priority['id_language'];
}
$from_date =  explode("-",$row['FROM_DATE']);
$to_date = explode("-",$row['TO_DATE']);

$from_date_ = $from_date[2].'.'.$from_date[1].'.'.$from_date[0];
$to_date_  =  $to_date[2].'.'.$to_date[1].'.'.$to_date[0];

$birth =  explode("-",$row['BORN']);
$borned_input = $birth[2].'.'.$birth[1].'.'.$birth[0];

if($submit){
$student_name = ($submit) ? $safe->input($_POST['student_name']) : '';
$student_surname = ($submit) ? $safe->input($_POST['student_surname']) : '';
$address = ($submit) ? $safe->input($_POST['address']) : '';
$phone = ($submit) ? $safe->input($_POST['phone']) : '';
$birthdate = ($submit) ? $safe->input($_POST['birthdate']) : '';
$citizenship = ($submit) ? $safe->input($_POST['citizenship']) : '';
$study_program = ($submit) ? $safe->input($_POST['study_program']) : '';
$study_year = ($submit) ? $safe->input($_POST['study_year']) : '';
$student_year = ($submit) ? $safe->input($_POST['student_year']) : '';
$semester = ($submit) ? $safe->input($_POST['semester']) : '';
$notes = ($submit) ? $safe->input($_POST['notes']) : '';
$ztp = ($submit) ? $safe->input($_POST['ztp']) : 0;
$soc = ($submit) ? $safe->input($_POST['soc']) : 0;
$bilateral_final = ($submit) ? $safe->input($_POST['bilateral_final']) : 0;
$date_from = ($submit) ? $safe->input($_POST['date_from']) : '';
$date_to = ($submit) ? $safe->input($_POST['date_to']) : '';
$lang_final = ($submit) ? $safe->input($_POST['lang_final']) : 0;
$student_level = ($submit) ? $safe->input($_POST['student_level']) : '';
$required_level = ($submit) ? $safe->input($_POST['required_level']) : '';
$state = ($submit) ? $safe->input($_POST['state']) : 0;
$cancelled = ($submit) ? $safe->input($_POST['cancelled']) : 0;


$bilateral_1 = ($submit) ? $safe->input($_POST['bilateral_1']) : 0;
$bilateral_2 = ($submit) ? $safe->input($_POST['bilateral_2']) : 0;
$bilateral_3 = ($submit) ? $safe->input($_POST['bilateral_3']) : 0;
$lang_1 = ($submit) ? $safe->input($_POST['lang_1']) : 0;
$lang_2 = ($submit) ? $safe->input($_POST['lang_2']) : 0;
$lang_3 = ($submit) ? $safe->input($_POST['lang_3']) : 0;


  $temp_born = explode(".",$birthdate);

  if(count($temp_born) != 3 && strlen($temp_born[2]) != 4 && strlen($temp_born[1]) != 2 && 
  strlen($temp_born[0]) != 2 && is_int($temp_born[2]) == false && is_int($temp_born[1]) == false && is_int($temp_born[0]) == false){
    $error_log .= 'Nesprávne zadaný dátum narodenia'.'<br>';  
    $error = true; 
  }
  else{
    $born = $temp_born[2].'/'.$temp_born[1].'/'.$temp_born[0];
  }
  $date_from = explode(".",$date_from);
  $date_from_ = $date_from[2].'/'.$date_from[1].'/'.$date_from[0];
  $date_to = explode(".",$date_to);
  $date_to_ = $date_to_[2].'/'.$date_to_[1].'/'.$date_to_[0];
  
  



if ($error== false){

$sql1 = 'UPDATE STUDENTS
    SET FIRSTNAME="'. $student_name .'", LASTNAME="'. $student_surname .'",BORN="'. $born .'",GENDER="'. $gender .'",CITIZENSHIP="'. $citizenship .'", EMAIL="'. $email .'", YEAR="'. $year .'"
    WHERE id="'. $id_stud .'"';
$query0 = mysqli_query($link,$sql1) or die(mysqli_error($link));

 
$sql = 'UPDATE STUDENT_STUDY_PROGRAMS SET ID_STUDYPROGRAM="'. $study_program .'" WHERE ID_STUDENT="'. $id_stud .'"';
$query1 = mysqli_query($link,$sql) or die(mysqli_error($link));

$sql ='UPDATE STUDENT_EXCHANGES
    SET STUDY_YEAR="'. $study_year .'", AGREEMENT_ID="'. $bilateral_final .'",FROM_DATE="'. $date_from_ .'",TO_DATE="'. $date_to_ .'",SEMESTER="'. $semester .'",ID_LANGUAGE="'. $lang_final .'",STUDENTLEVEL="'. $student_level .'",REQUIREDLEVEL="'. $required_level .'",SOCIALSTIPEND="'. $soc .'",HANDICAPPED="'. $ztp .'",NOTES="'. $notes .'",CANCELLED="'. $cancelled .'",YEAR="'. $year .'",STATE="'. $state .'",ADDRESS="'. $address .'",PHONE="'. $phone .'",STUDENT_YEAR="'. $student_year .'"
    WHERE id="'. $id_exchange .'"';
$query2 = mysqli_query($link,$sql) or die(mysqli_error($link));


$sql ='UPDATE AGREEMENTS_PRIORITY
    SET ID_UNIVERSITY="'. $bilateral_1 .'", ID_STUDENT="'. $id_stud .'", ID_LANGUAGE="'. $lang_1 .'"
    WHERE id_student="'. $id_stud .'" AND priority = 1';
$query3 = mysqli_query($link,$sql) or die(mysqli_error($link));

$sql ='UPDATE AGREEMENTS_PRIORITY
    SET ID_UNIVERSITY="'. $bilateral_2 .'", ID_STUDENT="'. $id_stud .'", ID_LANGUAGE="'. $lang_2 .'"
    WHERE id_student="'. $id_stud .'" AND priority = 2';
$query4 = mysqli_query($link,$sql) or die(mysqli_error($link));

$sql ='UPDATE AGREEMENTS_PRIORITY
    SET ID_UNIVERSITY="'. $bilateral_3 .'", ID_STUDENT="'. $id_stud .'", ID_LANGUAGE="'. $lang_3 .'"
    WHERE id_student="'. $id_stud .'" AND priority = 3';
$query5 = mysqli_query($link,$sql) or die(mysqli_error($link));



 
}
echo '<meta http-equiv="refresh" content="0"';
}                                           
?>

<html>
<!-- Latest compiled and minified CSS -->
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="moj_style.css">
<form class="form-horizontal" name="application" method="post" enctype="multipart/form-data">
<fieldset>
<!-- Form Name -->
<legend>Editácia prihlášky - <?php echo $row['FIRSTNAME'].' '.$row['LASTNAME']; ?></legend>
<?=$error_log?>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Meno</label>  
  <div class="col-md-4">
  <input id="textinput" name="student_name" type="text" value="<?=$row['FIRSTNAME']?>"placeholder="" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Priezvisko</label>  
  <div class="col-md-4">
  <input id="textinput" name="student_surname" type="text" value="<?=$row['LASTNAME']?>" placeholder="" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Kontaktná adresa</label>  
  <div class="col-md-4">
  <input id="textinput" name="address" type="text" value="<?=$row['address']?>" placeholder="" class="form-control input-md" required=""> 
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Telefónne číslo</label>  
  <div class="col-md-4">
  <input id="textinput" name="phone" type="text" value="<?=$row['phone']?>" placeholder="" class="form-control input-md" required="">
  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Pohlavie</label>
  <div class="col-md-4">
    <select id="selectbasic" name="gender" class="form-control">
    <option value="F" <?php if($row['GENDER'] == "F") {echo 'selected="selected"';}?>> žena</option>
    <option value="M" <?php if($row['GENDER'] == "M") {echo 'selected="selected"';}?>>muž</option>
    </select>
  </div>
</div>
<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Príslušnosť</label>
  <div class="col-md-4">
    <select id="selectbasic" name="citizenship" class="form-control">
<?php
    $query = "SELECT ID,NAME FROM COUNTRIES ORDER BY NAME ASC;";
    $result = mysqli_query($link,$query);
    while ($row1 = mysqli_fetch_array($result))
    {
      $selected = ($row1['ID'] == $row['CITIZENSHIP']) ? ' selected="selected"' : '';
      echo "<option value='".$row1['ID']."' ".$selected.">".$row1['NAME']."</option>";
    }
?>
</select>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Dátum narodenia</label>  
  <div class="col-md-4">
  <input id="textinput1" name="birthdate" type="text" value="<?=$borned_input?>" placeholder="dd.mm.yyyy" class="form-control input-md datepicker" required="">
  <i class="fa fa-calendar datepicker-icon"></i>
  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Aktuálny študijný program</label>
  <div class="col-md-4">
    <select id="selectbasic" name="study_program" class="form-control">
    <option value="None">Výber študijného programu</option>
<?php
    $query = "SELECT ID, CODE, NAME  from STUDY_PROGRAMS order by NAME ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row2 = mysqli_fetch_array($result))
    {
      $selected = ($row2['ID'] == $row['ID_STUDYPROGRAM']) ? ' selected="selected"' : '';
      echo "<option value='".$row2['ID']."' ".$selected.">".$row2['NAME']." - ".$row2['CODE']."</option>";
    }
?>
</select>
    
  </div>
</div>

<!-- Multiple Radios -->
<div class="form-group">
  <label class="col-md-4 control-label" for="radios">Výber semestra</label>
  <div class="col-md-4">
  <div class="radio">
    <label for="radios-0">
      <input type="radio" name="semester" id="radios-0" value="W" <?php if($row['SEMESTER'] == "W") {echo 'checked="checked"';}?>>
      Zimný
    </label>
	</div>
  <div class="radio">
    <label for="radios-1">
      <input type="radio" name="semester" id="radios-1" value="S" <?php if($row['SEMESTER'] == "S") {echo 'checked="checked"';}?>>
      Letný
    </label>
	</div>
  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Stupeň štúdia</label>
  <div class="col-md-4">
    <select id="selectbasic" name="study_year" class="form-control">
    <option value="None">Výber stupňa štúdia</option>         
    <option value="1" <?php if($row['STUDY_YEAR'] == 1) {echo 'selected="selected"';}?>>Bc.</option>
    <option value="2" <?php if($row['STUDY_YEAR'] == 2) {echo 'selected="selected"';}?>>Mgr.</option>
    <option value="3" <?php if($row['STUDY_YEAR'] == 3) {echo 'selected="selected"';}?>>Phd.</option>
    </select>
    
  </div>
</div>
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Rok štúdia</label>
  <div class="col-md-4">
    <select id="selectbasic" name="student_year" class="form-control">
    <option value="None">Výber roka štúdia</option>         
    <option value="1" <?php if($row['STUDY_YEAR'] == 1) {echo 'selected="selected"';}?>>1.</option>
    <option value="2" <?php if($row['STUDY_YEAR'] == 2) {echo 'selected="selected"';}?>>2.</option>
    <option value="3" <?php if($row['STUDY_YEAR'] == 3) {echo 'selected="selected"';}?>>3.</option>
    <option value="4" <?php if($row['STUDY_YEAR'] == 4) {echo 'selected="selected"';}?>>4.</option>
    <option value="5" <?php if($row['STUDY_YEAR'] == 5) {echo 'selected="selected"';}?>>5.</option>
    <option value="6" <?php if($row['STUDY_YEAR'] == 6) {echo 'selected="selected"';}?>>6.</option>
    </select>
    
  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Výber bilaterálnej dohody #1</label>
  <div class="col-md-4">
    <select id="selectbasic" name="bilateral_1" class="form-control">
    <option value="None">Výber bilaterálnej dohody</option>
<?php
    $query = "SELECT a.ID_UNIVERSITY,a.FROM_DATE, a.ID, a.BC, a.MGR, a.PHD, a.TO_DATE, a.SUBJECT_AREA_ID,u.ID as id_university, u.NAME as university_name, s.NAME as subject_name FROM AGREEMENTS as a join UNIVERSITIES as u on a.ID_UNIVERSITY = u.ID join SUBJECT_AREAS as s on a.SUBJECT_AREA_ID = s.ID ORDER BY u.NAME ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row_b = mysqli_fetch_array($result))
    {
      $temp = '';
      if($row_b['BC'] == 1){
        $temp .= ' Bc.';
      }
      if($row_b['MGR'] == 1){
        $temp .= ' Mgr.';
      }
      if($row_b['PHD'] == 1){
        $temp .= ' Phd.';
      }
      $selected = ($bilateral_array[0] == $row_b['id_university']) ? ' selected="selected"' : '';
      echo "<option value='".$row_b['id_university']."' ".$selected.">".$row_b['university_name']." - ".$row_b['subject_name']." (".$row_b['FROM_DATE']." - ".$row_b['TO_DATE'].").$temp</option>";
    }
?>
</select>
    <select id="selectbasic" name="lang_1" class="form-control">
    <option value="None">Výber preferovaného jazyka</option>
<?php
    $query = "SELECT ID,NAME FROM LANGUAGES order by NAME ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row3 = mysqli_fetch_array($result))
    {
      $selected = ($languages_array[0] == $row3['ID']) ? ' selected="selected"' : '';
      echo "<option value='".$row3['ID']."' ".$selected.">".$row3['NAME']."</option>";
    }
    
?></select>
  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Výber bilaterálnej dohody #2</label>
  <div class="col-md-4">
    <select id="selectbasic" name="bilateral_2" class="form-control">
    <option value="None">Výber bilaterálnej dohody</option>
<?php
    $query = "SELECT a.ID_UNIVERSITY,a.FROM_DATE, a.ID, a.BC, a.MGR, a.PHD, a.TO_DATE, a.SUBJECT_AREA_ID,u.ID as id_university, u.NAME as university_name, s.NAME as subject_name FROM AGREEMENTS as a join UNIVERSITIES as u on a.ID_UNIVERSITY = u.ID
               join SUBJECT_AREAS as s on a.SUBJECT_AREA_ID = s.ID ORDER BY u.NAME ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row_b1 = mysqli_fetch_array($result))
    {
      $temp = '';
      if($row_b1['BC'] == 1){
        $temp .= ' Bc.';
      }
      if($row_b1['MGR'] == 1){
        $temp .= ' Mgr.';
      }
      if($row_b1['PHD'] == 1){
        $temp .= ' Phd.';
      }
      $selected = ($bilateral_array[1] == $row_b1['id_university']) ? ' selected="selected"' : '';
      echo "<option value='".$row_b1['id_university']."' ".$selected.">".$row_b1['university_name']." - ".$row_b1['subject_name']." (".$row_b1['FROM_DATE']." - ".$row_b1['TO_DATE'].").$temp</option>";
    }
?>
</select>
    <select id="selectbasic" name="lang_2" class="form-control">
    <option value="None">Výber preferovaného jazyka</option>
<?php
    $query = "SELECT ID,NAME FROM LANGUAGES order by NAME ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row_l1 = mysqli_fetch_array($result))
    {
      $selected = ($languages_array[1] == $row_l1['ID']) ? ' selected="selected"' : '';
      echo "<option value='".$row_l1['ID']."' ".$selected.">".$row_l1['NAME']."</option>";
    }
?>
</select>
  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Výber bilaterálnej dohody #3</label>
  <div class="col-md-4">
    <select id="selectbasic" name="bilateral_3" class="form-control">
    <option value="None">Výber bilaterálnej dohody</option>
<?php
    $query = "SELECT a.ID_UNIVERSITY,a.FROM_DATE, a.ID, a.BC, a.MGR, a.PHD, a.TO_DATE, a.SUBJECT_AREA_ID,u.ID as id_university, u.NAME as university_name, s.NAME as subject_name FROM AGREEMENTS as a join UNIVERSITIES as u on a.ID_UNIVERSITY = u.ID
               join SUBJECT_AREAS as s on a.SUBJECT_AREA_ID = s.ID ORDER BY u.NAME ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row_b2 = mysqli_fetch_array($result))
    {
      $temp = '';
      if($row_b2['BC'] == 1){
        $temp .= ' Bc.';
      }
      if($row_b2['MGR'] == 1){
        $temp .= ' Mgr.';
      }
      if($row_b2['PHD'] == 1){
        $temp .= ' Phd.';
      }
      $selected = ($bilateral_array[2] == $row_b2['id_university']) ? ' selected="selected"' : '';
      echo "<option value='".$row_b2['id_university']."' ".$selected.">".$row_b2['university_name']." - ".$row_b2['subject_name']." (".$row_b2['FROM_DATE']." - ".$row_b2['TO_DATE'].").$temp</option>";
    }
?>
</select>
    <select id="selectbasic" name="lang_3" class="form-control">
    <option value="None">Výber preferovaného jazyka</option>
<?php
    $query = "SELECT ID,NAME FROM LANGUAGES order by NAME ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row_l2 = mysqli_fetch_array($result))
    {
      $selected = ($languages_array[2] == $row_l2['ID']) ? ' selected="selected"' : '';
      echo "<option value='".$row_l2['ID']."' ".$selected.">".$row_l2['NAME']."</option>";
    }
?>
</select>
  </div>
</div>

<!-- Textarea -->
<div class="form-group">
  <label class="col-md-4 control-label" for="textarea">Účasť na projektoch/iné aktivity</label>
  <div class="col-md-4">                     
    <textarea class="form-control" id="textarea" name="notes"><?=$row['NOTES']?></textarea>
  </div>
</div>

<!-- Multiple Checkboxes (inline) -->
<div class="form-group">
  <label class="col-md-4 control-label" for="checkboxes">ZŤP</label>
  <div class="col-md-4">
    <label class="checkbox-inline" for="checkboxes-0">
      <input type="checkbox" name="ztp" id="checkboxes-0" value="1" <?php if($row['HANDICAPPED'] == 1) {echo 'checked="checked"';}?>>
      Áno
    </label>
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="checkboxes">Poberateľ sociálneho štipendia</label>
  <div class="col-md-4">
    <label class="checkbox-inline" for="checkboxes-0">
      <input type="checkbox" name="soc" id="checkboxes-0" value="1" <?php if($row['SOCIALSTIPEND'] == 1) {echo 'checked="checked"';}?>>
      Áno
    </label>
  </div>
</div>
<?php
if($userrole === "admin"){
?>
<h2>Údaje zadávané administrátorom</h2>
<hr>
<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Výber vybranej bilaterálnej dohody</label>
  <div class="col-md-4">
    <select id="selectbasic" name="bilateral_final" class="form-control">
    <option value="None">Výber bilaterálnej dohody</option>
<?php
    $query = "SELECT a.ID_UNIVERSITY,a.FROM_DATE, a.ID, a.BC, a.MGR, a.PHD, a.TO_DATE, a.SUBJECT_AREA_ID,u.ID as id_university, u.NAME as university_name, s.NAME as subject_name FROM AGREEMENTS as a join UNIVERSITIES as u on a.ID_UNIVERSITY = u.ID
               join SUBJECT_AREAS as s on a.SUBJECT_AREA_ID = s.ID ORDER BY u.NAME ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row_b_final = mysqli_fetch_array($result))
    {
      $temp = '';
      if($row_b_final['BC'] == 1){
        $temp .= ' Bc.';
      }
      if($row_b_final['MGR'] == 1){
        $temp .= ' Mgr.';
      }
      if($row_b_final['PHD'] == 1){
        $temp .= ' Phd.';
      }
      $selected = ($row_b_final['id_university'] == $row['AGREEMENT_ID']) ? ' selected="selected"' : '';
      echo "<option value='".$row_b_final['id_university']."' ".$selected.">".$row_b_final['university_name']." - ".$row_b_final['subject_name']." (".$row_b_final['FROM_DATE']." - ".$row_b_final['TO_DATE'].").$temp</option>";
    }
?>
</select>
</div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Začiatok výmenného pobytu</label>  
  <div class="col-md-4">
  <input id="textinput2" name="date_from" type="text" value="<?=$from_date_?>" placeholder="dd.mm.yyyy" class="form-control input-md datepicker" required="">
  <i class="fa fa-calendar datepicker-icon"></i>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Koniec výmenného pobytu</label>  
  <div class="col-md-4">
  <input id="textinput3" name="date_to" type="text" value="<?=$to_date_?>" placeholder="dd.mm.yyyy" class="form-control input-md datepicker" required="">
  <i class="fa fa-calendar datepicker-icon"></i>  
  </div>
</div>
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Výber finálneho jazyka</label>
  <div class="col-md-4">
  <select id="selectbasic" name="lang_final" class="form-control">
  <option value="None">Výber jazyka</option>
<?php
    $query = "SELECT ID,NAME FROM LANGUAGES order by NAME ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row_lang_final = mysqli_fetch_array($result))
    {
      $selected = ($row['ID_LANGUAGE'] == $row_lang_final['ID']) ? ' selected="selected"' : '';
      echo "<option value='".$row_lang_final['ID']."' ".$selected.">".$row_lang_final['NAME']."</option>";
    }
    
?></select>
</div>
</div>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Jazykový level študenta</label>  
  <div class="col-md-1">
  <input id="textinput" name="student_level" type="text" value="<?=$row['STUDENTLEVEL']?>" class="form-control input-md" required="">  
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Požadovaný jazykový level študenta</label>  
  <div class="col-md-1">
  <input id="textinput" name="required_level" type="text" value="<?=$row['REQUIREDLEVEL']?>"  class="form-control input-md" required="">  
  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Stav prihlášky</label>
  <div class="col-md-4">
    <select id="selectbasic" name="state" class="form-control">
    <option value="0" <?php if($row['STATE'] == 0) {echo 'selected="selected"';}?>>Podaná</option>
    <option value="1" <?php if($row['STATE'] == 1) {echo 'selected="selected"';}?>>Schválená</option>
    <option value="2" <?php if($row['STATE'] == 2) {echo 'selected="selected"';}?>>Zrušená</option>
    </select>
  </div>
</div>

<!-- Multiple Checkboxes (inline) -->
<div class="form-group">
  <label class="col-md-4 control-label" for="checkboxes">Zrušené</label>
  <div class="col-md-4">
    <label class="checkbox-inline" for="checkboxes-0">
      <input type="checkbox" name="cancelled" id="checkboxes-0" value="1" <?php if($row['CANCELLED'] == 1) {echo 'checked="checked"';}?>>
      Áno
    </label>
  </div>
</div>
<?php
}
?>
<!-- Button (Double) -->
<div class="form-group">
 
  <div class="col-md-8">
    <button id="button1id" name="send" class="btn btn-success">Odoslať</button>
  </div>
</div>

</fieldset>
</form>
</html>  
<?php
}

?>
