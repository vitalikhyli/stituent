@extends('u.base')

@section('title')
    Community Benefits
@endsection

@section('breadcrumb')
    <a href="/u">Home</a> > &nbsp;<b>Community Benefits</b>
@endsection

@section('style')

	<style>
	
		table td {
			padding: 5px;
			vertical-align: top;
		}

	</style>

@endsection

@section('main')



<div class="flex w-full mb-1">

	<div class="mb-2 flex flex-1 pt-1">

		@if($all_years->count() > 1)

			@if($selected_year == null)
				<div class="w-18 rounded-lg px-4 py-1 bg-blue text-white border border-transparent text-sm uppercase mr-2">All</div>
			@else
				<a href="?year=" class="">
					<div class="w-18 rounded-lg px-4 py-1 bg-grey-lightest border text-sm uppercase mr-2">All</div>
				</a>
			@endif

			@foreach($all_years as $year)

				@if($selected_year == $year)
					<div class="w-18 rounded-lg px-4 py-1 bg-blue text-white border border-transparent text-sm uppercase mr-2">FY{{ substr($year, 2, 2) }}</div>
				@else
					<a href="?year={{ $year }}" class="">
						<div class="w-18 rounded-lg px-4 py-1 bg-grey-lightest border text-sm uppercase mr-2">FY{{ substr($year, 2, 2) }}</div>
					</a>
				@endif

			@endforeach

		@endif

	</div>


	<div class="mb-2 flex text-right pt-1">

		@if($mode == 'pilot')

			<a href="/{{ Auth::user()->team->app_type }}/community-benefits?year={{ $selected_year }}" class="">
				<div class="w-18 rounded-lg px-4 py-1 bg-grey-lightest border text-sm uppercase ml-2">
					Both
				</div>
			</a>

			<a href="/{{ Auth::user()->team->app_type }}/community-benefits/non?year={{ $selected_year }}" class="">
				<div class="w-18 rounded-lg px-4 py-1 bg-grey-lightest border text-sm uppercase ml-2">
					General
				</div>
			</a>

			<div class="w-18 rounded-lg px-4 py-1 bg-green text-white border text-sm uppercase ml-2">
				PILOT
			</div>
			

		@elseif($mode == 'non')

			<a href="/{{ Auth::user()->team->app_type }}/community-benefits?year={{ $selected_year }}" class="">
				<div class="w-18 rounded-lg px-4 py-1 bg-grey-lightest border text-sm uppercase ml-2">
					Both
				</div>
			</a>

			<div class="w-18 rounded-lg px-4 py-1 bg-green text-white border border-transparent text-sm uppercase ml-2">
					General
			</div>

			<a href="/{{ Auth::user()->team->app_type }}/community-benefits/pilot?year={{ $selected_year }}" class="">
				<div class="w-18 rounded-lg px-4 py-1 bg-grey-lightest border text-sm uppercase ml-2">
					PILOT
				</div>
			</a>

		@else

			<div class="w-18 rounded-lg px-4 py-1 bg-green text-white border border-transparent text-sm uppercase ml-2">
				Both
			</div>

			<a href="/{{ Auth::user()->team->app_type }}/community-benefits/non?year={{ $selected_year }}" class="">
				<div class="w-18 rounded-lg px-4 py-1 bg-grey-lightest border text-sm uppercase ml-2">
						General
				</div>
			</a>

			<a href="/{{ Auth::user()->team->app_type }}/community-benefits/pilot?year={{ $selected_year }}" class="">
				<div class="w-18 rounded-lg px-4 py-1 bg-grey-lightest border text-sm uppercase ml-2">PILOT</div>
			</a>

		@endif

	</div>

    <div class="flex text-right ml-2">
      <input type="text" id="filter-input" onkeyup="filterTable()" class="border-2 p-2 text-base" placeholder="Filter Programs" />
    </div>


</div>



<div class="w-full px-6 py-4 bg-grey-lightest border-2">

	@if(!$benefits_by_year->first())

		<div class="w-full text-right">

			<div class="text-2xl text-left border-b mb-2">
				No Programs Yet
			</div>


			<a href="/u/community-benefits/new/{{ (!$mode) ? 'basic' : $mode }}/{{ $selected_year }}">
				<button type="button" class="bg-blue text-white px-4 py-2 rounded-full font-normal text-sm ml-2 hover:bg-blue-dark shadow-sm">
					Add {{ ($mode == 'pilot') ? 'PILOT ' : '' }}Program
					@if($selected_year)
						for FY{{ substr($selected_year,2,2) }}
					@endif
				</button>
			</a>

		</div>
		
	@else

		<table class="w-full text-grey-darkest align-top">
			@foreach ($summary as $year => $data)

				<tr class="">
					<td class="w-32">
						<div class="text-2xl">{{ $year }}</div>
						<div class="text-xs text-grey uppercase">AT-A-GLANCE</div>
					</td>
					<td class="">
						<b class="text-2xl">{{ $data['programs'] }} {{ Str::plural('Program', $data['programs']) }}</b><br>

						<div class="text-sm">
							<b>{{ $data['beneficiaries']->count() }} {{ Str::plural('Beneficiary', $data['beneficiaries']->count()) }}</b>
							{{ $data['beneficiaries']->implode('; ') }}
						</div>

					</td>
					<td class="w-1/3">
						<!-- <table class="mx-auto">
							<tr>
								
								<td class="text-right">${{ number_format($data['cash']) }}</td>
								<td class="text-left">Cash</td>
							</tr>
							<tr>
								
								<td class="text-right">${{ number_format($data['inkind']) }}</td>
								<td class="text-left">In Kind</td>
							</tr>
							<tr>
								
								<td class="text-right">${{ number_format($data['both']) }}</td>
								<td class="text-left">Both</td>
							</tr>
							<tr>
								
								<td class="text-right font-bold border-t">${{ number_format($data['total']) }}</td>
								<td class="text-left border-t">Total</td>
							</tr>
						</table> -->
					</td>
				</tr>

			@endforeach
		</table>

	@endif

</div>




<div class="pt-8 mt-2">


	@foreach($benefits_by_year as $year => $benefits)
	
		@include('u.community-benefits.table-benefits', ['benefits' => $benefits])

	@endforeach

	
</div>

@endsection

@section('javascript')

	<script type="text/javascript">


		function filterTable()
		{
		  input = document.getElementById('filter-input');
		  filter_string = input.value.toUpperCase();
		  lines = document.getElementsByClassName('line-div');
		  group_names = document.getElementsByClassName('line-name-div');

		  for (i = 0; i < lines.length; i++) {

		    group_name = group_names[i].innerHTML.trim().toUpperCase();

		    if (group_name.indexOf(filter_string) > -1) {

		      lines[i].style.display = "";

		    } else {

		      lines[i].style.display = "none";

		    }
		  }

		}


		$(document).ready(function() {

		    $('#filter-input').focus();

		});


	</script>

@endsection

