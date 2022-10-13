<?php

namespace Damms005\LaravelActivitylogUi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Spatie\Activitylog\Models\Activity;
use TCG\Voyager\Facades\Voyager;

class ActivitylogUiController extends Controller
{
	public function index(Request $request)
	{
		$rendered_activities = Activity::with('causer', 'subject')->orderBy('id', 'desc')->paginate(15);

		$this->mapActivitiesForView($rendered_activities);

		$all_activities = Activity::all(['description', 'subject_type']);

		return view('activitylog-ui::index', compact('rendered_activities', 'all_activities'));
	}

	public function show(Request $request)
	{
		$builder = Activity::with('causer', 'subject');

		if ($request->filled('causer_type')) {
			$builder = $builder->where('causer_type', $request->causer_type);
		}

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

		$rendered_activities = $builder->paginate(15);

		$this->mapActivitiesForView($rendered_activities);

		$request->flash();

		$all_activities = Activity::all(['description', 'subject_type']);

		return view('activitylog-ui::index', compact('rendered_activities', 'all_activities'));
	}

	/**
	 * Formats each item in the activities collection so that
	 * it conforms to that format expected by the blade View
	 *
	 * @param Collection $activities
	 *
	 * @return Collection
	 */
	public function mapActivitiesForView(LengthAwarePaginator &$activities)
	{
		return $activities->map(function ($activity) {
			$activity->causerObject  = $this->getVoyagerLinkOrLabelForCauser($activity);
			$activity->subjectClass  = collect(explode('\\', $activity->subject_type))->last();
			$activity->subjectObject = $this->getSubjectObject($activity);
			$activity->properties    = json_encode($activity->properties, JSON_PRETTY_PRINT);

			return $activity;
		});
	}

	public function getSubjectObject(Activity $activity)
	{
		$obj = collect();

        $keyName = app($activity->subject_type)->getKeyName();
		$model = $activity->subject_type::where($keyName, $activity->subject_id)->first();

		$obj->put('modelClassName', collect(explode('\\', $activity->subject_type))->last());

		if ($model) {
			$id = $model[$model->getKeyName()];

			$dataType = Voyager::model('DataType')->whereName($model->getTable())->first();

			$voyagerSlug = $activity->description != 'deleted' ? data_get($dataType, 'slug', '') : null;

			$obj->put('link', $this->getVoyagerLinkTagForTable($voyagerSlug, $id, "id:{$id}"));
		} else {
			$obj->put('link', $this->getVoyagerLinkTagForTable(null, $activity->subject_id, "id:{$activity->subject_id}"));
		}

		return $obj;
	}

	public function getVoyagerLinkTagForTable($voyager_slug, $id, $anchorText)
	{
		if ($this->isVoyagerRouteExists($voyager_slug)) {
			return collect(['href' => route("voyager.{$voyager_slug}.index") . '/' . $id, 'anchorText' => $anchorText]);
		}

		return collect(['href' => 'javascript:void(0)', 'anchorText' => $anchorText]);
	}

	public function getVoyagerLinkOrLabelForCauser(Activity $activity)
	{
		if ($this->isVoyagerUserExists($activity)) {
			$model = Voyager::model('DataType')->where('model_name', $activity->causer_type)->first();
			$slug = $model ? $model->slug : 'users';
			return $this->getVoyagerLinkTagForTable($slug, $activity->causer_id, optional($activity->causer)->fullname . ' (' . $activity->causer_id . ')');
		}

		return $this->getVoyagerLinkTagForTable(null, null, 'system anonymous action');
	}

	public function isVoyagerUserExists(Activity $activity)
	{
		return $activity->causer_id;// && Route::has('voyager.admin-users.index');
	}

	public function isVoyagerRouteExists($voyager_slug)
	{
		return $voyager_slug && Route::has("voyager.{$voyager_slug}.index");
	}
}
