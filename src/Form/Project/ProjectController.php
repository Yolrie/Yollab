<?php

namespace App\Controller;

use App\Form\Project\CreateProjectFlowType;
use App\Model\ProjectData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProjectController extends AbstractController
{
    #[Route('/project/new', name: 'app_project_new')]
    public function new(Request $request): Response
    {
        $projectData = new ProjectData();
        $flow = $this->createFlow(CreateProjectFlowType::class, $projectData);
        $flow->handleRequest($request);

        if ($flow->isFinished()) {
            // Ici tu ferais le persist en BDD plus tard
            $this->addFlash('success', sprintf(
                'Projet "%s" créé ! (visibilité: %s, priorité: %s)',
                $projectData->name,
                $projectData->visibility,
                $projectData->priority
            ));

            return $this->redirectToRoute('app_project_new');
        }

        return $this->render('project/new.html.twig', [
            'step_form' => $flow->getStepForm(),
            'flow' => $flow,
        ]);
    }
}
