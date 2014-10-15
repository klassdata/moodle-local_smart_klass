<?php
namespace Klap\xAPI;

/**
 * xAPI DataProvider Class
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class DataProvider_Moodle27 extends DataProvider {
    
    public function __construct() {   
    }
    
    public function getCourses (Collector $collector) {
        global $DB;
        
        $reprocess = (count($collector->getReproccessIds())>0) ? ' OR id IN (' . implode(',', $collector->getReproccessIds()) . ') ' : '';
        $limit = $collector->getMaxRegistrys();
        $limit = ($limit == null) ? 0 : $limit;
        $reg = $DB->get_records_select ('course', 
                                        'id>?' . $reprocess, 
                                        array($collector->getLastRegistry()),
                                        '', 
                                        'id, category, fullname, shortname, sectioncache, modinfo, startdate, timecreated, timemodified, enablecompletion',
                                        0, $limit
          );
        return $reg;
    }
    
}
