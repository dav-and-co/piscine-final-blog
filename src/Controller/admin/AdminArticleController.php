<?php


// permet de déclarer des types pour chacune des variables INT ...
declare(strict_types=1);

// on crée un namespace qui permet d'identifier le chemin afin d'utiliser la classe actuelle
namespace App\Controller\admin;

// on appelle le chemin (namespace) des classes utilisées et symfony fera le require de ces classes
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// on étend la class AbstractController qui permet d'utiliser des fonctions utilitaires pour les controllers (twig etc)
class AdminArticleController extends AbstractController

{
    // localhost/piscine-final-blog/public/

    // l'url est appelé et ça éxécute automatiquement la méthode définie sous la route

    // function qui récupère les données de la BDD
    #[Route('/admin/Articles', name: 'adminArticles')]
    public function GdPublicArticles(ArticleRepository $ArticleRepository, Request $request): response
    {
        $tri = $request->query->get('tri');
        $ordre = $request->query->get('ordre');

        if (!$tri) {
            $tri = 'id';
            $ordre = 'DESC';
        }

        // récupère tous les articles en BDD triés par ASC ou DESC
        $articles = $ArticleRepository->findBy([], [$tri => $ordre]);

        return $this->render('admin/page/articles.html.twig', [
            'articles' => $articles
        ]);
    }

    // function qui supprime un enregistrement de la BDD
    #[Route('/admin/delete/{id}', 'admin_delete')]
    public function deleteArticle(int $id, ArticleRepository $ArticleRepository, EntityManagerInterface $entityManager): Response
    {
        $article = $ArticleRepository->find($id);
        if ($article === null) {
            $html404 = $this->renderView('admin/partial/404.html.twig');
            return new Response($html404, 404);
        }

        try {
            // j'utilise la classe entity manager pour préparer la requête SQL de suppression cette requête n'est pas executée tout de suite
            $entityManager->remove($article);
            // j'execute la / les requête SQL préparée
            $entityManager->flush();

            // permet d'enregistrer un message dans la session de PHP
            // ce message sera affiché grâce à twig sur la prochaine page
            $this->addFlash('success', 'Article bien supprimé !');

        } catch (\Exception $exception) {
            return $this->render('admin/partial/error.html.twig', [
                'errorMessage' => $exception->getMessage()
            ]);
        }

        return $this->redirectToRoute('adminArticles');
    }

    #[Route('/admin/insert', 'admin_insert_article')]
    public function insertArticle(Request $request, EntityManagerInterface $entityManager)
    {
        $article = new Article();

        $articleCreateForm = $this->createForm(ArticleType::class, $article);

        $articleCreateForm->handleRequest($request);

        if ($articleCreateForm->isSubmitted() && $articleCreateForm->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article bien ajouté !');
            return $this->redirectToRoute('admin_insert_article');
        }

        $articleCreateFormView = $articleCreateForm->createView();


        return $this->render('admin/page/insert_article.html.twig', [
            'articleForm' => $articleCreateFormView
        ]);

    }
//mise à jour d'un article
    #[Route('/admin/articles/update/{id}', 'admin_update_article')]
    public function updateArticle(int $id, Request $request, EntityManagerInterface $entityManager, ArticleRepository $articleRepository)
    {
        $article = $articleRepository->find($id);

        $articleCreateForm = $this->createForm(ArticleType::class, $article);

        $articleCreateForm->handleRequest($request);

        if ($articleCreateForm->isSubmitted() && $articleCreateForm->isValid()) {
            $article->getUpdateAt(new \DateTime("NOW"));
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'article enregistré');
        }

        $articleCreateFormView = $articleCreateForm->createView();

        return $this->render('admin/page/update_article.html.twig', [
            'articleForm' => $articleCreateFormView
        ]);

    }

}
