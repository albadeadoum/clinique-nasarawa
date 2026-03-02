<?php
// src/Domain/Core/Enum/StatutRendezVous.php
namespace App\Enum;


enum StatutExamenDemande: string
{
    case DEMANDE = 'demande';
    case PRELEVE = 'preleve';
    case RESULTAT_RECU = 'resultat_recu';
    case ANNULE = 'annule';
}