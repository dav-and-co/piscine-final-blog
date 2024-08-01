<?php


// permet de déclarer des types pour chacune des variables INT ...
declare(strict_types=1);

// on crée un namespace qui permet d'identifier le chemin afin d'utiliser la classe actuelle
namespace App\Controller;

// on appelle le chemin (namespace) des classes utilisées et symfony fera le require de ces classes
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// on étend la class AbstractController qui permet d'utiliser des fonctions utilitaires pour les controllers (twig etc)
class GdPublicController extends AbstractController

{
    // localhost/piscine-final-blog/public/

    // l'url est appelé et ça éxécute automatiquement la méthode définie sous la route

    // function qui récupère les données de la BDD
    #[Route('/GdPublicArticles', name: 'GdPublicArticles')]
    public function GdPublicArticles(ArticleRepository $ArticleRepository) : response
    {
        // récupère tous les articles en BDD
        $articles = $ArticleRepository->findAll();

        return $this->render('gdPublic/page/GdPublic-articles.html.twig', [
            'articles' =>  $articles
        ]);
    }
// function qui récupère UNE donnée de la BDD au travers du choix de l'ID sur l'affichage de tous les articles
    #[Route('/GdPublicArticle-one/{id}', name: 'GdPublicArticle-one')]
    public function oneArticleFromDb(INT $id, ArticleRepository $ArticleRepository) : response {

        // récupère 1 article en BDD par son id
        $article = $ArticleRepository->find($id);

    //si pas de article ou d'article publié, affichage personnalisé
        if (!$article || !$article->isPublished()) {
            $html404 = $this->renderView('gdPublic/partial/404.html.twig');
            return new Response($html404, 404);
        }
        return $this->render('gdPublic/page/GdPublic-article.html.twig', [
            'article' => $article
        ]);
    }
    // function qui récupère les données de la BDD category
    #[Route('/GdPublicCategory', name: 'GdPublicCategory')]
    public function GdPublicCategory(CategoryRepository $CategoryRepository) : response
    {
        // récupère tous les articles en BDD
        $categories = $CategoryRepository->findAll();

        return $this->render('gdPublic/page/GdPublic-category.html.twig', [
            'categories' =>  $categories
        ]);
    }

    // function qui récupère UNE collection de la BDD au travers du choix de l'ID sur l'affichage de tous les articles
    #[Route('/GdPublicArticles-oneCat/{id}', name: 'GdPublicArticles-oneCat')]
    public function ArticlesFromCat(INT $id, CategoryRepository $CategoryRepository) : response {
        // récupère 1 article en BDD par son id

        $category = $CategoryRepository->find($id);

        //si pas de article ou article publié, affichage personnalisé
        if (!$category) {
            $html404 = $this->renderView('gdPublic/partial/404.html.twig');
            return new Response($html404, 404);
        }

        $title = $category->getTitle();

        return $this->render('gdPublic/page/GdPublic-articlefromCat.html.twig', [
            'category' => $category,
            'title'=>$title
        ]);
    }
}
