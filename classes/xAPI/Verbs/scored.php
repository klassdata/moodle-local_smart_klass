<?php
namespace Klap\xAPI;

/**
 * xAPI scored Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class scored extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/scored';

        $this->display['en-US'] = 'scored';
        $this->display['es'] = 'calificado';
    }

}
