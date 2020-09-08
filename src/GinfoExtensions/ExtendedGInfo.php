<?php

class ExtendedGInfo extends \Ginfo\Ginfo {
    
    public function __construct() {
        
        if ('\\' === \DIRECTORY_SEPARATOR) {
            //$this->os = new \ExtendedWindows(); // Seems like this sub-class which
                                                  // currently contains no overriden
                                                  // code, is not allowing powershell
                                                  // scripts to be found because it 
                                                  // breaks the file location logic
                                                  // in its parent class.
            $this->os = new \Ginfo\OS\Windows();
            
        } else {
            
            // bsd, linux, darwin, solaris
            $this->os = new \ExtendedLinux();
        }
    }
}
