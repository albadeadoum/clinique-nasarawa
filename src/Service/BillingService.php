<?php
namespace App\Service;

use App\Entity\Consultation;
use App\Entity\Facture;
use App\Entity\FactureLigne;
use App\Enum\ModePaiement;
use App\Enum\StatutPaiement;
use Doctrine\ORM\EntityManagerInterface;

class BillingService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function generateDraftInvoice(Consultation $c, float $forfaitConsultation = 0): Facture
    {
        $facture = $c->getFacture();

        if (!$facture) {
            $facture = new Facture();
            $facture->setConsultation($c);
            $facture->setDateEmission(new \DateTimeImmutable());
            $facture->setStatutPaiement(StatutPaiement::EN_ATTENTE);

            $this->em->persist($facture);

            // IMPORTANT: assure-toi que Consultation a setFacture()
            $c->setFacture($facture);
        }

        // Si déjà payée => on ne touche pas (protection)
        if ($facture->getStatutPaiement() === StatutPaiement::PAYE) {
            return $facture;
        }

        // Rebuild lignes (idempotent)
        $facture->clearLignes();

        if ($forfaitConsultation > 0) {
            $l = (new FactureLigne())
                ->setType('CONSULTATION')
                ->setLibelle('Consultation')
                ->setQuantite(1)
                ->setPrixUnitaire(number_format($forfaitConsultation, 2, '.', ''));
            $l->recalc();
            $facture->addLigne($l);
        }

        foreach ($c->getActesRealises() as $acte) {
            $pu = $acte->getPrixUnitaire();
            if ($pu === null || (float) $pu <= 0) continue;

            $l = (new FactureLigne())
                ->setType('ACTE')
                ->setLibelle($acte->getLibelle())
                ->setQuantite($acte->getQuantite() ?: 1)
                ->setPrixUnitaire($pu);

            $l->recalc();
            $facture->addLigne($l);
        }

        foreach ($c->getExamensDemandes() as $ex) {
            $pu = $ex->getPrixUnitaire();
            if ($pu === null || (float) $pu <= 0) continue;

            $l = (new FactureLigne())
                ->setType('EXAMEN')
                ->setLibelle($ex->getLibelle())
                ->setQuantite(1)
                ->setPrixUnitaire($pu);

            $l->recalc();
            $facture->addLigne($l);
        }

        $facture->recalcMontant();

        return $facture;
    }

    public function payInvoice(Facture $facture, ModePaiement $mode): void
    {
        if ($facture->getStatutPaiement() === StatutPaiement::PAYE) {
            return;
        }

        $facture->setModePaiement($mode);
        $facture->setDatePaiement(new \DateTimeImmutable());
        $facture->setStatutPaiement(StatutPaiement::PAYE);
    }
}