<?php
namespace Debug;

/*
 * Отбелязва системни бъгове там, където системата не се е справила със ситуацията
 * 
 */

class Bug extends \SQL{
    
    public function __construct() {
        ;
    }
    
    public static function fix($a){
        
    }

    public static function boom($a){
        /*
         * log - съобщение за грешка
         * arr - масив с данни
         */
        $d = debug_backtrace();
        echo "<h1>".$a['log']."</h1>";
        echo $d[0]['file'].$d[0]['line'].'<br>';
        if(DEBUG){
            self::__showError(array_reverse($d));
        }
        exit();
    }
    
    private static function __showError($d){
        echo "<table border=1>";
        echo "<tr> <th>#</th> <th>File</th> <th>Line</th> <th>Class</th> <th>Function</th> <th>arrguments</th></tr>";
        $i=1;
        foreach ($d as $key => $value) {
            echo    "<tr>"
                        . "<td>".$i.'</td>'
                        . "<td>".$value['file'].'</td>'
                        . '<td>'.$value['line'].'</td>'
                        . '<td>'.$value['class'].'</td>'
                        . '<td>'.$value['function'].'</td>'
                        . '<td>';
                        foreach ($value['args'] as $v) {
                            echo "<pre>";
                            print_r($v);
                            echo "</pre>";
                        }
                        echo '</td>'
                    . '</tr>';
            $i++;
        }
        echo "</table>";
        echo "<pre>";
        print_r($a['arr']);
        echo "<br>";
    }
}
