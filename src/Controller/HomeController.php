<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    public function __construct(private CategoriesRepository $categ){

    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $user= $this->getUser();
        return $this->render('home/index.html.twig', [
           
            'categs' => $this->categ->findAll()
        ]);
    }

    #[Route('/detail/{id}', name: 'details')]
    public function details( Categories $categ): Response
    {
        // $categ = $this->categ->findOneById([
        //     'id' => $id
        // ]);
        return $this->render('home/detail.html.twig', [
           
            'categ' =>$categ
        ]);
    }
}
