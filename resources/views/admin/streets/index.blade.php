@extends('admin.base')

@section('title')
    Streets
@endsection

@section('breadcrumb')

	<a href="/admin">Admin Dashboard</a>

@endsection

@section('style')

  @livewireStyles

@endsection

@section('main')

	<div class="p-8">
		<div class="text-2xl font-bold border-b-4 pb-2">
			Streets
		</div>

		<table class="w-full text-lg text-gray-500">
			<tr>
				<td class="p-2 border-b">Total Streets Count:</td>
				<td class="p-2 border-b text-right">{{ number_format($streets_count) }}</td>
				<td class="p-2 border-b text-right">100.0%</td>
			</tr>

			<tr>
				<td class="p-2 border-b">Streets Missing Lat:</td>
				<td class="p-2 border-b text-right">{{ number_format($missing_count) }}</td>
				<td class="p-2 border-b text-right">{{ round(($missing_count/$streets_count)*100, 1) }}%</td>
			</tr>

			<tr>
				<td colspan="3" class="uppercase font-bold pt-4 border-b">
					Voters
				</td>
			</tr>

			<tr>
				<td class="p-2 border-b">Total Voters Count:</td>
				<td class="p-2 border-b text-right">{{ number_format($voters_count) }}</td>
				<td class="p-2 border-b text-right">100.0%</td>
			</tr>

			<tr>
				<td class="p-2 border-b">Missing Location Voters Count:</td>
				<td class="p-2 border-b text-right">{{ number_format($votermiss_count) }}</td>
				<td class="p-2 border-b text-right">{{ round(($votermiss_count/$voters_count)*100, 1) }}%</td>
			</tr>

			<tr>
				<td class="p-2 border-b">Estimated Voter GIS:</td>
				<td class="p-2 border-b text-right">{{ number_format($estimate_count) }}</td>
				<td class="p-2 border-b text-right">{{ round(($estimate_count/$voters_count)*100, 1) }}%</td>
			</tr>

			<tr>
				<td class="p-2 border-b">Outlier Voter GIS:</td>
				<td class="p-2 border-b text-right">{{ number_format($outlier_count) }}</td>
				<td class="p-2 border-b text-right">{{ round(($outlier_count/$voters_count)*100, 1) }}%</td>
			</tr>


		</table>

	</div>

@endsection