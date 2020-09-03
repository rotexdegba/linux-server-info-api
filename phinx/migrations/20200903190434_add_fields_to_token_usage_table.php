<?php
use \Lsia\BasePhinxDbMigration;

class AddFieldsToTokenUsageTable extends BasePhinxDbMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
//    public function change()
//    {
//
//    }
    
    public function up()
    {
        try {
            // place up commands here
            if( $this->hasTable('token_usage') ) {
                
                $table = $this->table('token_usage');
                $table->addColumn('http_status_code', 'string', ['limit'=>3, 'default'=>'200', 'null'=>false])
                      ->addColumn('request_error_details', 'text', ['null'=>true])
                      ->save();
            }
            
        } catch (\Exception $ex) {
            
            $this->printUpExceptionErrorMessage($ex);
            return;
        }
    }

    public function down()
    {
        try {
            // place down commands here
            if( $this->hasTable('token_usage') ) {
                
                $table = $this->table('token_usage');
                
                if($table->hasColumn('http_status_code')) {
                    
                    $table->removeColumn('http_status_code')
                          ->save();
                }
                
                if($table->hasColumn('request_error_details')) {
                    
                    $table->removeColumn('request_error_details')
                          ->save();
                }
            }
            
        } catch (\Exception $ex) {
            
            $this->printDownExceptionErrorMessage($ex);
            return;
        }
    }
}
