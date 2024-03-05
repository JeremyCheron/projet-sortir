<?php

namespace App\Controller;

use App\Services\CsvImporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CSVImportController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/csvimport', name: 'app_csv_import')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Request $request, CsvImporter $csvImporter): Response
    {

        $csvFile = $request->files->get('csvFile');

        if ($csvFile) {
            $csvFilePath = $csvFile->getPathName();

            $csvImporter->importUserCsv($csvFilePath);

            $message = "CSV File imported successfully !";
        } else {
            $message = "Please select a CSV file !";
        }

        return $this->render('csv_import/index.html.twig', [
            'message' => $message,
        ]);
    }
}
