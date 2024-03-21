<?php

namespace App\Service;

use App\Entity\DirtyWord;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;

class XmlProcessor
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function process($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        foreach ($xml->Word as $wordElement) {
            $dirtyWord = new DirtyWord();
            $dirtyWord->setWord(strval($wordElement));
            $dirtyWord->setType(strval($wordElement['type']));
            $this->entityManager->persist($dirtyWord);
        }

        $this->entityManager->flush();
    }
}
