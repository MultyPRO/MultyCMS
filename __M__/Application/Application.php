<?php
namespace Application;

abstract class Application extends \SQL{
    protected $con;                 // Connect with DB
    public $current_id;             // Do not change. SET in $this->GetBasicData. Държи кое е ID и какво е Wrapper за текущия момент
    public $current_mode;           // Site, Shop, Blog, Forum etc.
    private $content_id;            // SET in $this->GetContentBasicData()
    private $content_type;          // SET in $this->GetContentBasicData()
    public $ControllerID;
    public $ActionID;
    protected $LanguageID;
    protected $contentLanguageID;


    protected function __construct() {
        \Debug\Trace::mark();
        $this->con = $this->GetConnect();
    }

    public function getThemeID($a){
        \Debug\Trace::mark();
        if($a['class']){
            \Debug\Trace::mark();
            if($a['DomainID']){
                \Debug\Trace::mark();
                
                $class = $a['class'];
                $DomainID = $a['DomainID'];
                
                if( \Text::isOnlyLetters($class) ){
                    echo "Ura!";
                }
                $query = "SELECT ThemeID FROM ".$Class."s INNER JOIN ".$Class."Data ON ID = ".$Class."Data.AppID WHERE ".$Class."s.DomainID = ? && ".$Class."Data.LanguageID = ?";
                $params = array((int)$a['DomainID'], (int)$a['LanguageID']);
                $res = $this->GetQuery($this->con, $query, $params);
                if($res){
                    
                }
            }
        }
    }

    public function getData($a){
        \Debug\Trace::mark();
        if($a['class']){
            $class = $a['class'];
            if($a['DomainID']){
                \Debug\Trace::mark();
                $DomainID = $a['DomainID'];
                if($a['LanguageID']){
                    \Debug\Trace::mark();
                    $LanguageID = $a['LanguageID'];
                    if( \Text::isOnlyLatinLetters($class) ){
                        \Debug\Trace::mark();
                        $query = "SELECT * FROM ".$class."s INNER JOIN ".$class."data ON ".$class."s.ID = ".$class."data.AppID WHERE ".$class."s.DomainID = ? && ".$class."data.LanguageID = ?";
                        $params = array((int)$DomainID, (int)$LanguageID);
                        $res = $this->SelectQuery($this->con, $query, $params);
                        if($res){
                            \Debug\Trace::mark();
                            $uQ = "UPDATE ".$class."s SET lastVisit = ? WHERE ID = ". $res[0]['AppID'];
                            $uP = array(time());
                            $uR = $this->Query($this->con, $uQ, $uP);

                            $this->current_id = $res[0]["AppID"];
                            $this->current_mode = $class;
                            // Връща масива на таблицата от базата данни с име $mode."s" ($mode = 'Site' / $mode = 'Blog' / etc. )
                            return ['status'=>1, 'data'=>$res[0]];
                        } else {
                            \Debug\Trace::mark();
                        }
                    } else {
                        \Debug\Trace::mark();
                    }
                }
            }
        }
        return FALSE;
    }
    
    public function GetAppSettings(){
        $query = "SELECT attr, value FROM ".$this->current_mode."Settings WHERE ".$this->current_mode."ID = ? ORDER BY position";
        $params = array($this->current_id);
        $res = $this->SelectQuery($this->con, $query, $params);
        if($res){
            foreach ($res as $value) {
                $Settings[$value['attr']][] = $value['value'];
            }
            return $Settings;
        }
        return FALSE;
    }
    
    public function IsModul($PageID){
        $query = "SELECT value FROM Moduls WHERE PageID = ?";
        $params = array((int)$PageID);
        $res = $this->SelectQuery($this->con, $query, $params);
        if($res){
            switch ($res[0]['value']) {
                case 1:
                    $Modul = 'Blog';
                    break;
                
                case 2:
                    $Modul = 'Shop';
                    break;
                
                case 200:
                    $Modul = 'SearchFor';
                    break;
                
                case 255:
                    $Modul = 'Admin';
                    break;
                
                default:
                    $Modul = FALSE;
                    break;
            }
            return $Modul;
        }
        return FALSE;
    }
    
    
    public function GetContentData($a){
        \Debug\Trace::mark();
        if($a['ID']){
            \Debug\Trace::mark();
            if($a['AppID']){
                \Debug\Trace::mark();
                if($a['LanguageID']){
                    \Debug\Trace::mark();
                    $this->LanguageID = $a['LanguageID'];
                    switch ($this->current_mode) {
                        case 'site':
                            \Debug\Trace::mark();
                            $this->content_type = 'page';
                            break;

                        case 'shop':
                            \Debug\Trace::mark();
                            $this->content_type = 'product';
                            break;

                        default:
                            \Debug\Trace::mark();
                            \Debug\Bug::fix('Провери защо стана така');
                            break;
                    }
                    $t = $this->content_type;
                    $query = "SELECT * FROM ".$t."s "
                            . "INNER JOIN ".$t."data ON ".$t."s.ID = ".$t."data.ContentID "
                            . "WHERE ".$t."s.ID = ? && ".$t."s.AppID = ? && ".$t."data.LanguageID = ?";
                    $params = array((int)$a['ID'], (int)$a['AppID'], (int)$a['LanguageID']);
                    $res = $this->SelectQuery($this->con, $query, $params);
                    if($res){
                        \Debug\Trace::mark();
                        $this->content_id = $a['ID'];
                        $this->contentLanguageID = $res[0]['LanguageID'];// Ползва се от плъгините да си намерят ShortCodes -> value
                        $r['status'] = 1;
                        $r['data'] = $res[0];
                        return $r;
                    }
                    \Debug\Trace::mark();
                    $r['status'] = 0;
                    return $r;
                }
            }
        }
    }
    
    public function GetContentGallery(){
        $t = $this->content_type;
        $query = "SELECT gallery FROM ".$t."Gallery WHERE ".$t."ID = ?";
        $params = array($this->content_id);
        $res = $this->SelectQuery($this->con, $query, $params);
        if($res){
            return $res[0]['gallery'];
        }
        return FALSE;
    }

    







































    public function GetContentAsso(){
        $A = 'Assotiations';
        $ACM = 'AssoContentMix';
        $query = "SELECT $ACM.AssoID, $A.AssoTitle, $A.AssoIcon, $A.AssoDesc, $A.ParentID FROM $A INNER JOIN $ACM ON $A.AssoID = $ACM.AssoID WHERE $ACM.ContentID = ? && $ACM.ContentType = 1";
        $params = array($this->content_id);
        $res = $this->SelectQuery($this->con, $query, $params);
        if($res){
            $queryParent = "SELECT AssoID, AssoTitle, AssoIcon, AssoDesc FROM $A WHERE AssoID = ?";
            foreach ($res as $key => $value) {    
                $paramsParent = array((int)$value['ParentID']);
                $resParent = $this->SelectQuery($this->con, $queryParent, $paramsParent);
                if($resParent){
                    $Asso[$resParent[0]['AssoID']]['Icon'] = $resParent[0]['AssoIcon'];
                    $Asso[$resParent[0]['AssoID']]['Title'] = $resParent[0]['AssoTitle'];
                    $Asso[$resParent[0]['AssoID']]['Desc'] = $resParent[0]['AssoDesc'];
                    $Asso[$resParent[0]['AssoID']]['Sub'][] = $value;
                }
            }
            return $Asso;
        }
    }
    
    public function GetContentSettings(){
        $query = "SELECT attr, value FROM ".$this->content_type."Settings WHERE ".$this->content_type."ID = ? ORDER BY position";
        $params = array($this->content_id);
        $res = $this->SelectQuery($this->con, $query, $params);
        if($res){
            foreach ($res as $value) {
                $Settings[$value['attr']][] = $value['value'];
            }
            return $Settings;
        }
    }

    public function GetContentPlugins(){
        switch ($this->content_type) {
            case 'Page':
                $type = 1;
                break;
            case 'Posting':
                $type = 2;
                break;
            case 'Product':
                $type = 3;
                break;
            default:
                $type = 0;
                break;
        }
        $query = "SELECT Pl_ID, PluginID FROM ContentPluginMix WHERE ContentID = ? && type = ?";
        $params = array($this->content_id, $type);
        $res = $this->SelectQuery($this->con, $query, $params);
        if($res){
            foreach ($res as $value) {
                $queryGetFile = "SELECT pluginFile FROM Plugins WHERE PluginID = ?";
                $paramsGetFile = array((int)$value['PluginID']); 
                $resGetFile = $this->SelectQuery($this->con, $queryGetFile, $paramsGetFile);
                if($resGetFile){
                    $Plugins[$resGetFile[0]['plugin_file']]['file'] = $resGetFile[0]['pluginFile'];
                    $Plugins[$resGetFile[0]['plugin_file']]['Pl_ID'] = $value['Pl_ID'];
                    $Plugins[$resGetFile[0]['plugin_file']]['PluginID'] = $value['PluginID'];
                    $Plugins[$resGetFile[0]['plugin_file']]['settings'] = $this->GetPluginSettings($value['Pl_ID']);
                    $Plugins[$resGetFile[0]['plugin_file']]['shortCodes'] = $this->GetPluginShortCodes($value['Pl_ID']);
                }
            }
            return $Plugins;
        }
    }
    
            private function GetPluginSettings($Pl_ID){
                $query = "SELECT attr, value FROM ContentPluginSettings WHERE Pl_ID = ?";
                $params = array($Pl_ID);
                $res = $this->SelectQuery($this->con, $query, $params);
                if($res){
                    foreach ($res as $value) {
                        $settings[$value['attr']]  = $value['value'];
                    }
                    return $settings;
                }
            }
    
            private function GetPluginShortCodes($Pl_ID){
                $query = "SELECT SC.shortCode, SCD.value FROM PluginShortCodes SC INNER JOIN PluginShortCodeData SCD ON SC.ID_ = SCD.ID_ WHERE SC.Pl_ID = ? && SCD.LanguageID = ?";
                $params = array($Pl_ID, $this->contentLanguageID);
                $res = $this->SelectQuery($this->con, $query, $params);
                if(!$res){
                    $shortCodes['errorLang'] = true;
                    $params = array($Pl_ID, $this->LanguageID);
                    $res = $this->SelectQuery($this->con, $query, $params);
                }
                if(!$res){
                    $query = "SELECT SC.shortCode, SCD.LanguageID, SCD.value FROM PluginShortCodes SC INNER JOIN PluginShortCodeData SCD ON SC.ID_ = SCD.ID_ WHERE SC.Pl_ID = ?";
                    $params = array($Pl_ID);
                    $res = $this->SelectQuery($this->con, $query, $params);
                }
                if($res){
                    if($res[0]['LanguageID']){
                        $shortCodes['Lang'] = $res[0]['LanguageID'];
                    }
                    foreach ($res as $value) {
                        $shortCodes[$value['shortCode']]  = $value['value'];
                    }
                    return $shortCodes;
                }
                return FALSE;
            }
    

    public function IsSpecialPage($PageID){
        // Action
        // Controller - Ако няма контролер значи си е просто функция
        // SortCodes

        /*
         * SpecialPages:
         * 
         * ID
         * PageID   - Pages
         * ActionID - Actions
         */

        $querySelect = "SELECT * FROM SpecialPages WHERE PageID = ?";
        $paramsSelect = array((int)$PageID);
        $resSelect = $this->SelectQuery($this->con, $querySelect, $paramsSelect);
        if($resSelect){
            /*
             * Actions:
             * 
             * ActionID
             * action_func
             * ControllerID
             */

            $querySelectAction = "SELECT * FROM Actions WHERE ActionID = ?";
            $paramsSelectAction = array($resSelect[0]['ActionID']);
            $resSelectAction = $this->SelectQuery($this->con, $querySelectAction, $paramsSelectAction);
            if($resSelectAction){
                $action = $resSelectAction[0]['action_func'];
                /*
                 * Controllers:
                 * 
                 * ControllerID
                 * file
                 * is_public
                 */
                //echo __LINE__;

                if($resSelectAction[0]['ControllerID'] > 0){
                    $querySelectController = "SELECT file FROM Controllers WHERE ControllerID = ?";
                    $paramsSelectController = array($resSelectAction[0]['ControllerID']);
                    $resSelectController = $this->SelectQuery($this->con, $querySelectController, $paramsSelectController);
                    if($resSelectController){
                        $controller = $resSelectController[0]['file'];
                    }
                }

                    if($controller){
                        $f = C.$controller . ".php";
                    } else {
                        $f = C.$action . ".php";
                    }
                    if(is_readable($f)){
                        $this->ActionID = $resSelectAction[0]['ActionID'];
                        $this->ControllerID = $resSelectAction[0]['ControllerID'];
                        require_once $f;
                        if($controller){
                            $c = "\\Controller\\$controller";
                            $Controller = new $c($actionSC);
                            return $Controller->$action();
                        } else {
                            return @call_user_func($res4[0]['file'], $arr);
                        }
                    }
                    SystemLogs("Тази страница все още не функционира!", 'w');
                    return;
            }
        }
        return FALSE;
    }
            
    public function GetActionShortCodes($ActionID){
        $query = "SELECT S.short_code, T.value FROM ShortCodes S INNER JOIN ShortCodeTranslate T ON S.ShortCodeID = T.ShortCodeID  WHERE ActionID = ?";
        $params = array($ActionID);
        $res = $this->SelectQuery($this->con, $query, $params);
        if($res){
            return $res;
        }
    }

    
    
    
    public function CreateMenu($arr){
        global $Page;
        
        if($arr['pages']){
            foreach ($arr['pages'] as $v){
                $page_link = $Page->GiveMeLink($v['PageID'], $this->lang);
                $Menu .= '| <a   href="'.$page_link['PageLink'].'">'.$page_link['PageAnchor'].'</a> | ';
            }
        }
        return $Menu;
    }

    // OK Do not change. Извиква се от самия App или  Modul след като се съберат данните за да се поставят и съответните ShortCodes
    public function SetSC(){
        return $this->data['contentData']['SC'] = $this->SC;
    }
    
    // OK Do not change
    public function CNF(){
        return $this->data['contentData']['content_not_found'] = true;
    }
    
    public function GetCSS(){
        global $DATA;
        $query = "SELECT style_text FROM ProjectCSS WHERE name = ? && ( ProjectID = ? || ProjectID = 0 )";
        $params = array($DATA['queryParams'][1], $DATA['domainData']['ProjectID']);
        $res = $this->SelectQuery($this->con, $query, $params);
        if($res){
            $b = <<<B
   /***************************************************************************/
  /*                    Powered by The Best CMS Engine                        */
 /*                     Multy.PRO Version 2 rEVOLUTION                        */
/*                          since Nov 2014                                    */
/******************************************************************************/
/*                      Contact us - Vidov@Multy.PRO                          */
/******************************************************************************/
\n
B;
            $e = <<<E
\n
/******************************************************************************/
/*                              Viva Multy.PRO                                */
/******************************************************************************/
E;
            $style = $res[0]['style_text'];
            $sub = substr($style, 0, 8);
            if(strstr($sub,'//')){
                $style = file_get_contents($style);
            }
            
            exit($b.$style.$e);
        }
        myExit('No project style');
    }

    public function GetJS(){
        global $DATA;
        $query = "SELECT script_text FROM ProjectJS WHERE name = ? && ( ProjectID = ? || ProjectID = 0 )";
        $params = array($DATA['queryParams'][1], $DATA['domainData']['ProjectID']);
        $res = $this->SelectQuery($this->con, $query, $params);
        if($res){
            $b = <<<B
   /***************************************************************************/
  /*                    Powered by The Best CMS Engine                        */
 /*                     Multy.PRO Version 2 rEVOLUTION                        */
/*                          since Nov 2014                                    */
/******************************************************************************/
/*                      Contact us - Vidov@Multy.PRO                          */
/******************************************************************************/
\n
B;
            $e = <<<E
\n
/******************************************************************************/
/*                              Viva Multy.PRO                                */
/******************************************************************************/
E;
            $script = $res[0]['script_text'];
            $sub = substr($script, 0, 8);
            if(strstr($sub,'//')){
                $script = file_get_contents($script);
            }
            
            exit($b.$script.$e);
        }
        myExit('No project javascript');
    }    
























    public function CreateContent(){
        $query = "SELECT layout_code FROM Layouts WHERE LayoutID = ?";
        $params = array($this->data['contentData']['Basic']['LayoutID']);
        $res = $this->SelectQuery($this->con, $query, $params);
        
        $layout = htmlspecialchars_decode($res[0]['layout_code']);
        $PAGE_API = array(
            "[PAGE-Title]",
            "[PAGE-Text]"
        );
        
        $vars = array(
            $this->data['contentData']['More']['page_title'],
            $this->data['contentData']['More']['page_text']
        );
        
        $text = str_replace($PAGE_API, $vars, $layout);
        return $text;
    }
    
    
    public function GetContentChain(){
        if(!$this->data['queryParams']){
            return;
        }
        $arr = $this->data['queryParams'];

        $count = count($arr);
        $contr = 1;
        //echo $arr ."-". $separator ."-". $site_id;
        if( ($count == 1) ){
            //$this->data['contentData']['chain'] = $baza;
        }
        
        $Sid = $this->data['AppData']['Basic']['SiteID'];
        if($this->data['languageData']['LanguageID']){
            $Sid = $this->data['languageData']['LanguageID'];
        } else {
            $Sid = $this->data['projectData']['LanguageID'];
        }
        
        $query = "SELECT PageDataMix.PageID, PageDataMix.LanguageID, Pages.PageID FROM Pages RIGHT JOIN PageDataMix ON Pages.PageID = PageDataMix.PageID WHERE Pages.SiteID = ? && Pages.ParentID = 0 && PageDataMix.page_title = ?";
        $params = array($Sid, $arr[0]);
        $get_page_info = $this->SelectQuery($this->con, $query, $params);
        $mine_id = $get_page_info[0]['PageID'];
        $lang_id = $get_page_info[0]['LanguageID'];
        //echo $mine_id;
        $URL = '/'.str_replace(' ', '+', $arr[0]);
        $JJ = true; $i = 1;
        $control = count($this->data['queryParams']);
        while($JJ == true){
            $query = "SELECT Pages.PageID, PageDataMix.LanguageID FROM Pages INNER JOIN PageDataMix ON Pages.PageID = PageDataMix.PageID WHERE PageDataMix.page_title = ? && Pages.ParentID = ?";
            $params = array($arr[$i], $mine_id);
            $get_page_info = $this->SelectQuery($this->con, $query, $params);
            
            if($get_page_info){    
                $control--;
            
                if($i == 1){
                    $baza =  "<a href='/".  str_replace(' ', '+',$arr[0])."'>".$arr[0]."</a>";
                }
                $mine_id = $get_page_info[0]['PageID'];
                $lang_id = $get_page_info[0]['LanguageID'];
                $URL .= "/".str_replace(' ', '+', $arr[$i]);
                if($i != $count-1){
                    $baza .= ">> <a href='".$URL."'>".$arr[$i]."</a>";
                } else {
                    $baza .= ">> ". $arr[$i];
                }
            } else {
                $JJ = FALSE;
            }
            
            $i++;
            if($i > 30){
                $JJ = FALSE;
            }
        }
        
        $this->ReqSpecFile($mine_id);
        //echo $mine_id;
        if($control != 1){
            $this->PNF();
            return;
        }
        $this->content_current_id = $mine_id;
        $this->content_current_lang = $lang_id;
        
        $this->data['contentData']['content_url'] = $URL;
        $this->data['contentData']['chain'] = $baza;
     
    }
    
    
    public function GetURLMarker(){
        
        if(count($this->data['queryParams']) == 1){
            
            $query = "SELECT PageDataMix.PageID, Pages.PageID FROM Pages RIGHT JOIN PageDataMix ON Pages.PageID = PageDataMix.PageID WHERE Pages.SiteID = ? && Pages.ParentID = 0 && PageDataMix.page_title = ?";
            $params = array($this->data['domainData']['DomainID'], $this->data['queryParams'][0]);
            $res = $this->SelectQuery($this->con, $query, $params);
            if($res){
                $query2 = "SELECT value FROM SpecialPages WHERE PageID = ?";
                $params2 = array($res[0]['PageID']);
                $res2 = $this->SelectQuery($this->con, $query2, $params2);
                if($res2){
                    $this->data['contentData']['SpecialPage'] = $res2[0]['value'];
                    $this->ReqSpecFile($res2[0]['value']);
                    //$this->modul =  $res2[0]['value'];
                    //$this->contentData['ComtentIsModul'] = $this->modul;
                    //array_shift($this->queryParams);
                }
             //   echo $this->modul;
            }
        }
        
    }
    
    public function Set($var, $val){
        $this->$var = $val;
    }
    

   
    
////////////////////////////////////////////////////////////////////////////////
/*                                                                            */
////////////////////////////////////////////////////////////////////////////////
    



    
}