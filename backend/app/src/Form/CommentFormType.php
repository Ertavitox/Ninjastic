<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Topic;
use App\Entity\User;
use App\Helper\Validator;
use App\Helper\ValidatorEConvMessage;
use App\Service\WordCensor;
use Doctrine\ORM\EntityManagerInterface;

class CommentFormType
{

    private WordCensor $wordCensor;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->setWordCensor(new WordCensor($entityManager));
    }

    public function createUpdate(EntityManagerInterface $entityManager, Comment $entity): array
    {
        $error = array();
        $v = new Validator();
        $v
            ->addDefaultFilter('trim')
            ->addRule('original', "notEmpty");

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
        $topic_id = (int) $_POST["topic_id"];
        if (null === $topic_id || !is_numeric($topic_id)) {
            $error["topic_id"] = "Please choose topic from list";
            return array("entity" => $entity, "error" => $error);
        }
        $topicEntity = $this->checkTopicEntity($userId, $entityManager);
        if ($topicEntity === null) {
            $error["topic_id"] = "Please choose topic from list";
            return array("entity" => $entity, "error" => $error);
        }

        $entity = $this->setEntityData($entity, $topicEntity, $userEntity, $_POST);
        if ($v->clearAndValidate($_POST)) {
            $post = $v->getCleanedValues();
            $entity = $this->setEntityData($entity, $topicEntity, $userEntity, $post);
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

    public function checkTopicEntity(int $id, EntityManagerInterface $entityManager): ?Topic
    {
        $userEM = $entityManager->getRepository(Topic::class);
        $userEntity = $userEM->find($id);
        return $userEntity;
    }

    public function checkUserEntity(int $id, EntityManagerInterface $entityManager): ?User
    {
        $userEM = $entityManager->getRepository(User::class);
        $userEntity = $userEM->find($id);
        return $userEntity;
    }

    private function setEntityData(Comment $entity, Topic $topicEntity, User $userEntity, $post)
    {
        $entity->setOriginal($post['original']);
        $censoredText = $this->wordCensor->censorWords($post["original"]);
        $entity->setMessage($censoredText);
        $entity->setStatus($post['status']);
        $entity->setTopic($topicEntity);
        $entity->setUser($userEntity);
        if (!$entity->getId() || $entity->getId() == 0 || is_null($entity->getId())) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }
        $entity->setUpdatedAt(new \DateTimeImmutable());
        return $entity;
    }

    public function setWordCensor(WordCensor $wordCensor): void
    {
        $this->wordCensor = $wordCensor;
    }
}
