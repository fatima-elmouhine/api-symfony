<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Groupe;
use ApiPlatform\Metadata\Put;
use App\Repository\GroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[AsController]
class UserGroupController extends AbstractController
{
    public function __construct(
        EntityManagerInterface $entityManager,
    ){
        $this->entityManager = $entityManager;
    }

    // #[Route(
    //     name: 'registerUserGroup',
    //     path: 'api/users/{id}/groups/{group}',
    //     methods: ['PATCH'],
    //     // defaults: [
    //     //     '_api_resource_class' => [User::class, Groupe::class],
    //     //     '_api_operation_name' => '_api_/users/{id}/groups/{group_id}',
    //     // ],
    // )]
    public function __invoke(string $id, string $group, GroupeRepository $groupeRepository, Request $request ) : Response

    {  
        $groupInfo = $groupeRepository->find($group);
        $userInfo = $this->entityManager->getRepository(User::class)->find($id);
        $groupInfo = $this->entityManager->getRepository(Groupe::class)->find($group);
        
        $user = $userInfo->setGroupe($groupInfo);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        
        return $this->json($user, 201);
    }


}
