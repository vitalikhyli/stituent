@extends('print')

@section('title')
    Case: {{ $thecase->subject }}
@endsection


@section('style')

	<style>


	</style>

@endsection

@section('main')

<div style="font-size:10pt;"> <!-- Seems small but best fit for page -->

	<div class="border-b pb-4 pt-2 text-center tracking-widest mb-6">
		OFFICE OF<br />
		<span class="text-2xl">{{ Auth::user()->team->name }}</span>
	</div>

	<div class="text-xl px-2 py-1 font-semibold text-center mb-2 tracking-widest">
		<i class="far fa-file mr-1"></i> Case Report
	</div>

	<div class="text-center mb-8">
		<div class="text-sm italic">Prepared by</div>
		{{ Auth::user()->name }} / {{ \Carbon\Carbon::now()->format("F j, Y") }}
	</div>

	<div class="flex">

		<div class="table mt-2 w-1/2 mr-2">

			<div class="table-row">
				<div class="table-cell font-semibold w-1/6 text-right p-1">
					Subject
				</div>
				<div class="table-cell p-1 font-bold">
					{{ $thecase->subject }}
				</div>
			</div>

			<div class="table-row">
				<div class="table-cell font-semibold w-1/6 text-right p-1">
					Opened
				</div>
				<div class="table-cell p-1">
					{{ \Carbon\Carbon::parse($thecase->date)->format("F j, Y") }}
					<span class="text-grey-dark text-sm ml-2">
					({{ \Carbon\Carbon::parse($thecase->date)->diffForHumans() }})
					</span>
				</div>
			</div>


			<div class="table-row">
				<div class="table-cell font-semibold w-1/6 text-right p-1">
					Type
				</div>
				<div class="table-cell p-1">
					{{ (!$thecase->type) ? 'General' : $thecase->type }}
				</div>
			</div>

			<div class="table-row">
				<div class="table-cell font-semibold w-1/6 text-right p-1">
					Priority
				</div>
				<div class="table-cell p-1">
					{{ $thecase->priority }}
				</div>
			</div>

			<div class="table-row">
				<div class="table-cell font-semibold w-1/6 text-right p-1">
					Status
				</div>
				<div class="table-cell p-1">
					{{ ucwords($thecase->status) }}
					@if($thecase->status == 'resolved')
						<i class="fas fa-check-circle ml-1"></i>
					@endif
				</div>
			</div>

			<div class="table-row">
				<div class="table-cell font-semibold w-1/6 text-right p-1">
					Summary:
				</div>
				<div class="table-cell p-1 italic">
					{{ $thecase->notes }}

					@if($thecase->closing_remarks)

						<div class="mt-2">
							<span class="font-semibold border-b border-black text-sm">Closing Remarks</span><br />
							 {{ $thecase->closing_remarks }}
						</div>

					@endif

				</div>
			</div>

		</div>

		<div class="table mt-2 w-1/2 ml-2">

			<div class="table-row">
				<div class="table-cell font-semibold w-1/6 text-right p-1 whitespace-no-wrap">
					Assigned to:
				</div>
				<div class="table-cell p-1">
					{{ $thecase->assignedTo()->name }}
				</div>
			</div>

			<div class="table-row">
				<div class="table-cell font-semibold w-1/6 text-right p-1">
					People:
				</div>
				<div class="table-cell p-1">
					<ul class="">
						@if($thecase->people->count() >0)
							@foreach($thecase->people as $theperson)
								<li>{{ $theperson->full_name }}<br/>
									@if($theperson->primary_phone)
										Phone: {{ $theperson->primary_phone }}
										<br/>
									@endif

									@if($theperson->primary_email)
										Email: {{ $theperson->primary_email }}
										<br/>
									@endif

									@if($theperson->work_email)
										Work: {{ $theperson->work_email }}
										<br/>
									@endif

									@if($theperson->work_phone)
										Work: {{ $theperson->work_phone }}
										<br/>
									@endif
									<span class="text-sm text-grey-darker">{{ $theperson->full_address }}</span></li>
							@endforeach
						@endif
					</ul>
				</div>
			</div>


		</div>

	</div>



	<div class="text-xl px-2 py-1 font-semibold text-center mb-2 tracking-widest">
		Contact History
	</div>


	<center>
		<div class="table w-full text-left">
		@foreach($contacts as $thecontact)
			<div class="table-row">
		
				<div class="table-cell p-2 {{ (!$loop->last) ? 'border-b' : '' }}">

					<div class="font-bold">
						{{ \Carbon\Carbon::parse($thecontact->date)->format("F j, Y") }}

						@if($thecontact->user->name)
							<span class="font-normal">
								/ {{ $thecontact->user->name }}
								@if($thecontact->user->permissions)
									@if($thecontact->user->permissions->title)
										<span class="text-grey-dark">
											({{ $thecontact->user->permissions->title }})
										</span>
									@endif
								@endif
							</span>
						@endif
					</div>

					{{ $thecontact->notes }}

				</div>
			</div>
		@endforeach
		</div>
	</center>

</div>

@endsection
