<?php
namespace Klap\xAPI;

/**
 * xAPI Assessment Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
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
