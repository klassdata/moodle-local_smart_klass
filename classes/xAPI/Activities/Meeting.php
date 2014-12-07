<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Meeting Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
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
