<?php

namespace App\Models\AllData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gibdd2Full extends Model
{
    use HasFactory;

    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'all_data';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gibdd2_full';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
