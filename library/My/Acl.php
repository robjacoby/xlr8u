<?php

class My_Acl extends Zend_Acl
{
    public function __construct()
    {
        // resources
        $this->add(new Zend_Acl_Resource(My_Acl_Resources::ACCOUNT_FREE));
        $this->add(new Zend_Acl_Resource(My_Acl_Resources::ADMIN_SECTION));
        $this->add(new Zend_Acl_Resource(My_Acl_Resources::PUBLICPAGE));

        $this->addRole(new Zend_Acl_Role(My_Acl_Roles::GUEST));
        $this->addRole(new Zend_Acl_Role(My_Acl_Roles::FREE), My_Acl_Roles::GUEST);
        $this->addRole(new Zend_Acl_Role(My_Acl_Roles::ADMIN), My_Acl_Roles::FREE);

        $this->allow(My_Acl_Roles::GUEST, My_Acl_Resources::PUBLICPAGE);
        $this->allow(My_Acl_Roles::ADMIN, My_Acl_Resources::ADMIN_SECTION);
        $this->allow(My_Acl_Roles::FREE, My_Acl_Resources::ACCOUNT_FREE);

        $this->deny(My_Acl_Roles::ADMIN, My_Acl_Resources::ACCOUNT_FREE);
    }
}