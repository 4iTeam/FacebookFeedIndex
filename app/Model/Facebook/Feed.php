<?php
namespace App\Model\Facebook;
use App\Model;
use Illuminate\Support\Str;

class Feed extends Model{
    protected $table='fb_feeds';
    protected $fillable=['id','name','type'];
    public $incrementing=false;
	protected static function boot()
	{
		parent::boot();
		static::creating(function (Feed $self) {
			do {
				$self->slug = $uuid = (string)Str::uuid();
			}while(false/*static::query()->find($uuid)*/);
		});
	}
    function posts(){
        return $this->hasMany(Post::class);
    }
}
