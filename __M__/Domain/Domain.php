<?php
namespace Domain;

class Domain extends \SQL{

    protected $con;
    public $data;
    private $activeApps = array(
        'site' => 1,
        'blog' => 1,
        'shop' => 1
    );

    public function __construct() {
        $this->con = $this->GetConnect();
    }
    
    public function getData($domain = FALSE){
        if($domain){
            if($domain * $domain > 0){
                $query = "SELECT * FROM domains WHERE DomainID = ?";
            } else {
                $query = "SELECT * FROM domains WHERE domain = ?";
            }
            $params = array(${'domain'});
            $res = $this->SelectQuery($this->con, $query, $params);
            if($res){
                $res[0]['appClass'] = $this->GetAppClass($res[0]['appType']);
                if($res[0]['appClass']){
                    $this->data = $res[0];
                    return array('status'=>1, 'data'=> $res[0]);
                } else {
                    return array('status'=>0, 'log'=>'This app class do not exist!');
                }
            } else {
                return array('status'=>0, 'log'=>'Can not take domain data!');
            }
        } else {
            return array('status'=>0, 'log'=>'Domain is require!');
        }
        return FALSE;
    }
        private function GetAppClass($AppType) {
            switch ($AppType) {
                case 1:
                    $App = 'site';
                    break;
                
                case 2:
                    $App = 'blog';
                    break;

                case 3:
                    $App = 'shop';
                    break;

                case 255:
                    $App = 'admin';
                    break;

                default:
                    $App = FALSE;
            }
            return  $App;
        }
    
    public function getSettings(){
        if($this->data['DomainID']){
            $query = "SELECT attr, value FROM DomainSettings WHERE DomainID = ? ORDER BY position";
            $params = array($this->data['DomainID']);
            $res = $this->SelectQuery($this->con, $query, $params);
            if($res){
                foreach ($res as $value) {
                    $Settings[$value['attr']] = $value['value'];
                }
                return $Settings;
            }
        }
        return FALSE;
    }
    
    public function validate($domain){
        if(!strstr($domain,'.')){
            $domain = $domain.".multy.pro";
        }
        return $domain;
    }
    
    public function getAllApps($DomainID = FALSE){
        if(!$DomainID){
            if(!$this->data['DomainID']){
                $r = array('status'=>0, 'log'=>'Not set DomainID!');
            } else {
                $DomainID = $this->data['DomainID'];
            }
        }
        if($DomainID){
            $moduls = explode(',', MODULS);
            $t = array();
            $params = array((int)$DomainID);
            foreach ($moduls as $value) {
                $query = "SELECT ".$value."ID FROM ".$value."s WHERE DomainID = ?";
                $res = $this->SelectQuery($this->con, $query, $params);
                if($res){
                    $t[$value] = $res[0][$value.'ID'];
                }
            }
            return $t;
        }
        return FALSE;
    }
    
    
    public function GetPlugins(){
        $query = "SELECT Pl_ID, PluginID FROM PluginContentMix WHERE ContentID = ? && type = ?";
        $params = array($this->DomainID, (int)0);
        $res = $this->SelectQuery($this->con, $query, $params);
        if($res){
            foreach ($res as $value) {
                $queryGetFile = "SELECT plugin_file FROM Plugins WHERE PluginID = ?";
                $paramsGetFile = array((int)$value['PluginID']); 
                $resGetFile = $this->SelectQuery($this->con, $queryGetFile, $paramsGetFile);
                if($resGetFile){
                    $Plugins[$resGetFile[0]['plugin_file']]['file'] = $resGetFile[0]['plugin_file'];
                    $Plugins[$resGetFile[0]['plugin_file']]['Pl_ID'] = $value['Pl_ID'];
                    $Plugins[$resGetFile[0]['plugin_file']]['PluginID'] = $value['PluginID'];
                    $Plugins[$resGetFile[0]['plugin_file']]['Settings'] = $this->GetPluginSettings($value['Pl_ID']);
                }
            }
            return $Plugins;
        }
    }
    
            private function GetPluginSettings($Pl_ID){
                $query = "SELECT attr, value FROM PluginSettings WHERE Pl_ID = ?";
                $params = array($Pl_ID);
                $res = $this->SelectQuery($this->con, $query, $params);
                if($res){
                    foreach ($res as $value) {
                        $settings[$value['attr']]  = $value['value'];
                    }
                    return $settings;
                }
            }
}
