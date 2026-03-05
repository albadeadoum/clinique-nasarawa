<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Entity\Prescription;
use App\Form\PrescriptionType;
use App\Repository\PrescriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/prescription')]
final class PrescriptionController extends AbstractController
{
    #[Route(name: 'app_prescription_index', methods: ['GET'])]
    public function index(PrescriptionRepository $prescriptionRepository): Response
    {
        return $this->render('prescription/index.html.twig', [
            'prescriptions' => $prescriptionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_prescription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $prescription = new Prescription();
        $form = $this->createForm(PrescriptionType::class, $prescription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($prescription);
            $entityManager->flush();

            return $this->redirectToRoute('app_prescription_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prescription/new.html.twig', [
            'prescription' => $prescription,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prescription_show', methods: ['GET'])]
    public function show(Prescription $prescription): Response
    {
        return $this->render('prescription/show.html.twig', [
            'prescription' => $prescription,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_prescription_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Prescription $prescription, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PrescriptionType::class, $prescription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_prescription_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prescription/edit.html.twig', [
            'prescription' => $prescription,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prescription_delete', methods: ['POST'])]
    public function delete(Request $request, Prescription $prescription, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$prescription->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($prescription);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_prescription_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/consultation/{id}/prescription/modal', name: 'app_consultation_prescription_modal', methods: ['GET', 'POST'])]
    public function modal(
        Consultation $consultation,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        // Règle: on édite la dernière prescription, sinon on en crée une
        $prescription = $consultation->getPrescriptions()->last() ?: null;

        if (!$prescription) {
            $prescription = new Prescription();
            $prescription->setConsultation($consultation);
            $em->persist($prescription);
        }

        $form = $this->createForm(PrescriptionType::class, $prescription, [
            'action' => $this->generateUrl('app_consultation_prescription_modal', ['id' => $consultation->getId()]),
        ]);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();

                return $this->json([
                    'success' => true,
                    'summaryHtml' => $this->renderView('prescription/_summary.html.twig', [
                        'consultation' => $consultation,
                    ]),
                ]);
            }

            // erreurs -> renvoyer le contenu HTML du formulaire
            return $this->json([
                'success' => false,
                'html' => $this->renderView('prescription/_modal_form.html.twig', [
                    'consultation' => $consultation,
                    'form' => $form->createView(),
                    'prescription' => $prescription,
                ]),
            ], 422);
        }

        // GET : afficher le modal
        return $this->render('prescription/_modal_form.html.twig', [
            'consultation' => $consultation,
            'form' => $form->createView(),
            'prescription' => $prescription,
        ]);
    }
}
