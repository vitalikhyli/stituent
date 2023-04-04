@extends('u.base')
<?php if (!defined('dir')) define('dir','/u'); ?>

@section('title')
    	{{ substr($household->full_address,0,25) }} ...
@endsection

@section('breadcrumb')

	<!-- {!! Auth::user()->Breadcrumb($household->full_name, 'show_person') !!} -->

	<a href="{{dir}}">Home</a> > 
	<a href="{{dir}}/households">Households</a> 

    > &nbsp;<b>{{ $household->full_address }}</b>

@endsection

@section('style')


@endsection

@section('main')

@include('elements.errors')



	<div class="text-2xl font-sans text-blue-darker">

		<i class="fas fa-home ml-1 mr-2"></i>
		<span class="mr-2">
			{{ (!$external) ? $household->full_address : $household->household }}
		</span>

		@if($external)

			<span class="rounded-full bg-white border cursor-pointer px-2 py-1 text-grey-darkest m-2 text-xs">
				<i class="fas fa-unlink mr-1"></i>
				not yet imported
			</span>

		@endif

	</div>



	<table class="text-base mt-2 w-full border-t-4 border-blue">
		<tr class="border-t">
			<td class="p-2 bg-grey-lighter w-32">
				Full Address
			</td>
			<td class="p-2">
				{{ (!$external) ? $household->full_address : $household->household }}
			</td>
		</tr>
		<tr class="border-t">
			<td class="p-2 bg-grey-lighter w-32">
				ID #
			</td>
			<td class="p-2 font-mono text-sm">
				{{ $household->id }}
			</td>
		</tr>
	</table>



	@if($household->people_and_voters()->count() > 0)
		<div class="text-xl font-sans text-blue-darker mt-6 mb-1">
			People Living Here
		</div>

		<table class="text-base w-full border-t-4 border-blue">

			<tr class="bg-grey-lightest border-b cursor-pointer text-sm">
				<td class="p-2">
					Name
				</td>
				<td class="p-2">
					Contacts
				</td>
				<td class="p-2">
					Cases
				</td>
				<td class="p-2">
					Born
				</td>
				<td class="p-2">
					Gender
				</td>
			</tr>

		@foreach($household->people_and_voters() as $theperson)
			<tr data-href="{{dir}}/constituents/{{ $theperson->id }}" class="clickable border-t hover:bg-orange-lightest cursor-pointer">
				<td class="p-2">
					<span class="{{ ($theperson->external) ? 'bg-grey-lighter' : 'bg-orange-lightest' }} rounded-full px-3 py-1 text-sm border shadow">
					<i class="fa fa-user mr-2"></i>
					{{ $theperson->full_name }}
					</span>
				</td>
				<td class="p-2">
					@if(!$theperson->external)
						@if(\App\Person::find($theperson->id)->contacts)
							{{ \App\Person::find($theperson->id)->contacts->count() }}
						@endif
					@else
						--
					@endif
				</td>
				<td class="p-2">
					@if(!$theperson->external)
						@if(\App\Person::find($theperson->id)->cases)
							{{ \App\Person::find($theperson->id)->cases()->count() }}
						@endif
					@else
						--
					@endif
				</td>
				<td class="p-2">
					{{ $theperson->dob }}
				</td>
				<td class="p-2">
					{{ $theperson->gender }}
				</td>
			</tr>
		@endforeach
		</table>
	@endif







	@if(!$external)
	@if($household->cases->count() > 0)
		<div class="text-xl font-sans text-blue-darker mt-8 mb-1">
			Cases
		</div>

		
			<table class="text-base w-full border-t-4 border-blue">
			@foreach($household->cases as $thecase)
				<tr data-href="{{dir}}/cases/{{ $thecase->id }}" class="clickable border-t hover:bg-orange-lightest cursor-pointer {{ ($thecase->resolved) ? 'opacity-50' : '' }}">
					<td class="p-2 w-6 align-top">
						<i class="fa fa-folder-open"></i>
					</td>
					<td class="p-2 align-top text-sm">
						{{ \Carbon\Carbon::parse($thecase->date)->format("n/j/y") }}
					</td>
					<td class="p-2 align-top text-sm">
						{{ ($thecase->resolved) ? 'Closed' : 'Open' }}
					</td>
					<td class="p-2 align-top">
						<div class="font-bold">{{ $thecase->subject }}</div>
						<div class="text-sm">{{ $thecase->notes }}</div>
					</td>
				</tr>
			@endforeach
			</table>
	@endif
	@endif



	@if($nearby_households->count() > 0)
		<div class="text-xl font-sans text-blue-darker mt-6 mb-1">
			Nearby Households
		</div>

		<table class="text-base w-full border-t-4 border-blue">

		@foreach($nearby_households as $neighbor)
			<tr data-href="{{dir}}/households/{{ $neighbor->id }}" class="clickable border-t cursor-pointer hover:bg-orange-lightest">

				<td class="p-2 text-sm">

					<i class="fas fa-home mr-2"></i> {{ $neighbor->full_address }}

				</td>

				<td class="p-2 break-words align-top" style="word-break: break-word;">

					<span class="text-xs text-grey-darker">
						<span class="mr-2">
							({{ $neighbor->total_residents }}) 
						</span>
						@for ($i = 0; $i < $neighbor->total_residents; $i++)
						    <i class="fas fa-user"></i>
						@endfor
					</span>

				</td>

				<td class="p-2 text-xs text-grey-darker">
					@if(!$neighbor->external)
						<?php
							$cases_count = \App\Household::find($neighbor->id)->cases->count();
						?>
						@if($cases_count > 0)
							<span class="mr-2">
								({{ $cases_count }}) 
							</span>
							@for ($i = 0; $i < $cases_count; $i++)
							    <i class="fas fa-folder"></i>
							@endfor
						@endif
					@endif
				</td>
	
			</tr>
		@endforeach
		</table>
	@endif


<br />
<br />
@endsection

@section('javascript')
<script type="text/javascript">
	$(document).ready(function() {

	    $(".clickable").click(function() {
	        window.location = $(this).data("href");
	    });

	});
</script>
@endsection