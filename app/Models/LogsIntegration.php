<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LogsIntegration
 * 
 * @property int $id
 * @property string $type
 * @property string $entite
 * @property int|null $entite_id
 * @property string $action
 * @property string|null $details
 * @property string $statut
 * @property Carbon $date_log
 *
 * @package App\Models
 */
class LogsIntegration extends Model
{
	protected $table = 'logs_integration';
	public $timestamps = false;

	protected $casts = [
		'entite_id' => 'int',
		'date_log' => 'datetime'
	];

	protected $fillable = [
		'type',
		'entite',
		'entite_id',
		'action',
		'details',
		'statut',
		'date_log'
	];
}
