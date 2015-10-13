<?PHP
function process_subject_areas()
{
	print '<b>Subject Areas</b><br /><br />';
	if (isset($_GET['act']))
        {
                if ($_POST['Edit'] === 'edit')
                {
                        $sa = db_subject_areas();
                        edit_subject_area($sa);
                }
                else if ($_POST['Add'] === 'add')
                        add_subject_area();
                else if ($_POST['Save'] === 'save')
                        save_subject_area();
                else if ($_POST['Remove'] === 'remove')
                {
                        $sa = db_subject_areas(); 
                        remove_record($sa);
                }
                else if ($_POST['Remove'] === 'yes')
                        yes_remove_subject_area();
        }
	$sadata = db_subject_areas();
	show_table(array('ID', 'Subject area CODE', 'Subject area NAME'), $sadata);
        print '<br /><br />For a list of areas, see: <a href="http://egracons.eu/sites/default/files/APPLICATION_OF_ISCED_CODES_IN_EGRACONS_TOOL_2015%2004%2008_1.pdf" target="_blank">ISCED CODES</a><br />';
}

function edit_subject_area($sas)
{
	$sa = retrieve_first_checked_record($sas);
	if ($sp === NULL) infomsg("Please tick the record you want to edit");
	else addedit_subject_area($sa);
}

function addedit_subject_area($sa)
{
	$column_labels = array('ID', 'Subject area CODE', 'Subject area NAME');
	$column_types = array('RDONLY', 'text', 'text');
	show_edit_form($column_labels, $sa, $column_types, NULL);
}

function add_subject_area()
{
	addedit_subject_area(array('-', 'enter code', 'enter name'));
}

function save_subject_area()
{
	$id = $_POST['0'];
	$code = htmlspecialchars($_POST['1']);
	$name = htmlspecialchars($_POST['2']);
	db_save_subject_area($id, $code, $name);
}

function yes_remove_subject_area()
{
        db_remove_subject_area($_POST['ids']);
}

?>
