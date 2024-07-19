<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Rendering;
use App\Form\ProjectType;
use App\Form\AddRenderingType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Uid\Uuid;

#[route('/project')]
class ProjectController extends AbstractController
{
    #[Route('/', name: 'app_project')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        // $projects = $entityManager->getRepository(Project::class)->findAll(); Ici on affichait tous les projets
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
    public function show(Project $project, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {

        
        if ($project->getCreatedBy() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès refusé : Vous n\'etes pas le créateur de ce projet'); //TODO sweetAlert
        }

        $rendering = new Rendering();
        $form = $this->createForm(AddRenderingType::class, $rendering);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rendering->setProject($project);
            $rendering->setUser($this->getUser());

            $frontPngFile = $form->get('frontPng')->getData();
            $towardPngFile = $form->get('towardPng')->getData();

            if ($frontPngFile) {
                $frontPngFileName = $this->uploadFile($frontPngFile, $slugger);
                $rendering->setFrontPng($frontPngFileName);
            }

            if ($towardPngFile) {
                $towardPngFileName = $this->uploadFile($towardPngFile, $slugger);
                $rendering->setTowardPng($towardPngFileName);
            }

            $entityManager->persist($rendering);
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('project/show.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    private function uploadFile($file, SluggerInterface $slugger): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move(
                $this->getParameter('renderings_directory'),
                $newFilename
            );
        } catch (FileException $e) {
            // Handle exception
        }

        return $newFilename;
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