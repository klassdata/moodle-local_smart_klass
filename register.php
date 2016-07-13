<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Register Execution Smart Klass Server Connection
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once (dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

require_once(dirname(__FILE__).'/classes/xAPI/Helpers/Curl.php');

require_login();
require_capability('local/smart_klass:manage', context_system::instance());

$context = context_system::instance();
$strheading = get_string('configure_access','local_smart_klass');
$PAGE->set_pagelayout('standard');
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/smart_klass/register.php'));
$PAGE->set_title( $strheading );
$PAGE->navbar->add($strheading);

echo $OUTPUT->header();

$serialnumber = get_config('local_smart_klass', 'smartklass_serialnumber');

if($serialnumber != false){
	$url = new moodle_url ('/local/smart_klass/dashboard.php?cid='.$context->id);
    $PAGE->requires->js_init_call('M.local_smart_klass.refreshContent', array((string)$url), true );
}
else{
	$message_error = optional_param('m', '', PARAM_TEXT);
	echo '<div style="color:#f00">'.$message_error.'</div>';

	$manager_name = '';
	$manager_lastname = '';
	$manager_email = '';

	$site = get_site();
	if($admin_user = get_admin()){
		$manager_name = $admin_user->firstname;
		$manager_lastname = $admin_user->lastname;
		$manager_email = $admin_user->email;
	}

	$rdcaptcha = rand(1,7);
	$captcha_hash = array('1' => 'A38B', '2' => 'ramrt', '3' => 'j1j4x', '4' => 'A7an5', '5' => '32193', '6' => 'Ba3j4', '7' => 'Zqu54');

	echo '
	<style>
	.field1{
		float:left;width:40%;text-align:right;
	}

	.field2{
		float:left;width:60%;text-align:left
	}

	.field3{
		margin-right:1em;font-weight:bold
	}
	.field4{
		width: 20em;
	}
	</style>

	<script type="text/javascript">
		function check_fields(){
			var error = false;
			if(!document.getElementById("institution").value){
					document.getElementById("institution").style.border = "2px solid #ff0000";
					error = true;
			}else{
					document.getElementById("institution").style.border = "";
			}

			if(!document.getElementById("name").value){
					document.getElementById("name").style.border = "2px solid #ff0000";
					error = true;
			}else{
					document.getElementById("name").style.border = "";
			}

			if(!document.getElementById("lastname").value){
					document.getElementById("lastname").style.border = "2px solid #ff0000";
					error = true;
			}else{
					document.getElementById("lastname").style.border = "";
			}

			if(!document.getElementById("email").value){
					document.getElementById("email").style.border = "2px solid #ff0000";
					error = true;
			}else{
					expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
					if ( !expr.test(document.getElementById("email").value) ){
						document.getElementById("email").style.border = "2px solid #ff0000";
						error = true;
						document.getElementById("error_email").style.visibility = "visible";
					}
					else{
						document.getElementById("email").style.border = "";
						document.getElementById("error_email").style.visibility = "hidden";
					}
			}

			if(!document.getElementById("password").value){
					document.getElementById("password").style.border = "2px solid #ff0000";
					error = true;
			}else{
					document.getElementById("password").style.border = "";

			}

			if(!document.getElementById("confirmpassword").value){
					document.getElementById("confirmpassword").style.border = "2px solid #ff0000";
					error = true;
			}else{
					document.getElementById("confirmpassword").style.border = "";
			}

			if(document.getElementById("confirmpassword").value != document.getElementById("password").value){
				error = true;
				document.getElementById("error_password").style.visibility = "visible";
				document.getElementById("error_password_length").style.visibility = "hidden";
			}
			else{
				document.getElementById("error_password").style.visibility = "hidden";
				if(document.getElementById("password").value && document.getElementById("password").value.length < 8){
					error = true;
					document.getElementById("error_password_length").style.visibility = "visible";
				}
				else{
					document.getElementById("error_password_length").style.visibility = "hidden";
				}
			}

			if(!document.getElementById("captcha").value){
					document.getElementById("captcha").style.border = "2px solid #ff0000";
					error = true;
			}else{
					if(document.getElementById("captcha").value != "'.$captcha_hash[$rdcaptcha].'"){
						error = true;
						document.getElementById("error_captcha").style.visibility = "visible";
						document.getElementById("captcha").style.border = "2px solid #ff0000";
					}
					else{
						document.getElementById("captcha").style.border = "";
						document.getElementById("error_captcha").style.visibility = "hidden";
					}
			}

			if(document.getElementById("terms").checked == false){
					document.getElementById("termdiv").style.color = "#ff0000";
					error = true;
			}else{
					document.getElementById("termdiv").style.color = "";
			}

			if(error == false){
				return true;
			}

			return false;
		}
	</script>

	<div style="color:#535353; background-color:#fff">
		<div style="margin-left:1em;"><img style="margin-top:5px" src="images/logo.png"/></div>
		<hr/>
		<div style="margin-left:20%"><h4 style="font-weight:normal">'. get_string('register_institution','local_smart_klass') .'</h4></div>
		<div style="text-align:center"><img src="images/klass-learning-analytics.png"/></div>
		<div style="text-align:center">'. get_string('warning_activity_completion','local_smart_klass') .'</div>
		<hr/>

		<form name="registerForm" action="save_institution.php" method="POST">
			<div>
				<div class="field1"><label for="institution" class="field3">'. get_string('institution','local_smart_klass') .'</label></div>
				<div class="field2"><input type="text" id="institution" name="institution" class="field4" value="'.$site->shortname.'"/></div>
			</div>
			<div style="clear:both"></div>

			<div>
				<div class="field1"><label for="name" class="field3">'. get_string('manager_name','local_smart_klass') .'</label></div>
				<div class="field2"><input type="text" id="name" name="name" class="field4" value="'.$manager_name.'"/></div>
			</div>
			<div style="clear:both"></div>

			<div>
				<div class="field1"><label for="lastname" class="field3">'. get_string('manager_lastname','local_smart_klass') .'</label></div>
				<div class="field2"><input type="text" id="lastname" name="lastname"  class="field4" value="'.$manager_lastname.'"/></div>
			</div>
			<div style="clear:both"></div>

			<div>
				<div class="field1"><label for="email" class="field3">'. get_string('manager_email','local_smart_klass') .'</label></div>
				<div class="field2"><input type="text" id="email" name="email" class="field4" value="'.$manager_email.'"/></div>
			</div>
			<div style="clear:both"></div>

			<div>
				<div class="field1"><label for="password" class="field3">'. get_string('password','local_smart_klass') .'</label></div>
				<div class="field2"><input type="password" id="password" name="password" class="field4"/></div>
			</div>
			<div style="clear:both"></div>

			<div>
				<div class="field1"><label for="confirmpassword" class="field3">'. get_string('confirm_password','local_smart_klass') .'</label></div>
				<div class="field2"><input type="password" id="confirmpassword" name="confirmpassword" class="field4"/></div>
			</div>
			<div style="clear:both"></div>

			<div>
				<div class="field1"><label for="captcha" class="field3">'. get_string('captcha','local_smart_klass') .'</label></div>
				<div class="field2"><img style="height:45px; widht:100px" src="images/'.$rdcaptcha.'.jpg"/></div>
			</div>
			<div style="clear:both"></div>
			<div>
				<div class="field1"><p/></div>
				<div class="field2"><input type="text" id="captcha" name="captcha" class="field4"/></div>
			</div>
			<div style="clear:both"></div>

			<div>
				<div class="field1"><p/></div>
				<div id="termdiv" class="field2"><input type="checkbox" id="terms" name="terms" />'. get_string('accept','local_smart_klass') .' <a target="_black" href="http://klassdata.com/terms-and-conditions/">'. get_string('terms','local_smart_klass') .'</a></div>
			</div>

			<div>
				<span id="error_captcha" style="color:#f00;visibility:hidden"> - '. get_string('captcha_no_match','local_smart_klass') .' </span>
				<span id="error_email" style="color:#f00;visibility:hidden"> - '. get_string('email_wrong','local_smart_klass') .' </span>
			</div>
			<div>
				<span id="error_password" style="color:#f00;visibility:hidden"> - '. get_string('password_no_match','local_smart_klass') .' </span>
				<span id="error_password_length" style="color:#f00;visibility:hidden"> - '. get_string('password_no_length','local_smart_klass') .' </span>
			</div>


			<div>
				<div class="field1"><input type="submit" name="submit" value="'. get_string('register_submit','local_smart_klass') .'" onclick="return check_fields();"/></div>
				<div class="field2"><input type="reset" /></div>
			</div>
			<div style="clear:both"></div>
			<input type="hidden" id="rdcaptcha" name="rdcaptcha" value="'. $rdcaptcha.'" />
		</form>

	</div>
	';
}


echo $OUTPUT->footer();
