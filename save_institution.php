<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/classes/xAPI/Helpers/Curl.php');

require_login();
require_capability('local/smart_klass:manage', context_system::instance());


$strheading = get_string('configure_access','local_smart_klass');
$PAGE->set_pagelayout('standard');
$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/local/smart_klass/save_institution.php'));
$PAGE->set_title( $strheading );
$PAGE->navbar->add($strheading);

echo $OUTPUT->header();

$institution       = required_param('institution', PARAM_TEXT);
$name              = required_param('name', PARAM_TEXT);
$lastname          = required_param('lastname', PARAM_TEXT);
$email             = required_param('email', PARAM_TEXT);
$password          = required_param('password', PARAM_TEXT);
$confirm_password  = required_param('confirmpassword', PARAM_TEXT);
$captcha_value     = required_param('captcha', PARAM_TEXT);
$accept_terms_form = required_param('terms', PARAM_TEXT);

global $CFG;
$moodle_url = $CFG->wwwroot;

$accept_terms = '1';
if($accept_terms_form != 'on'){
    $accept_terms = '0';
}

$curl = new Curl();

$server = 'https://smartklass.klassdata.com/plugin_register.php';

$params = array(
    'institution_name' => $institution,
    'manager_name'     => $name . ' ' . $lastname,
    'manager_email'    => $email,
    'manager_password' => $password,
    'confirm_password' => $confirm_password,
    'accept_terms'     => $accept_terms,
    'moodle_url'       => $moodle_url
);

echo "Registering Institution----------------<br>";
$output_json = $curl->post( $server, $params);

$output = json_decode($output_json);

if (is_object ($output) == true) {
    if (isset($output->errors)) {
        $url = new moodle_url ('/local/smart_klass/register.php?m='. $output->errors[0]->detail);
        $PAGE->requires->js_init_call('M.local_smart_klass.refreshContent', array((string)$url), true );
        echo $OUTPUT->footer();
    }
    elseif (isset($output->data)) {
        echo "<br>successfully registered<br><br>";

        set_config('registered', true, 'local_smart_klass');

        if (isset($output->data->lrs_endpoint)) {
            set_config('lrs_endpoint', $output->data->lrs_endpoint, 'local_smart_klass');
        }
        if (isset($output->data->lrs_username)) {
            set_config('lrs_username', $output->data->lrs_username, 'local_smart_klass');
        }
        if (isset($output->data->lrs_password)) {
            set_config('lrs_password', $output->data->lrs_password, 'local_smart_klass');
        }
        if (isset($output->data->smartklass_serialnumber)){
            set_config('smartklass_serialnumber', $output->data->smartklass_serialnumber, 'local_smart_klass');
        }
        if (isset($output->data->dashboard_endpoint)){
            set_config('dashboard_endpoint', $output->data->dashboard_endpoint, 'local_smart_klass');
        }

        $context = context_system::instance();
        $url = new moodle_url ('/local/smart_klass/dashboard.php?cid='.$context->id);
        $PAGE->requires->js_init_call('M.local_smart_klass.refreshContent', array((string)$url), true );
        echo $OUTPUT->footer();
    }
}
else {
    $url = new moodle_url ('/local/smart_klass/register.php?m=smart_klass cannot connect with server');
    $PAGE->requires->js_init_call('M.local_smart_klass.refreshContent', array((string)$url), true );
    echo $OUTPUT->footer();
}




