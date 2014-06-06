<?php
namespace Klap\xAPI;

/**
 * xAPI xApiObject Abstract Class
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

abstract class xApiObject
{
    abstract public function expose();

    public function __toString() {
        return  json_encode ($this->expose(), JSON_PRETTY_PRINT);
    }
}