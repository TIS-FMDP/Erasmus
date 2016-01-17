<?php  
function edit_application(){  
 
include 'includes/form.php';
include 'includes/safe.php';
include 'includes/class.phpmailer.php';
$link = db_connect();
$code = substr(md5(uniqid(rand(), true)), 16, 16); 
$submit = (isset($_POST['send'])) ? true : false;

$student_name = ($submit) ? $safe->input($_POST['student_name']) : '';
$student_surname = ($submit) ? $safe->input($_POST['student_surname']) : '';
$student_surname = ($submit) ? $safe->input($_POST['student_surname']) : '';
$birthdate = ($submit) ? $safe->input($_POST['birthdate']) : '';
$citizenship = ($submit) ? $safe->input($_POST['citizenship']) : '';
$study_program = ($submit) ? $safe->input($_POST['study_program']) : '';
$study_year = ($submit) ? $safe->input($_POST['study_year']) : '';
$semester = ($submit) ? $safe->input($_POST['semester']) : '';
$notes = ($submit) ? $safe->input($_POST['notes']) : '';
$ztp = ($submit) ? $safe->input($_POST['ztp']) : 0;

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
if (exist('users', 'email', $email))
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
    $sql = 'INSERT INTO students (firstname,middlenames,lastname,born,student_id,gender,citizenship, email, year) VALUES ("' . $student_name . '", "", "'.$student_surname.'", "' . $born . '", "","' . $gender .'", "' . $citizenship .'","' . $email .'","' . $year .'");';    
    $query1 = mysqli_query($link,$sql) or die(mysqli_error($link));
    
    //insert into users
    $student_id = mysqli_insert_id($link);
    $sql = 'INSERT INTO users (role, email, passwd, name,student_id, reg_code,reg_valid) VALUES ("student", "' . $email . '", "' . md5($pass) . '","' . $student_name . $student_surname.'", "'. $student_id .'", "'. $code .'",0);';    
    $query2 = mysqli_query($link,$sql) or die(mysqli_error($link));
    
    $sql = 'INSERT INTO student_study_programs (id_student,id_studyprogram) VALUES ("' . $student_id . '", "'. $study_program .'");';    
    $query3 = mysqli_query($link,$sql) or die(mysqli_error($link));
    
    $id_student_program = mysqli_insert_id($link);
    $sql = 'INSERT INTO student_exchanges (id_student_study_program,study_year,agreement_id,from_date,to_date,semester,id_language,studentlevel,requiredlevel,socialstipend,handicapped,notes,cancelled,year) VALUES ("' . $id_student_program . '", "'. $study_year .'",0,1970/01/01, 1970/01/01, "'. $semester .'",0,"","",0,"'. $ztp .'","'. $notes .'",0,"'. $year .'");';    
    $query4 = mysqli_query($link,$sql) or die(mysqli_error($link));
    if($query1 && $query2 && $query3 && $query4){
      $error_log .= 'Boli ste úspešne zaregistrovaný!';
      try{
        $mail = new PHPMailer();
        $mail->From = "erasmus fmfi"; 
        $mail->AddAddress($email);
        $mail->Subject = "Registrácia na stránke Erasmus FMFI"; 
        $email_body = file_get_contents('user_register.txt');
        $patterns = array('([{]EMAIL[}])', '([{]PASSWORD[}])', '([{]CODE[}])');
        
        $replacements = array($nick, $pass, $code);
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
<link rel="stylesheet" type="text/css" href="moj_style.css">
<form class="form-horizontal" name="application" method="post">
<fieldset>
<!-- Form Name -->
<legend>Editácia prihlášky</legend>
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
    $query = "SELECT id,name FROM countries ORDER BY name ASC;";
    $result = mysqli_query($link,$query);
    while ($row = mysqli_fetch_array($result))
    {
      echo "<option value='".$row['id']."'>".$row['name']."</option>";
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
    $query = "SELECT id, code, name  from study_programs order by name ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result))
    {
      echo "<option value='".$row['id']."'>".$row['name']." - ".$row['code']."</option>";
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

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Výber bilaterálnej dohody #1</label>
  <div class="col-md-4">
    <select id="selectbasic" name="bilateral_1" class="form-control">
    <option value="None">Výber bilaterálnej dohody</option>
    ';
    $query = "SELECT a.id_university,a.from_date, a.id, a.bc, a.mgr, a.phd, a.to_date, a.subject_area_id, u.name as university_name, s.name as subject_name FROM agreements as a join universities as u on a.id_university = u.id
               join subject_areas as s on a.subject_area_id = s.id ORDER BY u.name ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result))
    {
      $temp = '';
      if($row['bc'] == 1){
        $temp .= ' Bc.';
      }
      if($row['mgr'] == 1){
        $temp .= ' Mgr.';
      }
      if($row['phd'] == 1){
        $temp .= ' Phd.';
      }
      echo "<option value='".$row['id']."'>".$row['university_name']." - ".$row['subject_name']." (".$row['from_date']." - ".$row['to_date'].").$temp</option>";
    }
    echo '</select>
    <select id="selectbasic" name="lang_1" class="form-control">
    <option value="None">Výber preferovaného jazyka</option>
    ';
    $query = "SELECT id,name FROM languages order by name ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result))
    {
      echo "<option value='".$row['id']."'>".$row['name']."</option>";
    }
    echo '</select>
  </div>
      <label for="checkboxes-1">
        <input type="checkbox" name="checkboxes" id="checkboxes-1" value="1">
        Vyber pre študenta túto dohodu
      </label>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Výber bilaterálnej dohody #2</label>
  <div class="col-md-4">
    <select id="selectbasic" name="bilateral_2" class="form-control">
    <option value="None">Výber bilaterálnej dohody</option>
    ';
    $query = "SELECT a.id_university,a.from_date, a.id, a.bc, a.mgr, a.phd, a.to_date, a.subject_area_id, u.name as university_name, s.name as subject_name FROM agreements as a join universities as u on a.id_university = u.id
               join subject_areas as s on a.subject_area_id = s.id ORDER BY u.name ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result))
    {
      $temp = '';
      if($row['bc'] == 1){
        $temp .= ' Bc.';
      }
      if($row['mgr'] == 1){
        $temp .= ' Mgr.';
      }
      if($row['phd'] == 1){
        $temp .= ' Phd.';
      }
      echo "<option value='".$row['id']."'>".$row['university_name']." - ".$row['subject_name']." (".$row['from_date']." - ".$row['to_date'].").$temp</option>";
    }
    echo '</select>
    <select id="selectbasic" name="lang_2" class="form-control">
    <option value="None">Výber preferovaného jazyka</option>
    ';
    $query = "SELECT id,name FROM languages order by name ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result))
    {
      echo "<option value='".$row['id']."'>".$row['name']."</option>";
    }
    echo '</select>
  </div>
       <label for="checkboxes-2">
            <input type="checkbox" name="checkboxes" id="checkboxes-2" value="1">
            Vyber pre študenta túto dohodu
      </label>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Výber bilaterálnej dohody #3</label>
  <div class="col-md-4">
    <select id="selectbasic" name="bilateral_3" class="form-control">
    <option value="None">Výber bilaterálnej dohody</option>
    ';
    $query = "SELECT a.id_university,a.from_date, a.id, a.bc, a.mgr, a.phd, a.to_date, a.subject_area_id, u.name as university_name, s.name as subject_name FROM agreements as a join universities as u on a.id_university = u.id
               join subject_areas as s on a.subject_area_id = s.id ORDER BY u.name ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result))
    {
      $temp = '';
      if($row['bc'] == 1){
        $temp .= ' Bc.';
      }
      if($row['mgr'] == 1){
        $temp .= ' Mgr.';
      }
      if($row['phd'] == 1){
        $temp .= ' Phd.';
      }
      echo "<option value='".$row['id']."'>".$row['university_name']." - ".$row['subject_name']." (".$row['from_date']." - ".$row['to_date'].").$temp</option>";
    }
    echo '</select>
    <select id="selectbasic" name="lang_3" class="form-control">
    <option value="None">Výber preferovaného jazyka</option>
    ';
    $query = "SELECT id,name FROM languages order by name ASC;";
    $result = mysqli_query($link,$query) or die(mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result))
    {
      echo "<option value='".$row['id']."'>".$row['name']."</option>";
    }
    echo '</select>
  </div>
      <label for="checkboxes-3">
            <input type="checkbox" name="checkboxes" id="checkboxes-3" value="1">
            Vyber pre študenta túto dohodu
      </label>
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

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Stav prihlášky</label>
  <div class="col-md-4">
    <select id="selectbasic" name="selectbasic" class="form-control">
      <option value="1">Podaná</option>
      <option value="2">Schválená</option>
      <option value="3">Papierovo prijatá</option>
      <option value="4">V poradovníku</option>
      <option value="5">Zamietnutá</option>
    </select>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Pridaj body</label>  
  <div class="col-md-4">
  <input id="textinput" name="textinput" type="text" placeholder="placeholder" class="form-control input-md">
    <button id="singlebutton" name="singlebutton" class="btn btn-primary">Pripočítaj</button>
  <span class="help-block">Vpíš body ktoré sa majú pripočítať k celkovému počtu bodov.</span>  
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Zmeň počet bodov</label>  
  <div class="col-md-4">
  <input id="textinput" name="textinput" type="text" placeholder="placeholder" class="form-control input-md">
  <span class="help-block">Prepíš celkový počet bodov.</span>  
  </div>
</div>

<!-- Button (Double) -->
<div class="form-group">
  <div class="col-md-8">
    <button id="button1id" name="send" class="btn btn-success">Uložiť</button>
  </div>
</div>

</fieldset>
</form>
</html>';
}

?>


