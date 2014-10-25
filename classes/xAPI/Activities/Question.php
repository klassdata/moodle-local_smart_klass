<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Question Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Question extends ActivityObject {

    public function __construct() {
        $this->type = 'http://adlnet.gov/expapi/activities/question';

        $this->name['en-US'] = 'Question';
        $this->name['es'] = 'Cuestión'; 
        
        $this->description['en-US'] = 'A question is typically part of an assessment and requires a response from the learner, a response that is then evaluated for correctness.';
        $this->description['es'] = 'Una pregunta suele ser parte de una evaluación y requiere una respuesta por parte del alumno, una respuesta que luego será evaluada para su corrección.'; 
        
        $this->moreInfo = 'http://adlnet.gov/expapi/activities/question/';
    }

}
