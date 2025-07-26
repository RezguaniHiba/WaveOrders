<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Utilisateur
 * 
 * @property int $id
 * @property string $nom
 * @property string $email
 * @property string|null $telephone
 * @property string $mot_de_passe_hash
 * @property string $role
 * @property bool $actif
 * @property Carbon|null $date_creation
 * 
 * @property Collection|Client[] $clients
 * @property Collection|Commande[] $commandes
 *
 * @package App\Models
 */
class Utilisateur extends Model
{
	protected $table = 'utilisateurs';
	public $timestamps = false;

	protected $casts = [
		'actif' => 'bool',
		'date_creation' => 'datetime'
	];

	protected $fillable = [
		'nom',
		'email',
		'telephone',
		'mot_de_passe_hash',
		'role',
		'actif',
		'date_creation'
	];

	public function clients()
	{
		return $this->hasMany(Client::class, 'commercial_id');
	}

	public function commandes()
	{
		return $this->hasMany(Commande::class, 'cree_par');
	}
}
