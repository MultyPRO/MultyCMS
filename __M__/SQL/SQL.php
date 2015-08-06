<?php

abstract class SQL {
    
////////////////////////////////////////////////////////////////////////////////
// Config
    
    protected $insert_id;
    private $stmt;
    private $stmt2;

    private $thread = array(
        'localhost'     =>      array(
            'default' =>      array(
                'user'  => 'root',
                'pass'  => 'root'
            ),
        
            'dataBase'     => array(
                'user'  =>  '*****',
                'pass'  =>  '*****'
            )
        ),
    
        'another_host'      =>      array(
            'dataBase'     => array(
                'user'  =>  '*****',
                'pass'  =>  '*****'
            )
        )
    );
    
    private static $con = array();

////////////////////////////////////////////////////////////////////////////////
    protected function __construct() {
        ;
    }

    // Това работи.
    // Създава и после връща връзка към базата данни за съответния клас.
    /*
     * Пример :
     * 
     * class Exam extends SQL
     * 
     *      private $con;
     * 
     *      __construct {
     *          $this->con = $this->GetConnect('multypro_mega', "localhost");
     *      }
     * }
     * 
     */
    protected function GetConnect($DB = "default", $HOST = 'localhost') {
        $h = md5($DB,$HOST);
        
        if(!self::$con[$h]){
            
            self::$con[$h] = mysqli_connect($HOST, $this->thread[$HOST][$DB]['user'], $this->thread[$HOST][$DB]['pass'], $DB) or die ($this->DB_false());
            mysqli_set_charset(self::$con[$h], "utf8");
        }
        return self::$con[$h];
    }// end GetConnect(); работи вече

    /* Това работи
     * Взема данни от базата данни според създадената връзка $con, която се създава от всеки клас за себе се GetConnect($DB, $HOST);
     * 
     * Пример :
     * 
     * class Exam{
     * 
     *      Връзката е вече създадена от конструктора на класа.     
     * 
     *      function GetData($params){
     *          $query = "SELECT * FROM site_data WHERE site_domain = ?";
     *          $params = array($params);
     *          
     *      Може и сега да се създаде нова връзка ако е нужно:
     *      $con = $this->GetConnect(DataBase, Host);
     * 
     *          $arr = $this->Query($this->con, $query, $params); 
     *      }
     * }
     * 
     * 
    
     * Готово е!
     * Връща масив със полетата от DB
     * 
     * Пример :
     * 
     * $site_data = $Site->GetSiteData("demo");
     * echo $site_data['site_title'];

     */ 
        
    protected function Query($con, $query, $params){
        $prepare = $this->PrepareQuery($con, $query, $params);
        if($prepare){
            //$arr = @mysqli_execute($prepare);
            $arr = $prepare->execute();
            if($this->stmt2->insert_id){
                return $this->stmt2->insert_id;
            }
            if($this->stmt2->update_id){
                return $this->stmt2->update_id;
            }
            $this->DB_false($query, $params);
            return $arr;
        } else {
            $this->DB_false($query, $params);
        }
    }

    protected function SelectQuery($con, $query, $params){
        $stmt = $this->PrepareQuery($con, $query, $params);
        
        if(!$stmt)
                        return;
        mysqli_execute($stmt);
        
// SELECT * FROM Users
        
        $rows = mysqli_stmt_result_metadata($stmt);

        while($field = mysqli_fetch_field($rows)){
            $fields[] = &$row[$field->name];
            $az[] = $field->name;
        }
        
        call_user_func_array(array($stmt,'bind_result'),$fields);
        $i=0;
        while(mysqli_stmt_fetch($stmt)){
            foreach ($az as $key => $value) {
                $arr[$i][$value] = $row[$value];
            }
            $i++;
        }
        
        if($arr){
            return $arr;
        } else {
            $this->DB_false($query, $params);
            return FALSE;
        }
    }

        private function PrepareQuery($con, $query, $params){
                foreach ($params as $type) {
                    switch ($type) {
                        case is_string($type):
                            $t[] = 's';
                            break;

                        case is_int($type):
                            $t[] = 'i';
                            break;

                        case is_double($type):
                            $t[] = 'd';
                            break;

                        case is_float($type):
                            $t[] = 'f';
                            break;
                    }
                    $p[] = $type;
                    $type = '';
                }

                $this->stmt = mysqli_prepare($con, $query);
                $this->stmt2 = $this->stmt;
                $ref = $this->ref($p);
                $types = @array(implode('',$t));
                $bind=array_merge($types,$ref);
                @call_user_func_array(array($this->stmt,'bind_param'),$bind);
                return $this->stmt;
                /*
                $h = md5($query);
                if(!$this->stmt[$h]){
                    $this->stmt[$h] = mysqli_prepare($con, $query);
                    $ref = $this->ref($p);
                    $types = array(implode('',$t));
                    $bind=array_merge($types,$ref);
                    @call_user_func_array(array($this->stmt[$h],'bind_param'),$bind);
                }
                return $this->stmt[$h];
                 */
            }

            private function ref(&$arr){ // added & in call time parameter definition, must be a reference to the actual data
                $refs=array();
                if($arr){
                    foreach($arr as $key => $value) {
                        $refs[$key] = &$arr[$key];
                    }
                }
                return $refs;
            }

    private function DB_false($query, $params){
        $info=debug_backtrace();
        foreach ($params as $value) {
            if($value){
                $par .= $value.',';
            } else {
                $par .= 'null,';
            }
        }
        $text =  "<div style='background-color: pink; padding: 10px;'>IP:".$_SERVER['REMOTE_ADDR']."<br />".$info[2]['file'].' -> '.$info[2]['line']."<br />SQL грешка!<br /> File:". $info[1]['file']."<br />Line: ".$info[1]['line'].'<br />$'.$info[2]['class'].' -> '.$info[2]['function']."($par) <br />$query <br /></div><br /><br />";        
        
        
        if($_SERVER['REMOTE_ADDR'] == '94.156.86.109-'){
            //var_dump($info);
            if(true){
                echo "SQL Error";
                if(CheckSelutionForThis($info[2]['class']."_".$info[2]['function'].'.php')){
                    echo $par;
                    return FALSE;
                }else{
                    echo "<table border=".$i." style=''>";
                    foreach ($info as $k => $v){
                        echo "<tr><td>".$v['file']."</td><td>".$v['function']."</td><td>".$v['line']."</td></tr>";
                    }
                    echo "<table><br />";
                    
                }
            }
            //echo "<pre>";
            //var_dump($info);
            return FALSE;
                
        }else{
            //echo "Изникна малка техническа неточност!<br />Моля преминете към последната стабилна версия на системата от <a href='http://Multy.PRO/-/Version/Last/1'>ТУК</a>".__CLASS__;
            //
            
            //$e = new Email('vidov@multy.pro', 'savagrup@gmail.com', 'SQL Error', $text, 'ENGINE ver.2');
            //$e->SendEmail();
        }
    }

    public function __set($name, $value) {
        \Bug\Error::boom(array('name' => $name, 'value' => $value));
    }
    
    public function __get($name) {
        \Bug\Error::boom(array('name' => $name));
    }
    
    public function __call($name, $arguments) {
        \Bug\Error::boom(array('name' => $name, 'args' => $arguments));
    }
    
    public static function __callStatic($name, $arguments) {
        \Bug\Error::boom(array('name' => $name, 'args' => $arguments));
    }
    
}