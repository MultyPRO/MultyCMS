<?php

function __autoload($class_name) {
    $dir = explode('\\', $class_name);
    $class_name =  end($dir) . '.php';
    try {
        
        // Ако името на неймспейса съвпада с името на класа
        if(count($dir) == 1){
            foreach (explode(',', FOLDERS) as $folder) {
                if(is_readable(constant($folder).$dir[0].DIRECTORY_SEPARATOR.$class_name)){
                    require_once constant($folder).$dir[0].DIRECTORY_SEPARATOR.$class_name;
                    return;
                }
            }
        }
        
        if($parts[0] == "C"){
            if(is_readable(C.$class_name)){
                require_once C.$class_name;
                return;
            }
        }
        elseif($parts[0] == "Plugin"){
            if(is_readable(E."Plugins/$class_name")){
                require_once E."Plugins/$class_name";
                return;
            }
        }
        elseif($parts[0] == "Snippet"){
            if(is_readable(E."Snippets/$class_name")){
                require_once E."Snippets/$class_name";
                return;
            }
        }
        
        if(is_readable(M.$dir[0].'/'.$class_name)){
            require_once M.$dir[0]."/".$class_name;
            return;
        }
        
        if(is_readable(E.$class_name)){
            require_once E.$class_name;
            return;
        }
        
        if(is_readable(__I__.dir[0].$class_name)){
            require_once __I__.dir[0].$class_name;
            return;
        }
        else{
            throw new Exception ("Изобщо не мога да намеря файлът", 1);
        }
    } catch (Exception $autoload) {
        //myExit($class_name, $autoload);
    }
}
