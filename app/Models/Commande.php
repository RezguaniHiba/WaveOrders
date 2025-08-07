<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Commande
 * 
 * @property int $id
 * @property string $numero
 * @property int $client_id
 * @property int|null $commercial_id
 * @property int|null $cree_par
 * @property Carbon $date_commande
 * @property Carbon|null $date_livraison_prevue
 * @property string $statut
 * @property float|null $montant_ht
 * @property float|null $montant_tva
 * @property float|null $montant_ttc
 * @property float|null $remise_percent
 * @property float|null $remise_montant
 * @property string|null $notes
 * @property string|null $wavesoft_piece_id
 * @property Carbon|null $date_export_wavesoft
 * @property Carbon|null $date_maj
 * 
 * @property Client $client
 * @property Utilisateur|null $utilisateur
 * @property Collection|HistoriqueCommande[] $historique_commandes
 * @property Collection|LignesCommande[] $lignes_commandes
 * @property Collection|TransfertsWavesoft[] $transferts_wavesofts
 *
 * @package App\Models
 */
class Commande extends Model
{
	protected $table = 'commandes';
	public $timestamps = false;

	protected $casts = [
		'client_id' => 'int',
		'commercial_id' => 'int',
		'cree_par' => 'int',
		'date_commande' => 'datetime',
		'date_livraison_prevue' => 'datetime',
		'montant_ht' => 'float',
		'montant_tva' => 'float',
		'montant_ttc' => 'float',
		'remise_percent' => 'float',
		'remise_montant' => 'float',
		'date_export_wavesoft' => 'datetime',
		'date_maj' => 'datetime'
	];

	protected $fillable = [
		'numero',
		'client_id',
		'commercial_id',
		'cree_par',
		'date_commande',
		'date_livraison_prevue',
		'statut',
		'montant_ht',
		'montant_tva',
		'montant_ttc',
		'remise_percent',
		'remise_montant',
		'notes',
		'wavesoft_piece_id',
		'date_export_wavesoft',
		'date_maj'
	];

	public function client()
	{
		return $this->belongsTo(Client::class);
	}

	public function utilisateur()
	{
		return $this->belongsTo(Utilisateur::class, 'cree_par');
	}

	public function historique_commandes()
	{
		return $this->hasMany(HistoriqueCommande::class);
	}
	public function lignesCommande()
	{
		return $this->hasMany(LignesCommande::class);
	}

	public function transferts_wavesofts()
	{
		return $this->hasMany(TransfertsWavesoft::class);
	}
	//POUR NE MODIFIE QUE LES COMMANDES non encore livreou bien annule
	public function estModifiable()
	{
		return !in_array($this->statut, ['complètement_livree', 'annulee']);
	}
		public function reglements()
	{
		return $this->hasMany(Reglement::class);
	}
	public function getMontantRestantAttribute()

	{
    return $this->montant_ttc - $this->reglements()->sum('montant');
	}


}
