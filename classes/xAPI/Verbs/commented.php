<?php
namespace SmartKlass\xAPI;

/**
 * xAPI commented Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class commented extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/commented';

        $this->display['en-US'] = 'commented';
        $this->display['es'] = 'comentado';
    }

}
