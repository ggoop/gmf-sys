<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysAuthorityRoleEntitiesTable extends Migration {
  public $mdID = "b80478e0af1c11e7a21e6b3538675156";
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    $md = Metadata::create($this->mdID);
    $md->mdEntity('gmf.sys.authority.role.entity')->comment('角色-数据')->tableName('gmf_sys_authority_role_entities');

    $md->bigIncrements('id');
    $md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
    $md->entity('role', 'gmf.sys.authority.role')->nullable()->comment('角色');
    $md->entity('entity', 'gmf.sys.entity')->nullable()->comment('实体');
    $md->entity('field', 'gmf.sys.entity.field')->nullable()->comment('字段');
    $md->string('field_name')->nullable()->comment('字段名称');
    $md->string('data_id')->nullable()->comment('数据ID');
    $md->string('data_type')->nullable()->comment('数据类型');
    $md->string('data_name')->nullable()->comment('数据名称');
    $md->enum('operation', 'gmf.sys.authority.data.operation.type.enum')->nullable()->comment('操作类型');
    $md->enum('opinion', 'gmf.sys.authority.opinion.type.enum')->default('permit')->comment('建议');
    $md->string('filter')->nullable()->comment('条件');
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
