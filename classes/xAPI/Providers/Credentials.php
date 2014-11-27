<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Credentials Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Credentials {
    
     
    const MAX_TIME = 20;
    
    private $dataprovider;
    private static $credentials;
      
    public static function getProvider () {

        if ( !(self::$credentials instanceof Credentials) ){
            self::$credentials = new Credentials();
            self::$credentials->dataprovider = DataProviderFactory::build();
        }
        return  self::$credentials;
    }
    
    public function updateCredentials () {
           
        $access_token = $this->dataprovider->getConfig('oauth_access_token');

        if ($access_token == false) return false;

        $server = $this->dataprovider->getConfig('oauth_server') . '/lms/payload';

        $curl = new Curl;
        $output = $curl->get( $server, array('access_token' => $access_token));
        
        if ( isset($output->error) ) $this->refreshToken ();
        $output = json_decode ($output);
        if ( isset($output->lrs_endpoint) ) 
            set_config('lrs_endpoint', $output->lrs_endpoint, 'local_smart_klass');
        if ( isset($output->lrs_username) )
            set_config('lrs_username', $output->lrs_username, 'local_smart_klass');
        if ( isset($output->lrs_password) )
            set_config('lrs_password', $output->lrs_password, 'local_smart_klass');
        
        if ( isset($output->dashboard_url) )
            set_config('dashboard_endpoint', $output->dashboard_endpoint, 'local_smart_klass');
        
        
        if ( isset($output->tracker_endpoint) )
            set_config('tracker_endpoint', $output->tracker_endpoint, 'local_smart_klass');
        if ( isset($output->tracker_id) )
            set_config('tracker_id', $output->tracker_id, 'local_smart_klass');

    }
    
    
    public function getCredentials () {
        global $SESSION;
        
        $cicle = (integer) $this->dataprovider->getConfig('credential_cicle');
        if ($cicle == 0 ||  $cicle <= time()) {
            $this->dataprovider->setConfig('credential_cicle', time() + self::MAX_TIME);
            $this->updateCredentials();      
        }
       
        $credentials = new \stdClass();
        $credentials->lrs_endpoint = $this->dataprovider->getConfig('lrs_endpoint');
        $credentials->lrs_username = $this->dataprovider->getConfig('lrs_username');
        $credentials->lrs_password = $this->dataprovider->getConfig('lrs_password');
        $credentials->dashboard_endpoint = $this->dataprovider->getConfig('dashboard_endpoint');
        $credentials->tracker_endpoint = $this->dataprovider->getConfig('tracker_endpoint');
        $credentials->tracker_id = $this->dataprovider->getConfig('tracker_id');
        
        return $credentials;
    }
    
    public function refreshToken(){   
        $refresh_token = $this->dataprovider->getConfig('oauth_refresh_token');

        if ($refresh_token == false) return false;

        $server = $this->dataprovider->getConfig('oauth_server') . '/oauth/refresh_token';

        $curl = new Curl;
        
        $params = array ( 
                            'client_id' => $this->dataprovider->getConfig('oauth_client_id'),
                            'client_secret' => $this->dataprovider->getConfig('oauth_client_secret'),
                            'refresh_token' => $this->dataprovider->getConfig('oauth_refresh_token'),
                            'grant_type' => 'refresh_token',
        );
        $output = $curl->post( $server, $params);
        
        if ( isset($output->error) ) return false;
        
        if ( isset($output->refresh_token) ) 
            set_config('oauth_refresh_token', $output->refresh_token, 'local_smart_klass');
            $this->dataprovider->setConfig('oauth_access_token', $output->access_token);
        if ( isset($output->access_token) )
            $this->dataprovider->setConfig('oauth_access_token', $output->access_token);
        
        $this->updateCredentials();
    }
    
   
    
    
}
