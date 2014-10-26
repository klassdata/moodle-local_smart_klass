<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Group Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class Group extends xApiObject {
   
    /**
     * @access private
     * @var string 
     */
    private $name = null;
    
    /**
     * @access private
     * @var string 
     */
    private $member = array();
    

    /**
     * @access private
     * @var string 
     */
    private $groupid = null;
    
    
    
    /**
     * @access private
     * @var string 
     */
    private $id = null;
    
    /**
     * @access private
     * @var string 
     */
    private $membersids = array();
    
    /**
     * @access private
     * @var string 
     */
    private $data_provider = null;

    public function __construct($id=null) {
        //Creo el Proveedor de Datos de Moodle
        $this->data_provider = DataProviderFactory::build();
       
        //Si existe userid trata de obtener la información de usuario de la base de datos de moodle
        $this->setGroupid($id);
    }
    
    public function addMember ( $id=null, $type='mbox' ) {
        if ($id == $this->id) return false;
        $user = $this->getInverseFunctionalId ($id, $type);

        array_push($this->member, $user );
        array_push($this->membersids, $id );
        return $this;
    }
    
    public function setGroupid ($id=null, $type='mbox') {
        if ( in_array($id, $this->membersids) ) return;
        $user = $this->getInverseFunctionalId ($id, $type);
        $this->groupid = $user;
        return $this;
    }
    
    private function  getInverseFunctionalId ($id=null, $type='mbox') {
        $obj = new \stdClass();
        $user = $this->data_provider->get_user($id);
        
        switch ($type) {
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
    
    public function getGroupId () {
        return $this->groupid;
    }
    
    
    public function setName ($name) {
        $this->name = $name;
        return $this;
    }
    
    public function getName () {
        return $this->name;
    }

    public function expose () {
        $obj = new \stdClass();
        $class = new \ReflectionClass(get_class($this));
        $obj->objectType = $class->getShortName();
        if (!empty($this->name))
            $obj->name = $this->name;
        $obj->member = $this->member;
        
        if ( !empty($this->groupid) ){
            switch (key($this->groupid)) {
                case 'mbox':
                    $obj->mbox = $this->groupid->mbox;
                    break;
                case 'mbox_sha1sum':
                    $obj->mbox_sha1sum = $this->groupid->mbox_sha1sum;
                    break;
                case 'account':
                    $obj->account = $this->groupid->account;
                    break;
                case 'openID':
                    //No soportado en la versión actual
                    $obj->openID = 'unidentified';
                    break;
            }
        }
        
        return $obj;
    }

}