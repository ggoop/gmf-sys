<?php
namespace Gmf\Sys\Http\Middleware;
use Closure;
use Exception;
use Gmf\Sys\Builder;

class LnsCheck {

	public function handle($request, Closure $next, $item = '') {
		if (!$request->oauth_ent_id) {
			throw new Exception("没有找到企业信息!" . $item, 8000);
		}
		if ($request->oauth_ent_id && $item) {
			$query = DB::table('gmf_sys_ent_lns as el');
			$query->join('gmf_sys_lns as l', 'el.lns_id', '=', 'l.id');
			$query->select('l.request_code', 'l.answer_code', 'l.content', 'l.fm_date', 'l.to_date');
			$query->where('el.ent_id', $request->oauth_ent_id);
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
				$item = explode(':', $value);
				if ($item && count($item) == 2 && $item[0] === $item) {
					$checker = new Builder;
					$checker->code($item[0])->number($item[1]);
				}
			}
			if ($checker) {

			}

			if ($checker && $checker->number > 0) {
				$num = 0;
				$query = $lns->item_type::where('id', '!=', '');
				if ($lns->item_filter) {
					$query->whereRaw($lns->item_filter);
				}
				if ($lns->field) {
					$num = $query->max($lns->field);
				} else {
					$num = $query->count();
				}
				if ($num >= $lns->item_number) {
					throw new Exception($lns->item_name . "超过许可控制数" . $lns->item_number, 8000);
				}
			}
		}
		return $next($request);
	}
}
