<?php
namespace SmartKlass\xAPI;

/**
 * xAPI registered Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class registered extends VerbObject{

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/registered';

        $this->display['en-US'] = 'registered';
        $this->display['es'] = 'regitrado';
    }

}
