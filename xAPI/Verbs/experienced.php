<?php
namespace Klap\xAPI;

/**
 * xAPI experienced Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class experienced extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/experienced';

        $this->display['en-US'] = 'experienced';
        $this->display['es'] = 'experimentado';
    }

}
