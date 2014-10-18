<?php
namespace Klap\xAPI;

/**
 * xAPI commented Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class commented extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/commented';

        $this->display['en-US'] = 'commented';
        $this->display['es'] = 'comentado';
    }

}
