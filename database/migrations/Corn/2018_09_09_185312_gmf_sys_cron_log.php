<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class GmfSysCronLog extends Migration {
  public $mdID = "01e8b41e8c3c17b0852d6525f8cb756a";
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    $md = Metadata::create($this->mdID);
    $md->mdEntity('gmf.sys.cron.log')->comment('执行日志')->tableName('gmf_sys_cron_logs');

    $md->increments('id');
    $md->entity('request', 'gmf.sys.cron.request')->nullable()->comment('任务');
    $md->timestamp('fm_time')->nullable()->comment('开始时间');
    $md->timestamp('to_time')->nullable()->comment('结束时间');
    $md->string('name')->nullable()->comment('名称');
    $md->text('memo')->nullable()->comment('备注');
    $md->boolean('succeed')->default(0)->comment('成功');
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