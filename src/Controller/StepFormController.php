<?php

// src/Controller/StepFormController.php

namespace App\Controller;

use App\Service\StepFormService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\Step1Type;
use App\Form\Step2Type;
// Ajoute d'autres formulaires nécessaires ici

class StepFormController extends AbstractController
{
    #[Route('/', name: 'multi_step_form')]
    public function index(Request $request, SessionInterface $session, StepFormService $stepFormService): Response
    {
        $step = $request->query->get('step', 1);
        $totalSteps = 5;

        $form = $this->getFormForStep($step);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stepFormService->processStep($step, $form->getData());

            if ($step < $totalSteps) {
                return $this->redirectToRoute('multi_step_form', ['step' => $step + 1]);
            }

            return $this->redirectToRoute('success_page');
        }

        $template = $stepFormService->getTemplateForStep($step);

        return $this->render($template, [
            'form' => $form->createView(),
            'steps' => range(1, $totalSteps),
            'currentStep' => $step,
        ]);
    }

    #[Route('/success', name: 'success_page')]
    public function success(): Response
    {
        return $this->render('success.html.twig');
    }

    private function getFormForStep(int $step)
    {
        switch ($step) {
            case 1:
                return $this->createForm(Step1Type::class);
            case 2:
                return $this->createForm(Step2Type::class);
            // Ajoute ici d'autres cases pour chaque étape.
            default:
                throw $this->createNotFoundException('Step not found.');
        }
    }
}
