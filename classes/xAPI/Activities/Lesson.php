<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Lesson Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Lesson extends ActivityObject {

    public function __construct() {
        $this->type = 'http://adlnet.gov/expapi/activities/lesson';

        $this->name['en-US'] = 'Lesson';
        $this->name['es'] = 'Lección'; 
        
        $this->description['en-US'] = 'A lesson is learning content that may or may not take on the form of a SCO (formal, tracked learning).  A lesson is the most generic form.';
        $this->description['es'] = 'Una lección es un contenido de aprendizaje que puede o no tomar la forma de un SCO (seguimiento de aprendizaje formal). Una lección es la forma más genérica.';
        
        $this->moreInfo = 'http://adlnet.gov/expapi/activities/lesson/';
    }

}
