<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Simulation Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Simulation extends ActivityObject {

    public function __construct() {
        $this->type = 'http://adlnet.gov/expapi/activities/simulation';

        $this->name['en-US'] = 'Simulation';
        $this->name['es'] = 'Simulación'; 
        
        $this->description['en-US'] = 'A simulation is an attempted task or series of tasks in an artificial context that mimics reality.  Tasks would likely take on the form of interactions, or the simulation could be self-contained content. ';
        $this->description['es'] = 'Una simulación es un intento de tarea o serie de tareas en un contexto artificial que imita la realidad. Las tareas probablemente toman la forma de interacciones, o la simulación podría ser contenido autónomo.'; 
        
        $this->moreInfo = 'http://adlnet.gov/expapi/activities/simulation/';
    }

}
