<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reglement extends Model
{
    use HasFactory;
    protected $casts = [
		'date_reglement' => 'datetime',
    ];
    protected $fillable = [
        'commande_id',
        'montant',
        'mode',
        'fichier_justificatif',
        'type_facturation',
        'date_reglement',
        'client_payeur_id',
        'commentaire',
        'cree_par',
        'wavesoft_ref',
    ];

    /**
     * Relation avec la commande.
     */
    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    /**
     * Utilisateur qui a saisi le règlement.
     */
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'cree_par');
    }

    /**
     * Client payeur (si différent du client de la commande).
     */
    public function clientPayeur()
    {
        return $this->belongsTo(Client::class, 'client_payeur_id');
    }
     /**
     * Accesseur pour le libellé du mode de paiement
     */
    public function getModeLibelleAttribute()
    {
        $modes = [
            'especes' => 'Espèces',
            'cheque' => 'Chèque',
            'carte_bancaire' => 'Carte bancaire',
            'virement' => 'Virement',
            'autre' => 'Autre'
        ];

        return $modes[$this->mode] ?? $this->mode;
    }

    /**
     * Accesseur pour le libellé du type de facturation
     */
    public function getTypeFacturationLibelleAttribute()
    {
        $types = [
            'facturer_client' => 'Facturer client',
            'client_payeur' => 'Client payeur',
            'autre' => 'Autre'
        ];

        return $types[$this->type_facturation] ?? $this->type_facturation;
    }
}
