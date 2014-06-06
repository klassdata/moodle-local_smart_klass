<?php
namespace Klap\xAPI;

/**
 * xAPI launched Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class launched extends VerbObject {
 
    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/launched';

        $this->display['en-US'] = 'launched';
        $this->display['es'] = 'lanzado';
    }

}
