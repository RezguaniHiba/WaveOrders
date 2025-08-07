<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Client
 * 
 * @property int $id
 * @property string|null $code_wavesoft
 * @property string $nom
 * @property string|null $email
 * @property string|null $telephone
 * @property string|null $adresse
 * @property string|null $ville
 * @property string|null $code_postal
 * @property string|null $pays
 * @property Carbon $date_creation
 * @property Carbon|null $date_maj
 * @property int|null $commercial_id
 * 
 * @property Utilisateur|null $utilisateur
 * @property Collection|Commande[] $commandes
 *
 * @package App\Models
 */
class Client extends Model
{
	protected $table = 'clients';
	public $timestamps = false;

	protected $casts = [
		'date_creation' => 'datetime',
		'date_maj' => 'datetime',
		'commercial_id' => 'int'
	];

	protected $fillable = [
		'code_wavesoft',
		'nom',
		'email',
		'telephone',
		'adresse',
		'ville',
		'code_postal',
		'pays',
		'date_creation',
		'date_maj',
		'commercial_id'
	];

	public function utilisateur()
	{
		return $this->belongsTo(User::class, 'commercial_id');
	}

	public function commandes()
	{
		return $this->hasMany(Commande::class);
	}
}
