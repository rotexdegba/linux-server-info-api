<?php
use \Lsia\BasePhinxDbMigration;

class AddTokensTableToDb extends BasePhinxDbMigration
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
            if( ! $this->hasTable('tokens') ) {
                
                $table = $this->table('tokens');
                $table//->addColumn('id', 'biginteger', ['signed'=>false, 'identity'=>true])
                      ->addColumn('generators_username', 'string', ['limit'=>255, 'null'=>false])
                      ->addColumn('token', 'string', ['limit'=>255, 'null'=>false])
                      ->addColumn('date_created', 'string', ['limit'=>255, 'default'=>'0000-00-00 00:00:00', 'null'=>false])
                      ->addColumn('date_last_edited', 'string', ['limit'=>255, 'default'=>'0000-00-00 00:00:00', 'null'=>false])
                      ->addColumn('max_requests_per_day', 'biginteger', ['signed'=>false, 'default'=>0, 'null'=>false])
                      ->addColumn('expiry_date', 'string', ['limit'=>255, 'default'=>'0000-00-00 00:00:00', 'null'=>false])
                      ->addColumn('creators_ip', 'string', ['limit'=>255, 'null'=>false, 'default'=>'NOIP'])
                      ->create();
                
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
            if( $this->hasTable('tokens') ) {
                
                $this->table('tokens')->drop()->save();
            }
            
        } catch (\Exception $ex) {
            
            $this->printDownExceptionErrorMessage($ex);
            return;
        }
    }
}
