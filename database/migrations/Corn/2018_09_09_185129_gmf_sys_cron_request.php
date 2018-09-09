<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class GmfSysCronRequest extends Migration {
  public $mdID = "01e8b41eaa36175098c0e3ca879a9556";
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    $md = Metadata::create($this->mdID);
    $md->mdEntity('gmf.sys.cron.request')->comment('任务请求')->tableName('gmf_sys_cron_requests');

    $md->string('id', 100)->primary();
    $md->string('client_id')->nullable()->comment('应用ID');
    $md->entity('user', config('gmf.user.entity'))->nullable()->comment('用户');
    $md->entity('ent', config('gmf.ent.entity'))->nullable()->comment('企业');
    $md->entity('scheme', 'gmf.sys.cron.scheme')->nullable()->comment('方案');
    $md->longText('payload')->nullable()->comment('来源');
    //0，未决，1等待执行，2已执行，3正在执行，
    $md->integer('status')->nullable()->default('0')->comment('状态');
    $md->timestamp('fm_time')->nullable()->comment('开始时间');
    $md->timestamp('to_time')->nullable()->comment('结束时间');
    $md->integer('retries')->nullable()->default('0')->comment('重试次数');
    $md->integer('attempts')->nullable()->default('0')->comment('执行次数');
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