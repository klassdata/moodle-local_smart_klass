<?php
namespace Klap\xAPI;

/**
 * xAPI answered Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class answered extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/answered';

        $this->display['en-US'] = 'answered';
        $this->display['es'] = 'respondido';
    }
}
