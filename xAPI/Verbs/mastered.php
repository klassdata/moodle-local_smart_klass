<?php
namespace Klap\xAPI;

/**
 * xAPI mastered Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class mastered extends VerbObject {
   
    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/mastered';

        $this->display['en-US'] = 'mastered';
        $this->display['es'] = 'dominado';
    }

}
