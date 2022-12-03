<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Groupe;
use App\Repository\GroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

#[AsController]
class UserGroupController extends AbstractController
{
    public function __construct(
        Security $security,
        EntityManagerInterface $entityManager,
    ){
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function __invoke(string $id, string $group, GroupeRepository $groupeRepository ) : Response

    {  

        if ($id == $this->security->getUser()->getId() || $this->security->getUser()->getRoles()[0] == 'ROLE_ADMIN') {
            $groupInfo= $groupeRepository->find($group);
            $userInfo = $this->entityManager->getRepository(User::class)->find($id);
            $groupInfo = $this->entityManager->getRepository(Groupe::class)->find($group);
            $user = $userInfo->setGroupe($groupInfo);
            $userInfo->setUpdatedAt(\DateTimeImmutable::createFromMutable(new \DateTime()));
    
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            if ($this->security->getUser()->getRoles()[0] == 'ROLE_ADMIN') {
                return  new JsonResponse(['message' => 'Cet utilisateur fait maintenant parti du groupe.'], 201);
            }
            return new JsonResponse(['message' => 'Vous êtes maintenant inscrit'], 201);
        }else{

            return  new JsonResponse('Vous ne pouvez pas accéder à cette route  ', 403);
        }

        
    }


}
