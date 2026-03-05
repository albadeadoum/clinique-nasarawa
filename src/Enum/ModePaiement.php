<?php
namespace App\Enum;

enum ModePaiement: string
{
    case ESPECES = 'especes';
    case CARTE = 'carte';
    case MOBILE_MONEY = 'mobile_money';
    case VIREMENT = 'virement';
}