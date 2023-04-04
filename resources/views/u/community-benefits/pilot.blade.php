@extends('u.base')

@section('title')
    PILOT
@endsection

@section('breadcrumb')
    <a href="/u">Home</a> > <a href="/u/community-benefits">Community Benefits</a> > &nbsp;<b>PILOT</b>
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


<div class="text-2xl font-sans pb-8">

	<a class="float-right text-lg rounded-full px-4 py-2 text-grey-lightest bg-green hover:text-white" href="/u/community-benefits">
		All Community Benefits <i class="fa fa-arrow-right"></i>
	</a>

	<i class="fa fa-dollar-sign text-blue mr-2 text-3xl z-1"></i>

	PILOT
</div>


<div class="hidden text-3xl font-black p-4 border-4 border-red mt-4 underline text-red">
	Extremely Important:
	<a traget="_blank" href="https://btu.org/in-hearing-and-new-report-btu-pushes-for-pilot-payments/">
		"In New Report and Hearing, BTU Pushes for PILOT Payments from City Nonprofits"
	</a>
</div>


<div class="w-full px-6 py-4 bg-grey-lightest border-2">

		<table class="w-full text-grey-darkest align-top">
			@foreach ($summary as $year => $data)

				<tr>
					<td class="w-32">
						<div class="text-2xl">{{ $year }}</div>
						<div class="text-xs text-grey uppercase">AT-A-GLANCE</div>
					</td>
					<td class="">
						<b class="text-2xl">{{ $data['programs'] }} Programs</b><br>

						<div class="text-sm">
							<b>{{ $data['beneficiaries']->count() }} Beneficiaries:</b>
							{{ $data['beneficiaries']->implode('; ') }}
						</div>
					</td>
					<td class="w-1/3">
						<table class="mx-auto">
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
						</table>
					</td>
				</tr>

			@endforeach
		</table>

</div>

<div class="pt-8 mt-8">

	

	<div class="font-bold text-2xl border-b-4 border-blue py-2 mb-6">
		PILOT FY{{ substr($pilots->max('fiscal_year'), 2, 2) }}
		<!-- <a href="/u/community-benefits/new">
			<button type="button" class="float-right px-4 py-2 rounded-full font-normal border text-sm ml-2 shadow-sm">
				<i class="far fa-file-excel"></i>&nbsp; Generate Report
			</button>
		</a> -->
		<a href="/u/community-benefits/new">
			<button type="button" class="float-right bg-blue text-white px-4 py-2 rounded-full font-normal text-sm ml-2 hover:bg-blue-dark shadow-sm">
				Add Program
			</button>
		</a>
	</div>

	<table class="table">

		<tr class="">
			<th></th>
			<th class="uppercase text-grey-dark text-sm">Program</th>
			<th class="uppercase text-grey-dark text-sm">Payment</th>
			<th class="uppercase text-grey-dark text-sm">Parties</th>
		</tr>

		@foreach ($pilots as $pilot)
			<tr>
				<td class="text-grey">{{ $loop->iteration }}.</td>
				<td>
					<b>{{ $pilot->program_name }}</b>
					<br>
					<span class="text-grey-dark text-sm">{{ $pilot->program_description }}</span>
				</td>
				<td>
					<b>${{ number_format($pilot->value) }}</b><br>
					<div class="text-sm text-grey-dark">
						{{ $pilot->value_type }}, {{ $pilot->time_frame }}
					</div>
					<div class="text-sm text-grey">
						({{ round(($pilot->value / $summary[$pilot->fiscal_year]['total']) * 100, 2) }}%)
					</div>
				</td>
				<td class="w-1/3">
					
					<i>Beneficiaries:</i> {{ $pilot->beneficiaries }}<br>
					
					<span class="text-grey-dark text-sm">
						<i>Initiators:</i> {{ $pilot->initiators }}<br>
						<i>Partners:</i> {{ $pilot->partners }}
					</span>
					
								
							
				</td>
			</tr>
		@endforeach

	</table>

	
</div>

@endsection

