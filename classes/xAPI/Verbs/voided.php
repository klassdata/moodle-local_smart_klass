<?php
namespace SmartKlass\xAPI;

/**
 * xAPI voided Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class voided extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/voided';

        $this->display['en-US'] = 'voided';
        $this->display['es'] = 'anulado';
    }

}
