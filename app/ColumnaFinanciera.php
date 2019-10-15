<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ColumnaFinanciera extends Model
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
    protected $table = 'columna_financiera';
    
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

    protected $primaryKey = 'id_columna_financiera';
}
