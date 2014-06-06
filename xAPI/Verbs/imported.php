<?php
namespace Klap\xAPI;

/**
 * xAPI imported Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class imported extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/imported';

        $this->display['en-US'] = 'imported';
        $this->display['es'] = 'importado';
    }

}
