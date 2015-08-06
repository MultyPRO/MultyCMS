<?php
class Obj {
    
    public function __construct() {
        
    }
    
    public static function create($obj, $params = FALSE){
        return new $obj($params);
    }
}
