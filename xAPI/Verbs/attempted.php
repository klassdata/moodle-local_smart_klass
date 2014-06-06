<?php
namespace Klap\xAPI;

/**
 * xAPI attempted Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class attempted extends VerbObject {
 
    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/attempted';

        $this->display['en-US'] = 'attempted';
        $this->display['es'] = 'intentado';
    }

}
