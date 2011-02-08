<?php

/**
 * Model_Base_Result
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $sessionid
 * @property string $description
 * @property string $value
 * @property Model_Session $Session
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class Model_Base_Result extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('result');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('sessionid', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('description', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('value', 'string', null, array(
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
        $this->hasOne('Model_Session as Session', array(
             'local' => 'sessionid',
             'foreign' => 'id'));
    }
}