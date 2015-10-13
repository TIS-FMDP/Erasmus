<?PHP
function process_study_programs()
{
	print '<b>Study Programs</b><br /><br />';
	if (isset($_GET['act']))
        {
                if ($_POST['Edit'] === 'edit')
                {
                        $sp = db_study_programs();
                        edit_study_program($sp);
                }
                else if ($_POST['Add'] === 'add')
                        add_study_program();
                else if ($_POST['Save'] === 'save')
                        save_study_program();
                else if ($_POST['Remove'] === 'remove')
                {
                        $sp = db_study_programs(); 
                        remove_record($sp);
                }
                else if ($_POST['Remove'] === 'yes')
                        yes_remove_study_program();
        }
	$spdata = db_study_programs();
	show_table(array('ID', 'Study program CODE', 'Study program NAME'), $spdata);
}

function edit_study_program($sps)
{
	$sp = retrieve_first_checked_record($sps);
	if ($sp === NULL) infomsg("Please tick the record you want to edit");
	else addedit_study_program($sp);
}

function addedit_study_program($sp)
{
	$column_labels = array('ID', 'Study program CODE', 'Study program NAME');
	$column_types = array('RDONLY', 'text', 'text');
	show_edit_form($column_labels, $sp, $column_types, NULL);
}

function add_study_program()
{
	addedit_study_program(array('-', 'enter code', 'enter name'));
}

function save_study_program()
{
	$id = $_POST['0'];
	$code = htmlspecialchars($_POST['1']);
	$name = htmlspecialchars($_POST['2']);
	db_save_study_program($id, $code, $name);
}

function yes_remove_study_program()
{
        db_remove_study_programs($_POST['ids']);
}

?>
