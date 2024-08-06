<?php

namespace App\Controller\admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
{

    #[Route('/admin/users/insert', 'admin_insert_user')]
    public function insertAdmin(UserPasswordHasherInterface $passwordHasher, Request $request, EntityManagerInterface $entityManager)
    {

        if ($request->getMethod() === "POST") {
            $username = $request->request->get('username');
            $password = $request->request->get('password');

            $user = new User();

            try {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $password
                );

                $user->setUsername($username);
                $user->setPassword($hashedPassword);
                $user->setRoles(['ROLE_ADMIN']);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'utilisateur créé');

            } catch (\Exception $exception) {
                // attention, éviter de renvoyer le message directement
                // récupéré depuis les erreurs SQL
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('admin/page/user/insert_user.html.twig');
    }

}