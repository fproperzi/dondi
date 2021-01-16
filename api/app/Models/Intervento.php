<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Intervento extends Model
{
	protected $table = "interventi";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		"impianto_id",
		"user_id",
		"in_at",
		"out_at",
    ];
	
	public static $rules = array(
		'user_id' => 'required',
		'impianto_id' => 'required',
		'in_at' => 'required',
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