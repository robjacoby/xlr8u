<?php

/**
 * Model_Invoice
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Model_Invoice extends Model_Base_Invoice
{

    public function findOneById($id)
    {
        $invoice = Doctrine_Core::getTable('Model_Invoice')->findOneById($id);
        if ($invoice)
            return $invoice;
        else
            return false;
    }
}