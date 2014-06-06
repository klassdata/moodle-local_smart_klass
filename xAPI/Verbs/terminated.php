<?php
namespace Klap\xAPI;

/**
 * xAPI terminated Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class terminated extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/terminated';

        $this->display['en-US'] = 'terminated';
        $this->display['es'] = 'terminado';
    }

}
