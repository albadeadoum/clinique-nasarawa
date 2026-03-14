<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Entity\PrescriptionPrestation;
use App\Entity\ResultatLaboratoire;
use App\Entity\ResultatLaboratoireLigne;
use App\Enum\StatutPrescriptionPrestation;
use App\Form\ResultatLaboratoireType;
use App\Repository\PrescriptionPrestationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/laboratoire')]
final class LaboratoireController extends AbstractController
{
    #[Route('', name: 'app_laboratoire_index', methods: ['GET'])]
    public function index(PrescriptionPrestationRepository $repository): Response
    {
        $aTraiter = $repository->findExamensLaboAPrendreEnCharge();
        $enCours = $repository->findExamensLaboEnCours();
        $realises = $repository->findExamensLaboRealises();

        return $this->render('laboratoire/index.html.twig', [
            'aTraiter' => $aTraiter,
            'enCours' => $enCours,
            'realises' => $realises,
        ]);
    }

    #[Route('/prestation/{id}', name: 'app_laboratoire_show', methods: ['GET'])]
    public function show(PrescriptionPrestation $prestation): Response
    {
        $this->verifierDestinationLaboratoire($prestation);

        return $this->render('laboratoire/show.html.twig', [
            'prestation' => $prestation,
        ]);
    }

    #[Route('/prestation/{id}/prendre-en-charge', name: 'app_laboratoire_prendre_en_charge', methods: ['POST'])]
    public function prendreEnCharge(
        PrescriptionPrestation $prestation,
        EntityManagerInterface $em
    ): Response {
        $this->verifierDestinationLaboratoire($prestation);

        if ($prestation->getStatut() === StatutPrescriptionPrestation::PAYE) {
            $prestation->setStatut(StatutPrescriptionPrestation::EN_COURS);
            $em->flush();
        }

        return $this->redirectToRoute('app_laboratoire_show', [
            'id' => $prestation->getId(),
        ]);
    }

    #[Route('/prestation/{id}/realiser', name: 'app_laboratoire_realiser', methods: ['POST'])]
    public function realiser(
        PrescriptionPrestation $prestation,
        EntityManagerInterface $em
    ): Response {
        $this->verifierDestinationLaboratoire($prestation);

        if (\in_array($prestation->getStatut(), [
            StatutPrescriptionPrestation::PAYE,
            StatutPrescriptionPrestation::EN_COURS,
        ], true)) {
            $prestation->setStatut(StatutPrescriptionPrestation::REALISE);
            $em->flush();
        }

        return $this->redirectToRoute('app_laboratoire_show', [
            'id' => $prestation->getId(),
        ]);
    }

    private function verifierDestinationLaboratoire(PrescriptionPrestation $prestation): void
    {
        $service = $prestation->getTarifPrestation()?->getServiceExecution();

        if ($service !== 'laboratoire') {
            throw $this->createNotFoundException('Cette prestation ne relève pas du laboratoire.');
        }
    }

    #[Route('/bon/consultation/{id}', name: 'app_laboratoire_bon_show', methods: ['GET'])]
    public function bonShow(
        Consultation $consultation,
        PrescriptionPrestationRepository $repository
    ): Response {
        $examens = $repository->findExamensLaboPayesParConsultation($consultation->getId());

        if (count($examens) === 0) {
            $this->addFlash('warning', 'Aucun examen laboratoire payé trouvé pour cette consultation.');
            return $this->redirectToRoute('app_laboratoire_index');
        }

        return $this->render('laboratoire/bon_show.html.twig', [
            'consultation' => $consultation,
            'examens' => $examens,
        ]);
    }

    #[Route('/bon/consultation/{id}/print', name: 'app_laboratoire_bon_print', methods: ['GET'])]
    public function bonPrint(
        Consultation $consultation,
        PrescriptionPrestationRepository $repository
    ): Response {
        $examens = $repository->findExamensLaboPayesParConsultation($consultation->getId());

        if (count($examens) === 0) {
            throw $this->createNotFoundException('Aucun examen laboratoire imprimable pour cette consultation.');
        }

        return $this->render('laboratoire/bon_print.html.twig', [
            'consultation' => $consultation,
            'examens' => $examens,
        ]);
    }

    #[Route('/prestation/{id}/resultat', name: 'app_laboratoire_resultat_edit', methods: ['GET', 'POST'])]
public function saisirResultat(
    Request $request,
    PrescriptionPrestation $prestation,
    EntityManagerInterface $em
): Response {
    $this->verifierDestinationLaboratoire($prestation);

    $resultat = $prestation->getResultatLaboratoire();
    if (!$resultat) {
        $resultat = new ResultatLaboratoire();
        $resultat->setPrescriptionPrestation($prestation);
        $prestation->setResultatLaboratoire($resultat);

        $ligne = new ResultatLaboratoireLigne();
        $ligne->setDemande($prestation->getTarifPrestation()?->getLibelle() ?? 'Examen');
        $ligne->setOrdre(1);
        $resultat->addLigne($ligne);
    }

    $form = $this->createForm(ResultatLaboratoireType::class, $resultat, [
        'action' => $this->generateUrl('app_laboratoire_resultat_edit', [
            'id' => $prestation->getId(),
        ]),
        'method' => 'POST',
    ]);

    $form->handleRequest($request);

    if ($request->isXmlHttpRequest()) {
        if ($form->isSubmitted() && $form->isValid()) {
            $resultat->setDateValidation(new \DateTimeImmutable());

            $user = $this->getUser();
            if ($user && method_exists($user, 'getNomComplet')) {
                $resultat->setValidePar($user->getNomComplet());
            } elseif ($user && method_exists($user, 'getUserIdentifier')) {
                $resultat->setValidePar($user->getUserIdentifier());
            }

            if ($prestation->getStatut() !== StatutPrescriptionPrestation::REALISE) {
                $prestation->setStatut(StatutPrescriptionPrestation::REALISE);
            }

            $em->persist($resultat);
            $em->flush();

            return $this->json([
                'success' => true,
                'message' => 'Résultat laboratoire enregistré avec succès.',
            ]);
        }

        return $this->render('laboratoire/_resultat_form.html.twig', [
            'form' => $form->createView(),
            'prestation' => $prestation,
            'resultat' => $resultat,
        ]);
    }

    if ($form->isSubmitted() && $form->isValid()) {
        $resultat->setDateValidation(new \DateTimeImmutable());

        $user = $this->getUser();
        if ($user && method_exists($user, 'getNomComplet')) {
            $resultat->setValidePar($user->getNomComplet());
        } elseif ($user && method_exists($user, 'getUserIdentifier')) {
            $resultat->setValidePar($user->getUserIdentifier());
        }

        if ($prestation->getStatut() !== StatutPrescriptionPrestation::REALISE) {
            $prestation->setStatut(StatutPrescriptionPrestation::REALISE);
        }

        $em->persist($resultat);
        $em->flush();

        return $this->redirectToRoute('app_laboratoire_show', [
            'id' => $prestation->getId(),
        ]);
    }

    return $this->render('laboratoire/resultat_form.html.twig', [
        'prestation' => $prestation,
        'form' => $form->createView(),
        'resultat' => $resultat,
    ]);
}

    #[Route('/prestation/{id}/resultat/print', name: 'app_laboratoire_resultat_print', methods: ['GET'])]
    public function imprimerResultat(PrescriptionPrestation $prestation): Response
    {
        $this->verifierDestinationLaboratoire($prestation);

        $resultat = $prestation->getResultatLaboratoire();
        if (!$resultat) {
            throw $this->createNotFoundException('Aucun résultat laboratoire disponible pour cette prestation.');
        }

        return $this->render('laboratoire/resultat_print.html.twig', [
            'prestation' => $prestation,
            'resultat' => $resultat,
        ]);
    }
}