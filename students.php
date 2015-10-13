<?PHP
function process_students()
{
	print '<b>Students</b><br /><br />';
	if (isset($_GET['act']))
        {
                if ($_POST['Edit'] === 'edit')
                {
                        $st = db_students();
                        edit_student($st);
                }
                else if ($_POST['Add'] === 'add')
                        add_student();
                else if ($_POST['Save'] === 'save')
                        save_student();
                else if ($_POST['Remove'] === 'remove')
                {
                        $st = db_students(); 
                        remove_record($st);
                }
		else if ($_POST['AddOption'] === 'add')
			add_study_program_to_student_and_edit_student();
                else if ($_POST['Remove'] === 'yes')
                        yes_remove();
                else if ($_POST['Remove'] === 'no')
			no_remove();
                else foreach($_POST as $var => $val)
                {
                        if (strncmp($var, 'deletelist', 10) === 0)
                        {
                                delete_sp_and_edit_student($var);
                                break;
                        }
                }
        }
	$stdata = db_students();
	$stfd = format_student_data($stdata);
	show_table(array('ID', 'First name', 'Middle names', 'Last name', 'Born', 'StudentID', 'Gender', 'Citizenship', 'E-mail', 'Study programs', 'Year 1st participated'), $stfd);
}

function yes_remove()
{
	if (isset($_POST['stid']))
		yes_delete_sp_and_edit_student();
	else
		yes_remove_students();
}

function no_remove()
{
	if (isset($_POST['stid']))
		no_delete_sp_and_edit_student();
}


function yes_delete_sp_and_edit_student()
{
        $spid = $_POST['ids'];
	$stid = $_POST['stid']; 
        db_remove_student_program_of_student($spid, $stid);
        no_delete_sp_and_edit_student();
}

function no_delete_sp_and_edit_student()
{
        $id = $_POST['stid'];
        $st = db_retrieve_student_byID($id);
        addedit_student($st);
}

function delete_sp_and_edit_student($var)
{
        $f = explode('_', $var);
        $spid = $_POST['listid_' . $f[1] . '_' . $f[2]];
	$stid = $_POST['0'];
        save_student();
        $details = db_retrieve_study_prog_and_student_details($spid, $stid);
        show_remove_form(array('Study program "' . $details[0] . '" from student "' . $details[1] . '"'), $spid, array('stid' => $stid));
        $st = db_retrieve_student_byID($stid);
        addedit_student($st);
}

function format_student_data($stdata)
{
	$fd = array();
	foreach($stdata as $st)
	{
		$fsp = "";
		$cnt = 0;
		foreach($st[9] as $sp)
		{
			if ($cnt > 0) $fsp = $fsp . ', ';
			$fsp = $fsp . $sp[1];
			$cnt++;
		}
		$st[6] = ($st[6][0] === 'M')?'male':'female';
		$st[7] = $st[7][1];
		$st[9] = $fsp;	
		$fd[] = $st;
	}
	return $fd;
}

function add_study_program_to_student_and_edit_student()
{
	$id=$_POST['0'];
	$selitem=$_POST['9'];
	db_add_study_program_to_student($id, $selitem);
	save_student();
	$st = db_retrieve_student_byID($id);
	addedit_student($st);
}

function edit_student($sts)
{
	$st = retrieve_first_checked_record($sts);
	if ($st === NULL) infomsg("Please tick the record you want to edit");
	else addedit_student($st);
}

function addedit_student($st)
{
	$column_labels = array('ID', 'First name', 'Middle names', 'Last name', 'Born', 'Student ID', 'Gender', 'Citizenship', 'E-mail', 'Study programs', 'Year 1st participated');
	$column_types = array('RDONLY', 'text', 'text', 'text', 'date', 'text', '*', '*', 'text', '+', '*');
	$studprogs = db_study_programs(TRUE);
	$cntries = db_countries();
	$years = selection_of_years();
	show_edit_form($column_labels, $st, $column_types, array(array(array('M', 'male'), array('F', 'female')), $cntries, $studprogs, $years));
}

function add_student()
{
	$cntry = db_get_first_country();
	$curyear = date('Y');
        $newid = db_save_student('-', 'name', '', 'surname', '2000-12-24', '123', 'M', $cntry, 'email@email.com', array(), $curyear . '/' . ($curyear + 1)); 
        $st = db_retrieve_student_byID($newid);
	addedit_student($st);
}

function save_student()
{
	$id = $_POST['0'];
	$firstname = htmlspecialchars($_POST['1']);
	$middlename = htmlspecialchars($_POST['2']);
	$lastname = htmlspecialchars($_POST['3']);
	$born = htmlspecialchars($_POST['4']);
	$studentID = htmlspecialchars($_POST['5']);
	$gender = $_POST['6'];
	$citizenship = $_POST['7'];
	$email = htmlspecialchars($_POST['8']);
	$year = htmlspecialchars($_POST['10']);
	logmsg("year: $year");
	db_save_student($id, $firstname, $middlename, $lastname, $born, $studentID, $gender, $citizenship, $email, $year);
}

function yes_remove_students()
{
        db_remove_students_cascading($_POST['ids']);
}

?>
