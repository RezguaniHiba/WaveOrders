<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SyncWavesoft
 * 
 * @property int $id
 * @property string $entite_type
 * @property int $entite_id
 * @property string $action
 * @property string $statut
 * @property Carbon $date_creation
 * @property Carbon|null $date_traitement
 * @property string|null $erreur
 * @property int $tentatives
 *
 * @package App\Models
 */
class SyncWavesoft extends Model
{
	protected $table = 'sync_wavesoft';
	public $timestamps = false;

	protected $casts = [
		'entite_id' => 'int',
		'date_creation' => 'datetime',
		'date_traitement' => 'datetime',
		'tentatives' => 'int'
	];

	protected $fillable = [
		'entite_type',
		'entite_id',
		'action',
		'statut',
		'date_creation',
		'date_traitement',
		'erreur',
		'tentatives'
	];
}
