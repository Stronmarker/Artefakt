<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ProjectClientType; //Oublies pas ça, il faut importer le form dans le controller 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    #[Route('/project', name: 'app_project')]
    public function index(): Response
    {
        return $this->render('project/index.html.twig', [
            'controller_name' => 'ProjectController',
        ]);
    }

    #[Route('/project/create', name: 'project_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();

        $form = $this->createForm(ProjectClientType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client = new Client();
            $client->setClientName($form->get('client_name')->getData());
            $client->setClientEmail($form->get('client_email')->getData());

            $entityManager->persist($client);
            $entityManager->flush();

            $project->setClient($client);
            $project->setCreationDate(new \DateTime());
            $project->setModificationDate(new \DateTime());

            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('project/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/project/{id}', name: 'project_show')]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

}
