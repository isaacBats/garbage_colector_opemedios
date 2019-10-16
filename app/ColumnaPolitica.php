<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ColumnaPolitica extends Model
{

    /**
     * The database connection used by the model.
     *
     * @var string
     */
     protected $connection = 'mysql';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'columna_politica';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        //
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        //
    ];

    protected $primaryKey = 'id_columna_politica';
}
