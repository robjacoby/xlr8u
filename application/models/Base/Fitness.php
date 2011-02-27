<?php

/**
 * Model_Base_Fitness
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $userid
 * @property string $beep
 * @property string $toetaps
 * @property string $pushups
 * @property string $90hang
 * @property string $plank
 * @property string $3minstep
 * @property string $12minrun
 * @property string $waistcm
 * @property timestamp $datetime
 * @property Model_User $User
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class Model_Base_Fitness extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('fitness');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('userid', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('beep', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('toetaps', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('pushups', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('90hang', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('plank', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('3minstep', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('12minrun', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('waistcm', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('datetime', 'timestamp', null, array(
             'type' => 'timestamp',
             ));

        $this->option('type', 'INNODB');
        $this->option('collate', 'utf8_general_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Model_User as User', array(
             'local' => 'userid',
             'foreign' => 'id'));
    }
}