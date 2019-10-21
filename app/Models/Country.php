<?php

namespace App\Models;

/**
 * App\Models\Country
 *
 * @SWG\Definition (
 *      definition="Country",
 *      required={"name", "code"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *     @SWG\Property(
 *          property="code",
 *          description="code",
 *          type="string"
 *      ),
 *     @SWG\Property(
 *          property="alpfa_3",
 *          description="alpfa_3",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="name_de",
 *          description="name_de",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="name_fr",
 *          description="name_fr",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="name_it",
 *          description="name_it",
 *          type="string"
 *      ),
 * 
 * )
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $alpha_3
 * @property string $name_de
 * @property string $name_fr
 * @property string $name_it
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereAlpha3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereNameDe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereNameFr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereNameIt($value)
 * @mixin \Eloquent
 */
class Country extends Model
{
    /**
     * @var string
     */
    public $table = 'loc_countries';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    public $fillable = [
        'name',
        'code',
        'alpha_3',
        'name_de',
        'name_fr',
        'name_it',
        'name_rm'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'code' => 'string',
        'alpha_3' => 'string',
        'name' => 'string',
        'name_de' => 'string',
        'name_fr' => 'string',
        'name_it' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'code' => 'required',
        'alpha_3' => 'required',
        'name' => 'required',
        'name_de' => 'required',
        'name_fr' => 'required',
        'name_it' => 'required',
    ];
}
