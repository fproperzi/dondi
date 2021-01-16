<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InterventoFoto extends Model
{
	protected $table = "interventi_asporti";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		"intervento_id",
		"conferimento_id",
		"spurgo_id",
		"cdcer",
		"mq",
		"note",
    ];
	
	public static $rules = array(
		'intervento_id' => 'required',

	);
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
		"created_at",
		"updated_at",
	];
	

}