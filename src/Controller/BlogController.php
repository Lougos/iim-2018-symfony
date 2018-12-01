<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Contact;
use App\Form\ArticleType;
use App\Form\ContactType;
use App\Repository\ArticleRepository;
use App\Repository\ContactRepository;
use Doctrine\Common\Persistence\ObjectManager;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {
        $article = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $article
        ]);
    }

    /**
     * @Route("/about", name="about_show")
     */
    public function about(){
        return $this->render('blog/about.html.twig');
    }

    /**
     * @Route("/", name="home")
     */
    public function home(){
        return $this->render('blog/home.html.twig');
    }

    /**
     * @Route("/contact", name="contact_show")
     */
    public function contact(Request $request, ObjectManager $manager){
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($request->getMethod() === 'POST') {

            $manager->persist($contact);
            $manager->flush();
        }
        $contact->setCreatedAt(new \DateTime());
        return $this->render('blog/createContact.html.twig', [
            'formContact' => $form->createView(),
            ]);
    }

    /**
     * @Route ("/blog/new", name="blog_new")
     * @Route ("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null, Request $request, ObjectManager $manager){

        if(!$article){
            $article = new Article();
        }

        //$form = $this->createFormBuilder($article)
          //           ->add('title')
            //         ->add('content')
              //       ->add('image')
                //     ->getForm();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }


        return $this->render('blog/createArticle.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article){

        return $this->render('blog/show.html.twig', [
            'article' =>$article
        ]);
    }

    /**
     * @Route("//log/{id}", name="article_delete", methods="DELETE")
     */
    public function delete(Request $request, Article $article): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();
        }

        return $this->redirectToRoute('blog');
    }


}
