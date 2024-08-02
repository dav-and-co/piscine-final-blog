<?php


// permet de déclarer des types pour chacune des variables INT ...
declare(strict_types=1);

// on crée un namespace qui permet d'identifier le chemin afin d'utiliser la classe actuelle
namespace App\Controller\admin;

// on appelle le chemin (namespace) des classes utilisées et symfony fera le require de ces classes
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// on étend la class AbstractController qui permet d'utiliser des fonctions utilitaires pour les controllers (twig etc)
class AdminController extends AbstractController

{
    // localhost/piscine-final-blog/public/

    // l'url est appelé et ça éxécute automatiquement la méthode définie sous la route

    // function qui récupère les données de la BDD
    #[Route('/admin/Articles', name: 'adminArticles')]
    public function GdPublicArticles(ArticleRepository $ArticleRepository) : response
    {
        // récupère tous les articles en BDD
        $articles = $ArticleRepository->findBy([],['title' => 'ASC']);

        return $this->render('admin/page/articles.html.twig', [
            'articles' =>  $articles
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
        // j'utilise la classe entity manager pour préparer la requête SQL de suppression cette requête n'est pas executée tout de suite
        $entityManager->remove($article);
        // j'execute la / les requête SQL préparée
        $entityManager->flush();

        return $this->redirectToRoute('adminArticles');
    }

}