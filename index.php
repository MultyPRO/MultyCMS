<?php
define("START_SCRIPT_TIME", microtime(true));// Ползва се за да се определи времето за генериране на страницата. Останалата част е на края на тази страница.
////////////////////////////////////////////////////////////////////////////////
/*                    SYSTEM SETTINGS                                         */
////////////////////////////////////////////////////////////////////////////////
require_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'__config__.php';


//Показва в браузъра таблицата в която е пътя на програмата до счупеното място
define('DEBUG', 1);

//Ако е включена тази опция показва в браузъра стъпките на програмата.
define('TRACE', 1);


////////////////////////////////////////////////////////////////////////////////
/*                         LEVEL Security                                     */
////////////////////////////////////////////////////////////////////////////////
$Secutity   =   Obj::create("\Security\Security");
if($Secutity->checkForBan($_SERVER['REMOTE_ADDR'])){
    \Debug\Trace::mark();
    exit("<center>Вашият достъп до тази страница е ограничен поради засечени злоупотреби.<br />Ако според вас е станала грешка, моля уведомете ме чрез скайп: savagrup.<br /><br /><br /><br /> Поздрави,<br />Алекс Видов");
}

////////////////////////////////////////////////////////////////////////////////
/*                         LEVEL System                                       */
////////////////////////////////////////////////////////////////////////////////
$AddressBar                                                                     =   Obj::create("\AddressBar\AddressBar");
if($AddressBar->domain){
    \Debug\Trace::mark();
    $Domain                                                                     =   Obj::create("\Domain\Domain");

////////////////////////////////////////////////////////////////////////////////
/*                       Намиране на данните за домейна                       */
    //DomainID
    //appClass
////////////////////////////////////////////////////////////////////////////////    
    if($AddressBar->subDomain){
        \Debug\Trace::mark();
        $d                                                                      =   $Domain->getData($AddressBar->subDomain);
    } else {
        \Debug\Trace::mark();
        $d                                                                      =   $Domain->getData($AddressBar->domain);
    }
    if($d['status'] == 1){
        \Debug\Trace::mark();
        $DATA['domainData']                                                     =   $d['data'];
        $AppClass                                                               =   "\Application\\".$DATA['domainData']['appClass'];
        $App                                                                    =   new $AppClass();
        if($App){
            \Debug\Trace::mark();
            $Language                                                           =   Obj::create("\Language\Language");
            
////////////////////////////////////////////////////////////////////////////////
/*                       Намиране на данните за езика                         */
    //LanguageID
////////////////////////////////////////////////////////////////////////////////    
            if($AddressBar->language){
                \Debug\Trace::mark();
                if($Language->validate(['lang'=>$AddressBar->language])){
                    \Debug\Trace::mark();
                    $l                                                          =   $Language->getData(['lang' => $AddressBar->language]);
                } else {
                    \Debug\Trace::mark();
                    \Debug\Error::alert(['log'=>'Невалиден език']);
                }
            } 
            if($l['status'] == 0){
                \Debug\Trace::mark();
                $l                                                              =   $Language->getData(['lang' => $DATA['domainData']['LanguageID']]);
            }
            if($l['status'] == 1){
                \Debug\Trace::mark();
                $DATA['languageData']                                           =   $l['data'];
                
////////////////////////////////////////////////////////////////////////////////
/*                       Намиране на данните за приложението                  */
    //AppID
    //ThemeID
    //HomeID
////////////////////////////////////////////////////////////////////////////////    
                $a                                                              =   $App->getData([
                                                                                                    'class'     => $DATA['domainData']['appClass'],
                                                                                                    'DomainID'  => $DATA['domainData']['DomainID'],
                                                                                                    'LanguageID'=> $DATA['languageData']['LanguageID']
                                                                                                ]);
                if($a['status'] == 1){
                    \Debug\Trace::mark();
                    $DATA['appData']                                            =   $a['data'];
                    if($DATA['appData']){
                        \Debug\Trace::mark();

////////////////////////////////////////////////////////////////////////////////
/*                       Намиране на данните на съдържанието                  */
    //ContentID
    //layoutType
////////////////////////////////////////////////////////////////////////////////    
                        if($AddressBar->crumbs){
                            \Debug\Trace::mark();
                            $DATA['crumbs']                                     =   $AddressBar->crumbs;
                            $c                                                  =   $App->getContentID([
                                                                                                    'crumbs'    => $DATA['crumbs'],
                                                                                                    'AppID'     => $DATA['appData']['AppID']
                                                                                                    ]);
                        }
                        if($c['status'] == 0){
                            \Debug\Trace::mark();
                            $c['ID']                                            =   $DATA['appData']['HomeID'];
                        }
                        if($c['ID']){
                            \Debug\Trace::mark();
                            $cD                                                 =   $App->getContentData([
                                                                                                            'ID'        =>  $c['ID'],
                                                                                                            'AppID'     =>  $DATA['appData']['AppID'],
                                                                                                            'LanguageID'=>  $DATA['languageData']['LanguageID']
                                                                                                        ]);
                            if($cD['status'] == 1){
                                \Debug\Trace::mark();
                                $DATA['contentData']                                =   $cD['data'];
                                if($DATA['contentData']){
                                    \Debug\Trace::mark();
                                    /*
                                     * Вече имам:
                                     * domainData
                                     * appData
                                     *   ThemeID
                                     * contentData
                                     *   layoutType
                                     */
                                    // Тук искам да се показват снимките от този проект
                                    // $Image->Get();
                                    $Theme                                          =   new \Theme\Theme();
                                    $themeData                                      =   $Theme->getData($DATA['appData']['ThemeID']);
                                        // Get theme's css; js; etc...
                                            $Theme->get($themeData['name']);
                                    readfile(T.$themeData['name'].DIRECTORY_SEPARATOR.'index.html');
                                    $DATA['themeData']                              =   $themeData;
                                    $_SESSION['DATA']                               =   $DATA;
                                    \Debug\Bug::boom($DATA);
                                } else {
                                    \Debug\Bug::boom(array('log'=>'Opa!', 'arr' => $DATA));
                                }
                            } else {
                                \Debug\Bug::boom(array('log'=>'Opa!', 'arr' => $DATA));
                            }
                        } else {
                            \Debug\Bug::boom(array('log'=>'Не е намерено ID-то на съдеъжанието!', 'arr' => $DATA));
                        }
                    }
                }
            } else {
                \Debug\Bug::boom(array('log'=>'App data is not available!', 'arr' => $DATA));
            }
        } else {
            \Debug\Bug::boom(array('log'=>'Opa!', 'arr' => $DATA));
        }
    } else {
        \Debug\Bug::boom(array('log'=>'Opa!', 'arr' => $DATA));
    }
} else {
    \Debug\Trace::mark();
    \Debug\Error::alert(['log' => 'domainNotFound']);
    $d                                                                      =   $Domain->getData(ROOT_DOMAIN);
    \Debug\Bug::boom(array('log'=>'Включва ROOT_DOMAIN!', 'arr' => $DATA));
}

exit();