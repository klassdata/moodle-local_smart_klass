<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Interaction Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Interaction extends ActivityObject {

    public function __construct() {
        $this->type = 'http://adlnet.gov/expapi/activities/interaction';

        $this->name['en-US'] = 'interaction';
        $this->name['es'] = 'interaccion'; 
        
        $this->description['en-US'] = 'An interaction is typically a part of a larger activity (such as assessment or simulation) and refers to a control to which a learner provides input.  An interaction can be either an asset or function independently';
        $this->description['es'] = 'Una interacción es típicamente una parte de una actividad más grande (como la evaluación o simulación) y se refiere a un control para que un alumno proporciona la entrada. Una interacción puede ser un recurso o función de forma independiente';
        
        $this->moreInfo = 'http://adlnet.gov/expapi/activities/interaction/';
    }

}
