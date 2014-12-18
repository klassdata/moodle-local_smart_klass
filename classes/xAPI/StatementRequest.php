<?php
namespace SmartKlass\xAPI;


/**
 * xAPI Management Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class StatementRequest {
    
    /**
     * @access private
     * @var string 
     */
    private $statement = null;
    
    
    private $actortype = 'agent';
    
    
    private $actorids = array ();
    
    
    private $endpoint;
    
    private $proxyserver = array();
    
    private $authtype;
    
    private $auth;
    
    private $validate_statements;
    
    
    
    
    public function __construct( $endpoint=null, $authtype='basic', $auth=null, $validatestatements=false, $proxyserver=array()) {
        //Autoload library class
        require_once (dirname(__FILE__) . '/Autoloader.php');
        Autoloader::register();
        
        
        $this->statement = new Statement();
        
        $this->endpoint = $endpoint;
        $this->authtype = $authtype;
        $this->auth = $auth;
        
        $this->validate_statements = (true === $validatestatements);
         
       $this->proxyserver = ( empty($proxyserver['proxyhost']) ) ? null : $proxyserver;
        
        
    }
    
    
    //Retorna el Objeto json correspondiente del Agent instanciado
    public function __toString() {
        $obj = new \stdClass();

        return $obj;
    }

    public function setActor ($id, $type='agent') {
        
        if ($this->actortype == 'group') {
            $actor = new Group($id);
        } else {
            $actor = new Agent($id);
        } 
        $this->statement->setActor( $actor );
        return $this;
    }
    
    public function setVerb ($id='voided'){
        $verb = new Verb($id);
        $this->statement->setVerb( $verb ); 
        return $this;
    }
    
    
    public function setObject($object){  
        $this->statement->setObject( $object );
        return $this;
    }
    
    public function setResult ($param, $value) {
        $this->statement->setResult($param, $value);
        return $this;
    }
    
    public function setContext ($param, $value) {
        $this->statement->setContext($param, $value);
        return $this;
    }
    
    public function setTimestamp ($ts) {
        $this->statement->setTimestamp($ts);
        return $this;
    }
    
    public function getStatement() {
        return $this->statement;
    }
    
    
    
    
    public function sendStatement (){
        $url = $this->endpoint . 'statements';
        $statement = (string) $this->statement;
        
        //Validate statements
        if (true === $this->validate_statements) {
            $validate = new xAPIValidation();
            $validate_result = $validate->runValidation( json_decode($statement, true) );

            if ($validate_result['status'] == 'failed') {
                $result = new \stdClass();
                $result->msg = 'Validate statement fails: ' . implode(' ||| ' . $validate_result['status']);
                $result->errorcode = 400;
                return $result;

            }
        }
        return $this->processRequest($url, $statement);
    }
    
    public function getLRSversion () {
        $url = $this->endpoint . 'about';
        return $this->processRequest($url, null, 'GET');
    }
      
    private function processRequest ($url, $params=null, $method='POST') {
        
        $curl = new Curl ();
        
        $curl->setHeader('Content-Type', 'application/json; charset=UTF-8');
        $curl->setHeader('Accept', 'application/json');
        $curl->setHeader('X-Experience-API-Version', '1.0.');
        
        $curl->setOpt(CURLOPT_FOLLOWLOCATION, 1);
        
        $curl->setOpt(CURLOPT_HEADER, false);
        $curl->setOpt(CURLOPT_NOBODY, false);
        
        $curl->setOpt(CURLOPT_FAILONERROR, false);
	$curl->setOpt(CURLOPT_HTTP200ALIASES, (array)400); 

        $curl->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $curl->setOpt(CURLOPT_USERPWD, $this->auth);
        
        if (!is_null($this->proxyserver)) $curl->setProxy ($this->proxyserver);
        
        $curl->setOpt(CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=UTF-8',
            'Accept: application/json',
            'X-Experience-API-Version: 1.0.0'
        ));
        
        $result = new \stdClass();
        switch ($method) {
            case 'POST':
                $result->msg = $curl->post($url, $params);
                $result->errorcode = $curl->http_status_code;
                break;
            
            case 'GET':
                $result->msg = $curl->get($url, $params);
                $result->errorcode = $curl->http_status_code;
                break;
            
            case 'PUT':
                $result->msg = $curl->put($url, $params);
                $result->errorcode = $curl->http_status_code;
                break;
            
            case 'DELETE':
                $result->msg = $curl->delete($url, $params);
                $result->errorcode = $curl->http_status_code;
                break;
        }
		
        $curl->close();
	return $result;
        
    }
    
}


