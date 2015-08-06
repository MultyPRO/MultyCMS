<?php
namespace Language;

class Language extends \SQL{
    
    protected $con;
    
    public function __construct() {
        \Debug\Trace::mark();
        $this->con = $this->GetConnect();
    }
    
    public function getData($a = FALSE){
        \Debug\Trace::mark();
        if($a['lang']){
            \Debug\Trace::mark();
            if( ($a['lang'] * $a['lang'] ) > 0){
                \Debug\Trace::mark();
                $query = "SELECT * FROM languages L INNER JOIN flags F ON L.FlagID = F.FlagID WHERE L.LanguageID = ?";
            } else {
                \Debug\Trace::mark();
                $query = "SELECT * FROM languages L INNER JOIN flags F ON L.FlagID = F.FlagID WHERE L.abbr = ?";
            }
            $params = array($a['lang']);
            $res = $this->SelectQuery($this->con, $query, $params);
            if($res){
                \Debug\Trace::mark();
                return ['status' => 1, 'data' => $res[0]];
            }
            \Debug\Trace::mark();
            return ['status' => 0, 'log' => 'languageNotFound'];
        } else {
            \Debug\Bug::fix(['log'=>'Не е зададен език при извикването на този метод.']);
            return ['status' => 0, 'log' => 'languageNotSet'];
        }
        
        
        
    }
 
    public function validate($a){
        \Debug\Trace::mark();
        if($a['lang']){
            \Debug\Trace::mark();
            if((int)$LanguageID && $LanguageID > 0){
                \Debug\Trace::mark();
                $query = "SELECT LanguageID FROM languages WHERE LanguageID = ?";
            } else {
                \Debug\Trace::mark();
                $query = "SELECT abbr FROM languages WHERE abbr = ?";
            }
            $params = array($a['lang']);
            $res = $this->SelectQuery($this->con, $query, $params);
            if($res){
                \Debug\Trace::mark();
                return TRUE;
            } else {
                \Debug\Trace::mark();
                return FALSE;
            }
        }
        \Debug\Trace::mark();
        \Debug\Bug::fix(['log'=>'Не е зададен език при извикването на този метод.']);
    }
    
    public function GetAllLanguages(){
        $query = "SELECT * FROM Languages";
        $params = array(TRUE);
        $res = $this->SelectQuery($this->con, $query, $params);
        if($res){
            return $res;
        }
        return FALSE;
    }
}
