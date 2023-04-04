@extends(Auth::user()->team->app_type.'.base')

@section('title')
    @lang('Service Learning Partnerships')
@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
    <a href="/{{ Auth::user()->team->app_type }}/entities">Organizations</a> >
	Service Learning Partnerships

@endsection

@section('style')

	<style>

		table.tight > tbody > tr > td {
			padding: 2px 8px;
		}
	</style>

@endsection

@section('main')

<div class="border-b-4 pb-2 border-blue">
	<div class="text-2xl font-sans w-full">
		<div class="float-right text-base text-white rounded-full bg-blue px-4 py-2">
			Add Partnership
		</div>
		 {{ $slps->count() }} Service Learning Partnerships
	</div>
</div>

	<table class="table text-sm">

		<tr>
			<th>Course</th>
			<th>Faculty</th>
			<th>Filed By</th>
			<th>Department</th>
			<th>Partner</th>
		</tr>

		@foreach ($slps as $slp)

			<tr>
				<td>{{ $slp->course }}</td>
				<td>{{ $slp->faculty }}</td>
				<td>{{ $slp->filer }}</td>
				<td>
					<a href="/u/entities/{{ $slp->department->id }}">
						{{ $slp->department->name }}
					</a>
				</td>
				<td>
					<b>
						<a href="/u/entities/{{ $slp->partner->id }}">
							{{ $slp->partner->name }}
						</a>
					</b>
					<br>
					{{ $slp->partner_contact }}
					<br>
					{{ $slp->partner_email }}
				</td>
			</tr>

		@endforeach
	</table>




@endsection


