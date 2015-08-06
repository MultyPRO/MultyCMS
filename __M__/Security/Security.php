<?php
namespace Security;

class Security extends \SQL{
    
    protected $con;
    
    public function __construct() {
        $this->con = $this->GetConnect();
    }
		
    public function checkForBan($ip){
        
        $banIP = array(
            "151.237.15.147",
            "151.237.15.151",
            '94.156.86.109',
            '84.54.152.255',
            '82.146.27.47'
        );
        
        if(in_array($ip, $banIP)){
            return TRUE;
        }
        
        return FALSE;
    }
    
    private function checkThisIP($ip){
        return false;
    }

    private function checkThisSession($S_id){
        return false;
    }

    public function alert(){
            exit(__FUNCTION__);
            // Ot tuk se snemat absolutno vsi4ki danni za problema.
            // Koi ot kyde go e vikal i t.n. kato se polzvat Var(), Session() i dr.
    } 
}