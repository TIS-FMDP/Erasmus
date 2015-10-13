<?PHP
function process_fmfi_courses()
{
	print '<b>FMFI Courses</b><br /><br />';
	if (isset($_GET['act']))
        {
                if ($_POST['Edit'] === 'edit')
                {
                        $fc = db_fmfi_courses();
                        edit_fmfi_course($fc);
                }
                else if ($_POST['Add'] === 'add')
                        add_fmfi_course();
                else if ($_POST['Save'] === 'save')
                        save_fmfi_course();
                else if ($_POST['Remove'] === 'remove')
                {
                        $fc = db_fmfi_courses(); 
                        remove_record($fc);
                }
                else if ($_POST['Remove'] === 'yes')
                        yes_remove_fmfi_courses();
        }
	$fcdata = db_fmfi_courses();
	show_table(array('ID', 'Course CODE', 'Course NAME', 'Credits'), $fcdata);
}

function edit_fmfi_course($fcs)
{
	$fc = retrieve_first_checked_record($fcs);
	if ($fc === NULL) infomsg("Please tick the record you want to edit");
	else addedit_fmfi_course($fc);
}

function addedit_fmfi_course($fc)
{
	$column_labels = array('ID', 'Course CODE', 'Course NAME', 'Credits');
	$column_types = array('RDONLY', 'text', '100text', 'number');
	show_edit_form($column_labels, $fc, $column_types, NULL);
}

function add_fmfi_course()
{
	addedit_fmfi_course(array('-', 'enter code', 'enter name', 9));
}

function save_fmfi_course()
{
	$id = $_POST['0'];
	$code = htmlspecialchars($_POST['1']);
	$name = htmlspecialchars($_POST['2']);
	$credits = $_POST['3'];
	db_save_fmfi_course($id, $code, $name, $credits);
}

function yes_remove_fmfi_courses()
{
        db_remove_fmfi_courses($_POST['ids']);
}

?>
