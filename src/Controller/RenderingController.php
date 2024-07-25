<?php


namespace App\Controller;

use App\Entity\Project;
use App\Entity\Rendering;
use App\Form\AddRenderingType;
use App\Form\RenderingValidationFormType;
use App\Form\EditRenderingType; // Ajouter un formulaire pour l'édition
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Uid\Uuid;

class RenderingController extends AbstractController
{
    #[Route('/project/{id}/new-rendering', name: 'rendering_create', methods: ['GET', 'POST'])]
    public function createRendering(Request $request, Project $project, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $rendering = new Rendering();
        $form = $this->createForm(AddRenderingType::class, $rendering);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rendering->setProject($project);
            $rendering->setToken(Uuid::v4()->toRfc4122());

            $entityManager->persist($rendering);
            $entityManager->flush();

            return $this->redirectToRoute('rendering_show', ['project_id' => $project->getId(), 'rendering_id' => $rendering->getId()]);
        }

        return $this->render('rendering/create.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
        ]);
    }

    #[Route('/project/{project_id}/rendering/{rendering_id}', name: 'rendering_show', requirements: ['project_id' => '\d+', 'rendering_id' => '\d+'], methods: ['GET'])]
    public function showRendering(int $project_id, int $rendering_id, EntityManagerInterface $entityManager): Response
    {
        $project = $entityManager->getRepository(Project::class)->find($project_id);
        $rendering = $entityManager->getRepository(Rendering::class)->find($rendering_id);

        if (!$project || !$rendering) {
            throw $this->createNotFoundException('Project or rendering not found');
        }

        return $this->render('rendering/show.html.twig', [
            'project' => $project,
            'rendering' => $rendering,
        ]);
    }

    #[Route('/rendering/client/{token}', name: 'rendering_client_show', methods: ['GET'])]
    public function showClientRendering(string $token, EntityManagerInterface $entityManager): Response
    {
        $rendering = $entityManager->getRepository(Rendering::class)->findOneBy(['token' => $token]);

        if (!$rendering) {
            throw $this->createNotFoundException('Rendering not found');
        }

        return $this->render('rendering/client_show.html.twig', [
            'rendering' => $rendering,
        ]);
    }

    #[Route('/rendering/client/{token}/approve', name: 'rendering_client_approve', methods: ['GET', 'POST'])]
    public function approveRendering(Request $request, string $token, EntityManagerInterface $entityManager): Response
    {
        $rendering = $entityManager->getRepository(Rendering::class)->findOneBy(['token' => $token]);

        if (!$rendering) {
            throw $this->createNotFoundException('Rendering not found');
        }

        $form = $this->createForm(RenderingValidationFormType::class, $rendering);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('rendering_client_show', ['token' => $token]);
        }

        return $this->render('rendering/approve.html.twig', [
            'form' => $form->createView(),
            'rendering' => $rendering,
        ]);
    }

    #[Route('/project/{project_id}/rendering/{rendering_id}/edit', name: 'rendering_edit', methods: ['GET', 'POST'])]
    public function editRendering(Request $request, int $project_id, int $rendering_id, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $rendering = $entityManager->getRepository(Rendering::class)->find($rendering_id);

        if (!$rendering) {
            throw $this->createNotFoundException('Rendering not found');
        }

        $form = $this->createForm(EditRenderingType::class, $rendering);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('rendering_show', ['project_id' => $project_id, 'rendering_id' => $rendering_id]);
        }

        return $this->render('rendering/edit.html.twig', [
            'form' => $form->createView(),
            'rendering' => $rendering,
        ]);
    }

    #[Route('/project/{project_id}/rendering/{rendering_id}/delete', name: 'rendering_delete', methods: ['POST'])]
    public function deleteRendering(Request $request, int $project_id, int $rendering_id, EntityManagerInterface $entityManager): Response
    {
        $rendering = $entityManager->getRepository(Rendering::class)->find($rendering_id);

        if (!$rendering) {
            throw $this->createNotFoundException('Rendering not found');
        }

        if ($this->isCsrfTokenValid('delete'.$rendering->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rendering);
            $entityManager->flush();

            return $this->redirectToRoute('project_show', ['id' => $project_id]);
        }

        return $this->redirectToRoute('rendering_show', ['project_id' => $project_id, 'rendering_id' => $rendering_id]);
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
}
