<?php
namespace Lsia;

/**
 * Description of BasePhinxDbMigration
 *
 * @author rotimi
 */
class BasePhinxDbMigration extends \Phinx\Migration\AbstractMigration {

    
    protected function printUpExceptionErrorMessage(\Exception $e) {
        
        echo get_class($this) . '::up(): an exception occured while performing a migration.' . PHP_EOL . PHP_EOL;
        echo $e;
        echo PHP_EOL . PHP_EOL . get_class($this) . '::up(): Please roll back the last migration.' . PHP_EOL . PHP_EOL;
    }
    
    protected function printDownExceptionErrorMessage(\Exception $e) {
        
        echo get_class($this) . '::down(): an exception occured while rolling back the last migration.' . PHP_EOL . PHP_EOL;
        echo $e;
        echo PHP_EOL . PHP_EOL . get_class($this) . '::down(): Please re-run the last migration.' . PHP_EOL . PHP_EOL;
    }
    
    protected function currentAdapterIs($adapter='mysql') {
        
        return ( 
            trim(strtolower($this->getAdapter()->getAdapterType())) 
            === trim(strtolower($adapter)) 
        );
    }
}
