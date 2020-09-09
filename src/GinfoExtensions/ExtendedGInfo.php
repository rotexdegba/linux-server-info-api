<?php

class ExtendedGInfo extends \Ginfo\Ginfo {
    
    public function __construct() {
        
        if ('\\' === \DIRECTORY_SEPARATOR) {
            $this->os = new \ExtendedWindows();
            
        } else {
            
            // bsd, linux, darwin, solaris
            $this->os = new \ExtendedLinux();
        }
    }
}
