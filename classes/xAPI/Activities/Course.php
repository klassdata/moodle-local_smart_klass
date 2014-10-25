<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Course Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Course extends ActivityObject {

    public function __construct() {
        $this->type = 'http://adlnet.gov/expapi/activities/course';

        $this->name['en-US'] = 'Course';
        $this->name['es'] = 'Curso'; 
        
        $this->description['en-US'] = 'A course represents an entire “content package” worth of material.  The largest level of granularity.  Unless flat, a course consists of multiple modules.  A course is not content.';
        $this->description['es'] = 'Un curso representa toda un "paquete de contenido" de material. El mayor nivel de granularidad. A no ser que sea plano, un curso consta de varios módulos. Un curso es el contenido.'; 
        
        $this->moreInfo = 'http://adlnet.gov/expapi/activities/course/';
    }

}
