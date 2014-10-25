<?php
namespace SmartKlass\xAPI;

/**
 * xAPI passed Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * xAPI passed Verb
 *
 * This class 
 * @package xAPI
 */
class passed extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/passed';

        $this->display['en-US'] = 'passed';
        $this->display['es'] = 'superado';
    }

}
