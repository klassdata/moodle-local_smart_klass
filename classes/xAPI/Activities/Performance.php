<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Performance Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Performance extends ActivityObject {

    public function __construct() {
        $this->type = 'http://adlnet.gov/expapi/activities/performance';

        $this->name['en-US'] = 'Performance';
        $this->name['es'] = 'Interpretación'; 
        
        $this->description['en-US'] = 'A performance is an attempted task or series of tasks within a particular context.  Tasks would likely take on the form of interactions, or the performance could be self-contained content.  ';
        $this->description['es'] = 'Una interpretación es un intento de tarea o serie de tareas dentro de un contexto particular. Tareas probable es que toman la forma de las interacciones, o el rendimiento pueden ser de contenido autónomo.'; 
        
        $this->moreInfo = 'http://adlnet.gov/expapi/activities/performance/';
    }

}
