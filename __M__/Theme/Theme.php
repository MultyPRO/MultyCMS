<?php
namespace Theme;

class Theme extends \SQL {
    protected $con;
    protected $ThemeID;


    public function __construct($ThemeID = FALSE) {
        \Debug\Trace::mark();
        $this->con = $this->GetConnect();
        if($ThemeID){
            \Debug\Trace::mark();
            $this->ThemeID = $ThemeID;
        }
    }
    
    public function GetData($ThemeID = FALSE){
        \Debug\Trace::mark();
        if(!$this->ThemeID){
            \Debug\Trace::mark();
            $this->ThemeID = $ThemeID;
        }
        $query = "SELECT * FROM themes WHERE ThemeID = ?";
        $params = array($this->ThemeID);
        $res =  $this->SelectQuery($this->con, $query, $params);
        if($res){
            \Debug\Trace::mark();
            return $res[0];
        }
    }

    public function Get($name){
        $str    =   '/'.ltrim($_SERVER['QUERY_STRING'], 'C=');
        $exp    =   explode('/', $str);
        $end    =   end($exp);
        if(strstr($end, '.')){
            $exp    =   explode('.', $end);
            $ext    =   end($exp); 
        }
        switch ($ext) {
            case 'js'   :   $ctype  =   "application/javascript";           break;
            case 'css'  :   $ctype  =   "text/css";                         break;
            case 'jpg'  :
            case 'jpeg' :
            case "png"  :   $ctype  =   "image/png";                        break;
            case "gif"  :   $ctype  =   "image/gif";                        break;
            case 'bmp'  :   $ctype  =   "image/bmp";                        break;
            case 'otf'  :   $ctype  =   "application/font-sfnt";            break;
            case 'eot'  :   $ctype  =   "application/vnd.ms-fontobject";    break;
            case 'svg'  :   $ctype  =   "image/svg+xml";                    break;
            case 'ttf'  :
            case 'woff' :   $ctype="application/x-font-woff";               break;
        }
        if($ctype || $ok){
            $file = T.$name.urldecode('/'.ltrim($_SERVER['QUERY_STRING'], 'C='));
            if(is_readable($file)){
                //header("Access-Control-Allow-Origin: '*'");
                header("Content-type:  $ctype, charset=utf-8");
                readfile($file);
                exit();
            }
        }
        return FALSE;
    }
}