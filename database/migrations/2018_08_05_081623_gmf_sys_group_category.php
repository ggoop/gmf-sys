<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class GmfSysGroupCategory extends Migration {
    public $mdID = "01e89844c92513f0a7a9b9e1451e27e9";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        $md = Metadata::create($this->mdID);
        $md->mdEntity('gmf.sys.group.category')->comment('分类')->tableName('gmf_sys_group_categories');

        $md->string('id', 100)->primary();
        $md->entity('user',config('gmf.user.entity'))->nullable()->comment('用户');
        $md->entity('ent',config('gmf.ent.entity'))->nullable()->comment('企业');

        $md->entity('group','gmf.sys.group.item')->nullable()->comment('分组');
        $md->entity('root','gmf.sys.group.category')->nullable()->comment('根节点');
        $md->entity('parent','gmf.sys.group.category')->nullable()->comment('上级节点');

        $md->string('code')->nullable()->comment('编码');
        $md->string('name')->nullable()->comment('名称');
        $md->string('tag')->nullable()->comment('标签');
        $md->text('memo')->nullable()->comment('备注');
        $md->integer('sequence')->nullable()->default('0')->comment('顺序号');
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