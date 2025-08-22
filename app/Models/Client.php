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
	public function commandesImpayees()
	{
		return $this->hasMany(Commande::class)
			->selectRaw('commandes.*, (montant_ttc - COALESCE((SELECT SUM(montant) FROM reglements WHERE commande_id = commandes.id), 0)) as montant_du')
			->having('montant_du', '>', 0);
	}
	// Relations supplémentaires
public function reglements()
{
    return $this->hasManyThrough(//Pour recupperer $client->reglements
        Reglement::class, // Modèle final qu’on veut récupérer
        Commande::class,// Modèle intermédiaire par lequel on passe
        'client_id', // Clé étrangère dans commandes
        'commande_id' // Clé étrangère dans reglements
    )->with('clientPayeur');
}

// Méthodes pour les compteurs
public function getTotalCommandesAttribute()
{
    return $this->commandes()->sum('montant_ttc');
}
public function getTotalReglementsAttribute()
{
    return $this->reglements()->sum('montant');
}

// Dans le modèle Client
public function scopeSearch($query, $term)
{
    return $query->where(function($q) use ($term) {
        $q->where('nom', 'LIKE', "%$term%")
          ->orWhere('email', 'LIKE', "%$term%")
          ->orWhere('telephone', 'LIKE', "%$term%");
    });
}

public function scopeForCommercial($query, $commercialId)
{
    return $query->where('commercial_id', $commercialId);
}
	
}
