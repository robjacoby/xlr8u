<?php

/**
 * Model_Base_Usergroup
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $userid
 * @property Doctrine_Collection $Users
 * @property Doctrine_Collection $Session
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class Model_Base_Usergroup extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('usergroup');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('userid', 'integer', null, array(
             'type' => 'integer',
             ));

        $this->option('type', 'INNODB');
        $this->option('collate', 'utf8_general_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Model_User as Users', array(
             'local' => 'userid',
             'foreign' => 'id'));

        $this->hasMany('Model_Session as Session', array(
             'local' => 'id',
             'foreign' => 'groupid'));
    }
}