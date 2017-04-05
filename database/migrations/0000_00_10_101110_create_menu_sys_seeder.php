<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models\Menu;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMenuSysSeeder extends Migration {
	private $sequence = '80';
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$exception = DB::transaction(function () {
			$id = "c8280c0009f311e797dccd79a400c99f";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->root_id($id)->code('sys')->name('系统管理')
					->uri('sys')->sequence($this->sequence . '0000')->tag('sys');
			});
			//系统设置
			$id = "c8280e2009f311e79f49d90c5fb2cdc1";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.setting')->name('系统设置')->parent('sys')
					->sequence($this->sequence . '0100')->tag('sys');
			});
			$id = "c8280ed009f311e7b59f63fe4ef438e7";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.setting.language')->name('语言管理')->parent('sys.setting')
					->uri('sys.language.list')->sequence($this->sequence . '0101')->tag('sys');
			});
			$id = "c8280f4009f311e7b89065113bb45fff";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.setting.profile')->name('参数管理')->parent('sys.setting')
					->uri('sys.profile.list')->sequence($this->sequence . '0102')->tag('sys');
			});
			$id = "c8280fa009f311e7bd4f454fce5c2e80";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.setting.permit')->name('权限管理')->parent('sys.setting')
					->uri('sys.permit.list')->sequence($this->sequence . '0103')->tag('sys');
			});
			$id = "225ab5a00a0111e7a41adfddbd890d92";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.setting.role')->name('角色管理')->parent('sys.setting')
					->uri('sys.role.list')->sequence($this->sequence . '0104')->tag('sys');
			});
			//应用
			$id = "c828107009f311e78f4d9ff61e7de81c";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.menu')->name('应用')->parent('sys')
					->sequence($this->sequence . '0200')->tag('sys');
			});
			$id = "c828113009f311e7b027d722a9f64aa3";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.menu.list')->name('应用管理')->parent('sys.menu')
					->uri('sys.menu.list')->sequence($this->sequence . '0201')->tag('sys');
			});
			//元数据
			$id = "a8f236f009f511e794b95542102b8e68";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.md')->name('元数据')->parent('sys')
					->sequence($this->sequence . '0300')->tag('sys');
			});
			$id = "a8f2310009f511e79ff3159b3b8f4a83";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.md.enum')->name('枚举管理')->parent('sys.md')
					->uri('sys.md.enum.list')->sequence($this->sequence . '0301')->tag('sys');
			});
			$id = "c828119009f311e787e0271c551118dd";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.md.com')->name('组件')->parent('sys.md')
					->uri('sys.md.com.list')->sequence($this->sequence . '0302')->tag('sys');
			});
			//用户
			$id = "a8f2332009f511e79b378ddefe01d4d3";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.user')->name('用户')->parent('sys')
					->sequence($this->sequence . '0400')->tag('sys');
			});
			$id = "a8f2343009f511e7bc7f7d10e070b72a";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.user.list')->name('用户管理')->parent('sys.user')
					->uri('sys.user.list')->sequence($this->sequence . '0401')->tag('sys');
			});
			//企业
			$id = "a8f234d009f511e7be256922e6766e91";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.ent')->name('企业')->parent('sys')
					->sequence($this->sequence . '0500')->tag('sys');
			});
			$id = "a8f2354009f511e78680598d785435bd";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.ent.list')->name('企业管理')->parent('sys.ent')
					->uri('sys.ent.list')->sequence($this->sequence . '0501')->tag('sys');
			});
			//日志
			$id = "225ab7a00a0111e7aa6b651fe5aa9d72";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.log')->name('日志')->parent('sys')
					->sequence($this->sequence . '0600')->tag('sys');
			});
			$id = "225ab8a00a0111e7bab713d1c0e27d8e";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.log.login')->name('登录日志')->parent('sys.log')
					->uri('sys.log.login.list')->sequence($this->sequence . '0601')->tag('sys');
			});
			$id = "225ab8f00a0111e7a1f9bba118b299df";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.log.action')->name('操作日志')->parent('sys.log')
					->uri('sys.log.action.list')->sequence($this->sequence . '0602')->tag('sys');
			});
			$id = "225ab9500a0111e7b68083d568feadf5";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.log.error')->name('错误日志')->parent('sys.log')
					->uri('sys.log.error.list')->sequence($this->sequence . '0603')->tag('sys');
			});

			//调度请求
			$id = "d42debc00a0111e78c4f2b84de6b92f0";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.job')->name('调度请求')->parent('sys')
					->sequence($this->sequence . '0700')->tag('sys');
			});
			$id = "d42dee400a0111e7ad6ab3f0b2404491";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.job.request')->name('请求管理')->parent('sys.job')
					->uri('sys.job.request.list')->sequence($this->sequence . '0701')->tag('sys');
			});
			$id = "d42defa00a0111e786a9472ee3e95a55";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.job.schem')->name('请求方案')->parent('sys.job')
					->uri('sys.job.schem.list')->sequence($this->sequence . '0702')->tag('sys');
			});
			$id = "d42def100a0111e79bdb176cf74d4e6d";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.job.log')->name('请求日志')->parent('sys.job')
					->uri('sys.job.log')->sequence($this->sequence . '0703')->tag('sys');
			});

			//调度请求
			$id = "5431fd400a0211e7ae0c318ab71dd38b";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.dti')->name('接口')->parent('sys')
					->sequence($this->sequence . '0800')->tag('sys');
			});
			$id = "5431ff500a0211e7acf66b5205c6a513";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.dti.category')->name('接口分类')->parent('sys.dti')
					->uri('sys.dti.category')
					->sequence($this->sequence . '0801')->tag('sys');
			});
			$id = "5431ffe00a0211e793d7af8c60f451f5";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.dti.context')->name('接口上下文')->parent('sys.dti')
					->uri('sys.dti.context')->sequence($this->sequence . '0801')->tag('sys');
			});
			$id = "543200a00a0211e78407c1a122980e01";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.dti.data')->name('数据接口')->parent('sys.dti')
					->uri('sys.dti.data')->sequence($this->sequence . '0802')->tag('sys');
			});
			$id = "543201500a0211e7bbeeb328879248db";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.dti.run')->name('接口执行')->parent('sys.dti')
					->uri('sys.dti.run')->sequence($this->sequence . '0804')->tag('sys');
			});
			$id = "543202000a0211e798a3b93bad0d1f8f";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('sys.dti.result')->name('接口执行结果')->parent('sys.dti')
					->uri('sys.dti.result')->sequence($this->sequence . '0805')->tag('sys');
			});
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Menu::where('code', 'like', 'sys%')->delete();
	}
}
