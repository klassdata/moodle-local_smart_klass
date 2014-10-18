<?php
namespace Klap\xAPI;

/**
 * xAPI Meeting Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Meeting extends ActivityObject {

    public function __construct() {
        $this->type = 'http://adlnet.gov/expapi/activities/meeting';

        $this->name['en-US'] = 'Meeting';
        $this->name['es'] = 'Reunión'; 
        
        $this->description['en-US'] = 'A meeting is a gathering of multiple people for a common cause.';
        $this->description['es'] = 'Una reunión es un encuentro de varias personas por una causa común.'; 
        
        $this->moreInfo = 'http://adlnet.gov/expapi/activities/meeting/';
    }

}
