<?php

/**
 * @OA\Schema(
 *     schema="Currency",
 *     title="Currency",
 *     description="Currency model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier of the currency"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the currency"
 *     ),
 *     @OA\Property(
 *         property="symbol",
 *         type="string",
 *         description="The symbol of the currency"
 *     ),
 *     @OA\Property(
 *         property="exchange_rate",
 *         type="number",
 *         format="float",
 *         description="The exchange rate of the currency"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="The creation date of the currency"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="The last update date of the currency"
 *     ),
 *     @OA\Property(
 *         property="products",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Product"),
 *         description="The products associated with this currency"
 *     ),
 *     @OA\Property(
 *         property="product_prices",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/ProductPrice"),
 *         description="The product prices associated with this currency"
 *     )
 * )
 */
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Exception;

class Currency extends Model
{
	protected $table = 'currencies';

	protected $casts = [
		'exchange_rate' => 'float'
	];

	protected $fillable = [
		'name',
		'symbol',
		'exchange_rate'
	];

	public function products()
	{
		return $this->hasMany(Product::class);
	}

	public function product_prices()
	{
		return $this->hasMany(ProductPrice::class);
	}

	/**
     * Initialize the attributes for the Currency model.
     *
     * @param array $attributes
     */
    public function initAttributes($attributes)
    {
        $this->name = $attributes['name'] ?? null;
        $this->symbol = $attributes['symbol'] ?? null;
        $this->exchange_rate = $attributes['exchange_rate'] ?? null;
    }

    /**
     * Validate the attributes for the Currency model.
     *
     * @param array $attributes
     * @throws Exception
     */
    public static function validateAttributes($attributes)
    {
        $validator = Validator::make($attributes, [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:currencies,name', // Asegúrate de que el nombre sea único
            ],
            'symbol' => [
                'required',
                'string',
                'max:10', // Limitar el símbolo a un tamaño razonable
            ],
            'exchange_rate' => [
                'required',
                'numeric',
                'min:0', // La tasa de cambio no puede ser negativa
            ],
        ]);

        if ($validator->fails()) {
            throw new Exception(implode(', ', array_map(function ($inputErrors) {
                return implode(', ', $inputErrors);
            }, $validator->errors()->getMessages())));
        }
    }
}
