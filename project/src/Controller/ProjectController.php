<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UsersRepository;
use App\Form\UsersType;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class ProjectController extends AbstractController
{
    /**
     * @Route("/project", name="app_project")
     */
    public function welcome(EntityManagerInterface $manager, UsersRepository $repository, Request $request, PaginatorInterface 
        $paginator): Response
    {
        $users = $repository->findAll();
        $addPaginator = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            5
        );
        $entity = new Users();
        $form = $this->createForm(UsersType::class, $entity);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $task = $form->getData();
            $manager->persist($task);
            $manager->flush();

            return $this->redirectToRoute('welcome');
        }
        return $this->render('project/index.html.twig', [
            'controller_name' => 'ProjectController',
            'form' => $form->createView(),
            'users' => $addPaginator,
        ]);
    }
}
