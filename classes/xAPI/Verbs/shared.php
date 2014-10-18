<?php
namespace Klap\xAPI;

/**
 * xAPI shared Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class shared extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/shared';

        $this->display['en-US'] = 'shared';
        $this->display['es'] = 'compartido';
    }

}
