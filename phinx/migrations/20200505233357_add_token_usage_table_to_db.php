<?php
use \Lsia\BasePhinxDbMigration;

class AddTokenUsageTableToDb extends BasePhinxDbMigration
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
            if( ! $this->hasTable('token_usage') ) {
                
                $table = $this->table('token_usage');
                $table->addColumn('token_id', 'integer', ['signed'=>false])
                      ->addColumn('request_uri', 'string', ['limit'=>255, 'null'=>false])
                      ->addColumn('date_time_of_request', 'string', ['limit'=>255, 'default'=>'0000-00-00 00:00:00', 'null'=>false])
                      ->addColumn('request_full_details', 'text', ['null'=>false])
                      ->addColumn('requesters_ip', 'string', ['limit'=>255, 'null'=>false, 'default'=>'NOIP'])
                      ->create();
                
                $table->addForeignKey('token_id', 'tokens', 'id', [ 'delete'=>'CASCADE', 'update'=>'CASCADE'])
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
                
                $this->table('token_usage')->drop()->save();
            }
            
        } catch (\Exception $ex) {
            
            $this->printDownExceptionErrorMessage($ex);
            return;
        }
    }
}
