<?php
namespace App\Model\Traits;
use Closure;
use Carbon\Carbon;
/**
 * Trait TimeZoneTrait
 * @package App\Model\Traits
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $created_at_gmt
 * @property Carbon $updated_at_gmt
 */
trait TimeZoneTrait{
	/**
	 * Display timestamps in user's timezone
	 */
	protected function getCreatedAtAttribute($value){
        return $this->_asUserDateTime($value);
    }
	protected function getUpdatedAtAttribute($value){
        return $this->_asUserDateTime($value);
    }
	protected function getCreatedAtGmtAttribute(){
		$created_at=$this->getAttributeValue('created_at');
		if(!is_null($created_at)){
			$date= clone $created_at;
			/**
			 * @var Carbon $date
			 */
			return $date->setTimeZone('UTC');
		}
		return null;
	}
	protected function getUpdatedAtGmtAttribute(){
		$updated_at=$this->getAttributeValue('updated_at');
		if(!is_null($updated_at)){
			$date= clone $updated_at;
			return $date->setTimeZone('UTC');
		}
		return null;
	}
	private function _asUserDateTime($value){
	    if(!is_null($value)) {
	        $value=$this->asDateTime($value);
            $value->setTimeZone($this->_getUserTimeZone());
        }
	    return $value;
    }
	private function _getUserTimeZone(){
		$me = current_user();
		$tz=0;
		if($me) {
			$tz = $me->timezone;
			$gmt_offset=$me->gmt_offset;
		}
		if(!$tz){
			$tz=7;
		}
		if(is_numeric($tz)){
			$tz=timezone_name_from_abbr("", $tz*3600, 0);
		}
		return $tz;
	}
}
