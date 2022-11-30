<?php

namespace App\State;

use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserStateProcessor implements ProcessorInterface
{
    public function __construct(
        public readonly EntityManagerInterface $entityManager,
         UserPasswordHasherInterface $passwordHasher,
    ) {
        $this->passwordHasher = $passwordHasher;
    }
    
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if($operation instanceof Post) {
            $data->setCreatedAt(new \DateTimeImmutable());
            $data->setPassword($this->passwordHasher->hashPassword($data, $data->getPassword()));

        }
        if($operation instanceof Put || $operation instanceof Patch) {
            $data->setUpdatedAt(\DateTimeImmutable::createFromMutable(new \DateTime()));
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
