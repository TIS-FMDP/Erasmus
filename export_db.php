<?PHP

function db_export_students()
{
        global $link;
	$stm3 = $link->stmt_init();
	$stm3->prepare('SELECT COUNT(STUDENT_STUDY_PROGRAMS.ID_STUDYPROGRAM) AS CNT1 FROM STUDENT_STUDY_PROGRAMS GROUP BY STUDENT_STUDY_PROGRAMS.ID_STUDENT ORDER BY CNT1 DESC LIMIT 1');
	$stm3->execute();
	$stm3->bind_result($max);
	$stm3->fetch();
	$stm3->close();
        $stmnt = $link->stmt_init();
	$stm2 = $link->stmt_init();
        $stmnt->prepare('SELECT STUDENTS.ID, FIRSTNAME, MIDDLENAMES, LASTNAME, BORN, STUDENT_ID FROM STUDENTS ORDER BY LASTNAME,FIRSTNAME');
        $stm2->prepare('SELECT STUDY_PROGRAMS.CODE, STUDY_PROGRAMS.NAME FROM STUDY_PROGRAMS JOIN STUDENT_STUDY_PROGRAMS ON STUDY_PROGRAMS.ID=STUDENT_STUDY_PROGRAMS.ID_STUDYPROGRAM WHERE STUDENT_STUDY_PROGRAMS.ID_STUDENT=?');
        $stmnt->execute();
        $st = $stmnt->get_result();
	print '"ID","FIRSTNAME","MIDDLENAMES","LASTNAME","BORN","STUDENT_ID"';
	for ($i = 1; $i <= $max; $i++)
		print ',"STUDPROGCODE' . $i . '","STUDPROGNAME' . $i . '"';
	print_nl();
        while ($rw = $st->fetch_row())
        {
		print $rw[0] . ',"' . ddq($rw[1]) . '","' . ddq($rw[2]) . '","' . ddq($rw[3]) . '","' . ddq($rw[4]) . '","' . ddq($rw[5]) . '"';
		$stm2->bind_param('i', $rw[0]);
		$stm2->execute();
		$sp = $stm2->get_result();
		$cnt = 0;
		while ($rw2 = $sp->fetch_row())
		{
			print ',"' . ddq($rw2[0]) . '","' . ddq($rw2[1]) . '"';	
			$cnt++;
		}
		while ($cnt++ < $max) print ',"",""';
		print_nl();
        }
	$stm2->close();
        $stmnt->close();
}

function db_export_agreements()
{
	global $link;
	$maxfiles = db_count_files(idTabForName('AGREEMENTS'));
	$stm = $link->stmt_init();
	$stm->prepare('SELECT AGREEMENTS.ID, UNIVERSITIES.NAME, COUNTRIES.NAME, AGREEMENTS.FROM_DATE, AGREEMENTS.TO_DATE, SUBJECT_AREAS.CODE, SUBJECT_AREAS.NAME FROM AGREEMENTS JOIN UNIVERSITIES ON UNIVERSITIES.ID=AGREEMENTS.ID_UNIVERSITY JOIN COUNTRIES ON COUNTRIES.ID=UNIVERSITIES.ID_COUNTRY JOIN SUBJECT_AREAS ON SUBJECT_AREAS.ID=AGREEMENTS.SUBJECT_AREA_ID');
	$stm->execute();
	$st = $stm->get_result();
	print '"ID","UNIVERSITY","COUNTRY","VALIDFROM","VALIDTO","AREACODE","AREANAME"';
	for ($i = 1; $i <= $maxfiles; $i++)
		print ',"FILENAME' . $i . '","ORIGINAL_FILENAME' . $i . '","DESCRIPTION' . $i . '"';
	print_nl();
	while ($rw = $st->fetch_row())
	{
		print $rw[0] . ',"' . ddq($rw[1]) . '","' . ddq($rw[2]) . '","' . ddq($rw[3]) . '","' . ddq($rw[4]) . '","' . ddq($rw[5]) . '","' . ddq($rw[6]) . '"';
		print_files(idTabForName('AGREEMENTS'), $rw[0], $maxfiles);
		print_nl();
	}
	$stm->close();
}

function db_export_rector()
{
	global $link;
	$stm = $link->stmt_init();
	$stm->prepare('SELECT STUDENTS.LASTNAME, STUDENTS.FIRSTNAME, STUDENTS.BORN, STUDENTS.GENDER, SCOUNTRY.NAME, UCOUNTRY.NAME, UNIVERSITIES.CODE, STUDENT_EXCHANGES.SEMESTER, LANGUAGES.NAME, STUDENT_EXCHANGES.STUDENTLEVEL, STUDENT_EXCHANGES.REQUIREDLEVEL, STUDENT_EXCHANGES.STUDY_YEAR, STUDENT_EXCHANGES.SOCIALSTIPEND, STUDENT_EXCHANGES.HANDICAPPED, STUDENT_EXCHANGES.NOTES, STUDENTS.EMAIL, STUDENT_EXCHANGES.YEAR FROM STUDENT_EXCHANGES JOIN STUDENT_STUDY_PROGRAMS ON STUDENT_STUDY_PROGRAMS.ID=STUDENT_EXCHANGES.ID_STUDENT_STUDY_PROGRAM JOIN STUDENTS ON STUDENTS.ID=STUDENT_STUDY_PROGRAMS.ID_STUDENT JOIN AGREEMENTS ON AGREEMENTS.ID=STUDENT_EXCHANGES.AGREEMENT_ID JOIN UNIVERSITIES ON UNIVERSITIES.ID=AGREEMENTS.ID_UNIVERSITY JOIN COUNTRIES UCOUNTRY ON UCOUNTRY.ID=UNIVERSITIES.ID_COUNTRY JOIN COUNTRIES SCOUNTRY ON SCOUNTRY.ID=STUDENTS.CITIZENSHIP JOIN LANGUAGES ON LANGUAGES.ID=STUDENT_EXCHANGES.ID_LANGUAGE ORDER BY STUDENT_EXCHANGES.ID');
	$stm->execute();
	$st = $stm->get_result();
	print '"poradové číslo","fakulta","priezvisko","meno","dátum narodenia","pohlavie","štátna príslušnosť","krajina","univerzita","semester","hlavný vyučovací jazyk na zahr.uni.","momentálna úroveň hl.vyuč.jazyka","požadovaná úroveň hl.vyuč.jazyka","úroveň štúdia","Sociálne štipendium, ZŤP","poznámky","email","akademický rok"';
	print_nl();
	$por=1;
	while ($rw = $st->fetch_row())
	{
		print $por . ',"FMFI","' . ddq($rw[0]) . '","' . ddq($rw[1]) . '","' . ddq($rw[2]) . '","' . gender_sk($rw[3]) . '","' . ddq($rw[4]) . '","' . ddq($rw[5]) . '","' . ddq($rw[6]) . '","' . semester_sk($rw[7]) . '","' . ddq($rw[8]) . '","' . ddq($rw[9]) . '","' . ddq($rw[10]) . '",' . $rw[11] . ',"' . stipend_handicap_sk($rw[12], $rw[13]) . '","' . ddq($rw[14]) . '","' . ddq($rw[15]) . '","' . ddq($rw[16]) . '"';
		$por++;
		print_nl();
	}
	$stm->close();
}

function gender_sk($g)
{
	if ($g=='M') return "mužské"; 
	return "ženské";
}

function semester_sk($s)
{
	if ($s=='W') return 'ZS';
	return 'LS';
}

function stipend_handicap_sk($stip, $handicap)
{
	$rv = '';
	if ($stip == 1) $rv = 'soc.stip.';
	if ($handicap == 1) 
	{
		if (strlen($rw) > 0) $rv = $rv . ', ZŤP'; 
		else $rv = 'ZŤP';
	}
	return $rv;
}

function db_export_travels()
{
	global $link;
	$maxfiles = db_count_files(idTabForName('STUDENT_EXCHANGES'));
	$maxforeign = db_count_foreign_courses();
	$maxfmfi = db_count_fmfi_courses();
	$stm = $link->stmt_init();
	$stm->prepare('SELECT STUDENT_EXCHANGES.ID, STUDENT_EXCHANGES.FROM_DATE, STUDENT_EXCHANGES.TO_DATE, STUDENT_STUDY_PROGRAMS.ID_STUDENT, STUDENT_STUDY_PROGRAMS.ID_STUDYPROGRAM, STUDENTS.FIRSTNAME, STUDENTS.MIDDLENAMES, STUDENTS.LASTNAME, STUDENTS.STUDENT_ID, STUDENT_EXCHANGES.STUDY_YEAR, STUDY_PROGRAMS.CODE, STUDENT_EXCHANGES.AGREEMENT_ID, UNIVERSITIES.NAME, COUNTRIES.NAME FROM STUDENT_EXCHANGES JOIN STUDENT_STUDY_PROGRAMS ON STUDENT_STUDY_PROGRAMS.ID=STUDENT_EXCHANGES.ID_STUDENT_STUDY_PROGRAM JOIN STUDENTS ON STUDENTS.ID=STUDENT_STUDY_PROGRAMS.ID_STUDENT JOIN STUDY_PROGRAMS ON STUDY_PROGRAMS.ID=STUDENT_STUDY_PROGRAMS.ID_STUDYPROGRAM JOIN AGREEMENTS ON AGREEMENTS.ID=STUDENT_EXCHANGES.AGREEMENT_ID JOIN UNIVERSITIES ON UNIVERSITIES.ID=AGREEMENTS.ID_UNIVERSITY JOIN COUNTRIES ON COUNTRIES.ID=UNIVERSITIES.ID_COUNTRY');
	$stm->execute();
	$st = $stm->get_result();
	print '"ID","TRAVEL_FROM_DATE","TRAVEL_TO_DATE","ID_STUDENT","ID_STUDYPROGRAM","FIRSTNAME","MIDDLENAMES","LASTNAME","STUDENT_ID","STUDY_YEAR","STUDPROG_CODE","AGREEMENT_ID","UNIVERSITY","COUNTRY"';
	for ($i = 1; $i <= $maxfiles; $i++)
		print ',"FILENAME' . $i . '","ORIGINAL_FILENAME' . $i . '","DESCRIPTION' . $i . '"';
	for ($i = 1; $i <= $maxforeign; $i++)
		print ',"FOREIGN_COURSE_NAME' . $i . '","FOREIGN_COURSE_CODE' . $i . '","CREDITS' . $i . '","GRADE' . $i . '","COURSE_TYPE' . $i . '"';
	for ($i = 1; $i <= $maxfmfi; $i++)
		print ',"FMFI_COURSE_NAME' . $i . '","FMFI_COURSE_CODE' . $i . '","CREDITS' . $i . '"';
	print_nl();
	while ($rw = $st->fetch_row())
	{
		print $rw[0] . ',"' . $rw[1] . '","' . $rw[2] . '",' . $rw[3] . ',' . $rw[4] . ',"' . ddq($rw[5]) . '","' . ddq($rw[6]) . '","' . ddq($rw[7]) . '","' . ddq($rw[8]) . '",' . ddq($rw[9]) . ',"' . ddq($rw[10]) . '",' . $rw[11] . ',"' . ddq($rw[12]) . '","' . ddq($rw[13]) . '"';
		print_files(idTabForName('STUDENT_EXCHANGES'), $rw[0], $maxfiles);
		print_foreign_courses($rw[0], $maxforeign);
		print_fmfi_courses($rw[0], $maxfmfi);
		print_nl();
	}
	$stm->close();
}

function print_files($idtab, $idrec, $max)
{
	global $link;
	$stm = $link->stmt_init();
	$stm->prepare('SELECT ID, ORIGINAL_FILE_NAME, DESCRIPTION FROM FILES WHERE ID_TAB=? AND ID_RECORD=?');
	$stm->bind_param('ii', $idtab, $idrec);
	$stm->execute();
	$st = $stm->get_result();
	$cnt = 0;
	while ($rw = $st->fetch_row())
	{
		print ',' . $rw[0] . ',"' . ddq($rw[1]) . '","' . ddq($rw[2]) . '"';
		$cnt++;
	}
	while ($cnt++ < $max)
		print ',"","",""';	
	$stm->close();
}

function print_foreign_courses($idex, $max)
{
	global $link;
	$stm = $link->stmt_init();
	$stm->prepare('SELECT NAME, CODE, CREDITS, GRADE, COURSE_TYPE FROM STUDENT_FOREIGN_SUBJECTS WHERE ID_EXCHANGE=?');
	$stm->bind_param('i', $idex);
	$stm->execute();
	$st = $stm->get_result();
	$cnt = 0;
	while ($rw = $st->fetch_row())
	{
		print ',"' . ddq($rw[0]) . '","' . ddq($rw[1]) . '",' . $rw[2] . ',"' . ddq($rw[3]) . '","' . course_type($rw[4]) . '"';
		$cnt++;
	}
	while ($cnt++ < $max)
		print ',"","","","",""';	
	$stm->close();
}

function print_fmfi_courses($idex, $max)
{
	global $link;
	$stm = $link->stmt_init();
	$stm->prepare('SELECT FMFI_COURSES.FMFI_COURSE_NAME, FMFI_COURSES.FMFI_COURSE_CODE, FMFI_COURSES.CREDITS FROM STUDENT_FMFI_SUBJECTS JOIN FMFI_COURSES ON FMFI_COURSES.ID=STUDENT_FMFI_SUBJECTS.ID_FMFI_COURSE WHERE ID_EXCHANGE=?');
	$stm->bind_param('i', $idex);
	$stm->execute();
	$st = $stm->get_result();
	$cnt = 0;
	while ($rw = $st->fetch_row())
	{
		print ',"' . ddq($rw[0]) . '","' . ddq($rw[1]) . '",' . $rw[2];
		$cnt++;
	}
	while ($cnt++ < $max)
		print ',"","",""';	
	$stm->close();
}

function db_count_files($tab)
{
        global $link;
	$stm = $link->stmt_init();
	$stm->prepare('SELECT COUNT(ID) AS CNT1 FROM FILES WHERE FILES.ID_TAB=? GROUP BY FILES.ID_RECORD ORDER BY CNT1 DESC LIMIT 1');
	$stm->bind_param('i', $tab);
	$stm->execute();
	$stm->bind_result($max);
	$stm->fetch();
	$stm->close();
	return $max;
}

function db_count_foreign_courses()
{
        global $link;
	$stm = $link->stmt_init();
	$stm->prepare('SELECT COUNT(STUDENT_FOREIGN_SUBJECTS.ID) AS CNT1 FROM STUDENT_FOREIGN_SUBJECTS GROUP BY ID_EXCHANGE ORDER BY CNT1 DESC LIMIT 1');
	$stm->execute();
	$stm->bind_result($max);
	$stm->fetch();
	$stm->close();
	return $max;
}

function db_count_fmfi_courses()
{
        global $link;
	$stm = $link->stmt_init();
	$stm->prepare('SELECT COUNT(STUDENT_FMFI_SUBJECTS.ID) AS CNT1 FROM STUDENT_FMFI_SUBJECTS GROUP BY ID_EXCHANGE ORDER BY CNT1 DESC LIMIT 1');
	$stm->execute();
	$stm->bind_result($max);
	$stm->fetch();
	$stm->close();
	return $max;
}

function db_export_files()
{
	global $link, $ERA;
	$stm = $link->stmt_init();
	mkdir($ERA . 'files/all');
	chmod($ERA . 'files/all', 0700);
	$doc=fopen($ERA . 'files/all/list.txt', 'w+');
	fwrite($doc, "id\toriginal_file_name\tdescription\r\n");

	$stm->prepare('SELECT ID, ORIGINAL_FILE_NAME, DESCRIPTION FROM FILES');
	$stm->execute();
	$st = $stm->get_result();

	while ($rw = $st->fetch_row())
	{
		$fdir = $ERA . 'files/all/' . $rw[0];
		mkdir($fdir);
		chmod($fdir, 0700);
		copy($ERA . 'files/' . $rw[0], $fdir . '/' . $rw[1]);
		fwrite($doc, "$rw[0]\t$rw[1]\t$rw[2]\r\n");
	}
	$stm->close();
	fclose($doc);
	$cdir=getcwd();
	chdir($ERA . 'files');
	system('/usr/bin/zip -r -q all.zip all');
	readfile($ERA . 'files/all.zip');
	system('rm -rf ' . $ERA . 'files/all*');
	chdir($cdir);
}

function db_export_log()
{
	global $link;
	$stm = $link->stmt_init();
	$stm->prepare('SELECT LOG.ID, RECORDED, IP, ID_USER, USERS.NAME, USERS.ROLE, ID_TAB, ID_RECORD, OPERATION, DESCRIPTION, NEWVAL FROM LOG JOIN USERS ON USERS.ID=LOG.ID_USER ORDER BY RECORDED DESC');
	$stm->execute();
	$st = $stm->get_result();
	print '"ID","RECORDED","IP","ID_USER","USERNAME","USERROLE","ID_TAB","ID_RECORD","OPERATION","DESCRIPTION","NEWVALUE"';
	print_nl();
	while ($rw = $st->fetch_row())
	{
		print $rw[0] . ',"' . $rw[1] . '","' . ddq($rw[2]) . '",' . $rw[3] . ',"' . ddq($rw[4]) . '","' . ddq($rw[5]) . '",' . $rw[6] . ',' . $rw[7] . ',"' . operationNameForID($rw[8]) . '","' . ddq($rw[9]) . '","' . ddq($rw[10]) . '"';
		print_nl();
	}
	$stm->close();
}

function ddq($s)
{
	return str_replace('"', '""', $s);
}

function print_nl()
{
	print "\r\n";
}

?>
