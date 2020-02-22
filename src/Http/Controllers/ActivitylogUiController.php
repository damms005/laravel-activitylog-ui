<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivitylogUiController extends Controller
{
	public function index(Request $request)
	{
		$activities = Activity::with('causer', 'subject')->orderBy('id', 'desc')->paginate(15);
		$this->prepareActivitiesForView($activities);

		return view('admin.laravel-activitylog', compact('activities'));
	}

	public function show(Request $request)
	{
		$builder = \Spatie\Activitylog\Models\Activity::with('causer', 'subject');

		if ($request->filled('causer_id')) {
			$builder = $builder->where('causer_id', $request->causer_id);
		}

		if ($request->filled('subject_type')) {
			$builder = $builder->where('subject_type', $request->subject_type);
		}

		if ($request->filled('subject_id')) {
			$builder = $builder->where('subject_id', $request->subject_id);
		}

		if ($request->filled('description')) {
			$builder = $builder->where('description', $request->description);
		}

		if ($request->filled('from')) {
			$builder = $builder->where('created_at', '>=', $request->from);
		}

		if ($request->filled('to')) {
			$builder = $builder->where('created_at', '<=', "{$request->to} 11:59:59");
		}

		if ($request->filled('contain_data')) {
			$builder = $builder->where('properties', 'like', "%{$request->contain_data}%");
		}

		$activities = $builder->paginate(15);
		$this->prepareActivitiesForView($activities);
		$request->flash();

		return view('admin.laravel-activitylog', compact('activities'));
	}

	public function prepareActivitiesForView(&$activities)
	{
		return $activities->map(function ($activity) {
			$activity->causerObject  = $this->getVoyagerLinkOrLabelForCauser($activity);
			$activity->subjectClass  = collect(explode('\\', $activity->subject_type))->last();
			$activity->subjectObject = $this->getSubjectObject($activity);
			$activity->properties    = json_encode($activity->properties, JSON_PRETTY_PRINT);
		});
	}

	public function getSubjectObject($activity)
	{
		$obj   = collect();
		$model = $activity->subject_type::where('id', $activity->subject_id)->first();
		$obj->put('modelClassName', collect(explode('\\', $activity->subject_type))->last());
		if ($model) {
			$id       = $model[$model->getKeyName()];
			$dataType = \Voyager::model('DataType')->whereName($model->getTable())->first();
			$slug     = $dataType->slug;
			// $slug     = $activity->description != 'deleted' ? $dataType->slug : null;
			$obj->put('link', $this->getVoyagerLinkTagForTable($slug, $id, "id:{$id}"));
		} else {
			$obj->put('link', $this->getVoyagerLinkTagForTable(null, $activity->subject_id, "id:{$activity->subject_id}"));
		}

		return $obj;
	}

	public function getVoyagerLinkTagForTable($voyager_slug, $id, $anchorText)
	{
		return
		($voyager_slug && \Route::has("voyager.{$voyager_slug}.index"))
		?
		collect(['href' => route("voyager.{$voyager_slug}.index") . '/' . $id, 'anchorText' => $anchorText])
		:
		collect(['href' => 'javascript:void(0)', 'anchorText' => $anchorText]);
	}

	public function getVoyagerLinkOrLabelForCauser($activity)
	{
		return

		($activity->causer_id && \Route::has('voyager.users.index'))
		?
		$this->getVoyagerLinkTagForTable('users', $activity->causer_id, optional($activity->causer)->fullname)
		:
		$this->getVoyagerLinkTagForTable(null, null, 'system anonymous action');
	}
}
