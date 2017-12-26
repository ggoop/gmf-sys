<?php
namespace Gmf\Sys\Http\Middleware;
use Closure;
use DB;
use Exception;
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use GAuth;
class LnsCheck {

	public function handle($request, Closure $next, $item = '') {
		if (!GAuth::entId()) {
			throw new Exception("没有找到企业信息!" . $item, 8000);
		}
		if (GAuth::entId()&& $item) {
			$query = DB::table('gmf_sys_ent_lns as el');
			$query->join('gmf_sys_lns as l', 'el.lns_id', '=', 'l.id');
			$query->select('l.request_code', 'l.answer_code', 'l.content', 'l.fm_date', 'l.to_date');
			$query->where('el.ent_id', GAuth::entId());
			$lns = $query->first();
			if (empty($lns)) {
				throw new Exception("没有许可!" . $item, 8000);
			}
			if (empty($lns->request_code) || empty($lns->answer_code) || empty($lns->content)) {
				throw new Exception("许可错误!" . $item, 8000);
			}
			$items = explode(',', $lns->content);

			$checker = false;
			foreach ($items as $key => $value) {
				$datas = explode(':', $value);
				if ($datas && count($datas) == 2 && $datas[0] === $item) {
					$checker = new Builder;
					$checker->code($datas[0])->number($datas[1]);
				}
			}
			if (!$checker) {
				throw new Exception("许可系统错误!" . $item, 8000);
			}

			$item = Models\LnsItem::where('code', $checker->code)->first();
			if ($item) {
				$checker->name($item->name)
					->type($item->type)
					->field($item->field)
					->filter($item->filter);
			}
			if (empty($checker->type)) {
				throw new Exception("未设置许可!", 8000);
			}

			if ($checker && !empty($checker->type) && $checker->number > 0) {
				$num = 0;
				$query = $checker->type::where('id', '!=', '');
				if (!empty($checker->filter)) {
					$query->whereRaw($checker->filter);
				}
				if (!empty($checker->field)) {
					$num = $query->max($checker->field);
				} else {
					$num = $query->count();
				}
				if ($num >= $checker->number) {
					throw new Exception($checker->name . " - 超过许可控制数 - " . $checker->number, 8000);
				}
			}
		}
		return $next($request);
	}
}
