<?php
namespace AddressBar;

class AddressBar {
    /*
     * Този клас работи само и единствено с адрес бара на браузъра
     * Всички методи са за манипулацията му
     */
    
    public $ssl;
    public $www;
    public $language;
    public $subDomain;
    public $domain;
    public $domainExt;
    public $crumbs;
    public $hash;
    
    private $host;


    public function __construct() {
        \Debug\Trace::mark();
        $this->ssl          =   $this->checkForSSL();
        $this->www          =   $this->checkForWWW();
        $this->language     =   $this->getLanguage();
        $this->subDomain    =   $this->getSubDomain();
        $this->domain       =   $this->getDomain();
        $this->domainExt    =   $this->getDomainExt();
        $this->crumbs       =   $this->getCrumbs();
        $this->hash         =   $this->getHash();
    }
        
        private function checkForSSL(){
            \Debug\Trace::mark();
            if(isset($_SERVER['HTTPS'])){
                \Debug\Trace::mark();
                return TRUE;
            }
            \Debug\Trace::mark();
            return FALSE;
        }
        
        private function checkForWWW(){
            \Debug\Trace::mark();
            if(strstr($_SERVER['HTTP_HOST'], 'www')){
                \Debug\Trace::mark();
                return TRUE;
            }
            \Debug\Trace::mark();
            return FALSE;
        }
        
        private function getLanguage(){
            \Debug\Trace::mark();
            // Връща абривиатурата на езика в адрес бара ако има такава
            // Абривиатурата на езика е само от два символа.
            // Не го интересува дали езика е валиден
            // 
            // http://multy.pro
            // $AddressBar->GetLanguage() <- return FALSE
            // 
            // http://en.multy.pro
            // $AddressBar->GetLanguage() <- return en
            //
            // http://eng.multy.pro
            // $AddressBar->GetLanguage() <- return FALSE

            if(!$this->host){
                \Debug\Trace::mark();
                $this->host();
            }
            if(count($this->host) > 2 && strlen(end($this->host)) == 2){
                \Debug\Trace::mark();
                return end($this->host);
            }
            \Debug\Trace::mark();
            return FALSE;
        }

        private function getSubDomain(){
            \Debug\Trace::mark();
            // Връща под-домейна ако има такъв
            
            if(!$this->host){
                \Debug\Trace::mark();
                $this->host();
            }
            if( ( count($this->host) > 2 ) && ( strlen($this->host[2]) > 2 ) ){
                \Debug\Trace::mark();
                return $this->host[2];
            }
            \Debug\Trace::mark();
            return FALSE;
        }
        
        private function getDomain(){
            \Debug\Trace::mark();
            // Връща домейна и само домейна
            // Пренебрегва ако има език или суб домейн и връща само домейна

            if(!$this->host){
                \Debug\Trace::mark();
                $this->host();
            }
            \Debug\Trace::mark();
            return $this->host[1].'.'.$this->host[0];
        }
    
        private function getDomainExt(){
            \Debug\Trace::mark();
            // Връща окончанието на домейна .com; .pro; .net etc.
            if(!$this->host){
                \Debug\Trace::mark();
                $this->host();
            }
            \Debug\Trace::mark();
            return $this->host[0];
        }
        
        private function getCrumbs(){
            \Debug\Trace::mark();
            // Връща всички след наклонената черта в URL адреса
            if(isset($_GET['C'])){
                \Debug\Trace::mark();
                $GET = rtrim(strip_tags($_GET['C']),'/,"');
                $GET = explode('/', $GET);
                $g = $GET[count($GET)-1];
                $g = explode("*", $g);
                if(count($g) > 0){
                    \Debug\Trace::mark();
                    $GET[count($GET)-1] = $g[0];
                    array_shift($g);
                    $this->hash = $g;
                }
                \Debug\Trace::mark();
                return $GET;
            }
            return FALSE;
        }
        
        public function getHash(){
            \Debug\Trace::mark();
            // Връща хашовете в URL-то
            // Хаша в системата се обозначава със символа *
            if($this->hash){
                \Debug\Trace::mark();
                return $this->hash;
            } 
            \Debug\Trace::mark();
            return FALSE;
        }
        
        private function host(){
            \Debug\Trace::mark();
            $host = $_SERVER['HTTP_HOST'];
            $host = ltrim($host,'www.');
            $this->host = explode('.', $host);
            $this->host = array_reverse($this->host);
        }
        
    public function getCountHost(){
        \Debug\Trace::mark();
        // Връща колко броя има в масива
        if(!$this->host){
            \Debug\Trace::mark();
            $this->host();
        }
        \Debug\Trace::mark();
        return count($this->host);
    }
    
////////////////////////////////////////////////////////////////////////////////
    
    public function __get($name) {
        \Debug\Trace::mark();
        ;
    }
    public function __set($name, $value) {
        \Debug\Trace::mark();
        ;
    }
    
    public function __call($name, $arguments) {
        \Debug\Trace::mark();
        ;
    }
    
    public static function __callStatic($name, $arguments) {
        \Debug\Trace::mark();
        ;
    }
    
////////////////////////////////////////////////////////////////////////////////
    
    public function state(){
        \Debug\Trace::mark();
        echo ($this->ssl)?'SSL':'no SSL';
        echo '<br>';
        echo ($this->www)?'WWW':'no WWW';
        echo '<br>';
        echo $this->language;
        echo '<br>';
        echo $this->subDomain;
        echo '<br>';
        echo $this->domain;
        echo '<br>';
        echo $this->domainExt;
        echo '<br>';
        echo '<pre>';
        var_dump($this->queryString);
        var_dump($this->hash);
        echo '<br>';
    }
}
 
