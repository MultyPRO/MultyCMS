<?php
namespace Debug;

/*
 * Проследява пътя на програмата.
 * Част е от дебъгер системата.
 * Поставя се навсякъде в кода за да проследяваме постъпково от къде е минал кода.
 */
class Trace {
    
    private static $path;
    private static $i = 0;


    public function __construct() {
        ;
    }
    
    public static function mark(){
        $d = debug_backtrace();
        self::$path[self::$i] = $d[0]['file'].' '.$d[0]['line'].' '. $d[1]['class'].' '.$d[1]['function'];
        self::show();
    }
    
    private static function show(){
        if(TRACE){
            foreach (self::$path as $p){
                $t .= $p.'<br>';
            }
            echo $t;
        }
    }
    
    protected function saveTrace(){
        /*
         * При включена настройка записва пътя в БД на всяка крачка.
         */
    }
}
