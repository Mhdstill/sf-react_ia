<?php

// src/Service/StepFormManager.php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class StepFormService
{

    public function __construct(private EntityManagerInterface $entityManager, private ToolsService $toolsService)
    {
    }

    public function processStep(int $step, $data)
    {
        switch ($step) {
            case 1:
                $this->processStep1($data);
                break;
            case 2:
                $this->processStep2($data);
                break;
            default:
                throw new \InvalidArgumentException("Invalid step");
        }
    }

    private function processStep1($data)
    {
        // Convertir le nom en slug
        $slug = $this->toolsService->slugify($data['name']);

        // Définir le chemin où le dépôt sera cloné
        $baseDir = '/Users/mehdist/Desktop/Projets/';
        $targetDir = $baseDir . $slug;

        // Vérifier si le dossier existe déjà
        if (file_exists($targetDir)) {
            throw new \RuntimeException("Le dossier $targetDir existe déjà.");
        }

        // Commande git pour cloner le dépôt
        $repoUrl = 'https://github.com/dunglas/symfony-docker.git';
        $command = sprintf('git clone %s %s', escapeshellarg($repoUrl), escapeshellarg($targetDir));

        // Exécuter la commande
        $output = [];
        $returnVar = null;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \RuntimeException('Le clonage du dépôt a échoué.');
        }
        echo "Le dépôt a été cloné avec succès dans le dossier : $targetDir";

    }

    private function processStep2($data)
    {

    }

    public function getTemplateForStep(int $step): string
    {
        switch ($step) {
            case 2:
                return 'step2.html.twig';
            default:
                return 'index.html.twig';
        }
    }
}
