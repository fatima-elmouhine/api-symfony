<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Groupe;
use DateTimeImmutable;
use App\Repository\GroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsController]
class GroupUserController extends AbstractController
{

    public function __construct(
        EntityManagerInterface $entityManager,
    ){
        $this->entityManager = $entityManager;
    }

    public function __invoke( GroupeRepository $groupeRepository, Request $request ) : Response

    {  
        $groupInfo = $this->entityManager->getRepository(Groupe::class)->findAll();

        $arrayDataGroup = [];

        foreach ($groupInfo as $key => $value) {
            $arrayDataGroup[$key]['name'] = $value->getName();
            $users = $value->getUsers();
            $i = 0;
            foreach ($users as $key2 => $value) {
                $arrayDataGroup[$key]['users'][$key2]['firstname'] = $value->getFirstname();
                $arrayDataGroup[$key]['users'][$key2]['lastname'] = $value->getLastname();
            }
            
            

        }
        return $this->json($arrayDataGroup, 200);
    }
}
