<?php
namespace SmartKlass\xAPI;

/**
 * xAPI xApiObject Abstract Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

abstract class xApiObject
{
    abstract public function expose();

    public function __toString() {
        return  json_encode ($this->expose()); 
    }
}