<?php
namespace Klap\xAPI;


/**
 * xAPI Management Class
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
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
        
        $this->proxyserver['url'] = ( !empty($proxyserver['url']) ) ? $proxyserver['url'] : null;
        $this->proxyserver['port'] = ( !empty($proxyserver['url']) ) ? ( (!empty($proxyserver['port'])) ? $proxyserver['port'] : 80 ) : null;
        
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
        
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, false);
        
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
	    curl_setopt($curl, CURLOPT_HTTP200ALIASES, (array)400); 

        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $this->auth);

        if  ( !empty($this->proxyserver['url']) ){
            curl_setopt($curl, CURLOPT_PROXYPORT, $this->proxyserver['port']);
            curl_setopt($curl, CURLOPT_PROXY, $this->proxyserver['url']);
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=UTF-8',
            'Accept: application/json',
            'X-Experience-API-Version: 1.0'
        ));

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");  
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $statement);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); 


        curl_setopt($curl, CURLOPT_URL, $url);

        $result = new \stdClass();
        $result->msg = curl_exec($curl);
        $result->errorcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
        curl_close($curl);
		return $result;
    }
    
    
    
}


