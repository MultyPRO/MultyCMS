<?php
namespace Application;

class Site extends Application implements iApp{
    
    public $ContentType     = "Page";// Ползва се от HTML -> HeaderTranslate за да извади всички преводи за тази страница
    public $ContentTypeID   = 1;
    public $FirstPageID;// Ползва се за да се провери дали е модул от Wrapper->IsModul;
    public $ContentLanguageID;
    public $SC = array(
        "[Title]",
        "[Pic]",
        "[Text]",
        "[Date]",
        "[Hits]",
        "[url]",
        "[Author]",
        "[Crumbs]",
        "[Asso]"
    );
    
    public $SC_ = array();
    
    public function __construct() {
        parent::__construct();
    }
    
    
    public function GetContentID($a){
        \Debug\Trace::mark();
        if($a['crumbs']){
            \Debug\Trace::mark();
            if($a['AppID']){
                \Debug\Trace::mark();
                $crumbs = 0;// Ползва се от BodyContent->Crumbs за да отреже невалидните. Виж там
                $count = count($a['crumbs']);
                $query = "SELECT P.ID, PD.LanguageID FROM pages P INNER JOIN pageData PD ON P.ID = PD.ContentID WHERE P.ParentID = 0 && PD.title = ? && P.AppID = ?";
                $params = array($a['crumbs'][0], (int)$a['AppID']);
                $res =  $this->SelectQuery($this->con, $query, $params);
                if($res){
                    \Debug\Trace::mark();
                    $crumbs++;
                    $ParentID = $res[0]['ID'];
                    $this->LanguageID = $res[0]['LanguageID'];
                    $this->FirstPageID = $ParentID;
                    if($count == 1){
                        \Debug\Trace::mark();
                        return [
                            'status'        => 1,
                            'crumbsCount'   => $crumbs, /* За какво се ползва? */
                            'ID'            =>  $res[0]['ID']
                        ];
                    }
                    for($i=1; $i < $count; $i++){
                        \Debug\Trace::mark();
                        $query2 = "SELECT P.ID, PD.LanguageID FROM pages P INNER JOIN pagedata PD ON P.ID = PD.ContentID WHERE P.ParentID = ? && PD.title = ? && P.AppID = ?";
                        $params2 = array($ParentID, $a['crumbs'][$i], $a['AppID']);
                        echo $query2;
                        var_dump($params2);
                        $res2 =  $this->SelectQuery($this->con, $query2, $params2);   
                        if($res2){
                            \Debug\Trace::mark();
                            $crumbs++;
                            $r['status'] = 1;
                            $r['crumbsCount'] = $crumbs;
                            $r['ID'] = $res2[0]['ID'];
                            $r['ParentID'] = $ParentID;
                            $ParentID = $res2[0]['ID'];
                            $this->LanguageID = $res2[0]['LanguageID'];
                        } else {
                            \Debug\Trace::mark();
                            $r['status'] = 0;
                            $r['crumbsCount'] = $crumbs;
                            $r['ID'] = $ParentID;
                        }
                    }
                    \Debug\Trace::mark();
                    return $r;
                } else {
                    \Debug\Trace::mark();
                    \Debug\Error::alert($a);
                    \Debug\Bug::fix($a);
                }
            } else {
                \Debug\Bug::fix($a);
            }
        }
        \Debug\Trace::mark();
        \Debug\Bug::fix($a);
        return FALSE;
    }
    
    
    
    
    
    
    
}