<?PHP
function process_universities()
{
	print '<b>Universities</b><br /><br />';
	if (isset($_GET['act']))
        {
                if ($_POST['Edit'] === 'edit')
                {
                        $uni = db_universities_without_countries();
                        edit_university($uni);
                }
                else if ($_POST['Add'] === 'add')
                        add_university();
                else if ($_POST['Save'] === 'save')
                        save_university();
                else if ($_POST['Remove'] === 'remove')
                {
                        $uni = db_universities_with_countries(); 
                        remove_record($uni);
                }
                else if ($_POST['Remove'] === 'yes')
                        yes_remove_university();
        }
	$unidata = db_universities_with_countries();
	show_table(array('ID', 'University name (country)', 'Erasmus code'), $unidata);
}

function edit_university($agg)
{
	$aggr = retrieve_first_checked_record($agg);
	if ($aggr === NULL) infomsg("Please tick the record you want to edit");
	else addedit_university($aggr);
}

function addedit_university($aggr)
{
	$column_labels = array('ID', 'University name', 'Country', 'Erasmus code');
	$column_types = array('RDONLY', 'text', '*', 'text');
	$column_seldata = db_countries();
	show_edit_form($column_labels, $aggr, $column_types, array($column_seldata));
}

function add_university()
{
	$ctrys = db_get_first_country();
	addedit_university(array('-', 'enter university name', $ctrys[0], 'enter erasmus code'));
}

function yes_remove_university()
{
	db_remove_university($_POST['ids']);
}

function save_university()
{
	$id = $_POST['0'];
	$uniName = htmlspecialchars($_POST['1']);
	$cntry = htmlspecialchars($_POST['2']);
	$uniCode = htmlspecialchars($_POST['3']);
	db_save_university($id, $uniName, $cntry, $uniCode);
}

?>
