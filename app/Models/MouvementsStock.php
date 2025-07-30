<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MouvementsStock
 * 
 * @property int $id
 * @property int $ligne_commande_id
 * @property int $quantite
 * @property string $type_mouvement
 * @property Carbon|null $date_mouvement
 * 
 * @property LignesCommande $lignes_commande
 *
 * @package App\Models
 */
class MouvementsStock extends Model
{
	protected $table = 'mouvements_stock';
	public $timestamps = false;

	protected $casts = [
		'ligne_commande_id' => 'int',
		'quantite' => 'int',
		'date_mouvement' => 'datetime'
	];

	protected $fillable = [
		'ligne_commande_id',
		'quantite',
		'type_mouvement',
		'date_mouvement'
	];

	public function lignes_commande()
	{
		return $this->belongsTo(LignesCommande::class, 'ligne_commande_id');
	}
}
