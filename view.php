<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Register Execution Smart Klass Server Connection
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

require_login();
$context = context_system::instance();
require_capability('local/smart_klass:manage', $context);

$action  = optional_param('a', SMART_KLASS_ACTION_DEFAULT, PARAM_ALPHA );


$strheading = get_string('controlpanel', 'local_smart_klass');


$PAGE->set_context($context);
switch ($action) {
   case  SMART_KLASS_ACTION_DEFAULT:
       
       $provider = SmartKlass\xAPI\Credentials::getProvider();
       $credentials = $provider->updateCredentials();
        
       $PAGE->set_pagelayout('standard');
       $PAGE->set_url(new moodle_url('/local/smart_klass/view.php'));
       $PAGE->set_title( $strheading );
       $PAGE->set_heading( $strheading );
       $PAGE->navbar->add($strheading);
       
       $content = $OUTPUT->heading($strheading);
  
       //Collectors
       $harvesters = local_smart_klass_get_harvesters();
       $data = array();
       
       foreach ($harvesters as $harvester){
           $line = array();
           
           $img = ($harvester->active == 1) ? 
                  html_writer::empty_tag('img',array('src'=>$OUTPUT->pix_url('i/completion-auto-pass'), 'alt'=>get_string('yes'), 'class'=>'icon')) :
                  html_writer::empty_tag('img',array('src'=>$OUTPUT->pix_url('i/completion-auto-enabled'), 'alt'=>get_string('no'), 'class'=>'icon'));
           
           $activate_image = array();
           if ($harvester->active == 1) {
               $activate_image['src'] = $OUTPUT->pix_url('i/completion-auto-pass');
               $activate_image['alt'] = get_string('activate', 'local_smart_klass');
               $activate_image['class'] = 'icon';
           } else {
              $activate_image['src'] = $OUTPUT->pix_url('i/completion-auto-enabled');
              $activate_image['alt'] = get_string('deactivate', 'local_smart_klass');
              $activate_image['class'] = 'icon'; 
           }
           $img = html_writer::link(
                    new moodle_url(
                                    '/local/smart_klass/view.php', 
                                    array(
                                            'cid'=>$context->id, 
                                            'a'=>SMART_KLASS_ACTION_ACTIVATE, 
                                            'cid'=>$harvester->id
                                    )
                    ),
                    html_writer::empty_tag('img', $activate_image)
                );
   
           $line[] = $img;
           $line[] = html_writer::link(new moodle_url('/local/smart_klass/view.php', array('action'=>SMART_KLASS_ACTION_EDIT, 'cid'=>$harvester->id)), $harvester->name);
           
           $collectordata = json_decode($harvester->data,true);
           $v = $t = array();
           if ( empty($collectordata) ){
               $v[] = '-';             
               $t[] = '-';
           } else {
                foreach($collectordata as $d) {
                    $max_id = (isset($d['max_id']) ) ? $d['max_id']: 0;
                    $v[] = ((isset($d['last_registry']) ) ? $d['last_registry'] : '')  . ( ( empty($max_id) ) ? '' : '/' . $max_id);             
                    $t[] = (!isset($d['last_execution']) || $d['last_execution'] == 0) ? '-' : date('d/m/Y H:i:s', $d['last_execution']); 
                }
            }
           
           $line[] = implode(html_writer::empty_tag('br'), $v);
           $line[] = implode(html_writer::empty_tag('br'), $t);        
           
           
           $buttons = array();
           

           
           if ($harvester->active == 1) {
            $url = new moodle_url('/local/smart_klass/view.php', array('a' => 'h', 'cid'=>$harvester->id));
            $img = html_writer::empty_tag('img',array('src'=>$OUTPUT->pix_url('i/import'), 'alt'=>get_string('harvest', 'local_smart_klass'), 'class'=>'icon'));
            $line[] = $OUTPUT->action_link($url, $img, new popup_action('click', $url));
           } else {
               $line[] = '';
           }
           $data[] = $line;
       }
       $table = new html_table();
       $table->head  = array('', get_string('collector_name', 'local_smart_klass'),  get_string('last_registry', 'local_smart_klass'), get_string('last_exectime', 'local_smart_klass'),'');
       $table->size  = array('5%', '40%', '20%', '20%',  '15%');
       $table->align = array('center', 'left', 'center', 'left','center');
       $table->width = '100%';
       $table->data  = $data;
       
       $url = new moodle_url('/local/smart_klass/view.php', array('a' => 'h'));
       
       $content .= $OUTPUT->box_start();
       //$content .= $OUTPUT->heading( get_string('collector_status', 'local_smart_klass') );
       
       
       $content .= (get_config('local_smart_klass', 'croninprogress') == true) ? 'Proceso de recolección en curso' : 'Proceso de recolección detenido';
       $content .= '(' . get_config('local_smart_klass', 'harvestcicles') . ')';
       $content .= html_writer::table($table);
       $content .= $OUTPUT->action_link($url, get_string('fullharvester', 'local_smart_klass'), new popup_action('click', $url));
       $content .= $OUTPUT->box_end();
      
       //Logs
       /*$content .= $OUTPUT->box_start();
       $content .= $OUTPUT->heading( get_string('execution_log', 'local_smart_klass') );
       $content .= SmartKlass\xAPI\Logger::get_logs();
       $content .= $OUTPUT->box_end();*/
       
       echo $OUTPUT->header();
       echo $content;
       echo $OUTPUT->footer();
       break;
   
  
   case SMART_KLASS_ACTION_HARVERTS:
       $collector_id = optional_param('cid', 0, PARAM_INT);
       
       //require_once(dirname(__FILE__).'/javascript/geshi/geshi.php');
       $PAGE->set_pagelayout('popup');
       
       $url = new moodle_url('/local/smart_klass/view.php', array('a' => 'e'));
       $PAGE->set_title( $strheading );
       $PAGE->set_heading( $strheading );
       
       require_once ('./classes/xAPI/StatementRequest.php');
       
       echo $OUTPUT->header();
       echo $OUTPUT->box_start();
       $collector = ($collector_id > 0) ?  array($collector_id) : null;
       local_smart_klass_harvest($collector);
       echo $OUTPUT->box_end();
       echo $OUTPUT->footer();
       break;
   
   case SMART_KLASS_ACTION_ACTIVATE:
       $collector_id = optional_param('cid', 0, PARAM_INT);
       if ($collector_id > 0) local_smart_klass_activate_harvester($collector_id);
       redirect( new moodle_url('/local/smart_klass/view.php') );
       break;
    
}


