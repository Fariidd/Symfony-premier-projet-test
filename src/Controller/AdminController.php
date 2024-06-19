<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\CategorieRepository;
use App\Repository\SerieRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin',name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/list',name: 'list')]
    public function listAll(SerieRepository $repository): Response
    {
        $allSeries=$repository->findAll();
        return $this->render('admin/dashboard.html.twig', [
            'allSeries' => $allSeries,
        ]);
    }

    #[Route('/new', name:'new')]
    public function addSerie(EntityManagerInterface $em,Request $request,FileUploader $fileUploader):Response
    {
        $serie = new Serie();
        $formSerie = $this->createForm(SerieType::class, $serie);
        $formSerie->handleRequest($request);
        if($formSerie->isSubmitted() && $formSerie->isValid()){
            $fileUploaded = $formSerie->get('poster')->getData();
            if($fileUploaded){
                $fileUploadedName = $fileUploader->upload($fileUploaded);
                $serie->setPoster($fileUploadedName);
            }
            $serie->setUpdatedAt(new \DateTimeImmutable);
            $em->persist($serie);
            $em->flush();
            $this->addFlash('success','Votre série a bien été enregistrée' );
            return $this->redirectToRoute('admin_list');
        }
        return $this->render('admin/form.html.twig',[
            'formSerie' => $formSerie,
        ]);
    }

    #[Route('/update/{id}', name:'update',requirements: ['id'=>'\d+'])]
    public function updateSerie(EntityManagerInterface $em,Request $request,FileUploader $fileUploader, Serie $serie):Response
    {
        $oldPoster = $serie->getPoster();
        $formUpdate = $this->createForm(SerieType::class,$serie);
        $formUpdate->handleRequest($request);

        if($formUpdate->isSubmitted() && $formUpdate->isValid()){
            $fileUploaded = $formUpdate->get('poster')->getData();
            if($fileUploaded){
                $fileUploadedName = $fileUploader->upload($fileUploaded);
                $serie->setPoster($fileUploadedName);
                unlink('img/series/'.$oldPoster);
            }
            else{
                $serie->setPoster($oldPoster);
            }
            $serie->setUpdatedAt(new \DateTimeImmutable());
            $em->persist($serie);
            $em->flush();
            $this->addFlash('success','Votre série a bien été mis à jour');
            return $this->redirectToRoute('admin_list');
        }

        return $this->render('admin/form.html.twig',[
            'formSerie'=>$formUpdate->createView(),
        ]);
    }

    #[Route('/delete/{id}', name:'delete',requirements: ['id'=>'\d+'])]
    public function deleteSerie(EntityManagerInterface $em,Serie $serie):Response
    {
        $poster = $serie->getPoster();
        $em->remove($serie);
        $em->flush();
        $this->addFlash('success','Votre série a bien été supprimée');
        return $this->redirectToRoute('admin_list');

    }
}
