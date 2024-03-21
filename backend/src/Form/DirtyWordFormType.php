<?php

namespace App\Form;

use App\Entity\DirtyWord;
use App\Helper\Validator;
use App\Helper\ValidatorEConvMessage;
use Doctrine\ORM\EntityManagerInterface;

class DirtyWordFormType
{
    public function createUpdate(EntityManagerInterface $entityManager, DirtyWord $entity)
    {
        $error = array();
        $v = new Validator();
        $v
            ->addDefaultFilter('trim')
            ->addRule('word', "notEmpty")
            ->addRule('type', "notEmpty");

        $entity = $this->setEntityData($entity, $_POST);

        if ($v->clearAndValidate($_POST)) {
            $post = $v->getCleanedValues();
            $entity = $this->setEntityData($entity, $post);
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

    private function setEntityData(DirtyWord $entity, $post)
    {
        $entity->setWord($post['word']);
        $entity->setType($post['type']);
        return $entity;
    }
}
