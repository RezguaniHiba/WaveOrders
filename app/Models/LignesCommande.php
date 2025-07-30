<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LignesCommande
 * 
 * @property int $id
 * @property int $commande_id
 * @property int $article_id
 * @property int $quantite
 * @property float $prix_unitaire_ht
 * @property float $taux_tva
 * @property float $remise_percent
 * @property float|null $montant_ht
 * @property float|null $montant_tva
 * @property string $statut
 * @property string|null $wavesoft_ligne_id
 * 
 * @property Commande $commande
 * @property Article $article
 * @property Collection|MouvementsStock[] $mouvements_stocks
 *
 * @package App\Models
 */
class LignesCommande extends Model
{
	protected $table = 'lignes_commande';
	public $timestamps = false;

	protected $casts = [
		'commande_id' => 'int',
		'article_id' => 'int',
		'quantite' => 'int',
		'prix_unitaire_ht' => 'float',
		'taux_tva' => 'float',
		'remise_percent' => 'float',
		'montant_ht' => 'float',
		'montant_tva' => 'float'
	];

	protected $fillable = [
		'commande_id',
		'article_id',
		'quantite',
		'prix_unitaire_ht',
		'taux_tva',
		'remise_percent',
		'montant_ht',
		'montant_tva',
		'statut',
		'wavesoft_ligne_id'
	];

	public function commande()
	{
		return $this->belongsTo(Commande::class);
	}

	public function article()
	{
		return $this->belongsTo(Article::class);
	}

	public function mouvements_stocks()
	{
		return $this->hasMany(MouvementsStock::class, 'ligne_commande_id');
	}
}
