<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InterventoFoto extends Model
{
	protected $table = "interventi";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		"intervento_id",
		"foto",
    ];
	
	public static $rules = array(
		'intervento_id' => 'required',
		'foto' => 'required',
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