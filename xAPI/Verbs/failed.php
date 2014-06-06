<?php
namespace Klap\xAPI;

/**
 * xAPI failed Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class failed extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/failed';

        $this->display['en-US'] = 'failed';
        $this->display['es'] = 'fallado';
    }

}
