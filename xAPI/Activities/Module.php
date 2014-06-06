<?php
namespace Klap\xAPI;

/**
 * xAPI Module Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Module extends ActivityObject {

    public function __construct() {
        $this->type = 'http://adlnet.gov/expapi/activities/module';

        $this->name['en-US'] = 'Module';
        $this->name['es'] = 'Módulo'; 
        
        $this->description['en-US'] = 'A module represents any “content aggregation” at least one level below the course level.  Modules of modules can exist for layering purposes.  Modules are not content.  Modules are one level up from all content';
        $this->description['es'] = 'Un módulo representa cualquier "agregación de contenidos" por lo menos un nivel por debajo del nivel del curso. Pueden existir módulos de módulos para fines de estratificación. Los módulos no son contenidos. Los módulos son de un nivel superior a los contenidos.'; 
        
        $this->moreInfo = 'http://adlnet.gov/expapi/activities/module/';
    }

}
