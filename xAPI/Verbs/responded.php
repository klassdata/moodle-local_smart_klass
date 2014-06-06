<?php
namespace Klap\xAPI;

/**
 * xAPI responded Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class responded extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/responded';

        $this->display['en-US'] = 'responded';
        $this->display['es'] = 'regitrado';
    }

}
