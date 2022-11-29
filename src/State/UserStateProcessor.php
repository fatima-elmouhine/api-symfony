<?php

namespace App\State;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;

class UserStateProcessor implements ProcessorInterface
{
    public function __construct(
        public readonly EntityManagerInterface $entityManager
    ) {}
    
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        // Handle the state
        // dd('titi');

        // dd($operation instanceof Put);
        if($operation instanceof Post) {
            $data->setCreatedAt(new \DateTimeImmutable());
        }
        if($operation instanceof Put) {
            $data->setUpdatedAt(\DateTimeImmutable::createFromMutable(new \DateTime()));
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
