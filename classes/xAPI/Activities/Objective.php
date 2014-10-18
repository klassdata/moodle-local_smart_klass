<?php
namespace Klap\xAPI;

/**
 * xAPI Objective Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Objective extends ActivityObject {

    public function __construct() {
        $this->type = 'http://adlnet.gov/expapi/activities/objective';

        $this->name['en-US'] = 'Objective';
        $this->name['es'] = 'Objetivo'; 
        
        $this->description['en-US'] = 'An objective determines whether competency has been achieved in a desired area.  Objectives typically are associated with questions and assessments.  Objectives are not learning content and cannot be SCOs.';
        $this->description['es'] = 'Un objetivo determina si se ha logrado la competencia  evaluada en un área determinada. Los objetivos normalmente se asocian con preguntas y evaluaciones. Los objetivos no están aprendiendo el contenido y no pueden ser SCOs.'; 
        
        $this->moreInfo = 'http://adlnet.gov/expapi/activities/objective/';
    }

}
