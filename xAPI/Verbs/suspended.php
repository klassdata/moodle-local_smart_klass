<?php
namespace Klap\xAPI;

/**
 * xAPI suspended Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class suspended extends VerbObject{

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/suspended';

        $this->display['en-US'] = 'suspended';
        $this->display['es'] = 'suspendido';
    }

}
