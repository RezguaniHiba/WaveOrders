<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HistoriqueCommande
 * 
 * @property int $id
 * @property int $commande_id
 * @property string $action
 * @property string|null $details
 * @property string|null $ancienne_valeur
 * @property string|null $nouvelle_valeur
 * @property Carbon $date_action
 * 
 * @property Commande $commande
 *
 * @package App\Models
 */
class HistoriqueCommande extends Model
{
	protected $table = 'historique_commandes';
	public $timestamps = false;

	protected $casts = [
		'commande_id' => 'int',
		'date_action' => 'datetime'
	];

	protected $fillable = [
		'commande_id',
		'action',
		'details',
		'ancienne_valeur',
		'nouvelle_valeur',
		'date_action'
	];

	public function commande()
	{
		return $this->belongsTo(Commande::class);
	}
}
