<?php

/**
 * Model_Base_Diary
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $userid
 * @property date $dateField
 * @property string $breakfast
 * @property string $lunch
 * @property string $dinner
 * @property string $snacks
 * @property string $exercise
 * @property Model_User $User
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class Model_Base_Diary extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('diary');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('userid', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('dateField', 'date', null, array(
             'type' => 'date',
             ));
        $this->hasColumn('breakfast', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('lunch', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('dinner', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('snacks', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('exercise', 'string', null, array(
             'type' => 'string',
             'length' => '',
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