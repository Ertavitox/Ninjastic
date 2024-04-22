<?php

namespace App\Form;

use App\Entity\Topic;
use App\Entity\User;
use App\Helper\Validator;
use App\Helper\ValidatorEConvMessage;
use Doctrine\ORM\EntityManagerInterface;

class TopicFormType
{
    public function createUpdate(EntityManagerInterface $entityManager, Topic $entity)
    {
        $error = array();
        $v = new Validator();
        $v
            ->addDefaultFilter('trim')
            ->addRule('name', "notEmpty")
            ->addRule('description', "notEmpty");

        $userId = (int) $_POST["user_id"];
        if (null === $userId || !is_numeric($userId)) {
            $error["user_id"] = "Please choose user from list";
            return array("entity" => $entity, "error" => $error);
        }
        $userEntity = $this->checkUserEntity($userId, $entityManager);
        if ($userEntity === null) {
            $error["user_id"] = "Please choose user from list";
            return array("entity" => $entity, "error" => $error);
        }

        $entity = $this->setEntityData($entity, $userEntity, $_POST);
        if ($v->clearAndValidate($_POST)) {
            $post = $v->getCleanedValues();
            $entity = $this->setEntityData($entity, $userEntity, $post);
        } else {
            $eCodeConv = new ValidatorEConvMessage();
            $error = $eCodeConv->replaceErrorToMessage($v->getListOfErrors());
        }

        if (empty($error)) {
            $entityManager->persist($entity);
            $entityManager->flush();
        }

        return array("entity" => $entity, "error" => $error);
    }

    private function checkUserEntity(int $id, EntityManagerInterface $entityManager): ?User
    {
        $userEM = $entityManager->getRepository(User::class);
        $userEntity = $userEM->find($id);
        return $userEntity;
    }

    private function setEntityData(Topic $entity, User $userEntity, $post)
    {
        $entity->setName($post['name']);
        $entity->setDescription($post['description']);
        $entity->setStatus($post['status']);
        $entity->setUser($userEntity);
        if (!$entity->getId() || $entity->getId() == 0 || is_null($entity->getId())) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }
        $entity->setUpdatedAt(new \DateTimeImmutable());
        return $entity;
    }
}
