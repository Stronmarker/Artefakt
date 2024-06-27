<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Rendering;
use App\Form\ProjectType;
use App\Form\AddRenderingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProjectController extends AbstractController
{
    #[Route('/project', name: 'app_project')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $projects = $entityManager->getRepository(Project::class)->findAll();

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/project/create', name: 'project_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->getCreatedAt(new \DateTime());
            $project->getUpdatedAt(new \DateTime());

            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('app_project');
        }

        return $this->render('project/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/project/{id}', name: 'project_show')]
    public function show(Project $project, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $rendering = new Rendering();
        $form = $this->createForm(AddRenderingType::class, $rendering);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rendering->setProject($project);

            $frontPngFile = $form->get('frontPng')->getData();
            $towardPngFile = $form->get('towardPng')->getData();
            $gildingSvgFile = $form->get('gildingSvg')->getData();
            $laminationSvgFile = $form->get('laminationSvg')->getData();

            if ($frontPngFile) {
                $frontPngFileName = $this->uploadFile($frontPngFile, $slugger);
                $rendering->setFrontPng($frontPngFileName);
            }

            if ($towardPngFile) {
                $towardPngFileName = $this->uploadFile($towardPngFile, $slugger);
                $rendering->setTowardPng($towardPngFileName);
            }

            if ($gildingSvgFile) {
                $gildingSvgFileName = $this->uploadFile($gildingSvgFile, $slugger);
                $rendering->setGildingSvg($gildingSvgFileName);
            }

            if ($laminationSvgFile) {
                $laminationSvgFileName = $this->uploadFile($laminationSvgFile, $slugger);
                $rendering->setLaminationSvg($laminationSvgFileName);
            }

            $project->getUpdatedAt(new \DateTime());

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

    private function uploadFile($file, SluggerInterface $slugger)
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
}
