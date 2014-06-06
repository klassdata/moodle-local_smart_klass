<?php
namespace Klap\xAPI;

/**
 * xAPI registered Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class registered extends VerbObject{

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/registered';

        $this->display['en-US'] = 'registered';
        $this->display['es'] = 'regitrado';
    }

}
