<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class GmfSysCronScheme extends Migration {
  public $mdID = "01e8b41e7e83172080f81f45da101b6b";
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    $md = Metadata::create($this->mdID);
    $md->mdEntity('gmf.sys.cron.scheme')->comment('请求方案')->tableName('gmf_sys_cron_schemes');

    $md->string('id', 100)->primary();
    $md->string('client_id')->nullable()->comment('应用ID');
    $md->entity('user', config('gmf.user.entity'))->nullable()->comment('用户');
    $md->entity('ent', config('gmf.ent.entity'))->nullable()->comment('企业');
    //执行次数
    $md->integer('period')->nullable()->default('0')->comment('执行次数');
    $md->timestamp('fm_time')->nullable()->comment('开始时间');
    $md->timestamp('to_time')->nullable()->comment('结束时间');
    
    $md->integer('c_yearly')->nullable()->default('0')->comment('每年');
    $md->integer('c_quarterly')->nullable()->default('0')->comment('每季度');
    $md->integer('c_monthly')->nullable()->default('0')->comment('每月');
    $md->integer('c_weekly')->nullable()->default('0')->comment('每周');
    $md->integer('c_daily')->nullable()->default('0')->comment('每天');
    $md->integer('c_hourly')->nullable()->default('0')->comment('每小时');
    $md->integer('c_minute')->nullable()->default('0')->comment('每分钟');
    $md->string('name')->nullable()->comment('名称');
    $md->text('memo')->nullable()->comment('备注');
    $md->boolean('revoked')->default(0)->comment('注销');
    $md->timestamps();

    $md->build();

  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Metadata::dropIfExists($this->mdID);
  }
}