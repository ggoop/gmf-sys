<?php

namespace Gmf\Sys\Libs;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Models\Profile;

class Common {
	/**
	 * 获取参数配置信息
	 * @param  string $name 参数名称
	 * @return [type]       [description]
	 */
	public static function getProfileValue($name = '') {
		return Profile::getValue($name);
	}
	/**
	 * 取排名的前top数据，并对其它数据进行合并
	 * @param  Collection   $arrary    [description]
	 * @param  integer      $top       [description]
	 * @param  Array|array  $sumFields [description]
	 * @param  Closure|null $callback  function(Builder $b){}
	 * @return [type]                  [description]
	 */
	public static function takeOtherSum(Collection $arrary, $top = 5, Array $sumFields = [], Closure $callback = null) {

		$topData = $arrary->take($top);
		//other
		$other = $arrary->slice($top);
		if ($other->count()) {
			$b = new Builder;
			$callback && $callback($b);

			$other->each(function ($item, $key) use ($b, $sumFields) {
				foreach ($sumFields as $key => $value) {
					$b->{$value} = $b->{$value}+$item->{$value};
				}
			});
			$topData->push($b);
		}
		return $topData;
	}
}
