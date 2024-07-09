<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use App\Form\UploadPdfFormType;

class PdfController extends AbstractController
{
    private $httpClient;
    private $logger;

    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    #[Route('/upload-pdf', name: 'upload_pdf')]
    public function uploadPdf(Request $request): Response
    {
        $form = $this->createForm(UploadPdfFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logger->info('POST request received');

            $data = $form->getData();
            $file = $data['file'];
            $dimensions = $data['dimensions'];

            if (!$file) {
                $this->logger->error('No file received in the request');
                return new Response('Fichier invalide ou non fourni.', Response::HTTP_BAD_REQUEST);
            }

            $this->logger->info('Received a request', [
                'file' => $file->getClientOriginalName(),
                'dimensions' => $dimensions
            ]);

            if ($file->isValid()) {
                $this->logger->info('File is valid', ['file_path' => $file->getPathname()]);
                if ($file->getClientOriginalExtension() === 'pdf') {
                    $this->logger->info('File is a PDF');
                    $filePath = $file->getPathname();

                    // Renommer le fichier temporaire avec une extension correcte
                    $newFilePath = $filePath . '.pdf';
                    rename($filePath, $newFilePath);

                    // Utiliser FormDataPart pour gérer l'envoi multipart/form-data
                    $formFields = [
                        'dimensions' => $dimensions,
                        'file' => DataPart::fromPath($newFilePath)
                    ];
                    $formData = new FormDataPart($formFields);

                    try {
                        $response = $this->httpClient->request('POST', 'http://127.0.0.1:5000/api/crop_convert', [
                            'headers' => $formData->getPreparedHeaders()->toArray(),
                            'body' => $formData->bodyToString(),
                        ]);

                        if ($response->getStatusCode() === 200) {
                            $this->logger->info('Received a successful response from Flask API');
                            $data = $response->toArray();

                            // Store PNG URLs in the session to display them in the next request
                            $request->getSession()->set('png_urls', $data['png_urls']);

                            return $this->redirectToRoute('display_pngs');
                        } else {
                            $error = $response->toArray(false);
                            $this->logger->error('Error from Flask API', ['error' => $error]);
                            return new Response($error['error'] ?? 'Erreur lors du traitement du PDF', $response->getStatusCode());
                        }
                    } catch (\Exception $e) {
                        $this->logger->error('Error sending request to Flask API', [
                            'exception' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        return new Response('Erreur lors de l\'envoi à l\'API Flask', Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                } else {
                    $this->logger->error('File is not a PDF');
                    return new Response('Le fichier n\'est pas un PDF.', Response::HTTP_BAD_REQUEST);
                }
            } else {
                $this->logger->error('File is not valid');
                return new Response('Le fichier n\'est pas valide.', Response::HTTP_BAD_REQUEST);
            }
        }

        return $this->render('pdf/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/display-pngs', name: 'display_pngs')]
    public function displayPngs(Request $request): Response
    {
        $pngUrls = $request->getSession()->get('png_urls', []);

        return $this->render('pdf/display.html.twig', [
            'png_urls' => $pngUrls
        ]);
    }
}
