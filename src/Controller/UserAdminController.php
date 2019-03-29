<?php

namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAdminController extends BaseAdminController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function persistEntity($entity) 
    {
        $this->encodeUserPassword($entity);
        parent::persistEntity($entity);
    }

    protected function updateEntity($entity)
    {
        $this->encodeUserPassword($entity);
        parent::updateEntity($entity);
    }

    private function encodeUserPassword($entity) : void
    {
        $entity->setPassword(
            $this->passwordEncoder->encodePassword($entity, $entity->getPassword())
        );
    }
}