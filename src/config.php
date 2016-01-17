<?PHP
$ERA = '';
$logON = false;
$dekan = 'prof. RNDr. Jozef Masarik, DrSc.';
setlocale(LC_COLLATE, "sk_SK");
$first_year = 2014;
$default_study_program = 93; //mIKV
$DB_USER='erasmus';
$DB_TABLE='erasmus';
$DB_PASS='***';

function study_points($i){
  switch ($i) {
    case 0:
        $points = 25;
        break;
    case 1:
        $points = 50;
        break;
    case 2:
        $points = 75;
        break;
  }
  return $points;
}
?>
