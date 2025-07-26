<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TransfertsWavesoft
 * 
 * @property int $id
 * @property int $commande_id
 * @property Carbon|null $date_export
 * @property string|null $fichier_genere
 * @property string|null $etat_traitement
 * @property string|null $message_retour
 * @property string|null $wavesoft_code_objet
 * @property string|null $wavesoft_etat
 * 
 * @property Commande $commande
 *
 * @package App\Models
 */
class TransfertsWavesoft extends Model
{
	protected $table = 'transferts_wavesoft';
	public $timestamps = false;

	protected $casts = [
		'commande_id' => 'int',
		'date_export' => 'datetime'
	];

	protected $fillable = [
		'commande_id',
		'date_export',
		'fichier_genere',
		'etat_traitement',
		'message_retour',
		'wavesoft_code_objet',
		'wavesoft_etat'
	];

	public function commande()
	{
		return $this->belongsTo(Commande::class);
	}
}
