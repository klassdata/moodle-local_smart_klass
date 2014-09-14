<?
require_once (dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');


	global $DB;
	

	
//	if(confirm_sesskey($_POST['sesskey'])){
		$date = time();
		$obj =  array('access_token'=>$_POST['access_token'], 'refresh_token'=>$_POST['refresh_token'], 'userid'=>$_POST['userid'],'dashboard_role'=>$_POST['dashboard_role'],"created"=>$date,"email"=>$_POST['email']) ;
		print_r($obj);
		
		$DB->insert_record('local_klap_dashboard_oauth', $obj);
//	}

?>

