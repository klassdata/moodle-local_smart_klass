<?php
namespace Klap\xAPI;

/**
 * Collector Class
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

abstract class Collector  {

    const STRATEGY_LAST_ID = 'sLID';
    const STRATEGY_LAST_TIMESTAMP = 'sLTD';
    const STRATEGY_ALL = 'sALL';
    const STRATEGY_DEFAULT = 'sLID';
      
    private $name = null;
    private $data = null;
    private $last_registry = null;
    private $last_execution = null;
    
    protected $dataprovider=null;
    
    private $aditional_collectors;

    public function __construct ($data=null) {
        $this->dataprovider = new DataProvider;
        
        $class = new \ReflectionClass(get_class($this));
        $this->name = str_replace('Collector','',$class->getShortName());
        $collector = $this->dataprovider->getCollector($this->name);
        
        if ( empty($collector) ) return null;
        
        $this->data = json_decode($collector->data);
        if ($this->data==null) $this->data = new \stdClass();
        $this->last_registry = ( empty($collector->lastregistry) ) ? 0 : $collector->lastregistry;
        $this->last_execution = ( empty($collector->lastexecute) ) ? 0 : $collector->lastexecute;
        
        //compruebo si tiene fijada estrategia de ejecución del colector y sino fijo la estrategia por defecto
        $this->data->strategy = ( isset($this->data->strategy) ) ? 
                                    $this->data->strategy :
                                    self::STRATEGY_DEFAULT;            
        
        $this->data->reprocessids = ( isset($this->data->reprocessids) && is_array($this->data->reprocessids) ) ? 
                                    $this->data->reprocessids :
                                    array();
        
        $this->data->max_id = $this->getMaxId();
        
    
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
        
        Logger::add_to_log($this->name . ' ----------------------------------- INICIO');
        
        if (empty($collection)) {
            Logger::add_to_log('No hay nuevos registros a actualizar (' . get_class($this) . ')');
            Logger::add_to_log($this->name . ' ----------------------------------- FIN');
            return;
        } else {
            Logger::add_to_log('Total Registros (' . count($collection) . ')');
        }
        
        
        foreach ($collection as $element){
            $xApi = $this->getXapi();
            $xApi = $this->prepareStatement($xApi, $element);
            $log_element = $element->id . ': ';
            if ( empty($xApi) ){
                $log_element .= 'KO (Valor Null para sentencia generada)';
                $this->dataprovider->updateCollector ($this->name, null, null, $this->data);
                Logger::add_to_log($log_element); 
                continue;
            }
            $xApi->setContext('platform',  $this->dataprovider->get_platform_version() );
            $regid_extension = new Extension(
                                            'http://l-miner.klaptek.com/xapi/extensions/regid',
                                            $this->dataprovider->get_reg_id($element->id, get_class($this))
                                            );
           $xApi->setContext('extension',  $regid_extension );
            $result = $xApi->sendStatement();
             
            Logger::add_to_log('-- ERRORCODE: ' . $result->errorcode);
             if ($result->errorcode == '200') {
                    if ( get_config('local_klap', 'save_log') ) {
                        $log_element .= 'OK (ERRORCODE: ' . $result->errorcode . ' MSG: Sentencia enviada correctamente)';

                        if ( get_config('local_klap', 'savelog_ok_statement') ) {
                            $log_element .= ' - STATEMENT: ' . (string) $xApi->getStatement();
                        }
                    }
                    $tt = strtotime ($xApi->getStatement()->getTimestamp());
                    if ($this->last_execution < $tt) 
                        $this->last_execution = $tt;
                    if ($this->last_registry < $element->id) 
                        $this->last_registry = $element->id;
                    $this->removeReproccessId($element->id);
             } else {
                 $msg = json_decode($result->msg);
                 if ( isset($msg->message) ) {
                    $msg = $msg->message;
                 } else if (isset($msg->error->message)) {
                    $msg = $msg->error->message . '(in ' . $msg->error->file . ' - line ' . $msg->error->line . ')';
                 }
                 $this->addReproccessIds($element->id);
                 if (get_config('local_klap', 'save_log')) {
                    $log_element .= 'KO (ERRORCODE: ' . $result->errorcode . ' MSG: ' . $msg . ') - STATEMENT: ' . (string) $xApi->getStatement();
                 }
                    
            }

             $this->dataprovider->updateCollector ($this->name, $this->last_execution, $this->last_registry, $this->data);
             Logger::add_to_log($log_element); 
        } 
        Logger::add_to_log($this->name . ' ----------------------------------- FIN');
    }
    
    private function getXapi() {
       $auth = $this->dataprovider->getAuth();
       $validate_statements = $this->dataprovider->validateStatements();
       return new StatementRequest($auth->endpoint, $auth->type, $auth->chain, $validate_statements);
    }
    
    public function getReproccessIds (){
        return $this->data->reprocessids;
    }
    
    public function addReproccessIds ($ids=array() ){
        if ( is_array($ids) && count($ids)>0 )
            $this->data->reprocessids = array_merge($this->data->reprocessids, $ids);
        else {
            if ( !in_array($ids, $this->data->reprocessids) )
                $this->data->reprocessids[] =  $ids;
            
           
        }
    }
    
    public function removeReproccessId ($id) {
        if (($key = array_search($id, $this->data->reprocessids)) !== false) {
            unset($this->data->reprocessids[$key]);
        }
    }
    
    public function getStrategy (){
        switch ($this->data->strategy){
            case self::STRATEGY_DEFAULT: return STRATEGY_DEFAULT;
            case self::STRATEGY_LAST_ID: return STRATEGY_LAST_ID;
            case self::STRATEGY_LAST_TIMESTAMP: return STRATEGY_LAST_TIMESTAMP;
            case self::STRATEGY_ALL: return STRATEGY_ALL;
        }
        return STRATEGY_DEFAULT;
    }
    
    public function setStrategy ($strategy){  
        switch ($strategy){
            case self::STRATEGY_DEFAULT: $this->data->strategy = STRATEGY_DEFAULT;break;
            case self::STRATEGY_LAST_ID: $this->data->strategy = STRATEGY_LAST_ID;break;
            case self::STRATEGY_LAST_TIMESTAMP: $this->data->strategy = STRATEGY_LAST_TIMESTAMP;break;
            case self::STRATEGY_ALL: $this->data->strategy = STRATEGY_ALL;break;
        }
    }
    public function getLastExecution (){
        return $this->last_execution;
    }
    
    public function setLastExecution ($new_execution){
        $this->last_execution = $new_execution;
    }
    
    public function getLastRegistry (){
        return $this->last_registry;
    }
    
    public function setLastRegistry ($new_registry){
        $this->last_registry = $new_registry;
    }
    
    public function setData ($param, $value){
        $this->data->$param = $value;
    }
    
    public function getData ($param, $value){  
        $this->data->$param = $value;
    }
    
    public function getMaxRegistrys () {
        return ( defined ( 'static::MAX_REGS' ) ) ? static::MAX_REGS : null;
    }
    
    public function getFileLog (){
        return $this->filelog;
    }
    
    abstract public function getMaxId ();
    
    abstract function collectData();
    abstract function prepareStatement(StatementRequest $xapi, $element);
}