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

	public function parent()
	{
		return $this->belongsTo(FamillesArticle::class, 'parent_id');
	}

	public function articles()
	{
		return $this->hasMany(Article::class, 'famille_id');
	}

	public function enfants()
	{
		return $this->hasMany(FamillesArticle::class, 'parent_id');
	}
	public function cheminComplet($separator = ' > ')
	{
		$chemin = [$this->libelle];
		$parent = $this->parent;

		while ($parent) {
			array_unshift($chemin, $parent->libelle);
			$parent = $parent->parent;
		}

		return implode($separator, $chemin);
	}
	public function getAllDescendantIds()
{
    $ids = collect();

    foreach ($this->enfants as $enfant) {
        $ids->push($enfant->id);
        $ids = $ids->merge($enfant->getAllDescendantIds());
    }

    return $ids;
}
	public function hasDescendantMatching($search)
	{
		foreach ($this->enfants as $enfant) {
			if (str_contains(strtolower($enfant->libelle), strtolower($search)) ||
				str_contains(strtolower($enfant->code_wavesoft), strtolower($search)) ||
				$enfant->hasDescendantMatching($search)) {
				return true;
			}
		}
		return false;
	}

}
