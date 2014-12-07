<?php
namespace SmartKlass\xAPI;

/**
 * xAPI created Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class created extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/created';

        $this->display['en-US'] = 'created';
        $this->display['es'] = 'creado';
    }

}
