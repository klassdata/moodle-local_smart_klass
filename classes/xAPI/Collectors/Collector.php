<?php
namespace SmartKlass\xAPI;

/**
 * Collector Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

abstract class Collector  {

    const STRATEGY_LAST_ID = 'sLID';
    const STRATEGY_LAST_TIMESTAMP = 'sLTD';
    const STRATEGY_ALL = 'sALL';
    const STRATEGY_DEFAULT = 'sLID';
      
    private $name = null;
    private $data = null;
    private $max_registrys = null;
    
    protected $dataprovider=null;
    
    private $aditional_collectors;
    
    private $last_error  = null;

    public function __construct ($data=null) {
        $this->dataprovider = DataProviderFactory::build();
        
        $class = new \ReflectionClass(get_class($this));
        $this->name = str_replace('Collector','',$class->getShortName());
        $collector = $this->dataprovider->getCollector($this->name);
        
        if ( empty($collector) ) return null;
        
        $this->data = json_decode($collector->data, true);
        if ($this->data==null) $this->data = array();
        
        /*foreach ($this->data as $table => &$data) {
            /*$data[$table]->last_registry = isset($data[$table]->last_registry) ? $data[$table]->last_registry : 0;
            $data[$table]->last_execution = isset($data[$table]->last_execution) ? $data[$table]->last_execution : 0;
            $data[$table]->strategy = ( isset($data[$table]->strategy) ) ? $data[$table]->strategy : self::STRATEGY_DEFAULT; 
            $data[$table]->reprocessids = ( isset($data[$table]->reprocessids) && is_array($data[$table]->reprocessids) ) ? 
                                    $data[$table]->reprocessids :
                                    array();
        
            $data[$table]->max_id = $this->getMaxId($table);
            
        }*/

        $collection = $this->collectData($this);
        $this->execute( $collection );
        
        if (is_array($this->aditional_collectors) && count($this->aditional_collectors>0)){
            foreach ($this->additional_collectors as $collector_name=>$object){
                try {
                    $addicional_collector = new $collectorName($object);

                } catch (Exception $e) {}
            }
        }
    }
    
    protected function registerAdiccionalCollector ($name, $object) {
        if ( !is_array($this->aditional_collectors) ) $this->aditional_collectors = array();
        $this->aditional_collectors[$name.'Collector'] = $object;
        
    }
    
    protected function execute( $collection=null ) {
        ini_set('max_execution_time', 0);

        Logger::add_to_log('start', $this->name);

        if (empty($collection)) {
            $msg = $this->dataprovider->getLanguageString('no_record_to_update', 'local_smart_klass');
            Logger::add_to_log('msg', $msg);
            Logger::add_to_log('end', $this->name);
            return;
        } else {
            Logger::add_to_log('total_records', count($collection));
        }
        
        $xApi = $this->getXapi();
        
			
        $lrs_version = $xApi->getLRSversion();
        if ($lrs_version->errorcode != 200) {
            Logger::add_to_log('msg', $this->dataprovider->getLanguageString('lrs_error', 'local_smart_klass'));
            Logger::add_to_log('errorcode', $this->msg);
            Logger::add_to_log('error', $this->errorcode);
            Logger::add_to_log('end', $this->name);
            return;
        }
        
        
        
        foreach ($collection as $element){
            $xApi = $this->getXapi();
            $xApi = $this->prepareStatement($xApi, $element);
            
            
            $log_obj = new \stdClass();
            $log_obj->start = date('d/m/Y H:i:s');
            
            $regid = $this->dataprovider->get_reg_id($element->id, get_class($this));
            $log_obj->moodleid = $regid->uri;
            if ( empty($xApi) ){
                $this->dataprovider->updateCollector ($this->name, $this->data);
                
                $log_obj->result = $this->dataprovider->getLanguageString('ko', 'local_smart_klass');
                $log_obj->msg = $this->getLastError();
                $log_obj->moodleid = $regid->uri;
                Logger::add_to_log('registry', $log_obj);
                continue;
            }
            //Set plattform
            $xApi->setContext('platform',  $this->dataprovider->get_platform_version() );
            
            //Set regId
            $regid_extension = new Extension(
                                            'http://xapi.klassdata.com/extensions/regid',
                                            $regid->uri
                                            );
           $xApi->setContext('extension',  $regid_extension );       
           
			
           $result = $xApi->sendStatement();

            if ($result->errorcode == '200') {
                if ( get_config('local_smart_klass', 'save_log') ) {
                    $log_obj->result = $this->dataprovider->getLanguageString('ok', 'local_smart_klass');;
                    $log_obj->msg = $this->dataprovider->getLanguageString('statement_send_ok', 'local_smart_klass');
                    $log_obj->errorcode = $result->errorcode;
                    $id = json_decode($result->msg);
                    $log_obj->lrsid = current($id);
                    if ( get_config('local_smart_klass', 'save_log') && get_config('local_smart_klass', 'savelog_ok_statement') ) 
                        $log_obj->statement = json_decode( (string) $xApi->getStatement() );

                }
                $tt = strtotime ($xApi->getStatement()->getTimestamp());
                if ($this->getLastExecution($regid->table) < $tt) 
                    $this->setLastExecution($regid->table, $tt);
                if ($this->getLastRegistry($regid->table) < $regid->id) 
                     $this->setLastRegistry($regid->table, $regid->id);
                $this->removeReproccessId($regid->table, $regid->id);
                $this->setData($regid->table, 'max_id', $this->getMaxId($regid->table));
                //reset number of cicles of harvester
                set_config('harvestcicles', 0, 'local_smart_klass');
            } else {
                $msg = $result->msg;
                if ( isset($msg->message) ) {
                   $msg = $msg->message;
                } else if (isset($msg->error->message)) {
                   $msg = $msg->error->message . '(in ' . $msg->error->file . ' - line ' . $msg->error->line . ')';
                }
                $this->addReproccessIds($regid->table, $regid->id);

                if (get_config('local_smart_klass', 'save_log')){
                    $log_obj->result = $this->dataprovider->getLanguageString('ko', 'local_smart_klass');;
                    $log_obj->msg = $msg;
                    $log_obj->errorcode = $result->errorcode;
                    $log_obj->lrsid = 'null';
                    $log_obj->statement = json_decode( (string) $xApi->getStatement() );
                }
            }
            $log_obj->end = date('d/m/Y H:i:s');
            Logger::add_to_log('registry', $log_obj);

            $this->dataprovider->updateCollector ($this->name, $this->data);
            
        
            
        } 
        Logger::add_to_log('end', $this->name);
    }
    
    private function getXapi() {
       $auth = $this->dataprovider->getAuth();
       $validate_statements = $this->dataprovider->validateStatements();
       $proxy = $this->dataprovider->getProxy();
       
       return new StatementRequest($auth->endpoint, $auth->type, $auth->chain, $validate_statements, $proxy);
    }
    
    public function getReproccessIds ($table){
        return $this->data[$table]['reprocessids'];
    }
    
    public function addReproccessIds ($table, $ids=array() ){
        if ( is_array($ids) && count($ids)>0 )
    $this->data[$table]['reprocessids'] = array_merge($this->data[$table]['reprocessids'], $ids);
        else {
            if ( !in_array($ids, $this->data[$table]['reprocessids']) )
                $this->data[$table]['reprocessids'][] =  $ids;
        }
    }
    
    public function removeReproccessId ($table, $id) {
        if (($key = array_search($id, $this->data[$table]['reprocessids'])) !== false) {
            unset($this->data[$table]['reprocessids'][$key]);
        }
    }
    
    public function getStrategy ($table){
        switch ($this->data[$table]['strategy']){
            case self::STRATEGY_DEFAULT: return STRATEGY_DEFAULT;
            case self::STRATEGY_LAST_ID: return STRATEGY_LAST_ID;
            case self::STRATEGY_LAST_TIMESTAMP: return STRATEGY_LAST_TIMESTAMP;
            case self::STRATEGY_ALL: return STRATEGY_ALL;
        }
        return STRATEGY_DEFAULT;
    }
    
    public function setStrategy ($table, $strategy){  
        switch ($strategy){
            case self::STRATEGY_DEFAULT: $this->data[$table]['strategy'] = STRATEGY_DEFAULT;break;
            case self::STRATEGY_LAST_ID: $this->data[$table]['strategy'] = STRATEGY_LAST_ID;break;
            case self::STRATEGY_LAST_TIMESTAMP: $this->data[$table]['strategy'] = STRATEGY_LAST_TIMESTAMP;break;
            case self::STRATEGY_ALL: $this->data[$table]['strategy'] = STRATEGY_ALL;break;
        }
    }
    public function getLastExecution ($table){    
        return ( empty($this->data[$table]['last_execution']) )  ? 0 : $this->data[$table]['last_execution'];
    }
    
    public function setLastExecution ($table, $new_execution){
        if ( empty($new_execution) ) return;
        $this->data[$table]['last_execution'] = $new_execution;
    }
    
    public function getLastRegistry ($table){
        return ( empty($this->data[$table]['last_registry']) )  ? 0 : $this->data[$table]['last_registry'];
    }
    
    public function setLastRegistry ($table, $new_registry){
        if ( empty($new_registry) ) return;
        $this->data[$table]['last_registry'] = $new_registry;
    }
    
    public function getData ($table, $param){  
        return $this->data[$table][$param] = $value;
    }
    
    public function setData ($table, $param, $value){
        $this->data[$table][$param] = $value;
    }

    public function getMaxRegistrys () {
        return ( empty($this->max_registrys) ) ? 
            ( defined ( 'static::MAX_REGS' ) ) ? static::MAX_REGS : null : 
            $this->max_registrys;
    }
    
    public function setMaxRegistrys ($max) {
        $this->max_registrys = $max;
    }
    
    public function getFileLog (){
        return $this->filelog;
    }

    public function getInstructors ($courseid) {
        $instructors = null;
        $instructorsids = $this->dataprovider->getInstructors($courseid);
        if (count($instructorsids) > 0){
            if ( count($instructorsids) > 1 ){          
                $instructors = new Group( array_shift($instructorsids) ); 
                foreach ($instructorsids as $id) {
                    $instructors->addMember($id);
                }
            } else {
                $instructors = new Agent($instructorsids);
            }
            
        }
        return $instructors;
    }
    
    public function getMaxId($table) {
        return $this->dataprovider->getMaxId($table);
    }
    
    protected function setLastError ($error) {
        $this->last_error = (string) $error;
    }
    
    protected function getLastError () {
        return $this->last_error;
    }
   
    abstract function collectData();
    abstract function prepareStatement(StatementRequest $xapi, $element);
}