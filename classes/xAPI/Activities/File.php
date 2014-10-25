<?php
namespace SmartKlass\xAPI;

/**
 * xAPI File Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class File extends ActivityObject {

    public function __construct() {
        $this->type = 'http://adlnet.gov/expapi/activities/file';

        $this->name['en-US'] = 'file';
        $this->name['es'] = 'fichero'; 
        
        $this->description['en-US'] = 'A file is similar to a link, only the resource is more likely to be used at a) a different time, b) can be used offline, and/or c) could be used with a different system. Files are not considered learning content or SCOs.  If a file is intended for this purpose, it should be re-categorized.';
        $this->description['es'] = 'Un archivo es similar a un enlace, sólo el recurso es más probable que se utilicen en a) un tiempo diferente, b) se puede utilizar fuera de línea, y / o c) podría ser utilizado con un sistema diferente. Los archivos no se consideran contenidos de aprendizaje o SCO. Si un archivo está dirigido a este fin, hay que volver a clasificarse.';
        
        $this->moreInfo = 'http://adlnet.gov/expapi/activities/file/';
    }

}
