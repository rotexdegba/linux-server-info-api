<?php
declare(strict_types=1);

namespace Lsia\Atlas\Models;

use Atlas\Mapper\Record;

/**
 * Description of BaseRecord
 *
 * @author rotimi
 */
class BaseRecord extends Record {
    
    protected $siriusValidationRules = [];
    
    public function getSiriusValidationRules(): array {
        
        return $this->siriusValidationRules;
    }
}
