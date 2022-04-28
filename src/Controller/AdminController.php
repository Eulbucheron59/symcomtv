<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/products', name: 'app_admin_products')]
    public function adminProducts(ProductRepository $productRepository) : Response 
    {

        $em = $this->getDoctrine()->getManager();

        $colonnes = $em->getClassMetadata(Product::class)->getFieldNames();

        $products = $productRepository->findAll();

        return $this->render('admin/products.html.twig', [
            'colonnes' => $colonnes,
            'products' => $products
        ]);
    }
    #[Route('/admin/products/create', name: 'app_admin_products_create')]
    public function adminProductCreate(Product $product = null, Request $request, EntityManagerInterface $manager, SluggerInterface $slugger) : Response 
    {
        if(!$product){
        $product = new Product();
        }

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // Class Symfony qui permet de traiter les fichiers (type file)
            /** @var UploadedFile $imageFile */

                        // Grace au Form, nous pouvons récupérer les données insérer dans le champs 'picture'

            $imageFile = $form->get('picture')->getData();

            // dump($imageFile->guessExtension());     Fonction permettant de récuperer l'extension d'un fichier

            // On teste si l'on récupère bien une donnée 
            if($imageFile){

                // On stocke le nom original du fichier (sans  l'extension)
                $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

                // dump($originalName);
                
                
                // On attribut un nom plus propre au ficher 
                $sluggedFileName = $slugger->slug($originalName);

                $newImageName = $sluggedFileName . '-' . uniqid() .'.'. $imageFile->guessExtension();

                    

                // On va tenter de copier l'image dans le bon dossier 
                try{

                    $imageFile->move( $this->getParameter('image_directory'), $newImageName );

                }catch(FileException $e){
                    echo "Erreur: " . $e->getMessage();
                }

                $product->setPicture($newImageName);
            }

            $manager->persist($product);
            $manager->flush();

            $this->addFlash('success', "Le produit:" . $product->getTitle() . " a bien été ajouté." );

            return $this->redirectToRoute('app_admin_products');
        }

        return $this->render('admin/create.html.twig', [
            'form' => $form->createView(), 
            

        ]);
    }


    #[Route('/admin/products/edit/{id}', name: 'app_admin_products_edit')]
    public function adminProductEdit(Product $product = null, Request $request, EntityManagerInterface $manager, SluggerInterface $slugger, $id) : Response 
    {
        if(!$product){
        $product = new Product();
        }

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // Class Symfony qui permet de traiter les fichiers (type file)
            /** @var UploadedFile $imageFile */

                        // Grace au Form, nous pouvons récupérer les données insérer dans le champs 'picture'

            $imageFile = $form->get('picture')->getData();

            // dump($imageFile->guessExtension());     Fonction permettant de récuperer l'extension d'un fichier

            // On teste si l'on récupère bien une donnée 
            if($imageFile){

                // On stocke le nom original du fichier (sans  l'extension)
                $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

                // dump($originalName);
                
                
                // On attribut un nom plus propre au ficher 
                $sluggedFileName = $slugger->slug($originalName);

                $newImageName = $sluggedFileName . '-' . uniqid() .'.'. $imageFile->guessExtension();

                    

                // On va tenter de copier l'image dans le bon dossier 
                try{

                    $imageFile->move( $this->getParameter('image_directory'), $newImageName );

                }catch(FileException $e){
                    echo "Erreur: " . $e->getMessage();
                }

                $product->setPicture($newImageName);
            }

            $manager->persist($product);
            $manager->flush();

            $this->addFlash('success', "Le produit : " . $product->getTitle() . " a bien été modifié." );

            return $this->redirectToRoute('app_admin_products');
        }

        return $this->render('admin/edit.html.twig', [
            'form' => $form->createView(), 
            'id' => $id

        ]);
    }

    #[Route('/admin/products/delete/{id}', name: 'app_admin_products_delete')]
        public function adminDeleteProducts(Product $product,EntityManagerInterface $manager , $id) :Response 
        {
            $id = $product->getId();

            $manager->remove($product);
            $manager->flush();

            $this->addFlash('success', "Le produit : " . $product->getTitle() . " a bien été supprimé." );

            return $this->redirectToRoute('app_admin_products');
        }
    

    

}
