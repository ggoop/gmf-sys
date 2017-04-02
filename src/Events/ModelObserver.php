<?php

namespace Gmf\Sys\Events;
use Gmf\Sys\Uuid;
use Illuminate\Database\Eloquent\Model;

class ModelObserver {
	/*
			Eloquent 模型会触发许多事件，让你可以借助以下的方法在模型的生命周期的多个时间点进行监控：
			creating, created, updating, updated, saving, saved, deleting, deleted, restoring, restored.
			事件让你每当有特定的模型类在数据库保存或更新时，执行代码。

		当一个新模型被初次保存将会触发 creating 以及 created 事件。如果一个模型已经存在于数据库且调用了 save 方法，将会触发 updating 和 updated 事件。在这两种情况下都会触发 saving 和 saved 事件。

		让我们在 服务提供者 中定义一个 Eloquent 事件监听器来作为示例。在我们的事件监听器中，我们会在指定的模型上调用 isValid 方法，并在模型无效时返回 false。从 Eloquent 事件监听器中返回 false 的话会取消 save 和 update 的操作

	*/
	/**
	 * 监听创建的事件。
	 *
	 * @param  Model  $model
	 * @return void
	 */
	public function creating($model) {
		//自动生成ID
		if (!$model->incrementing) {
			if (empty($model->{$model->getKeyName()})) {
				$model->{$model->getKeyName()} = Uuid::generate(1, 'gmf', Uuid::NS_DNS, "");
			}
		}
	}
	/**
	 * 监听创建的事件。
	 *
	 * @param  Model  $model
	 * @return void
	 */
	public function created(Model $model) {
		//
	}
	/**
	 * 监听saving的事件。
	 *
	 * @param  Model  $model
	 * @return void
	 */
	public function saving(Model $model) {
		if (method_exists($model, 'validate') && is_callable(array($model, 'validate'))) {
			return $model->validate();
		}
	}

	/**
	 * 监听删除事件。
	 *
	 * @param  Model  $model
	 * @return void
	 */
	public function deleting(Model $model) {
		//
	}
	/**
	 * 监听删除事件。
	 *
	 * @param  Model  $model
	 * @return void
	 */
	public function deleted(Model $model) {
		//
	}
}