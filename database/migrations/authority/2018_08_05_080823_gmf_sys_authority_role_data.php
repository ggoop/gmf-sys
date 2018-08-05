<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class GmfSysAuthorityRoleData extends Migration {
    public $mdID = "01e89843aac91c2098a635a710c49da4";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        $md = Metadata::create($this->mdID);
        $md->mdEntity('gmf.sys.authority.role.data')->comment('数据权限')->tableName('gmf_sys_authority_role_datas');

        $md->string('id', 100)->primary();
        $md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
        $md->entity('role', 'gmf.sys.authority.role')->nullable()->comment('角色');
        $md->string('data_id')->nullable()->comment('名称');
        $md->string('data_type')->nullable()->comment('名称');
        $md->enum('opinion', 'gmf.sys.authority.opinion.type.enum')->default('permit')->comment('建议');
        $md->text('memo')->nullable()->comment('备注');
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