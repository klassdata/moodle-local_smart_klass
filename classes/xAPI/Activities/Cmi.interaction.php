<?php
namespace Klap\xAPI;

/**
 * xAPI CmiInteraction Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class CmiInteraction extends ActivityObject {

    public function __construct() {
        $this->type = 'http://adlnet.gov/expapi/activities/cmi.interaction';

        $this->name['en-US'] = 'SCORM Interaction';
        $this->name['es'] = 'Interacción de tipo SCORM'; 
        
        $this->description['en-US'] = 'SCORM Interaction.';
        $this->description['es'] = 'Interacción de tipo SCORM.'; 
        
        $this->moreInfo = 'http://adlnet.gov/expapi/activities/cmi.interaction/';
    }

}
