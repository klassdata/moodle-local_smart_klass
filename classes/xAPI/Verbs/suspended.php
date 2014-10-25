<?php
namespace SmartKlass\xAPI;

/**
 * xAPI suspended Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class suspended extends VerbObject{

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/suspended';

        $this->display['en-US'] = 'suspended';
        $this->display['es'] = 'suspendido';
    }

}
