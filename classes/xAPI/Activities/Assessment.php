<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Assessment Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Assessment extends ActivityObject {

    public function __construct() {
        $this->type = 'http://adlnet.gov/expapi/activities/assessment';

        $this->name['en-US'] = 'assessment';
        $this->name['es'] = 'evaluacion'; 
        
        $this->description['en-US'] = 'An assessment is an activity that determines a learner’s mastery of a particular subject area.  An assessment typically has one or more questions.';
        $this->description['es'] = 'Una evaluación es una actividad que determina el dominio del alumno de un área determinada. Una evaluación tiene típicamente una o más preguntas.';
        $this->moreInfo = 'http://adlnet.gov/expapi/activities/assessment/';
    }

}
