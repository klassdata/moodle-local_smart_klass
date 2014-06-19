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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');


/**
 * Register Execution Klap Server Connection
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_klap_lrs_login_form extends moodleform {

    /**
     * Form element definition
     */
    public function definition() {
        $form = $this->_form;

         // Username.
        $form->addElement('text', 'username', get_string('username'), array('size'=>52));
        $form->addRule('username', get_string('required'), 'required');
        $form->setType('username', PARAM_TEXT);
        $form->setDefault('username', get_config('local_klap', 'username'));

         // Password.
        $form->addElement('password', 'password', get_string('password'), array('size'=>52));
        $form->addRule('password', get_string('required'), 'required');
        $form->setType('password', PARAM_TEXT);
        $form->setDefault('password', get_config('local_klap', 'password'));

        $this->add_action_buttons();
    }

    /**
     * Setup the form depending on current values. This method is called after definition(),
     * data submission and set_data().
     * All form setup that is dependent on form values should go in here.
     *
     * We remove the element status if there is no current status (i.e. guide is only being created)
     * so the users do not get confused
     */
    public function definition_after_data() {
       
    }

    /**
     * Form vlidation.
     * If there are errors return array of errors ("fieldname"=>"error message"),
     * otherwise true if ok.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *               or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
      global $DB;  
      
      $err = parent::validation($data, $files);
      $err = array();
      $form = $this->_form;
 
     
        
      return $err;
    }

    /**
     * Return submitted data if properly submitted or returns NULL if validation fails or
     * if there is no submitted data.
     *
     * @return object submitted data; NULL if not valid or not submitted or cancelled
     */
    public function get_data() {
        $data = parent::get_data();

        return $data;
    }

}