<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Rendering;
use App\Form\ProjectType;
use App\Form\AddRenderingType;
use Symfony\Component\Uid\Uuid;
use App\Repository\ProjectRepository;
use App\Repository\RenderingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[route('/project')]
class ProjectController extends AbstractController
{
    #[Route('/', name: 'app_project')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $projects = $entityManager->getRepository(Project::class)->findBy(['createdBy' => $user]);

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/create', name: 'project_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setCreatedBy($this->getUser());


            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('app_project');
        }

        return $this->render('project/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'project_show', methods: 'GET')]
    public function show(Project $project, RenderingRepository $renderingRepository): Response
    {
        if ($project->getCreatedBy() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès refusé : Vous n\'êtes pas le créateur de ce projet');
        }
    
        $projectRenderings = $renderingRepository->findBy(
            ['project' => $project->getId()],
            ['createdAt' => 'DESC']
        );
    
        return $this->render('project/show.html.twig', [
            'project' => $project,
            'renderings' => $projectRenderings,
        ]);
    }

    #[Route('/{id}/edit', name: 'project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_project');
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'project_delete', methods: ['DELETE', 'POST'])]
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($project);
            $entityManager->flush();

            $this->addFlash('success', 'Project deleted successfully.');
        }

        return $this->redirectToRoute('app_project', [], Response::HTTP_SEE_OTHER);
    }
}
