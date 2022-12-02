<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;

class CurrentUserProvider implements ProviderInterface
{
    public function __construct(private Security $security)
    {
        $this->security = $security;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Retrieve the state from somewhere
        // dd($this->security->getUser());
        if ($this->security->getUser()) {
            return $this->security->getUser();
        }
        return new JsonResponse(['message' => 'Vous n\'êtes pas connecté'], 401);
    }
}
