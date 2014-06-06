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
 * Register Execution Klap Server Connection
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once (dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

require_login();
$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('local/klap:manage', $context);

$action  = optional_param('a', KLAP_ACTION_DEFAULT, PARAM_ALPHA );


$strheading = get_string('controlpanel', 'local_klap');

switch ($action) {
   case  KLAP_ACTION_DEFAULT:
       require_once (dirname(__FILE__) . '/xAPI/Helpers/Logger.php');
             
       $PAGE->set_pagelayout('standard');
       $PAGE->set_url(new moodle_url('/local/klap/view.php'));
       $PAGE->set_title( $strheading );
       $PAGE->set_heading( $strheading );
       $PAGE->navbar->add($strheading);

       $content = $OUTPUT->heading($strheading);
  
       //Collectors
       $harvesters = local_klap_get_harvesters();
       $data = array();
       
       foreach ($harvesters as $harvester){
           $line = array();
           
           $img = ($harvester->active == 1) ? 
                                                html_writer::empty_tag('img',array('src'=>$OUTPUT->pix_url('i/completion-auto-pass'), 'alt'=>get_string('yes'), 'class'=>'icon')) :
                                                html_writer::empty_tag('img',array('src'=>$OUTPUT->pix_url('i/completion-auto-enabled'), 'alt'=>get_string('no'), 'class'=>'icon'));
           
           $activate_image = array();
           if ($harvester->active == 1) {
               $activate_image['src'] = $OUTPUT->pix_url('i/completion-auto-pass');
               $activate_image['alt'] = get_string('activate', 'local_klap');
               $activate_image['class'] = 'icon';
           } else {
              $activate_image['src'] = $OUTPUT->pix_url('i/completion-auto-enabled');
              $activate_image['alt'] = get_string('deactivate', 'local_klap');
              $activate_image['class'] = 'icon'; 
           }
           $img = html_writer::link(
                                            new moodle_url(
                                                            '/local/klap/view.php', 
                                                            array(
                                                                    'cid'=>$context->id, 
                                                                    'a'=>KLAP_ACTION_ACTIVATE, 
                                                                    'cid'=>$harvester->id
                                                            )
                                            ),
                                            html_writer::empty_tag('img', $activate_image)
                                        );
   
           
           
           $line[] = $img;
           $line[] = html_writer::link(new moodle_url('/local/klap/view.php', array('action'=>KLAP_ACTION_EDIT, 'cid'=>$harvester->id)), $harvester->name);
           
           $collectordata = json_decode($harvester->data);
           $txtlastregistry = $harvester->lastregistry;
           $txtlastregistry .= ( empty($collectordata->max_id) ) ? '' : '/' . $collectordata->max_id;
           $line[] = $txtlastregistry;
           $line[] = ($harvester->lastexectime == 0) ? '-' : date('d/m/Y H:i:s', $harvester->lastexectime);          
           
           
           $buttons = array();
           

           
           if ($harvester->active == 1) {
            $url = new moodle_url('/local/klap/view.php', array('a' => 'h', 'cid'=>$harvester->id));
            $img = html_writer::empty_tag('img',array('src'=>$OUTPUT->pix_url('i/import'), 'alt'=>get_string('harvest'), 'class'=>'icon'));
            $line[] = $OUTPUT->action_link($url, $img, new popup_action('click', $url));
           } else {
               $line[] = '';
           }
           $data[] = $line;
       }
       $table = new html_table();
       $table->head  = array('', get_string('collector_name', 'local_klap'),  get_string('last_registry', 'local_klap'), get_string('last_exectime', 'local_klap'),'');
       $table->size  = array('5%', '40%', '20%', '20%',  '15%');
       $table->align = array('center', 'left', 'center', 'left','center');
       $table->width = '100%';
       $table->data  = $data;
       
       $url = new moodle_url('/local/klap/view.php', array('a' => 'h'));
       
       $content .= $OUTPUT->box_start();
       $content .= $OUTPUT->heading( get_string('collector_status', 'local_klap') );
       
       $lastcron = $DB->get_field_sql('SELECT MAX(lastcron) FROM {modules}');
       $cronoverdue = ($lastcron < time() - 3600 * 24);
       
       if (!$cronoverdue) $content .= $OUTPUT->box(get_string('cronwarning', 'admin'), 'generalbox adminwarning');
       $content .= html_writer::table($table);
       $content .= $OUTPUT->action_link($url, get_string('fullharvester', 'local_klap'), new popup_action('click', $url));
       $content .= $OUTPUT->box_end();
      
       //Logs
       $content .= $OUTPUT->box_start();
       $content .= $OUTPUT->heading( get_string('execution_log', 'local_klap') );
       $content .= Klap\xAPI\Logger::get_logs();
       $content .= $OUTPUT->box_end();
       
       echo $OUTPUT->header();
       echo $content;
       echo $OUTPUT->footer();
       break;
   
  
   case KLAP_ACTION_HARVERTS:
       $collector_id = optional_param('cid', 0, PARAM_INT);
       
       //require_once(dirname(__FILE__).'/javascript/geshi/geshi.php');
       $PAGE->set_pagelayout('popup');
       
       $url = new moodle_url('/local/klap/view.php', array('a' => 'e'));
       $PAGE->set_title( $strheading );
       $PAGE->set_heading( $strheading );
       
       require_once ('./xAPI/StatementRequest.php');
       
       echo $OUTPUT->header();
       echo $OUTPUT->box_start();
       $collector = ($collector_id > 0) ?  array($collector_id) : null;
       local_klap_harvest($collector);
       echo $OUTPUT->box_end();
       echo $OUTPUT->footer();
       break;
   
   case KLAP_ACTION_ACTIVATE:
       $collector_id = optional_param('cid', 0, PARAM_INT);
       if ($collector_id > 0) local_klap_activate_harvester($collector_id);
       redirect( new moodle_url('/local/klap/view.php') );
       break;
    
}


