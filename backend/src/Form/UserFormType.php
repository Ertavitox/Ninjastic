<?php

namespace App\Form;

use App\Entity\User;
use App\Helper\Validator;
use App\Helper\ValidatorEConvMessage;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

class UserFormType
{
    public function createUpdate(EntityManagerInterface $entityManager, User $entity)
    {
        $error = array();
        $v = new Validator();
        $v
            ->addDefaultFilter('trim')
            ->addRule('name', "notEmpty")
            ->addRule('email', "email");

        if (is_null($entity->getId())) {
            $v->addRule('password', 'notEmpty');
        }
        if (!empty($_POST['password']) || !empty($_POST['password_again'])) {
            $v->addRule('password', 'minLength', array(8));
            $v->addRule('password', 'equals', array($_POST['password_again']));
        }

        $entity = $this->setEntityData($entity, $_POST);

        if ($v->clearAndValidate($_POST)) {
            $post = $v->getCleanedValues();
            $entity = $this->setEntityData($entity, $post);
        } else {
            $eCodeConv = new ValidatorEConvMessage();
            $error = $eCodeConv->replaceErrorToMessage($v->getListOfErrors());
        }

        if (empty($error)) {
            try {
                $entityManager->persist($entity);
                $entityManager->flush();
            } catch (UniqueConstraintViolationException $ex) {
                $content = $ex->getMessage();
                if (strpos($content, "Duplicate entry") !== false) {
                    $error['email'] = 'The email address is already in use!';
                }
            }
        }

        return array("entity" => $entity, "error" => $error);
    }

    private function setEntityData(User $entity, $post)
    {
        $entity->setEmail($post['email']);
        $entity->setName($post['name']);
        if (!empty($_POST['password']) || !empty($_POST['password_again'])) {
            $entity->setPassword($post['password']);
        }
        $entity->setStatus($post['status']);
        if (!$entity->getId() || $entity->getId() == 0 || is_null($entity->getId())) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }
        $entity->setUpdatedAt(new \DateTimeImmutable());
        return $entity;
    }
}
