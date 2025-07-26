<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FamillesArticle
 * 
 * @property int $id
 * @property string|null $code_wavesoft
 * @property string $libelle
 * @property int|null $parent_id
 * 
 * @property FamillesArticle|null $familles_article
 * @property Collection|Article[] $articles
 * @property Collection|FamillesArticle[] $familles_articles
 *
 * @package App\Models
 */
class FamillesArticle extends Model
{
	protected $table = 'familles_articles';
	public $timestamps = false;

	protected $casts = [
		'parent_id' => 'int'
	];

	protected $fillable = [
		'code_wavesoft',
		'libelle',
		'parent_id'
	];

	public function familles_article()
	{
		return $this->belongsTo(FamillesArticle::class, 'parent_id');
	}

	public function articles()
	{
		return $this->hasMany(Article::class, 'famille_id');
	}

	public function familles_articles()
	{
		return $this->hasMany(FamillesArticle::class, 'parent_id');
	}
}
