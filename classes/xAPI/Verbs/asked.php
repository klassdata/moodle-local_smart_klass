<?php
namespace Klap\xAPI;

/**
 * xAPI asked Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class asked extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/asked';

        $this->display['en-US'] = 'asked';
        $this->display['es'] = 'anulado';
    }

    
}
