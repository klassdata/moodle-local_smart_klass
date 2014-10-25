<?php
namespace SmartKlass\xAPI;

/**
 * xAPI interacted Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class interacted extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/interacted';

        $this->display['en-US'] = 'interacted';
        $this->display['es'] = 'interactuado';
    }
    
}
