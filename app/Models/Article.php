<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Article
 * 
 * @property int $id
 * @property string $code_wavesoft
 * @property string $reference
 * @property string $designation
 * @property string|null $description
 * @property float $prix_ht
 * @property float $taux_tva
 * @property string|null $unite
 * @property int|null $stock_disponible
 * @property int|null $stock_reserve
 * @property Carbon|null $date_maj_stock
 * @property bool $actif
 * @property int|null $famille_id
 * 
 * @property FamillesArticle|null $familles_article
 * @property Collection|LignesCommande[] $lignes_commandes
 *
 * @package App\Models
 */
class Article extends Model
{
	protected $table = 'articles';
	public $timestamps = false;

	protected $casts = [
		'prix_ht' => 'float',
		'taux_tva' => 'float',
		'stock_disponible' => 'int',
		'stock_reserve' => 'int',
		'date_maj_stock' => 'datetime',
		'actif' => 'bool',
		'famille_id' => 'int'
	];

	protected $fillable = [
		'code_wavesoft',
		'reference',
		'designation',
		'description',
		'prix_ht',
		'taux_tva',
		'unite',
		'stock_disponible',
		'stock_reserve',
		'date_maj_stock',
		'actif',
		'famille_id'
	];

	public function familles_article()
	{
		return $this->belongsTo(FamillesArticle::class, 'famille_id');
	}

	public function lignes_commandes()
	{
		return $this->hasMany(LignesCommande::class);
	}
}
