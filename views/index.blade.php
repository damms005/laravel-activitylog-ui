@extends('voyager::master')

@section('page_title', 'Spatie Activity Log')

@section('page_header')

<h1 class="page-title">
	<i class="voyager-logbook"></i>
	<p>Spatie Activity Log</p>
	<span class="page-description">Activity Log Entries - default activity types: created, updated and deleted activities.</span>
</h1>

@stop

@section('content')
<div class="page-content read container-fluid mt-10">
	<div class="row">
		<div class="col-md-12">

			<div class="bg-blue-lighteR rounded border mt-8 mb-8 p-4">
				<form action="{{route('activitylog.filter.submit')}}" method="POST">
					@csrf
					<div>
						<h4>Audit</h4>
						<hr>
						Causer
						<label class="w-64 mr-2">
							<select name="causer_type" class="select" style="height: 28px">
								<option value="">&nbsp;</option>
								@foreach (config('activitylog-ui.user_model') as $type)
								<option {{ $type == old('causer_type') ? 'selected' : '' }} value="{{$type}}">{{(new \ReflectionClass($type))->getShortName()}}</option>
								@endforeach
							</select>
						</label>
						<label class="w-64 mr-2">
                            <input value="{{old('causer_id')}}" name="causer_id" class="h-10 w-16" type="number">
						</label>

						Acted-on Entity
						<label class="w-64 mr-2">
							<select name="subject_type" class="select" style="height: 28px">
								<option value="">&nbsp;</option>
								@foreach ($subject_types as $subject_type)
								<option {{ $subject_type == old('subject_type') ? 'selected' : '' }}>{{(new \ReflectionClass($subject_type))->getShortName()}}</option>
								@endforeach
							</select>
						</label>

						Acted-on Entity ID
						<label class="mr-2">
							<input value="{{old('subject_id')}}" name="subject_id" class="h-10 w-16" type="number">
						</label>

						Action
						<label class="w-24 mr-2">
							<select name="description" class="select" style="height: 28px">
								<option value="">&nbsp;</option>
								@foreach ($descriptions as $description)
								<option {{ $description == old('description') ? 'selected' : '' }}>{{$description}}</option>
								@endforeach
							</select>
						</label>

						<label class="inline w-48 mr-2">
							From
							<input value="{{old('from')}}" name="from" class="h-10" type="date">
						</label>

						<label class="inline w-48 mr-2">
							To
							<input value="{{old('to')}}" name="to" class="h-10" type="date">
						</label>

						Containing
						<label class="w-64 mr-2">
							<input value="{{old('contain_data')}}" name="contain_data" class="h-10" type="text">
						</label>

						<input class="bg-green-dark rounded p-2 px-4 text-white" type="submit" value="Audit" />
					</div>
				</form>
			</div>

			<div class="panel panel-bordered overflow-x-scroll">

				<table class="table table-hover">
					<thead class="thead-default">
						<tr>
							<th>#</th>
							<th>Log ID</th>
							<th>Action Taken</th>
							<th>Involved User</th>
							<th>Acted-on Entity</th>
							<th>Entity ID</th>
							<th>Details</th>
							<th>Date</th>
						</tr>
					</thead>
					<tbody>
						@foreach($rendered_activities as $activity)
						<tr>
							<td>{{($loop->index)+1}}</td>
							<td>{{$activity->id}}</td>
							<td>{{$activity->description}}</td>
							<td>
								<a href='{{$activity->causerObject->get('href')}}'>
									{{$activity->causerObject->get('anchorText')}}
								</a>
							</td>
							<td>{{$activity->subjectObject->get('modelClassName')}}</td>
							<td>
								<a href='{{$activity->subjectObject->get('link')->get('href')}}'>
									{{$activity->subjectObject->get('link')->get('anchorText')}}
								</a>
							</td>
							<td>
								@dump($activity->properties->toArray())
							</td>
							<td>{{$activity->created_at}}</td>
						</tr>

						@endforeach
					</tbody>
				</table>
			</div>
			<p>
				<span class="bg-blue-dark text-white rounded p-2 px-8">
					{{$rendered_activities->total()}} item{{ $rendered_activities->total() > 1 ? 's' : '' }}
				</span>
			</p>
			<br>
			{{ $rendered_activities->appends( request()->except(['_token']) )->links() }}
		</div>
	</div>
</div>

@endsection
