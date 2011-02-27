<?php

/**
 * Model_Session
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Model_Session extends Model_Base_Session
{
    public static function findAll()
    {
        return Doctrine_Query::create()->from('Model_Session s')->execute();
    }

    public static function findOneById($id)
    {
        $session = Doctrine_Core::getTable('Model_Session')->findOneById($id);
        if ($session){
            return $session;
        } else {
            return false;
        }
    }

    /**
     *
     * @param Model_User $user
     */
    public static function getEvents(Model_User $user)
    {
        $events = Doctrine_Core::getTable('Model_Session')->findByUserid($user->id);
        if ($events){
            return $events;
        } else {
            return false;
        }
    }
}