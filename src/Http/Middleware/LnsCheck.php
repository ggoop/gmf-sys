<?php
namespace Gmf\Sys\Http\Middleware;
use Closure;
use DB;
use Exception;

class LnsCheck {

	public function handle($request, Closure $next, $item = '') {
		if (!$request->oauth_ent_id) {
			throw new Exception("没有许可!" . $item, 1);
		}
		if ($request->oauth_ent_id && $item) {
			$query = DB::table('gmf_sys_ent_lns as el');
			$query->join('gmf_sys_lns as l', 'el.lns_id', '=', 'l.id');
			$query->join('gmf_sys_lns_items as li', 'l.id', '=', 'li.lns_id');
			$query->select('l.serial_number', 'l.request_code', 'l.request_date', 'l.answer_code', 'li.type as item_type', 'li.name as item_name', 'li.number as item_number', 'li.filter as item_filter');
			$query->where('el.ent_id', $request->oauth_ent_id);
			$query->where('li.code', $item);
			$lns = $query->first();
			if (empty($lns)) {
				throw new Exception("没有许可!" . $item, 1);
			}
			if (empty($lns->item_type) || empty($lns->answer_code)) {
				throw new Exception("许可错误!" . $item, 1);
			}
			if ($lns->item_number > 0) {
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
					throw new Exception($lns->item_name . "超过许可控制数" . $lns->item_number, 1);
				}
			}

		}
		return $next($request);
	}
}
