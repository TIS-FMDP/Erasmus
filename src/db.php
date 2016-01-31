<?PHP

function db_connect()
{
	global $DB_USER, $DB_PASS, $DB_NAME;
	$link = new mysqli('localhost',$DB_USER,$DB_PASS,$DB_NAME);
    mysqli_set_charset($link,"utf8");
        if ($link->connect_error)
		die('Could not connect to database erasmus ' . $link->connect_error); 
	return $link;
}     

function exist($table, $field, $value, $where = '')
{
  global $link; mysqli_set_charset($link,"utf8");
  $link = db_connect();
  mysqli_set_charset($link,"utf8");
  if ($value == '-')
  {
    return false;
  }
  if (empty($where))
  {
    $podmienka = ';';
  }
  else
  {
    $podmienka =  ' AND NOT ID="' . $where . '";';
  }
  $sql = 'SELECT ID FROM ' . $table . ' WHERE ' . $field . '="' . $value . '"' . $podmienka;
  $query = mysqli_query($link,$sql);
  return (boolean) mysqli_num_rows($query);
}


function valid_email($email)
{
 return filter_var($email, FILTER_VALIDATE_EMAIL);
}
/*-------------- AGREEMENTS ---------------*/

// builds a 2d array with data about all agreements 
function db_retrieve_agreements_data($as_selection=FALSE)
{
	global $link; mysqli_set_charset($link,"utf8");
    mysqli_set_charset($link,"utf8");
	$zm = array();
	$stmnt = $link->stmt_init();
	$stmnt->prepare('SELECT AGREEMENTS.ID, UNIVERSITIES.ID, UNIVERSITIES.NAME, UNIVERSITIES.CODE, COUNTRIES.NAME, CONTACT_PERSON, SUBJECT_AREAS.CODE, SUBJECT_AREAS.NAME, FROM_DATE, TO_DATE, BC, MGR, PHD, TOTAL_NUMBER, SUBJECT_AREAS.ID FROM AGREEMENTS JOIN UNIVERSITIES ON UNIVERSITIES.ID=AGREEMENTS.ID_UNIVERSITY JOIN COUNTRIES ON COUNTRIES.ID=UNIVERSITIES.ID_COUNTRY JOIN SUBJECT_AREAS ON SUBJECT_AREAS.ID=AGREEMENTS.SUBJECT_AREA_ID ORDER BY UNIVERSITIES.CODE,TO_DATE');
	$stmnt->execute();
	$agreements = $stmnt->get_result();
	while ($row = $agreements->fetch_row())
	{
		if ($as_selection)
			$rw = array($row[0], $row[2] . ' ('. $row[4] . ', ' . $row[3] . ', ' . $row[6] . '-' . $row[7] . ') ' . $row[8] . ' - ' . $row[9]);
		else
		{
			$rw = array($row[0], array($row[1], $row[2], $row[4], $row[3]), $row[8], $row[9], $row[5], array($row[14], $row[6] . ' - ' .  $row[7]), $row[10], $row[11], $row[12], $row[13]);
			$fls = db_list_files(3, $row[0]);
			$rw[] = $fls;
		}
		$zm[] = $rw;
	}
	$stmnt->close();
	return $zm;
}

function db_retrieve_agreement_byID($id)
{
	global $link; mysqli_set_charset($link,"utf8");
    mysqli_set_charset($link,"utf8");
	$stmnt = $link->stmt_init();
	$stmnt->prepare('SELECT AGREEMENTS.ID, UNIVERSITIES.ID, UNIVERSITIES.NAME, UNIVERSITIES.CODE, COUNTRIES.NAME, FROM_DATE, TO_DATE, SUBJECT_AREA_ID, CONTACT_PERSON, BC, MGR, PHD, TOTAL_NUMBER FROM AGREEMENTS JOIN UNIVERSITIES ON UNIVERSITIES.ID=AGREEMENTS.ID_UNIVERSITY JOIN COUNTRIES ON COUNTRIES.ID=UNIVERSITIES.ID_COUNTRY WHERE (AGREEMENTS.ID=?)');
	$stmnt->bind_param('i', $id);
	$stmnt->execute();
	$stmnt->bind_result($agid, $unid, $uniname, $unicode, $cntry, $fromdate, $todate, $subjareaid, $contpers, $bc, $mgr, $phd, $totalnum); 
	$stmnt->fetch();
	$rw = array($agid, array($unid, $uniname, $cntry, $unicode), $fromdate, $todate, $contpers, $subjareaid, $bc, $mgr, $phd, $totalnum);
	$stmnt->close();
	$fls = db_list_files(3, $agid);
	$rw[] = $fls;
	return $rw;
}

function db_save_agreement($id, $uniID, $validFROM, $validTO, $subjareaID, $coord, $bsc, $mgr, $phd, $totalnum)
{
	global $link; mysqli_set_charset($link,"utf8");
    mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$op = 0;
	if ($id == '-') $op = 1;
	if ($op === 1)
	{
		$stm->prepare('INSERT INTO AGREEMENTS (ID_UNIVERSITY, FROM_DATE, TO_DATE, SUBJECT_AREA_ID, CONTACT_PERSON, BC, MGR, PHD, TOTAL_NUMBER) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        	$stm->bind_param('issisiiii', $uniID, $validFROM, $validTO, $subjareaID, $coord, $bsc, $mgr, $phd, $totalnum);
	}
	else 
	{
		$stm->prepare('UPDATE AGREEMENTS SET ID_UNIVERSITY=?, FROM_DATE=?, TO_DATE=?, SUBJECT_AREA_ID=?, CONTACT_PERSON=?, BC=?, MGR=?, PHD=?, TOTAL_NUMBER=? WHERE ID=?');
		$stm->bind_param('issisiiiii', $uniID, $validFROM, $validTO, $subjareaID, $coord, $bsc, $mgr, $phd, $totalnum, $id);
	}
	$stm->execute();
	if ($op == 1)
        {
		$inserted_id = $stm->insert_id;
		db_append_to_log('AGREEMENTS', $inserted_id, 'add', 'record added', "$uniID, $validFROM, $validTO, $subjareaID, $coord, $bsc, $mgr, $phd, $totalnum");
	}
	else 
	{
        	db_append_to_log('AGREEMENTS', $id, 'edit', 'record updated', "$uniID, $validFROM, $validTO, $subjareaID, $coord, $bsc, $mgr, $phd, $totalnum");
		$inserted_id = $id;
	}
	$stm->close();
	return $inserted_id;
}

function db_remove_agreements_cascading($ids)
{
	global $link; mysqli_set_charset($link,"utf8");
    mysqli_set_charset($link,"utf8");
	$idar = explode(', ', $ids);
	$stm2 = $link->stmt_init();
	$stm2->prepare('DELETE FROM AGREEMENTS WHERE ID=?');
	foreach($idar as $id)
	{
		$stm2->bind_param('i', $id);
		$stm2->execute();
		db_append_to_log('AGREEMENTS', $id, 'remove', 'record removed', "--");
	}
	$stm2->close();
	$fids = db_retrieve_file_ids(3, $idar);
	db_delete_files($fids);
}

function db_get_first_agreement()
{
	global $link; mysqli_set_charset($link,"utf8");
    mysqli_set_charset($link,"utf8");
	$link = db_connect();
	$stm = $link->stmt_init();
	$stm->prepare('SELECT ID FROM AGREEMENTS LIMIT 1');
	$stm->execute();
	$stm->bind_result($agid);
	$stm->fetch();
	$stm->close();
	return $agid; 
}

/*---------------------SUBJECT_AREAS-----------------------*/
function db_get_first_subject_area()
{
	global $link; mysqli_set_charset($link,"utf8");
    mysqli_set_charset($link,"utf8");
	$link = db_connect();
	$stm = $link->stmt_init();
	$stm->prepare('SELECT ID FROM SUBJECT_AREAS LIMIT 1');
	$stm->execute();
	$stm->bind_result($said);
	$stm->fetch();
	$stm->close();
	return $said; 
}

function db_subject_areas($as_selection = FALSE)
{
	global $link; mysqli_set_charset($link,"utf8");
    mysqli_set_charset($link,"utf8");
	$stmnt = $link->stmt_init();
	$stmnt->prepare('SELECT ID, CODE, NAME FROM SUBJECT_AREAS ORDER BY CODE');
	$stmnt->execute();
	$sa = $stmnt->get_result();
	$sadata = array();
	while ($rw = $sa->fetch_row())
		if ($as_selection) $sadata[] = array($rw[0], $rw[1] . ': ' . $rw[2]);
		else $sadata[] = array($rw[0], $rw[1], $rw[2]);
	$stmnt->close();
	return $sadata;
}

function db_save_subject_area($id, $code, $name)
{
	global $link; mysqli_set_charset($link,"utf8");
    mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$op = 0;
	if ($id == '-') $op = 1;
	if ($op === 1)
	{
		$stm->prepare('INSERT INTO SUBJECT_AREAS (CODE, NAME) VALUES (?, ?)');
        	$stm->bind_param('ss', $code, $name);
	}
	else 
	{
		$stm->prepare('UPDATE SUBJECT_AREAS SET CODE=?, NAME=? WHERE ID=?');
		$stm->bind_param('ssi', $code, $name, $id);
	}
	$stm->execute();
	if ($op == 1)
	{
		$inserted_id = $stm->insert_id;
		db_append_to_log('SUBJECT_AREAS', $inserted_id, 'add', 'record added', "$code, $name, $id");

	}
	else 
	{
		$inserted_id = $id;
                db_append_to_log('SUBJECT_AREAS', $id, 'edit', 'record updated', "$code, $name, $id");
	}
	$stm->close();
	return $inserted_id;
}

function db_remove_subject_area($ids)
{
	global $link; mysqli_set_charset($link,"utf8");
    mysqli_set_charset($link,"utf8");
	$idar = explode(', ', $ids);
	$stm = $link->stmt_init();
	$stm->prepare('DELETE FROM SUBJECT_AREAS WHERE ID=?');
	foreach($idar as $id)
	{
		$stm->bind_param('i', $id);
		$stm->execute();
		db_append_to_log('SUBJECT_AREAS', $id, 'remove', 'record removed', "--");
	}
	$stm->close();
}


/*---------------------COUNTRIES-----------------------*/
function db_get_first_country()
{
	global $link; mysqli_set_charset($link,"utf8");
    mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$stm->prepare('SELECT ID, NAME FROM COUNTRIES LIMIT 1');
	$stm->execute();
	$stm->bind_result($uniid, $uniname);
	$stm->fetch();
	$stm->close();
	return array($uniid, $uniname);
}

function db_countries()
{
	global $link;
    mysqli_set_charset($link,"utf8");
	$stmnt = $link->stmt_init();
	$stmnt->prepare('SELECT ID, NAME FROM COUNTRIES ORDER BY NAME');
	$stmnt->execute();
	$cntry = $stmnt->get_result();
	$cntrydata = array();
	while ($rw = $cntry->fetch_row())
		$cntrydata[] = array($rw[0], $rw[1]);
	$stmnt->close();
	return $cntrydata;
}

/*----------------------USERS----------------------*/
//loads variables based on $userid
function db_users_load()
{
	global $userid, $userrole, $useremail, $username, $link;
    mysqli_set_charset($link,"utf8");
	$stmnt = $link->stmt_init();
	$stmnt->prepare('SELECT ROLE, EMAIL, NAME FROM USERS WHERE ID=?');
	$stmnt->bind_param('i', $userid);
	$stmnt->execute();
	$stmnt->bind_result($userrole, $useremail, $username);
	$stmnt->fetch();
	$stmnt->close();
}

//verifies user credentials against database
function db_try_to_login($email, $passwd)
{
	global $userid, $link; mysqli_set_charset($link,"utf8");

	$stmnt = $link->stmt_init();
	$stmnt->prepare('SELECT ID FROM USERS WHERE EMAIL=? AND PASSWD=? AND reg_valid = 1');
        $mail = $email;
	$pass = md5($passwd);
	$stmnt->bind_param('ss', $mail, $pass);
	$stmnt->execute();
	$stmnt->bind_result($userid);
	$stmnt->fetch();
	$stmnt->close();
	$_SESSION['userid']=$userid;
}

/*-------------------UNIVERSITIES--------------------*/
function db_universities_with_or_without($with)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stmnt = $link->stmt_init();
	if ($with)
		$stmnt->prepare('SELECT UNIVERSITIES.ID, UNIVERSITIES.NAME, COUNTRIES.NAME, UNIVERSITIES.CODE FROM UNIVERSITIES, COUNTRIES WHERE COUNTRIES.ID=UNIVERSITIES.ID_COUNTRY ORDER BY UNIVERSITIES.NAME');
	else $stmnt->prepare('SELECT ID, NAME, ID_COUNTRY, CODE FROM UNIVERSITIES ORDER BY UNIVERSITIES.NAME');
	$stmnt->execute();
	$uni = $stmnt->get_result();
	$unidata = array();
	while ($rw = $uni->fetch_row())
		if ($with)
			$unidata[] = array($rw[0], $rw[1] . ' (' . $rw[2] . ', ' . $rw[3] . ')', $rw[3]);
		else $unidata[] = array($rw[0], $rw[1], $rw[2], $rw[3]);
	$stmnt->close();
	return $unidata;
}

function db_universities_with_countries()
{
	return db_universities_with_or_without(TRUE);
}

function db_universities_without_countries()
{
	return db_universities_with_or_without(FALSE);
}

function db_remove_university($ids)
{
	global $link; mysqli_set_charset($link,"utf8");
	$idar = explode(', ', $ids);
	$stm = $link->stmt_init();
	$stm->prepare('DELETE FROM UNIVERSITIES WHERE ID=?');
	foreach($idar as $id)
	{
		$stm->bind_param('i', $id);
		$stm->execute();
		db_append_to_log('UNIVERSITIES', $id, 'remove', 'record removed', "--");
	}
	$stm->close();
}

function db_save_university($id, $uniName, $cntry, $uniCode)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$op = 0;
	if ($id == '-') $op = 1;
	if ($op === 1)
	{
		$stm->prepare('INSERT INTO UNIVERSITIES (NAME, ID_COUNTRY, CODE) VALUES (?, ?, ?)');
        	$stm->bind_param('sis', $uniName, $cntry, $uniCode);
	}
	else 
	{
		$stm->prepare('UPDATE UNIVERSITIES SET NAME=?, ID_COUNTRY=?, CODE=? WHERE ID=?');
		$stm->bind_param('sisi', $uniName, $cntry, $uniCode, $id);
	}
	$stm->execute();
	if ($op == 1)
	{
		$inserted_id = $stm->insert_id;
		db_append_to_log('UNIVERSITIES', $inserted_id, 'add', 'record added', "$uniName, $cntry, $uniCode");
	}
	else 
	{
		$inserted_id = $id;
        	db_append_to_log('UNIVERSITIES', $id, 'edit', 'record updated', "$uniName, $cntry, $uniCode");
	}
	$stm->close();
	return $inserted_id;
}

/*------------------STUDYPROGRAMS-------------------------*/
function db_study_programs($as_selection = FALSE)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stmnt = $link->stmt_init();
	$stmnt->prepare('SELECT ID, CODE, NAME FROM STUDY_PROGRAMS ORDER BY CODE');
	$stmnt->execute();
	$sp = $stmnt->get_result();
	$spdata = array();
	while ($rw = $sp->fetch_row())
		if ($as_selection) $spdata[] = array($rw[0], $rw[1] . ': ' . $rw[2]);
		else $spdata[] = array($rw[0], $rw[1], $rw[2]);
	$stmnt->close();
	return $spdata;
}

function db_remove_study_programs($ids)
{
	global $link; mysqli_set_charset($link,"utf8");
	$idar = explode(', ', $ids);
	$stm = $link->stmt_init();
	$stm->prepare('DELETE FROM STUDY_PROGRAMS WHERE ID=?');
	foreach($idar as $id)
	{
		$stm->bind_param('i', $id);
		$stm->execute();
		db_append_to_log('STUDY_PROGRAMS', $id, 'remove', 'record removed', "--");
	}
	$stm->close();
}

function db_save_study_program($id, $code, $name)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$op = 0;
	if ($id == '-') $op = 1;
	if ($op === 1)
	{
		$stm->prepare('INSERT INTO STUDY_PROGRAMS (CODE, NAME) VALUES (?, ?)');
        	$stm->bind_param('ss', $code, $name);
	}
	else 
	{
		$stm->prepare('UPDATE STUDY_PROGRAMS SET CODE=?, NAME=? WHERE ID=?');
		$stm->bind_param('ssi', $code, $name, $id);
	}
	$stm->execute();
	if ($op == 1)
	{
		$inserted_id = $stm->insert_id;
		db_append_to_log('STUDY_PROGRAMS', $inserted_id, 'add', 'record added', "$code, $name, $id");

	}
	else 
	{
		$inserted_id = $id;
                db_append_to_log('STUDY_PROGRAMS', $id, 'edit', 'record updated', "$code, $name, $id");
	}
	$stm->close();
	return $inserted_id;
}

function db_list_study_programs_for_student($id)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$stm->prepare('SELECT STUDY_PROGRAMS.ID, STUDY_PROGRAMS.CODE, STUDY_PROGRAMS.NAME FROM STUDY_PROGRAMS, STUDENT_STUDY_PROGRAMS WHERE (STUDENT_STUDY_PROGRAMS.ID_STUDYPROGRAM=STUDY_PROGRAMS.ID) AND (STUDENT_STUDY_PROGRAMS.ID_STUDENT=?) ORDER BY STUDY_PROGRAMS.CODE');
	$stm->bind_param('i', $id);
	$stm->execute();
	$sp = $stm->get_result();
	$stsp = array();
	while ($rw = $sp->fetch_row())
		$stsp[] = array($rw[0], $rw[1] . ': ' . $rw[2]); 
	$stm->close(); 
	return $stsp;
}

/*------------------FMFI COURSES-------------------------*/
function db_fmfi_courses($as_selection=FALSE)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stmnt = $link->stmt_init();
	$stmnt->prepare('SELECT ID, FMFI_COURSE_CODE, FMFI_COURSE_NAME, CREDITS FROM FMFI_COURSES ORDER BY FMFI_COURSE_CODE');
	$stmnt->execute();
	$fc = $stmnt->get_result();
	$fcdata = array();
	while ($rw = $fc->fetch_row())
		if ($as_selection)
			$fcdata[] = array($rw[0], $rw[2] . ' (' . $rw[1] . ') ' . $rw[3] . ' credits'); 
		else $fcdata[] = $rw; 
	$stmnt->close();
	return $fcdata;
}

function db_retrieve_fmfi_course_byID($id)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stmnt = $link->stmt_init();
	$stmnt->prepare('SELECT FMFI_COURSE_CODE, FMFI_COURSE_NAME, CREDITS FROM FMFI_COURSES WHERE ID=?');
	$stmnt->bind_param('i', $id);
	$stmnt->execute();
	$stmnt->bind_result($code, $name, $credits); 
	$stmnt->fetch();
	$rw = array($id, $code, $name, $credits);
	$stmnt->close();
	return $rw;
}

function db_remove_fmfi_courses($ids)
{
	global $link; mysqli_set_charset($link,"utf8");
	$idar = explode(', ', $ids);
	$stm = $link->stmt_init();
	$stm->prepare('DELETE FROM FMFI_COURSES WHERE ID=?');
	foreach($idar as $id)
	{
		$stm->bind_param('i', $id);
		$stm->execute();
		db_append_to_log('FMFI_COURSES', $id, 'remove', 'record removed', "--");
	}
	$stm->close();
}

function db_save_fmfi_course($id, $code, $name, $credits)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$op = 0;
	if ($id == '-') $op = 1;
	if ($op === 1)
	{
		$stm->prepare('INSERT INTO FMFI_COURSES (FMFI_COURSE_CODE, FMFI_COURSE_NAME, CREDITS) VALUES (?, ?, ?)');
        	$stm->bind_param('ssi', $code, $name, $credits);
	}
	else 
	{
		$stm->prepare('UPDATE FMFI_COURSES SET FMFI_COURSE_CODE=?, FMFI_COURSE_NAME=?, CREDITS=? WHERE ID=?');
		$stm->bind_param('ssii', $code, $name, $credits, $id);
	}
	$stm->execute();
	if ($op == 1)
	{
		$inserted_id = $stm->insert_id;
		db_append_to_log('FMFI_COURSES', $inserted_id, 'add', 'record added', "$code, $name, $credits");
	}
	else 
	{
		$inserted_id = $id;
                db_append_to_log('FMFI_COURSES', $id, 'edit', 'record updated', "$code, $name, $credits");
	}
	$stm->close();
	return $inserted_id;
}

function db_get_first_fmfi()
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$stm->prepare('SELECT ID FROM FMFI_COURSES LIMIT 1');
	$stm->execute();
	$stm->bind_result($fmfiid);
	$stm->fetch();
	$stm->close();
	return $fmfiid; 
}

/*------------------STUDENTS-------------------------*/
function db_get_first_student()
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$stm->prepare('SELECT ID FROM STUDENTS LIMIT 1');
	$stm->execute();
	$stm->bind_result($stid);
	$stm->fetch();
	$stm->close();
	return $stid; 
}

function db_students($as_selection=FALSE)
{
	global $link, $selected_year;

	if (($selected_year === "ALL") || (strlen($selected_year) === 0)) $where_year = "";
	else $where_year = ' WHERE YEAR=?';

	$stmnt = $link->stmt_init();
	$stmnt->prepare('SELECT STUDENTS.ID, FIRSTNAME, MIDDLENAMES, LASTNAME, BORN, STUDENT_ID, GENDER, CITIZENSHIP, COUNTRIES.NAME, EMAIL, YEAR FROM STUDENTS JOIN COUNTRIES ON COUNTRIES.ID=CITIZENSHIP' . $where_year . ' ORDER BY LASTNAME,FIRSTNAME');
	if (strlen($where_year) > 0) $stmnt->bind_param('s', $selected_year);
	$stmnt->execute();
	$st = $stmnt->get_result();
	$stdata = array();
	while ($rw = $st->fetch_row())
	{
		if ($as_selection)
		{
			$stid = $rw[5];
			if (strlen($stid) > 0) $stid = ', ' . $stid;
			$strec = array($rw[0], $rw[3] . ' ' . $rw[1] . ' ' . $rw[2] . ' (' . $rw[4] . $stid . ')');
		}
		else
		{
			$strec = array($rw[0], $rw[1], $rw[2], $rw[3], $rw[4], $rw[5], $rw[6], array($rw[7], $rw[8]), $rw[9]);
			$strec[] = db_list_study_programs_for_student($rw[0]);
			$strec[] = $rw[10];
		}
		$stdata[] = $strec;
	}
	$stmnt->close();
	return $stdata;
}

function db_remove_students_cascading($ids)
{
	global $link; mysqli_set_charset($link,"utf8");
	$idar = explode(', ', $ids);
	$stm = $link->stmt_init();
	$stm2 = $link->stmt_init();
	$stm3 = $link->stmt_init();
	$stm->prepare('DELETE FROM STUDENTS WHERE ID=?');
	$stm2->prepare('DELETE FROM STUDENT_STUDY_PROGRAMS WHERE ID_STUDENT=?');
        $stm3->prepare('DELETE STUDENT_EXCHANGES, STUDENT_SUBJECTS FROM STUDENT_EXCHANGES LEFT JOIN STUDENT_SUBJECTS ON STUDENT_EXCHANGES.ID=STUDENT_SUBJECTS.ID_EXCHANGE LEFT JOIN STUDENT_STUDY_PROGRAMS ON STUDENT_STUDY_PROGRAMS.ID=STUDENT_EXCHANGES.ID_STUDENT_STUDY_PROGRAM WHERE STUDENT_STUDY_PROGRAMS.ID=?');

	foreach($idar as $id)
	{
		$stm->bind_param('i', $id);
		$stm->execute();
		$stm2->bind_param('i', $id);
		$stm2->execute();
		$stm3->bind_param('i', $id);
		$stm3->execute();
		db_append_to_log('STUDENTS', $id, 'remove', 'record and all associated records removed', "--");
	}
	$stm->close();
	$stm2->close();
	$stm3->close();
}

function db_save_student($id, $firstname, $middlename, $lastname, $born, $studentID, $gender, $citiz, $email, $year)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$op = 0;
	if ($id == '-') $op = 1;
	if ($op === 1)
	{
		$stm->prepare('INSERT INTO STUDENTS (FIRSTNAME, MIDDLENAMES, LASTNAME, BORN, STUDENT_ID, GENDER, CITIZENSHIP, EMAIL, YEAR) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        	$stm->bind_param('ssssssiss', $firstname, $middlename, $lastname, $born, $studentID, $gender, $citiz, $email, $year);
	}
	else 
	{
		$stm->prepare('UPDATE STUDENTS SET FIRSTNAME=?, MIDDLENAMES=?, LASTNAME=?, BORN=?, STUDENT_ID=?, GENDER=?, CITIZENSHIP=?, EMAIL=?, YEAR=? WHERE ID=?');
		$stm->bind_param('ssssssissi', $firstname, $middlename, $lastname, $born, $studentID, $gender, $citiz, $email, $year, $id);
	}
	$stm->execute();
	if ($op == 1)
	{
		$inserted_id = $stm->insert_id;
		db_append_to_log('STUDENTS', $inserted_id, 'add', 'record added', "$firstname, $middlename, $lastname, $born, $studentID");
	}
	else 
	{
		$inserted_id = $id;
                db_append_to_log('STUDENTS', $id, 'edit', 'record updated', "$firstname, $middlename, $lastname, $born, $studentID");
	}
	$stm->close();
	return $inserted_id;
}

function db_retrieve_student_byID($id)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stmnt = $link->stmt_init();
	$stmnt->prepare('SELECT FIRSTNAME, MIDDLENAMES, LASTNAME, BORN, STUDENT_ID, GENDER, CITIZENSHIP, EMAIL, YEAR FROM STUDENTS WHERE ID=?');
	$stmnt->bind_param('i', $id);
	$stmnt->execute();
	$stmnt->bind_result($firstname, $middlename, $lastname, $born, $student_id, $gender, $citiz, $email, $year); 
	$stmnt->fetch();
	$rw = array($id, $firstname, $middlename, $lastname, $born, $student_id, $gender, $citiz, $email);
	$stmnt->close();
	$rw[] = db_list_study_programs_for_student($id);
	$rw[] = $year;
	return $rw;
}

function db_add_study_program_to_student($stid, $spid)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$stm->prepare('INSERT INTO STUDENT_STUDY_PROGRAMS SET ID_STUDENT=?, ID_STUDYPROGRAM=?');
	$stm->bind_param('ii', $stid, $spid);
	$stm->execute();
	$inserted_id = $stm->insert_id;
	$stm->close();
	db_append_to_log('STUDENT_STUDY_PROGRAMS', $inserted_id, 'add', 'record added', "$stid, $spid");
}

function db_retrieve_study_prog_and_student_details($spid, $stid)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$stm->prepare('SELECT STUDY_PROGRAMS.CODE, STUDY_PROGRAMS.NAME, STUDENTS.FIRSTNAME, STUDENTS.MIDDLENAMES, STUDENTS.LASTNAME FROM STUDENTS JOIN STUDENT_STUDY_PROGRAMS ON STUDENTS.ID = STUDENT_STUDY_PROGRAMS.ID_STUDENT JOIN STUDY_PROGRAMS ON STUDY_PROGRAMS.ID = STUDENT_STUDY_PROGRAMS.ID_STUDYPROGRAM WHERE (STUDENTS.ID=?) AND (STUDY_PROGRAMS.ID=?)');
	$stm->bind_param('ii', $stid, $spid);
	$stm->execute();
	$stm->bind_result($spcode, $spname, $firstname, $midnames, $lastname);
	$stm->fetch();
	$stm->close();
	return array($spcode . ': ' . $spname, $firstname . ' ' . $midnames . ' ' . $lastname);
}

function db_remove_student_program_of_student($spid, $stid)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$stm->prepare('DELETE FROM STUDENT_STUDY_PROGRAMS WHERE (ID_STUDENT=?) AND (ID_STUDYPROGRAM=?)');
	$stm->bind_param('ii', $stid, $spid);
	$stm->execute();
	$stm->close();
	db_append_to_log('STUDENT_STUDY_PROGRAMS', $spid, 'remove', 'record (spid=' . $spid . ', stid=' . $stid . ') removed', "--");
}

function db_retrieve_or_insert_student_study_program($stid, $spid)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$stm->prepare('SELECT ID FROM STUDENT_STUDY_PROGRAMS WHERE (ID_STUDENT=?) AND (ID_STUDYPROGRAM=?)');
	$stm->bind_param('ii', $stid, $spid);
	$stm->execute();
	$stm->bind_result($stspid);
	if ($stm->fetch() == NULL)
	{ 
		$stm->close();
		$stm2 = $link->stmt_init();
		$stm2->prepare('INSERT INTO STUDENT_STUDY_PROGRAMS SET ID_STUDENT=?, ID_STUDYPROGRAM=?');
		$stm2->bind_param('ii', $stid, $spid);
		$stm2->execute();
		$stspid = $stm2->insert_id;
		$stm2->close();
		db_append_to_log('STUDENT_STUDY_PROGRAMS', $stspid, 'add', 'record added', "$stid, $spid");
	}
	else $stm->close();
	return $stspid;
}

/*---------------------- TRAVELS -------------------------*/

function db_save_travel($id, $year, $agid, $datefrom, $dateto, $stid, $spid, $semester, $lang, $lhas, $lexp, $socs, $hcap, $notes, $cancelled, $acyear)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stspid = db_retrieve_or_insert_student_study_program($stid, $spid);
	$stm = $link->stmt_init();
	$op = 0;
	if ($id == '-') $op = 1;
	if ($op === 1)
	{
		$stm->prepare('INSERT INTO STUDENT_EXCHANGES (STUDY_YEAR, AGREEMENT_ID, FROM_DATE, TO_DATE, ID_STUDENT_STUDY_PROGRAM, SEMESTER, ID_LANGUAGE, STUDENTLEVEL, REQUIREDLEVEL, SOCIALSTIPEND, HANDICAPPED, NOTES, CANCELLED, YEAR) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        	$stm->bind_param('iissisissiisis', $year, $agid, $datefrom, $dateto, $stspid, $semester, $lang, $lhas, $lexp, $socs, $hcap, $notes, $cancelled, $acyear);
	}
	else 
	{
		$stm->prepare('UPDATE STUDENT_EXCHANGES SET STUDY_YEAR=?, AGREEMENT_ID=?, FROM_DATE=?, TO_DATE=?, ID_STUDENT_STUDY_PROGRAM=?, SEMESTER=?, ID_LANGUAGE=?, STUDENTLEVEL=?, REQUIREDLEVEL=?, SOCIALSTIPEND=?, HANDICAPPED=?, NOTES=?, CANCELLED=?, YEAR=? WHERE ID=?');
		$stm->bind_param('iissisissiisisi', $year, $agid, $datefrom, $dateto, $stspid, $semester, $lang, $lhas, $lexp, $socs, $hcap, $notes, $cancelled, $acyear, $id);
	}
	$stm->execute();
	if ($op == 1)
	{
		$inserted_id = $stm->insert_id;
		$stm->close();
		db_append_to_log('STUDENT_EXCHANGES', $inserted_id, 'add', 'record added', "$year, $agid, $datefrom, $dateto, $stspid");
	}
	else 
	{
		$stm->close();
		$stm = $link->stmt_init();
		$stm->prepare('DELETE FROM STUDENT_STUDY_PROGRAMS WHERE NOT EXISTS (SELECT * FROM STUDENT_EXCHANGES WHERE STUDENT_EXCHANGES.ID_STUDENT_STUDY_PROGRAM=STUDENT_STUDY_PROGRAMS.ID)');
		$stm->execute();
		$stm->close();	
		$inserted_id = $id;
                db_append_to_log('STUDENT_EXCHANGES', $id, 'edit', 'record updated', "$year, $agid, $datefrom, $dateto, $stspid");
	}
	return $inserted_id;
}

function db_remove_travel_cascading($ids)
{
	global $link; mysqli_set_charset($link,"utf8");
	$idar = explode(', ', $ids);
	$stm2 = $link->stmt_init();
	$stm2->prepare('DELETE FROM STUDENT_EXCHANGES WHERE ID=?');
	$stm3 = $link->stmt_init();
	$stm3->prepare('DELETE FROM STUDENT_SUBJECTS WHERE ID_EXCHANGE=?');
	foreach($idar as $id)
	{
		$stm2->bind_param('i', $id);
		$stm2->execute();
		$stm3->bind_param('i', $id);
		$stm3->execute();
		db_append_to_log('STUDENT_EXCHANGES', $id, 'remove', 'record and associated student subjects removed', "--");
	}
	$stm2->close();
	$stm3->close();
	$fids = db_retrieve_file_ids(7, $idar);
	db_delete_files($fids);
}

function travel_where($filter_a, $filter_d1)
{
	global $selected_year;

	if (($selected_year === "ALL") || (strlen($selected_year) === 0)) $where_year = NULL;
	else $where_year = 'STUDENT_EXCHANGES.YEAR=?';
	if (($filter_a != NULL) || ($filter_d1 != NULL) || ($where_year != NULL)) $where = ' WHERE '; else $where = '';
	if ($filter_a != NULL) { $where = $where . '(AGREEMENT_ID=?)'; $params = 'i'; } else $params = '';
	if (($filter_a != NULL) && ($filter_d1 != NULL)) $where = $where . ' AND '; 
	if ($filter_d1 != NULL) { $where = $where . '(STUDENT_EXCHANGES.FROM_DATE <= ?) AND (STUDENT_EXCHANGES.TO_DATE >= ?)'; $params = $params . 'ss'; }
	if (($where_year != NULL) && (count($where) > 7)) $where = $where . ' AND ';
	if ($where_year != NULL) { $where = $where . $where_year; $params = $params . 's'; }
	return array($where, $params);
}

function db_travels($filter_a=NULL, $filter_d1=NULL, $filter_d2=NULL)
{
	global $link, $selected_year;
	$stmnt = $link->stmt_init();
	$where = travel_where($filter_a, $filter_d1);
	$stmnt->prepare('SELECT STUDENT_EXCHANGES.ID, STUDY_YEAR, AGREEMENT_ID, UNIVERSITIES.NAME, COUNTRIES.NAME, AGREEMENTS.FROM_DATE, AGREEMENTS.TO_DATE, STUDENT_EXCHANGES.FROM_DATE, STUDENT_EXCHANGES.TO_DATE, STUDENT_STUDY_PROGRAMS.ID_STUDENT, STUDENTS.FIRSTNAME, STUDENTS.MIDDLENAMES, STUDENTS.LASTNAME, STUDENTS.STUDENT_ID, STUDENTS.BORN, STUDENT_STUDY_PROGRAMS.ID_STUDYPROGRAM, STUDY_PROGRAMS.CODE, STUDY_PROGRAMS.NAME, STUDENT_EXCHANGES.SEMESTER, STUDENT_EXCHANGES.ID_LANGUAGE, LANGUAGES.NAME, STUDENT_EXCHANGES.STUDENTLEVEL, STUDENT_EXCHANGES.REQUIREDLEVEL, STUDENT_EXCHANGES.SOCIALSTIPEND, STUDENT_EXCHANGES.HANDICAPPED, STUDENT_EXCHANGES.NOTES, STUDENT_EXCHANGES.CANCELLED, STUDENT_EXCHANGES.YEAR FROM STUDENT_EXCHANGES JOIN LANGUAGES ON LANGUAGES.ID=STUDENT_EXCHANGES.ID_LANGUAGE JOIN STUDENT_STUDY_PROGRAMS ON STUDENT_STUDY_PROGRAMS.ID=STUDENT_EXCHANGES.ID_STUDENT_STUDY_PROGRAM JOIN STUDENTS ON STUDENTS.ID=STUDENT_STUDY_PROGRAMS.ID_STUDENT JOIN AGREEMENTS ON AGREEMENTS.ID=STUDENT_EXCHANGES.AGREEMENT_ID JOIN UNIVERSITIES ON UNIVERSITIES.ID=AGREEMENTS.ID_UNIVERSITY JOIN COUNTRIES ON COUNTRIES.ID=UNIVERSITIES.ID_COUNTRY LEFT JOIN STUDY_PROGRAMS ON STUDY_PROGRAMS.ID=STUDENT_STUDY_PROGRAMS.ID_STUDYPROGRAM' . $where[0] . ' ORDER BY STUDENTS.LASTNAME, STUDENTS.FIRSTNAME');
	if (strlen($where[1]) === 4) $stmnt->bind_param($where[1], $filter_a, $filter_d2, $filter_d1);
	else if (strlen($where[1]) === 3)
	{
		if ($filter_a != NULL) $stmnt->bind_param($where[1], $filter_a, $filter_d2, $filter_d1);
		else $stmnt->bind_param($where[1], $filter_d2, $filter_d1, $selected_year);
	}
	else if (strlen($where[1]) === 2)
	{
		if ($filter_d1 != NULL) $stmnt->bind_param($where[1], $filter_d2, $filter_d1);
		else $stmnt->bind_param($where[1], $filter_a, $selected_year);
	}
	else if (strlen($where[1]) === 1)
	{	
		if ($filter_a != NULL) $stmnt->bind_param($where[1], $filter_a);
		else $stmnt->bind_param($where[1], $selected_year);
	}
	$stmnt->execute();
	$tv = $stmnt->get_result();
	$tvdata = array();
	while ($rw = $tv->fetch_row())
	{
		if ($rw[15] == NULL) $stp = NULL;
		else $stp = array($rw[15], $rw[16] . ': ' . $rw[17]);
		$stud = $rw[13];
		if (strlen($stud) > 0) $stud = $stud . ', ';
		$datefrom = $rw[7];
		$dateto = $rw[8];
		if ($datefrom === "0000-00-00") $datefrom = "";
		if ($dateto === "0000-00-00") $dateto = "";
		$tvrw = array($rw[0], $rw[1], array($rw[2], $rw[3] . ' (' . $rw[4] . '): ' . $rw[5] . ' - ' . $rw[6]), $datefrom, $dateto, array($rw[9], $rw[12] . ' ' . $rw[10] . ' ' . $rw[11] . ' (' . $stud . $rw[14] . ')'), $stp, $rw[18], array($rw[19], $rw[20]), $rw[21], $rw[22], $rw[23], $rw[24]);
		$fls = db_list_files(7, $rw[0]);
		$tvrw[] = $fls;
		$tvrw[] = db_foreign_courses_for_an_exchange($rw[0]) .
			  '---<br />' .
			  db_fmfi_courses_for_an_exchange($rw[0]);
		$tvrw[] = $rw[25];
		$tvrw[] = $rw[26];
		$tvrw[] = $rw[27];
		$tvdata[] = $tvrw;
	}
	$stmnt->close();
	return $tvdata;
}

function db_languages()
{
        global $link; mysqli_set_charset($link,"utf8");
        $stmnt = $link->stmt_init();
        $stmnt->prepare('SELECT ID, NAME FROM LANGUAGES ORDER BY NAME');
        $stmnt->execute();
        $lan = $stmnt->get_result();
        $langs = array();
        while ($rw = $lan->fetch_row())
                $langs[] = array($rw[0], $rw[1]);
        $stmnt->close();
        return $langs;
}

function db_travels_list()
{
	global $link; mysqli_set_charset($link,"utf8");
	$stmnt = $link->stmt_init();
	$stmnt->prepare('SELECT STUDENT_EXCHANGES.ID, UNIVERSITIES.NAME, COUNTRIES.NAME, STUDENT_EXCHANGES.FROM_DATE, STUDENT_EXCHANGES.TO_DATE, STUDENTS.FIRSTNAME, STUDENTS.MIDDLENAMES, STUDENTS.LASTNAME, STUDY_PROGRAMS.CODE FROM STUDENT_EXCHANGES JOIN STUDENT_STUDY_PROGRAMS ON STUDENT_STUDY_PROGRAMS.ID=STUDENT_EXCHANGES.ID_STUDENT_STUDY_PROGRAM JOIN STUDENTS ON STUDENTS.ID=STUDENT_STUDY_PROGRAMS.ID_STUDENT JOIN AGREEMENTS ON AGREEMENTS.ID=STUDENT_EXCHANGES.AGREEMENT_ID JOIN UNIVERSITIES ON UNIVERSITIES.ID=AGREEMENTS.ID_UNIVERSITY JOIN COUNTRIES ON COUNTRIES.ID=UNIVERSITIES.ID_COUNTRY LEFT JOIN STUDY_PROGRAMS ON STUDY_PROGRAMS.ID=STUDENT_STUDY_PROGRAMS.ID_STUDYPROGRAM ORDER BY STUDENTS.LASTNAME, STUDENTS.FIRSTNAME');
	$stmnt->execute();
	$tv = $stmnt->get_result();
	$tvdata = array();
	while ($rw = $tv->fetch_row())
	{
		$tvrw = array($rw[0], $rw[1] . ' (' . $rw[2] . ') ' . $rw[3] . ' - ' . $rw[4] . ': ' . $rw[5] . ' ' . $rw[6] . ' ' . $rw[7] . ' (' . $rw[8] . ')');
		$tvdata[] = $tvrw;
	}
	$stmnt->close();
	return $tvdata;
}

function db_travel_item_formatted($tvid)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stmnt = $link->stmt_init();
	$stmnt->prepare('SELECT STUDENT_EXCHANGES.ID, UNIVERSITIES.NAME, COUNTRIES.NAME, STUDENT_EXCHANGES.FROM_DATE, STUDENT_EXCHANGES.TO_DATE, STUDENTS.FIRSTNAME, STUDENTS.MIDDLENAMES, STUDENTS.LASTNAME, STUDY_PROGRAMS.CODE FROM STUDENT_EXCHANGES JOIN STUDENT_STUDY_PROGRAMS ON STUDENT_STUDY_PROGRAMS.ID=STUDENT_EXCHANGES.ID_STUDENT_STUDY_PROGRAM JOIN STUDENTS ON STUDENTS.ID=STUDENT_STUDY_PROGRAMS.ID_STUDENT JOIN AGREEMENTS ON AGREEMENTS.ID=STUDENT_EXCHANGES.AGREEMENT_ID JOIN UNIVERSITIES ON UNIVERSITIES.ID=AGREEMENTS.ID_UNIVERSITY JOIN COUNTRIES ON COUNTRIES.ID=UNIVERSITIES.ID_COUNTRY LEFT JOIN STUDY_PROGRAMS ON STUDY_PROGRAMS.ID=STUDENT_STUDY_PROGRAMS.ID_STUDYPROGRAM WHERE STUDENT_EXCHANGES.ID=?');
	$stmnt->bind_param('i', $tvid);
	$stmnt->execute();
	$tv = $stmnt->get_result();
	$rw = $tv->fetch_row();
	$tvrw = $rw[1] . ' (' . $rw[2] . ') ' . $rw[3] . ' - ' . $rw[4] . ': ' . $rw[5] . ' ' . $rw[6] . ' ' . $rw[7] . ' (' . $rw[8] . ')';
	$stmnt->close();
	return $tvrw;
}

function db_retrieve_travel_byID($tvid, $as_array=FALSE)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stmnt = $link->stmt_init();
	$stmnt->prepare('SELECT STUDY_YEAR, AGREEMENT_ID, FROM_DATE, TO_DATE, SEMESTER, STUDENT_STUDY_PROGRAMS.ID_STUDENT, STUDENT_STUDY_PROGRAMS.ID_STUDYPROGRAM, ID_LANGUAGE, STUDENTLEVEL, REQUIREDLEVEL, SOCIALSTIPEND, HANDICAPPED, NOTES, CANCELLED, STUDENT_EXCHANGES.YEAR FROM STUDENT_EXCHANGES JOIN STUDENT_STUDY_PROGRAMS ON STUDENT_STUDY_PROGRAMS.ID=STUDENT_EXCHANGES.ID_STUDENT_STUDY_PROGRAM WHERE STUDENT_EXCHANGES.ID=?');
	$stmnt->bind_param('i', $tvid);
	$stmnt->execute();
	$stmnt->bind_result($year, $agid, $fromdate, $todate, $semester, $stid, $spid, $lang, $has, $expect, $sstip, $handic, $notes, $cancelled, $acyear); 
	$stmnt->fetch();
	$rw = array($tvid, $year, $agid, $fromdate, $todate, $stid, $spid, $semester, $lang, $has, $expect, $sstip, $handic);
	$stmnt->close();
	$rw[] = db_list_files(7, $tvid); 
	if ($as_array === FALSE)
		$rw[] = db_foreign_courses_for_an_exchange($tvid, $as_array) . '---<br />' . 
			db_fmfi_courses_for_an_exchange($tvid, $as_array);
	else $rw[] = array(db_foreign_courses_for_an_exchange($tvid, $as_array), 
			 db_fmfi_courses_for_an_exchange($tvid, $as_array));
	$rw[] = $notes;
	$rw[] = $cancelled;
	$rw[] = $acyear;
	return $rw;
}

function db_get_first_travel()
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$stm->prepare('SELECT ID FROM STUDENT_EXCHANGES LIMIT 1');
	$stm->execute();
	$stm->bind_result($tvid);
	$stm->fetch();
	$stm->close();
	return $tvid; 
}

/*----------------------FOREIGN TRAVEL COURSES------------------------*/

function db_foreign_courses_for_an_exchange($idtv, $as_array=FALSE)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stmnt = $link->stmt_init();
	$stmnt->prepare('SELECT ID, CODE, NAME, CREDITS, GRADE, COURSE_TYPE FROM STUDENT_FOREIGN_SUBJECTS WHERE STUDENT_FOREIGN_SUBJECTS.ID_EXCHANGE=?');
	$stmnt->bind_param('i', $idtv);
	$stmnt->execute();
	$cs = $stmnt->get_result();
	if ($as_array) $csdata = array(); else $csdata = "";
	if ($cs != NULL)
		while ($rw = $cs->fetch_row())
			if ($as_array)
				$csdata[] = array($rw[0], $rw[1], $rw[2], $rw[3], $rw[4], array($rw[5], course_type($rw[5])));
			else { if (strlen($rw[4]) > 0) $grd = ', grade: ' . $rw[4] . ' '; else $grd = ' ';
                               $csdata = $csdata . $rw[2] . ' (' . $rw[1] . '), ' . $rw[3] . ' credits' . $grd . course_type($rw[5]) . "<br />\n"; 
			}
	$stmnt->close();
	return $csdata;
}

function course_type($code)
{
	switch ($code):
		case 'c': return 'compulsory';
		case 'e': return 'elective';
		case 'o': return 'optional';
	endswitch;
}

function course_type_sk($code)
{
	switch ($code):
		case 'c': return 'povinný';
		case 'e': return 'povinne voliteľný';
		case 'o': return 'výberový';
	endswitch;
}

function db_save_travel_course($id, $tvid, $code, $name, $credits, $grade, $ctype)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$op = 0;
	if ($id == '-') $op = 1;
	if ($op === 1)
	{
		$stm->prepare('INSERT INTO STUDENT_FOREIGN_SUBJECTS (ID_EXCHANGE, CODE, NAME, CREDITS, GRADE, COURSE_TYPE) VALUES (?, ?, ?, ?, ?, ?)');
        	$stm->bind_param('ississ', $tvid, $code, $name, $credits, $grade, $ctype);
	}
	else 
	{
		$stm->prepare('UPDATE STUDENT_FOREIGN_SUBJECTS SET ID_EXCHANGE=?, CODE=?, NAME=?, CREDITS=?, GRADE=?, COURSE_TYPE=? WHERE ID=?');
		$stm->bind_param('ississi', $tvid, $code, $name, $credits, $grade, $ctype, $id);
	}
	$stm->execute();
	if ($op == 1)
	{
		$inserted_id = $stm->insert_id;
		db_append_to_log('STUDENT_FOREIGN_SUBJECTS', $inserted_id, 'add', 'record added', "$tvid, $code, $name, $credits, $grade, $ctype");
	}
	else 
	{
		$inserted_id = $id;
                db_append_to_log('STUDENT_FOREIGN_SUBJECTS', $id, 'edit', 'record updated', "$tvid, $code, $name, $credits, $grade, $ctype");
	}
	$stm->close();
	return $inserted_id;
}

function db_remove_travel_courses($ids)
{
	global $link; mysqli_set_charset($link,"utf8");
	$idar = explode(', ', $ids);
	$stm = $link->stmt_init();
	$stm->prepare('DELETE FROM STUDENT_FOREIGN_SUBJECTS WHERE ID=?');
	foreach($idar as $id)
	{
		$stm->bind_param('i', $id);
		$stm->execute();
		db_append_to_log('STUDENT_FOREIGN_SUBJECTS', $id, 'remove', 'record removed', "--");
	}
	$stm->close();
}

/*----------------------TRAVEL FMFI COURSES------------------------*/
function db_remove_fmfi_travel_courses($ids)
{
	global $link; mysqli_set_charset($link,"utf8");
	$idar = explode(', ', $ids);
	$stm = $link->stmt_init();
	$stm->prepare('DELETE FROM STUDENT_FMFI_SUBJECTS WHERE ID=?');
	foreach($idar as $id)
	{
		$stm->bind_param('i', $id);
		$stm->execute();
		db_append_to_log('STUDENT_FMFI_SUBJECTS', $id, 'remove', 'record removed', "--");
	}
	$stm->close();
}

function db_fmfi_courses_for_an_exchange($idtv, $as_array=FALSE)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stmnt = $link->stmt_init();
	$stmnt->prepare('SELECT STUDENT_FMFI_SUBJECTS.ID, FMFI_COURSES.FMFI_COURSE_CODE, FMFI_COURSES.FMFI_COURSE_NAME, FMFI_COURSES.CREDITS, FMFI_COURSES.ID, STUDENT_FMFI_SUBJECTS.GRADE FROM STUDENT_FMFI_SUBJECTS JOIN FMFI_COURSES ON FMFI_COURSES.ID=STUDENT_FMFI_SUBJECTS.ID_FMFI_COURSE WHERE STUDENT_FMFI_SUBJECTS.ID_EXCHANGE=? ORDER BY FMFI_COURSES.FMFI_COURSE_CODE');
	$stmnt->bind_param('i', $idtv);
	$stmnt->execute();
	$cs = $stmnt->get_result();
	if ($as_array) $csdata = array(); else $csdata = "";
	if ($cs != NULL)
		while ($rw = $cs->fetch_row())
		{ 
			if ($as_array)
				$csdata[] = array($rw[0], array($rw[4], $rw[1], $rw[2], $rw[3]), $rw[5]);
			else $csdata = $csdata . $rw[1] . ': ' . $rw[2] . ', ' . $rw[3] . ' credits, grade ' . $rw[5] . "<br />\n"; 
		}
	$stmnt->close();
	return $csdata;
}

function db_save_fmfi_travel_course($id, $idtv, $idfmfi, $grade)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$op = 0;
	if ($id == '-') $op = 1;
	if ($op === 1)
	{
		$stm->prepare('INSERT INTO STUDENT_FMFI_SUBJECTS (ID_EXCHANGE, ID_FMFI_COURSE, GRADE) VALUES (?, ?, ?)');
        	$stm->bind_param('iis', $idtv, $idfmfi, $grade);
	}
	else 
	{
		$stm->prepare('UPDATE STUDENT_FMFI_SUBJECTS SET ID_EXCHANGE=?, ID_FMFI_COURSE=?, GRADE=? WHERE ID=?');
		$stm->bind_param('iisi', $idtv, $idfmfi, $grade, $id);
	}
	$stm->execute();
	if ($op == 1)
	{
		$inserted_id = $stm->insert_id;
		db_append_to_log('STUDENT_FMFI_SUBJECTS', $inserted_id, 'add', 'record added', "$idtv, $idfmfi, $grade");
	}
	else 
	{
		$inserted_id = $id;
                db_append_to_log('STUDENT_FMFI_SUBJECTS', $id, 'edit', 'record updated', "$idtv, $idfmfi, $grade");
	}
	$stm->close();
	return $inserted_id;
}

/*---------------------- LOGS -------------------------*/
function db_append_to_log($tabName, $idrec, $opname, $desc, $newval)
{
	global $userid, $link; 

	$idtab = idTabForName($tabName);
	$op = operationIDForName($opname);
	$ip = getRealUserIp();
	$rectm = date("Y-m-d H:i:s");
	$stm = $link->stmt_init();
	$stm->prepare('INSERT INTO LOG (RECORDED, IP, ID_USER, ID_TAB, ID_RECORD, OPERATION, DESCRIPTION, NEWVAL) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
	$stm->bind_param('ssiissss', $rectm, $ip, $userid, $idtab, $idrec, $op, $desc, $newval);
	$stm->execute();
	$stm->close();
}

function db_retrieve_logs()
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$stm->prepare('SELECT RECORDED, IP, USERS.NAME, ID_USER, ID_TAB, ID_RECORD, OPERATION, DESCRIPTION, NEWVAL FROM LOG, USERS WHERE USERS.ID=ID_USER ORDER BY RECORDED DESC');
	$stm->execute();
	$logdata = $stm->get_result();
	$rv = array();
	while ($rw = $logdata->fetch_row())
		$rv[] = array($rw[0], $rw[1], $rw[2] . '(' . $rw[3] . ')', tabNameForID($rw[4]), $rw[5], 
                              operationNameForID($rw[6]), $rw[7], $rw[8]);
	$stm->close();
	return $rv;
}

function tabNameForID($tabid)
{
	return array('COUNTRIES', 'UNIVERSITIES', 'AGREEMENTS', 'STUDENTS', 'STUDY_PROGRAMS', 'STUDENT_STUDY_PROGRAMS', 
                     'STUDENT_EXCHANGES', 'FMFI_COURSES', 'STUDENT_FOREIGN_SUBJECTS', 'FILES', 'USERS', 'STUDENT_FMFI_SUBJECTS')[$tabid - 1];
}

function idTabForName($tabname)
{
	return array('COUNTRIES' => 1, 'UNIVERSITIES' => 2, 'AGREEMENTS' => 3, 'STUDENTS' => 4, 'STUDY_PROGRAMS' => 5, 
                     'STUDENT_STUDY_PROGRAMS' => 6, 'STUDENT_EXCHANGES' => 7, 'FMFI_COURSES' => 8, 'STUDENT_SUBJECTS' => 9, 
                     'FILES' => 10, 'USERS' => 11, 'STUDENT_FMFI_SUBJECTS' => 12)[$tabname];
}

function operationNameForID($op)
{
	return array('add', 'remove', 'edit')[$op - 1];
}

function operationIDForName($opname)
{
	return array('add' => 1, 'remove' => 2, 'edit' => 3)[$opname];
}

function getRealUserIp()
{
    switch(true){
      case (!empty($_SERVER['HTTP_X_REAL_IP'])) : return $_SERVER['HTTP_X_REAL_IP'];
      case (!empty($_SERVER['HTTP_CLIENT_IP'])) : return $_SERVER['HTTP_CLIENT_IP'];
      case (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) : return $_SERVER['HTTP_X_FORWARDED_FOR'];
      default : return $_SERVER['REMOTE_ADDR'];
    }
}

/*----------------- FILES ----------------------- */
function db_list_files($tab, $record)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm2 = $link->stmt_init();
	logmsg("tab: " . $tab);
	$stm2->prepare('SELECT ID, ORIGINAL_FILE_NAME, DESCRIPTION FROM FILES WHERE (ID_TAB=?) AND (ID_RECORD=?)');
	$stm2->bind_param('ii', $tab, $record);
	$stm2->execute();
	$files = $stm2->get_result();
	$fls = array();
	while ($rw2 = $files->fetch_row())
		$fls[] = array($rw2[0], $rw2[1], $rw2[2]);
	$stm2->close();
	return $fls;
}

function db_retrieve_file_details($fileid)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$stm->prepare('SELECT ID_TAB, ID_RECORD, ORIGINAL_FILE_NAME, DESCRIPTION FROM FILES WHERE ID=?');
	$stm->bind_param('i', $fileid);
	$stm->execute();
	$stm->bind_result($idtab, $idrec, $filename, $desc);
	$stm->fetch();
	$details = array($fileid, $idtab, $idrec, $filename, $desc);
	$stm->close();
	return $details;
}

function db_retrieve_file_ids($idtab, $idrecs)
{
	global $link; mysqli_set_charset($link,"utf8");
        $stm = $link->stmt_init();
        $stm->prepare('SELECT ID FROM FILES WHERE ID_TAB=? AND ID_RECORD=?');
        $fids = array();
        foreach($idrecs as $id)
        {
                $stm->bind_param('ii', $idtab, $id);
                $stm->execute();
                $stm->bind_result($fileid);
                $stm->fetch();
		if ($fileid != NULL) $fids[] = $fileid;
        }
        $stm->close();
	return $fids;
}

function db_delete_files($ids)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$stm->prepare('DELETE FROM FILES WHERE ID=?');
	foreach($ids as $id)
	{
		logmsg("del file " . $id);
		$stm->bind_param('i', $id);
		$stm->execute();
        	db_append_to_log('FILES', $id, 'remove', 'file removed', $id);
	}
	$stm->close();
	if (count($ids) > 0) recycle_files($ids);
}

function db_insert_uploaded_file($tab, $idrecord, $filename, $description)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$stm->prepare('INSERT INTO FILES (ID_TAB, ID_RECORD, ORIGINAL_FILE_NAME, DESCRIPTION) VALUES (?, ?, ?, ?)');
	$stm->bind_param('iiss', $tab, $idrecord, $filename, $description);
	$stm->execute();
	$fileid = $stm->insert_id;
	$stm->close();
	return $fileid;
}

function db_file_for_download($id)
{
	global $link; mysqli_set_charset($link,"utf8");
	$stm = $link->stmt_init();
	$stm->prepare('SELECT ORIGINAL_FILE_NAME, PUBLIC FROM FILES WHERE ID=?');
	$stm->bind_param('i', $id);
	$stm->execute();
	$stm->bind_result($filename, $ispublic);
	$stm->fetch();
	$stm->close();
	return array($filename, $ispublic);
}

?>
