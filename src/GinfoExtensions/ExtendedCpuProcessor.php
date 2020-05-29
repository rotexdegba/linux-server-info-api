<?php

class ExtendedCpuProcessor extends Ginfo\Info\Cpu\Processor {
    
    private $allProcessorInfo = [];
    
    public function getAllProcessorInfo(): array {
        
        return $this->allProcessorInfo;
    }

    public function setAllProcessorInfo(array $allProcessorInfo) {
        
        $this->allProcessorInfo = $allProcessorInfo;
        
        return $this;
    }


    
}
