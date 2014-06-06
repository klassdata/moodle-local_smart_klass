<?php
namespace Klap\xAPI;

/**
 * xAPI interacted Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class interacted extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/interacted';

        $this->display['en-US'] = 'interacted';
        $this->display['es'] = 'interactuado';
    }
    
}
