<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class GmfSysGroupItem extends Migration {
    public $mdID = "01e89844cec01d10a3792506c18f39a5";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        $md = Metadata::create($this->mdID);
        $md->mdEntity('gmf.sys.group.item')->comment('组')->tableName('gmf_sys_group_items');

        $md->string('id', 100)->primary();
        $md->entity('user',config('gmf.user.entity'))->nullable()->comment('用户');
        $md->entity('ent',config('gmf.ent.entity'))->nullable()->comment('企业');

        $md->string('code')->nullable()->comment('编码');
        $md->string('name')->nullable()->comment('名称');
        $md->text('memo')->nullable()->comment('备注');
        $md->boolean('is_system')->default(0)->comment('系统');
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