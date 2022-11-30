<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Groupe;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
#[AsController]
class GroupUserController extends AbstractController
{

    public function __invoke(Groupe $data)
    {
        $data->setName('toto');
        return $data;
        dd($data);
        // $data = new User();
        // // $groupe->setName('toto');
        // // $groupe->addUser($data);
        // return $groupe;
    }
    // {
    //     // $data->addGroup($group);
    //     // return $data;
    // }
}
