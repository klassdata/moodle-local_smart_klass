<?php
namespace SmartKlass\xAPI;

/**
 * xAPI CmiInteraction Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
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
