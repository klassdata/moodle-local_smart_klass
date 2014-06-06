<?php
namespace Klap\xAPI;

/**
 * xAPI resumed Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class resumed extends VerbObject{
 
    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/resumed';

        $this->display['en-US'] = 'resumed';
        $this->display['es'] = 'reanudado';
    }

}
