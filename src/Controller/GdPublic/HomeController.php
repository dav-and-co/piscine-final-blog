<?php


// permet de déclarer des types pour chacune des variables INT ...
declare(strict_types=1);

// on crée un namespace qui permet d'identifier le chemin afin d'utiliser la classe actuelle
namespace App\Controller\gdPublic;

// on appelle le chemin (namespace) des classes utilisées et symfony fera le require de ces classes
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

// on étend la class AbstractController qui permet d'utiliser des fonctions utilitaires pour les controllers (twig etc)
class HomeController extends AbstractController

{
    // localhost/piscine-final-blog/public/

    // l'url est appelé et ça éxécute automatiquement la méthode définie sous la route
    #[Route('/', name: 'home')]
    public function AccueilPage()
    {
        return $this->render('gdPublic/page/home.html.twig'
        );
    }
}
