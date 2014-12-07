<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Link Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Link extends ActivityObject {

    public function __construct() {
        $this->type = 'http://adlnet.gov/expapi/activities/link';

        $this->name['en-US'] = 'Link';
        $this->name['es'] = 'Enlace'; 
        
        $this->description['en-US'] = 'A link is simply a means of expressing a link to another resource within, or external to, an activity.  A link is not synonymous with launching another resource and should be considered external to the current resource.  Links are not learning content, nor SCOs.  If a link is intended for this purpose, it should be re-categorized.';
        $this->description['es'] = 'Un enlace es simplemente un medio para expresar un enlace a otro recurso dentro o externo a  una actividad. Un enlace no es sinónimo de lanzar otro recurso y debe ser considerado un externo al recurso actual. Los vínculos no son contenidos de aprendizaje ni SCOs. Si un enlace está destinado para este fin, debe de ser clasificado de nuevo.';
        
        $this->moreInfo = 'http://adlnet.gov/expapi/activities/link/';
    }

}
