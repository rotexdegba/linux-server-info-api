<?php

class ExtendedWindows extends \Ginfo\OS\Windows {
    
    public function __construct()
    {
        ini_set('max_execution_time', 0);
        parent::__construct();
    }

    protected function getInfo(string $name): ?array
    {
       if ($this->hasInInfoCache($name)) {
           
           return $this->getFromInfoCache($name);
       }

        $result = \json_decode(
            shell_exec('chcp 65001 | powershell -file '. S3MVC_APP_ROOT_PATH.'\\vendor\\gemorroj\\ginfo\\bin\\windows\\'.$name.'.ps1'), 
            true
        );

        $finalResult = \is_scalar($result) ? [$result] : $result;

        $this->addToInfoCache($name, $finalResult);

        return $finalResult;
    }
}
