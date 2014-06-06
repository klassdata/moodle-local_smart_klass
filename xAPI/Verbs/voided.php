<?php
namespace Klap\xAPI;

/**
 * xAPI voided Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class voided extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/voided';

        $this->display['en-US'] = 'voided';
        $this->display['es'] = 'anulado';
    }

}
