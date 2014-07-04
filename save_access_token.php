<?
require_once (dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');


global $DB;
	
	$date = date('Y').date('m').date('d').date('H').date('i').date('s');
    $obj =  array('access_token'=>$_POST['code'], 'refresh_token'=>$_POST['refresh'], 'userid'=>$_POST['user_id'],'dashboard_role'=>$_POST['rol'],"created"=>$date) ;
	print_r($obj);
	
	$DB->insert_record('local_klap_dashboard_oauth', $obj);


?>

