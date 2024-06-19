<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    #[Route('/categorie/new', name: 'categorie_new')]
    public function addCategorie(EntityManagerInterface $manager,Request $request): Response
    {
        $categorie = new Categorie();
        $formCategorie = $this->createForm(CategorieType::class, $categorie);
        $formCategorie->handleRequest($request);
        if($formCategorie->isSubmitted() && $formCategorie->isValid()){
            $manager->persist($categorie);
            $manager->flush();
            $this->addFlash('success','Votre catégorie a bien été enregistrée' );
            return $this->redirectToRoute("categorie_new");
        }
        return $this->render('categorie/form.html.twig', [
            'formCategorie' => $formCategorie->createView(),
        ]);
    }
}
