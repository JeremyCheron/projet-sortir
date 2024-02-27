<?php

namespace App\Controller;

use App\Services\CsvImporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CSVImportController extends AbstractController
{
    #[Route('/csvimport', name: 'app_csv_import')]
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
