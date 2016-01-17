<?php  
function process_application(){  

include 'includes/form.php';
include 'includes/safe.php';
include 'includes/class.phpmailer.php';
$link = db_connect();
$code = substr(md5(uniqid(rand(), true)), 16, 16); 
$submit = (isset($_POST['send'])) ? true : false;

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

$bilateral_1 = ($submit) ? $safe->input($_POST['bilateral_1']) : 0;
$bilateral_2 = ($submit) ? $safe->input($_POST['bilateral_2']) : 0;
$bilateral_3 = ($submit) ? $safe->input($_POST['bilateral_3']) : 0;
$lang_1 = ($submit) ? $safe->input($_POST['lang_1']) : 0;
$lang_2 = ($submit) ? $safe->input($_POST['lang_2']) : 0;
$lang_3 = ($submit) ? $safe->input($_POST['lang_3']) : 0;


$birthdate = ($submit) ? $safe->input($_POST['birthdate']) : '';
$gender = ($submit) ? $safe->input($_POST['gender']) : '';
$email = ($submit) ? $safe->input($_POST['email']) : '';
$pass = ($submit) ? $safe->input($_POST['pass']) : '';
$pass_check = ($submit) ? $safe->input($_POST['pass_check']) : '';


//errors catching
$error = false;
$error_log = "";

$act_year = date("Y");
$next_year = $act_year + 1;
$year = $act_year.'/'.$next_year;


if($submit){
if($pass != $pass_check){
    $error_log .= 'Overenie hesla sa nezhoduje!'.'<br>';  
    $error = true;       
}
if (!valid_email($email)){
    $error_log .= 'Neplatný email!'.'<br>';  
    $error = true;       
}
if (strlen($pass) < 6 || strlen($pass) > 10){
    $error_log .= 'Dĺžka hesla musí byť 6 až 10 znakov!'.'<br>';  
    $error = true;     
}
if (exist('USERS', 'EMAIL', $email))
  {
    $error_log .= 'Zadaný email už niekto používa!'.'<br>';  
    $error = true;              
  }

  $temp_born = explode(".",$birthdate);
  // Palko edit 
  if(count($temp_born) != 3 && strlen($temp_born[2]) != 4 && strlen($temp_born[1]) != 2 && 
  strlen($temp_born[0]) != 2 && is_int($temp_born[2]) == false && is_int($temp_born[1]) == false && is_int($temp_born[0]) == false){
    $error_log .= 'Nesprávne zadaný dátum narodenia'.'<br>';  
    $error = true; 
  }
  else{
    $born = $temp_born[2].'/'.$temp_born[1].'/'.$temp_born[0];
  }
  

if ($error== false){

    // insert into students
    $sql = 'INSERT INTO STUDENTS (FIRSTNAME,MIDDLENAMES,LASTNAME,BORN,STUDENT_ID,GENDER,CITIZENSHIP, EMAIL, YEAR) VALUES ("' . $student_name . '", "", "'.$student_surname.'", "' . $born . '", "","' . $gender .'", "' . $citizenship .'","' . $email .'","' . $year .'");';    
    $query1 = mysqli_query($link,$sql) or die(mysqli_error($link));
    
    //insert into users
    $student_id = mysqli_insert_id($link);
    $sql = 'INSERT INTO USERS (ROLE, EMAIL, PASSWD, NAME,STUDENT_ID, reg_code,reg_valid) VALUES ("student", "' . $email . '", "' . md5($pass) . '","' . $student_name . $student_surname.'", "'. $student_id .'", "'. $code .'",0);';    
    $query2 = mysqli_query($link,$sql) or die(mysqli_error($link));
    
    $sql = 'INSERT INTO STUDENT_STUDY_PROGRAMS (ID_STUDENT,ID_STUDYPROGRAM) VALUES ("' . $student_id . '", "'. $study_program .'");';    
    $query3 = mysqli_query($link,$sql) or die(mysqli_error($link));
    
    $id_student_program = mysqli_insert_id($link);
    $sql = 'INSERT INTO STUDENT_EXCHANGES (ID_STUDENT_STUDY_PROGRAM,STUDY_YEAR,AGREEMENT_ID,FROM_DATE,TO_DATE,SEMESTER,ID_LANGUAGE,STUDENTLEVEL,REQUIREDLEVEL,SOCIALSTIPEND,HANDICAPPED,NOTES,CANCELLED,YEAR,STATE,ADDRESS,PHONE,STUDENT_YEAR) VALUES ("' . $id_student_program . '", "'. $study_year .'",0,1970/01/01, 1970/01/01, "'. $semester .'",0,"","","'. $soc .'","'. $ztp .'","'. $notes .'",0,"'. $year .'",0,"'. $address .'","'. $phone .'","'. $student_year .'");';    
    $query4 = mysqli_query($link,$sql) or die(mysqli_error($link));
    
    $sql = 'INSERT INTO AGREEMENTS_PRIORITY (ID_UNIVERSITY, ID_STUDENT, ID_LANGUAGE, PRIORITY) VALUES ("'. $bilateral_1 .'","'. $student_id .'", "'. $lang_1 .'", 1)';
    $query = mysqli_query($link,$sql) or die(mysqli_error($link));
    $sql = 'INSERT INTO AGREEMENTS_PRIORITY (ID_UNIVERSITY, ID_STUDENT, ID_LANGUAGE, PRIORITY) VALUES ("'. $bilateral_2 .'","'. $student_id .'", "'. $lang_2 .'", 2)';
    $query = mysqli_query($link,$sql) or die(mysqli_error($link));
    $sql = 'INSERT INTO AGREEMENTS_PRIORITY (ID_UNIVERSITY, ID_STUDENT, ID_LANGUAGE, PRIORITY) VALUES ("'. $bilateral_3 .'","'. $student_id .'", "'. $lang_3 .'", 3)';
    $query = mysqli_query($link,$sql) or die(mysqli_error($link));
    
    if($query1 && $query2 && $query3 && $query4){                                                           
      $error_log .= 'Boli ste úspešne zaregistrovaný!';
      try{
        $mail = new PHPMailer();
        $mail->From = "erasmus fmfi"; 
        $mail->AddAddress($email);
        $mail->Subject = "Registrácia na stránke Erasmus FMFI"; 
        $email_body = file_get_contents('user_register.txt');
        $patterns = array('([{]EMAIL[}])', '([{]PASSWORD[}])', '([{]CODE[}])');
        
        $replacements = array($email, $pass, $code);
        $email_body = preg_replace ($patterns, $replacements, $email_body);
        $mail->Body = $email_body;
        $mail->Send();
      } 
      catch (phpmailerException $e){
        $error_log .= $e->errorMessage();
      }
    }
}


}
echo '<html>
<!-- Latest compiled and minified CSS -->
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<form class="form-horizontal" name="application" method="post">
<fieldset>
<!-- Form Name -->
<legend>Nová prihláška</legend>
'.$error_log.'
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Meno</label>  
  <div class="col-md-4">
  <input id="textinput" name="student_name" type="text" placeholder="" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Priezvisko</label>  
  <div class="col-md-4">
  <input id="textinput" name="student_surname" type="text" placeholder="" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Kontaktná adresa</label>  
  <div class="col-md-4">
  <input id="textinput" name="address" type="text" placeholder="" class="form-control input-md" required=""> 
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Telefónne číslo</label>  
  <div class="col-md-4">
  <input id="textinput" name="phone" type="text" placeholder="" class="form-control input-md" required="">
  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Pohlavie</label>
  <div class="col-md-4">
    <select id="selectbasic" name="gender" class="form-control">
    <option value="F">žena</option>
    <option value="M">muž</option>
    </select>
  </div>
</div>
<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Príslušnosť</label>
  <div class="col-md-4">
    <select id="selectbasic" name="citizenship" class="form-control">
    ';
    $query = "SELECT ID,NAME FROM COUNTRIES ORDER BY NAME ASC;";
    $result = mysqli_query($link,$query);
    while ($row = mysqli_fetch_array($result))
    {
      echo "<option value='".$row['ID']."'>".$row['NAME']."</option>";
    }
    echo '</select>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Dátum narodenia</label>  
  <div class="col-md-4">
  <input id="textinput" name="birthdate" type="text" placeholder="dd.mm.yyyy" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Aktuálny študijný program</label>
  <div class="col-md-4">
    <select id="selectbasic" name="study_program" class="form-control">
    <option value="None">Výber študijného programu</option>
    ';
    $query = "SELECT ID, CODE, NAME  from STUDY_PROGRAMS order by NAME ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result))
    {
      echo "<option value='".$row['ID']."'>".$row['NAME']." - ".$row['CODE']."</option>";
    }
    echo '</select>
    
  </div>
</div>

<!-- Multiple Radios -->
<div class="form-group">
  <label class="col-md-4 control-label" for="radios">Výber semestra</label>
  <div class="col-md-4">
  <div class="radio">
    <label for="radios-0">
      <input type="radio" name="semester" id="radios-0" value="W" checked="checked">
      Zimný
    </label>
	</div>
  <div class="radio">
    <label for="radios-1">
      <input type="radio" name="semester" id="radios-1" value="S">
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
    <option value="1">Bc.</option>
    <option value="2">Mgr.</option>
    <option value="3">Phd.</option>
    </select>
    
  </div>
</div>
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Rok štúdia</label>
  <div class="col-md-4">
    <select id="selectbasic" name="student_year" class="form-control">
    <option value="None">Výber roka štúdia</option>         
    <option value="1">1.</option>
    <option value="2">2.</option>
    <option value="3">3.</option>
    <option value="4">4.</option>
    <option value="5">5.</option>
    </select>
    
  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Výber bilaterálnej dohody #1</label>
  <div class="col-md-4">
    <select id="selectbasic" name="bilateral_1" class="form-control">
    <option value="None">Výber bilaterálnej dohody</option>
    ';
    $query = "SELECT a.ID_UNIVERSITY,a.FROM_DATE, a.ID, a.BC, a.MGR, a.PHD, a.TO_DATE, a.SUBJECT_AREA_ID,u.ID as id_university, u.NAME as university_name, s.NAME as subject_name FROM AGREEMENTS as a join UNIVERSITIES as u on a.ID_UNIVERSITY = u.ID join SUBJECT_AREAS as s on a.SUBJECT_AREA_ID = s.ID ORDER BY u.NAME ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result))
    {
      $temp = '';
      if($row['BC'] == 1){
        $temp .= ' Bc.';
      }
      if($row['MGR'] == 1){
        $temp .= ' Mgr.';
      }
      if($row['PHD'] == 1){
        $temp .= ' Phd.';
      }
      echo "<option value='".$row['id_university']."'>".$row['university_name']." - ".$row['subject_name']." (".$row['FROM_DATE']." - ".$row['TO_DATE'].").$temp</option>";
    }
    echo '</select>
    <select id="selectbasic" name="lang_1" class="form-control">
    <option value="None">Výber preferovaného jazyka</option>
    ';
    $query = "SELECT ID,NAME FROM LANGUAGES order by NAME ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result))
    {
      echo "<option value='".$row['ID']."'>".$row['NAME']."</option>";
    }
    echo '</select>
  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Výber bilaterálnej dohody #2</label>
  <div class="col-md-4">
    <select id="selectbasic" name="bilateral_2" class="form-control">
    <option value="None">Výber bilaterálnej dohody</option>
    ';
    $query = "SELECT a.ID_UNIVERSITY,a.FROM_DATE, a.ID, a.BC, a.MGR, a.PHD, a.TO_DATE, a.SUBJECT_AREA_ID,u.ID as id_university, u.NAME as university_name, s.NAME as subject_name FROM AGREEMENTS as a join UNIVERSITIES as u on a.ID_UNIVERSITY = u.ID
               join SUBJECT_AREAS as s on a.SUBJECT_AREA_ID = s.ID ORDER BY u.NAME ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result))
    {
      $temp = '';
      if($row['BC'] == 1){
        $temp .= ' Bc.';
      }
      if($row['MGR'] == 1){
        $temp .= ' Mgr.';
      }
      if($row['PHD'] == 1){
        $temp .= ' Phd.';
      }
      echo "<option value='".$row['id_university']."'>".$row['university_name']." - ".$row['subject_name']." (".$row['FROM_DATE']." - ".$row['TO_DATE'].").$temp</option>";
    }
    echo '</select>
    <select id="selectbasic" name="lang_2" class="form-control">
    <option value="None">Výber preferovaného jazyka</option>
    ';
    $query = "SELECT ID,NAME FROM LANGUAGES order by NAME ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result))
    {
      echo "<option value='".$row['ID']."'>".$row['NAME']."</option>";
    }
    echo '</select>
  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Výber bilaterálnej dohody #3</label>
  <div class="col-md-4">
    <select id="selectbasic" name="bilateral_3" class="form-control">
    <option value="None">Výber bilaterálnej dohody</option>
    ';
    $query = "SELECT a.ID_UNIVERSITY,a.FROM_DATE, a.ID, a.BC, a.MGR, a.PHD, a.TO_DATE, a.SUBJECT_AREA_ID,u.ID as id_university, u.NAME as university_name, s.NAME as subject_name FROM AGREEMENTS as a join UNIVERSITIES as u on a.ID_UNIVERSITY = u.ID
               join SUBJECT_AREAS as s on a.SUBJECT_AREA_ID = s.ID ORDER BY u.NAME ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result))
    {
      $temp = '';
      if($row['BC'] == 1){
        $temp .= ' Bc.';
      }
      if($row['MGR'] == 1){
        $temp .= ' Mgr.';
      }
      if($row['PHD'] == 1){
        $temp .= ' Phd.';
      }
      echo "<option value='".$row['id_university']."'>".$row['university_name']." - ".$row['subject_name']." (".$row['FROM_DATE']." - ".$row['TO_DATE'].").$temp</option>";
    }
    echo '</select>
    <select id="selectbasic" name="lang_3" class="form-control">
    <option value="None">Výber preferovaného jazyka</option>
    ';
    $query = "SELECT ID,NAME FROM LANGUAGES order by NAME ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result))
    {
      echo "<option value='".$row['ID']."'>".$row['NAME']."</option>";
    }
    echo '</select>
  </div>
</div>

<!-- Textarea -->
<div class="form-group">
  <label class="col-md-4 control-label" for="textarea">Účasť na projektoch/iné aktivity</label>
  <div class="col-md-4">                     
    <textarea class="form-control" id="textarea" name="notes"></textarea>
  </div>
</div>
<!-- File Button --> 
<div class="form-group">
  <label class="col-md-4 control-label" for="filebutton">Motivačný list</label>
  <div class="col-md-4">
    <input id="filebutton" name="motivacny_list" class="input-file" type="file">
  </div>
</div>
<!-- File Button --> 
<div class="form-group">
  <label class="col-md-4 control-label" for="filebutton">Životopis</label>
  <div class="col-md-4">
    <input id="filebutton" name="zivotopis" class="input-file" type="file">
  </div>
</div>
<!-- File Button --> 
<div class="form-group">
  <label class="col-md-4 control-label" for="filebutton">Voliteľné</label>
  <div class="col-md-4">
    <input id="filebutton" name="volitelne" class="input-file" type="file">
  </div>
</div>


<!-- Multiple Checkboxes (inline) -->
<div class="form-group">
  <label class="col-md-4 control-label" for="checkboxes">ZŤP</label>
  <div class="col-md-4">
    <label class="checkbox-inline" for="checkboxes-0">
      <input type="checkbox" name="ztp" id="checkboxes-0" value="1">
      Áno
    </label>
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="checkboxes">Poberateľ sociálneho štipendia</label>
  <div class="col-md-4">
    <label class="checkbox-inline" for="checkboxes-0">
      <input type="checkbox" name="soc" id="checkboxes-0" value="1">
      Áno
    </label>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">E-mail</label>  
  <div class="col-md-4">
  <input id="textinput" name="email" type="text" placeholder="" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Password input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="passwordinput">Heslo</label>
  <div class="col-md-4">
    <input id="passwordinput" name="pass" type="password" placeholder="minimálne 6 znakov" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Password input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="passwordinput">Overenie hesla</label>
  <div class="col-md-4">
    <input id="passwordinput" name="pass_check" type="password" placeholder="znova zadajte vaše heslo" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Button (Double) -->
<div class="form-group">
  <label class="col-md-4 control-label" for="button1id">Odoslať</label>
  <div class="col-md-8">
    <button id="button1id" name="send" class="btn btn-success">Odoslať</button>
    <button id="button2id" name="reset" class="btn btn-danger">Vymazať</button>
  </div>
</div>

</fieldset>
</form>
</html>';
}

?>
