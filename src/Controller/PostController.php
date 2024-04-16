<?php

namespace App\Controller;

use App\DTO\SearchData;
use App\Form\SearchType;
use App\Repository\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


// #[Route('/profile')]
class PostController extends AbstractController
{
    #[Route('/post', name: 'app_post')]
    // #[Security("is_granted('ROLE_USER')")]
    public function index(PostRepository $postRepo, Request $request, SearchData $searchData): Response
    {
        
        $page = $request->query->getInt('page', 1);
        $searchData->page = $page;
        // generer le formulaire de recherche 
        $form = $this->createForm(SearchType::class, $searchData);
        //verifie si il y a une requete
        $form-> handleRequest($request);
        $params = [
            "query"=>$searchData->query,
            "categories"=>$searchData->categories,
            "page"=> $page
        ];
        // verifie si le formulaire est soumis et valide
        if($form->isSubmitted() && $form->isValid()) {
            $searchPost= $postRepo->searchResult();
            return $this->render('post/index.html.twig', [
                'formView' => $form->createView(),
                'res' => $searchPost,
                'params' => $params
    
            ]);
            
           
            
        }else{
            // Afficher tous les posts sans recherche 
            $posts = $postRepo->findPublished($page);
            return $this->render('post/index.html.twig', [
                'posts' => $posts,
                'formView' => $form->createView()
    
            ]);
        }

       
       
    }



    #[Route('/show/{slug}', name: 'show', methods:['GET', 'POST'])]
    public function show(string $slug, PostRepository $postRepo): Response
    {
        $post = $postRepo->findOneBy(['slug'=>$slug]);

     

   
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }





    
}