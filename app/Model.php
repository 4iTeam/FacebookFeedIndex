<?php
namespace App;
use App\Model\Traits\SingletonTrait;
use App\Model\Traits\TimeZoneTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Builder;
use App\Model\Token;

/**
 * Class Model
 * @package App
 * @method static static|null find($id)
 * @method static static create($attributes)
 * @method static static|null findOrFail($id)
 * @method static static firstOrCreate($attributes,$values)
 * @method static static updateOrCreate($attributes,$values)
 * @method static Builder where(...$args)
 */
class Model extends Eloquent{
	const EXPIRED_AT = 'expired_at';
	use SingletonTrait,TimeZoneTrait;
}