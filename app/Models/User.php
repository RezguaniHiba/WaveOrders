<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class User extends Authenticatable
{
	use Notifiable;
	protected $table = 'utilisateurs';

	protected $casts = [
		'actif' => 'bool',
		'date_creation' => 'datetime'
	];

	protected $hidden = [
		'mot_de_passe_hash'
		];

	protected $fillable = [
		'nom',
		'email',
		'remember_token',
		'telephone',
		'role',
		'actif',
		'date_creation'
	];
	public function getAuthPassword()
    {
        return $this->mot_de_passe_hash;
    }
	public function clients()
	{
		return $this->hasMany(Client::class, 'commercial_id');
	}

	public function commandes()
	{
		return $this->hasMany(Commande::class, 'cree_par');
	}
	
    // Méthodes utilitaires
    public function isAdmin() {
        return $this->role === 'admin';
    }

    public function isCommercial() {
        return $this->role === 'commercial';
    }

}
