<?php
namespace SmartKlass\xAPI;

/**
 * xAPI attended Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class attended extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/attended';

        $this->display['en-US'] = 'attended';
        $this->display['es'] = 'asistido';
    }
    
}
