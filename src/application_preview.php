<?php  
function application_preview(){  

$id = $_GET['id'];
global $userrole;
include 'includes/form.php';
include 'includes/safe.php';

$link = db_connect();

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
$points_array = array();  

while($row_priority = mysqli_fetch_array($queryy))   {

   $bilateral_array[] = $row_priority['id_university'];
   $languages_array[] = $row_priority['id_language'];
}

$sqlll = 'SELECT * 
FROM STUDY_POINTS SP
WHERE ID_STUDENT="'. $row['0'] .'"';
$queryyy = mysqli_query($link,$sqlll) or die(mysqli_error($link));
while($row_points = mysqli_fetch_array($queryyy))   {

   $points_array[] = $row_points['POINTS'];
}
$from_date =  explode("-",$row['FROM_DATE']);
$to_date = explode("-",$row['TO_DATE']);

$from_date_ = $from_date[2].'.'.$from_date[1].'.'.$from_date[0];
$to_date_  =  $to_date[2].'.'.$to_date[1].'.'.$to_date[0];

$birth =  explode("-",$row['BORN']);
$borned_input = $birth[2].'.'.$birth[1].'.'.$birth[0];
                                          
?>

<html>
<!-- Latest compiled and minified CSS -->
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="moj_style.css">
<form class="form-horizontal" name="application" method="post" enctype="multipart/form-data">
<fieldset>
<!-- Form Name -->
<legend>Náhľad prihlášky - <?php echo $row['FIRSTNAME'].' '.$row['LASTNAME']; ?></legend>
<?=$error_log?>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Meno</label>  
  <div class="col-md-4">
  <input disabled id="textinput" name="student_name" type="text" value="<?=$row['FIRSTNAME']?>"placeholder="" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Priezvisko</label>  
  <div class="col-md-4">
  <input disabled id="textinput" name="student_surname" type="text" value="<?=$row['LASTNAME']?>" placeholder="" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Kontaktná adresa</label>  
  <div class="col-md-4">
  <input disabled id="textinput" name="address" type="text" value="<?=$row['address']?>" placeholder="" class="form-control input-md" required=""> 
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Telefónne číslo</label>  
  <div class="col-md-4">
  <input disabled id="textinput" name="phone" type="text" value="<?=$row['phone']?>" placeholder="" class="form-control input-md" required="">
  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Pohlavie</label>
  <div class="col-md-4">
    <select id="selectbasic" name="gender" class="form-control">
    <option disabled value="F" <?php if($row['GENDER'] == "F") {echo 'selected="selected"';}?>> žena</option>
    <option disabled value="M" <?php if($row['GENDER'] == "M") {echo 'selected="selected"';}?>>muž</option>
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
      echo "<option disabled value='".$row1['ID']."' ".$selected.">".$row1['NAME']."</option>";
    }
?>
</select>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Dátum narodenia</label>  
  <div class="col-md-4">
  <input disabled id="textinput" name="birthdate" type="text" value="<?=$borned_input?>" placeholder="dd.mm.yyyy" class="form-control input-md" required="">
    
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
      echo "<option disabled value='".$row2['ID']."' ".$selected.">".$row2['NAME']." - ".$row2['CODE']."</option>";
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
      <input disabled type="radio" name="semester" id="radios-0" value="W" <?php if($row['SEMESTER'] == "W") {echo 'checked="checked"';}?>>
      Zimný
    </label>
	</div>
  <div class="radio">
    <label for="radios-1">
      <input disabled type="radio" name="semester" id="radios-1" value="S" <?php if($row['SEMESTER'] == "S") {echo 'checked="checked"';}?>>
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
    <option value="1" disabled <?php if($row['STUDY_YEAR'] == 1) {echo 'selected="selected"';}?>>Bc.</option>
    <option value="2" disabled<?php if($row['STUDY_YEAR'] == 2) {echo 'selected="selected"';}?>>Mgr.</option>
    <option value="3" disabled<?php if($row['STUDY_YEAR'] == 3) {echo 'selected="selected"';}?>>Phd.</option>
    </select>
    
  </div>
</div>
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Rok štúdia</label>
  <div class="col-md-4">
    <select id="selectbasic" name="student_year" class="form-control">
    <option value="None">Výber roka štúdia</option>         
    <option value="1" disabled<?php if($row['STUDY_YEAR'] == 1) {echo 'selected="selected"';}?>>1.</option>
    <option value="2" disabled<?php if($row['STUDY_YEAR'] == 2) {echo 'selected="selected"';}?>>2.</option>
    <option value="3" disabled<?php if($row['STUDY_YEAR'] == 3) {echo 'selected="selected"';}?>>3.</option>
    <option value="4" disabled<?php if($row['STUDY_YEAR'] == 4) {echo 'selected="selected"';}?>>4.</option>
    <option value="5" disabled<?php if($row['STUDY_YEAR'] == 5) {echo 'selected="selected"';}?>>5.</option>
    <option value="6" disabled<?php if($row['STUDY_YEAR'] == 6) {echo 'selected="selected"';}?>>6.</option>
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
      echo "<option disabled value='".$row_b['id_university']."' ".$selected.">".$row_b['university_name']." - ".$row_b['subject_name']." (".$row_b['FROM_DATE']." - ".$row_b['TO_DATE'].").$temp</option>";
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
      echo "<option disabled value='".$row3['ID']."' ".$selected.">".$row3['NAME']."</option>";
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
      echo "<option disabled value='".$row_b1['id_university']."' ".$selected.">".$row_b1['university_name']." - ".$row_b1['subject_name']." (".$row_b1['FROM_DATE']." - ".$row_b1['TO_DATE'].").$temp</option>";
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
      echo "<option disabled value='".$row_l1['ID']."' ".$selected.">".$row_l1['NAME']."</option>";
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
      echo "<option disabled value='".$row_b2['id_university']."' ".$selected.">".$row_b2['university_name']." - ".$row_b2['subject_name']." (".$row_b2['FROM_DATE']." - ".$row_b2['TO_DATE'].").$temp</option>";
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
      echo "<option disabled value='".$row_l2['ID']."' ".$selected.">".$row_l2['NAME']."</option>";
    }
?>
</select>
  </div>
</div>

<!-- Textarea -->
<div class="form-group">
  <label class="col-md-4 control-label" for="textarea">Účasť na projektoch/iné aktivity</label>
  <div class="col-md-4">                     
    <textarea disabled class="form-control" id="textarea" name="notes"><?=$row['NOTES']?></textarea>
  </div>
</div>

<!-- Multiple Checkboxes (inline) -->
<div class="form-group">
  <label class="col-md-4 control-label" for="checkboxes">ZŤP</label>
  <div class="col-md-4">
    <label class="checkbox-inline" for="checkboxes-0">
      <input disabled type="checkbox" name="ztp" id="checkboxes-0" value="1" <?php if($row['HANDICAPPED'] == 1) {echo 'checked="checked"';}?>>
      Áno
    </label>
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="checkboxes">Poberateľ sociálneho štipendia</label>
  <div class="col-md-4">
    <label class="checkbox-inline" for="checkboxes-0">
      <input disabled type="checkbox" name="soc" id="checkboxes-0" value="1" <?php if($row['SOCIALSTIPEND'] == 1) {echo 'checked="checked"';}?>>
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
    <option disabled value="None">Výber bilaterálnej dohody</option>
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
      echo "<option disabled value='".$row_b_final['id_university']."' ".$selected.">".$row_b_final['university_name']." - ".$row_b_final['subject_name']." (".$row_b_final['FROM_DATE']." - ".$row_b_final['TO_DATE'].").$temp</option>";
    }
?>
</select>
</div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Začiatok výmenného pobytu</label>  
  <div class="col-md-4">
  <input disabled id="textinput" name="date_from" type="text" value="<?=$from_date_?>" placeholder="dd.mm.yyyy" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Koniec výmenného pobytu</label>  
  <div class="col-md-4">
  <input disabled id="textinput" name="date_to" type="text" value="<?=$to_date_?>" placeholder="dd.mm.yyyy" class="form-control input-md" required="">  
  </div>
</div>
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Výber finálneho jazyka</label>
  <div class="col-md-4">
  <select id="selectbasic" name="lang_final" class="form-control">
  <option disabled value="None">Výber jazyka</option>
<?php
    $query = "SELECT ID,NAME FROM LANGUAGES order by NAME ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row_lang_final = mysqli_fetch_array($result))
    {
      $selected = ($row['ID_LANGUAGE'] == $row_lang_final['ID']) ? ' selected="selected"' : '';
      echo "<option disabled value='".$row_lang_final['ID']."' ".$selected.">".$row_lang_final['NAME']."</option>";
    }
    
?></select>
</div>
</div>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Jazykový level študenta</label>  
  <div class="col-md-1">
  <input disabled id="textinput" name="student_level" type="text" value="<?=$row['STUDENTLEVEL']?>" class="form-control input-md" required="">  
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Požadovaný jazykový level študenta</label>  
  <div class="col-md-1">
  <input disabled id="textinput" name="required_level" type="text" value="<?=$row['REQUIREDLEVEL']?>"  class="form-control input-md" required="">  
  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Stav prihlášky</label>
  <div class="col-md-4">
    <select id="selectbasic" name="state" class="form-control">
    <option value="0" disabled<?php if($row['STATE'] == 0) {echo 'selected="selected"';}?>>Podaná</option>
    <option value="1" disabled<?php if($row['STATE'] == 1) {echo 'selected="selected"';}?>>Schválená</option>
    <option value="2" disabled<?php if($row['STATE'] == 2) {echo 'selected="selected"';}?>>Zrušená</option>
    </select>
  </div>
</div>

<!-- Multiple Checkboxes (inline) -->
<div class="form-group">
  <label class="col-md-4 control-label" for="checkboxes">Zrušené</label>
  <div class="col-md-4">
    <label class="checkbox-inline" for="checkboxes-0">
      <input disabled type="checkbox" name="cancelled" id="checkboxes-0" value="1" <?php if($row['CANCELLED'] == 1) {echo 'checked="checked"';}?>>
      Áno
    </label>
  </div>
</div>
<h2>Zadávanie bodov</h2>
<hr>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Body za stupeň štúdia</label>  
  <div class="col-md-1">
  <input disabled id="textinput" name="stupen_body" type="text" value="<?=$points_array[0]?>"  class="form-control input-md">  
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Body za jazykový test</label>  
  <div class="col-md-1">
  <input disabled id="textinput" name="jazyk_body" type="text" value="<?=$points_array[1]?>"  class="form-control input-md">  
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Body za aktivity</label>  
  <div class="col-md-1">
  <input disabled id="textinput" name="aktivity_body" type="text" value="<?=$points_array[2]?>"  class="form-control input-md">  
  </div>
</div>
<?php
}
?>

</fieldset>
</form>
</html>  
<?php
}

?>
