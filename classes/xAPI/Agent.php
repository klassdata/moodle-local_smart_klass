<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Agent Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class Agent extends xApiObject {
    
    /**
     * Full name of the Agent.
     * @access private
     * @var string 
     */
    private $name = null;

    /**
     * An Inverse Functional type to the Agent is a value of an Agent that is guaranteed to
     * only ever refer to that Agent or Identified Group. The posible types are:
     * - mbox: Gernerate a mailto URI with the required format is "mailto:email address". 
     *          The local part of the email address must be URI encoded. 
     *          Only email addresses that have only ever been and will ever be assigned to this Agent, 
     *          but no others, should be used for this property and mbox_sha1sum.
     * - mbox_sha1sum: Generate a string with the SHA1 hash of a mailto URI (i.e. the value of an mbox property). 
     *                 An LRS MAY include Agents with a matching hash when a request is based on an mbox.
     * - openID: Type URI. An openID that uniquely identifies the Agent.
     * - account: Generate an Object witn an user account on an existing system e.g. an LMS or intranet.
     * @access private
     * @var string 
     */
    private $agentid_type = "mbox";

    /**
     * An Inverse Functional Identifier unique to the Agent.                                                                             º
     * @access private
     * @var string 
     */
    private $agentid = null;

    /**
     * @access private
     * @var string 
     */
    private $id = null;
    
    /**
     * @access private
     * @var string 
     */
    private $data_provider = null;

    public function __construct($id=null) {
        //Si existe userid trata de obtener la información de usuario de la base de datos de moodle
        $this->id = $id;
        
       //Creo el Proveedor de Datos de Moodle
       $this->data_provider = DataProviderFactory::build();         
    }
    
    public function expose () {
        $obj = new \stdClass();
        $user = $this->data_provider->get_user($this->id);
        
        $class = new \ReflectionClass(get_class($this));
        $obj->objectType = $class->getShortName();  
        
        $obj->name = $user->firstname . ' ' . $user->lastname;
        
        switch ($this->agentid_type) {
            case 'mbox':
                $obj->mbox = 'mailto:'.$user->email;
                break;
            case 'mbox_sha1sum':
                $obj->mbox_sha1sum = sha1('mailto:'.$user->email);
                break;
            case 'account':
                $accountobj = new \stdClass();
                $accountobj->homePage = $this->data_provider->get_homepage();
                $accountobj->name = $user->username;
                $obj->account = $accountobj;
                break;
            case 'openID':
                $obj->openID = 'unidentified';
                break;
        }
        
        return $obj;
    }
}