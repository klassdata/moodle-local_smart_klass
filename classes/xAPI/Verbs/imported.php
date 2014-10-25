<?php
namespace SmartKlass\xAPI;

/**
 * xAPI imported Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class imported extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/imported';

        $this->display['en-US'] = 'imported';
        $this->display['es'] = 'importado';
    }

}
