<?php
namespace Klap\xAPI;

/**
 * xAPI passed Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * xAPI passed Verb
 *
 * This class 
 * @package xAPI
 */
class passed extends VerbObject {

    public function __construct() {
        $this->id = 'http://adlnet.gov/expapi/verbs/passed';

        $this->display['en-US'] = 'passed';
        $this->display['es'] = 'superado';
    }

}
