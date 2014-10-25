<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Media Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Media extends ActivityObject {

    public function __construct() {
        $this->type = 'http://adlnet.gov/expapi/activities/media';

        $this->name['en-US'] = 'Media';
        $this->name['es'] = 'Archivo de medios'; 
        
        $this->description['en-US'] = 'Media refers to text, audio, or video used to convey information.  Media can be consumed (tracked: completed), but doesn’t have an interactive component that may result in a score, success, or failure.';
        $this->description['es'] = 'Un archivo de medios se refiere a texto, audio o vídeo que se utiliza para transmitir información. Los archivos de medios pueden ser consumidos (seguimiento: finalizado), pero no tiene un componente interactivo que puede resultar en una puntuación, aprobado o suspenso.'; 
        
        $this->moreInfo = 'http://adlnet.gov/expapi/activities/media/';
    }

}
