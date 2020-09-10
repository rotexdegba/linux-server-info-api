<?php

class ExtendedWindows extends \Ginfo\OS\Windows {
    
    public function __construct()
    {
        ini_set('max_execution_time', 0);
        parent::__construct();
    }

    protected function getInfo(string $name): ?array
    {
//        if (\array_key_exists($name, $this->infoCache)) {
//            
//            return $this->infoCache[$name];
//        }

        $result = \json_decode(
            shell_exec('chcp 65001 | powershell -file '. S3MVC_APP_ROOT_PATH.'\\vendor\\gemorroj\\ginfo\\bin\\windows\\'.$name.'.ps1'), 
            true
        );

        return \is_scalar($result) ? [$result] : $result;
    }
}
