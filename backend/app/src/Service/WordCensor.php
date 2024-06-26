<?php

namespace App\Service;

use App\Entity\DirtyWord;
use App\Repository\DirtyWordRepository;
use Doctrine\ORM\EntityManagerInterface;

class WordCensor
{
    private EntityManagerInterface $entityManager;
    private DirtyWordRepository $dirtyWordRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->setDirtyWordRepository($this->entityManager->getRepository(DirtyWord::class));
    }

    public function censorWords(string $text): string
    {
        $dirtyWords = $this->dirtyWordRepository->findAll();
        foreach ($dirtyWords as $dirtyWord) {
            $word = $dirtyWord->getWord();
            $pattern = '/' . preg_quote($word, '/') . '/i';
            $text = preg_replace($pattern, str_repeat('*', mb_strlen($word)), $text);
        }

        return $text;
    }

    public function setDirtyWordRepository(DirtyWordRepository $repostiroy): void
    {
        $this->dirtyWordRepository = $repostiroy;
    }
}
