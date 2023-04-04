@extends('campaign.base')

@section('title')
    @lang('Universe')
@endsection

@section('breadcrumb')
    <a href="/u"> @lang('Home')</a> > &nbsp;<b> @lang('Universe')</b>
@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

<div class="flex w-full">
	<div class="text-2xl font-sans border-b pb-2 w-full">
		 @lang('Campaign Universe')

		 <div class="text-base float-right p-2">
		 {{ \App\Campaign::where('team_id',Auth::user()->team_id)->first()->name }} on
		  {{ \App\Campaign::where('team_id',Auth::user()->team_id)->first()->election_day }}
			<button class="ml-4 bg-blue rounded-full text-white px-2 py-1 text-sm">Change Current Campaign</button>
</div>
	</div>
</div>


<?php $i = 1; ?>
<center>
	<div class="p-4 text-grey-darker w-2/3 ">
	<b>Your universe contains {{ $universe->count() }} voters.</b> Set the base list of voters who you want to reach. You can always add to or modify the universe later. <a href="/campaign/constituents_universe"><button class="m-1 bg-blue hover:bg-blue-dark rounded-full text-white px-2 py-1 text-sm">See All</button></a>
</div>
</center>

<!--
<table>
@foreach ($universe->take(10) as $thevoter)
<tr class="border-b"><td>{{ $i++ }}</td><td>{{ $thevoter->full_name }}</td><td>{{ $thevoter->registered }}</td><td>{{ $thevoter->member_id }}</td></tr>
@endforeach
</table>
-->



<div class="border-t">



</div>


<table class="border-r border-l border-t mt-4 w-full">
	<tr class="border-b hover:bg-orange-lightest cursor-pointer">
		<td class="p-2 w-8">
			<i class="fas fa-trash-alt"></i>
		</td>
		<td class="p-2 w-8">
			<i class="fas fa-edit"></i>
		</td>
		<td class="p-2 w-8">
			<i class="fas fa-arrows-alt"></i>
		</td>
		<td class="p-2 border-l">
			<span class="text-blue">Add</span>
		</td>
		<td class="p-2 border-l">
			Voters from Voter File for Legislative District 20<br />
		</td>
		<td class="border-l p-2 text-blue">
			+ 30,000
		</td>
		<td class="border-l p-2">
			<span class="text-red"></span>
		</td>
	</tr>

	<tr class="border-b hover:bg-orange-lightest cursor-pointer">
		<td class="p-2 w-8">
			<i class="fas fa-trash-alt"></i>
		</td>
		<td class="p-2 w-8">
			<i class="fas fa-edit"></i>
		</td>
		<td class="p-2 w-8">
			<i class="fas fa-arrows-alt"></i>
		</td>
		<td class="p-2 border-l">
			<span class="text-blue">Add</span>
		</td>
		<td class="p-2 border-l">
			Campaign People not linked to the Voter File
		</td>
		<td class="border-l p-2">
			<span class="text-blue">+ 91</span>
		</td>
		<td class="border-l p-2">
			
		</td>
	</tr>

	<tr class="border-b hover:bg-orange-lightest cursor-pointer">
		<td class="p-2 w-8">
			<i class="fas fa-trash-alt"></i>
		</td>
		<td class="p-2 w-8">
			<i class="fas fa-edit"></i>
		</td>
		<td class="p-2 w-8">
			<i class="fas fa-arrows-alt"></i>
		</td>
		<td class="p-2 border-l">
			<span class="text-red">Remove</span>
		</td>
		<td class="border-l p-2">
			Party = "R"<br />
			...who: age > 60 AND<br />
			...who: voted in G16
		</td>
		<td class="border-l p-2">
			<span class="text-blue"></span>
		</td>
		<td class="border-l p-2">
			<span class="text-red">- 10,030</span>
		</td>
	</tr>

	<tr class="border-b hover:bg-orange-lightest cursor-pointer">
		<td class="p-2 w-8">
			<i class="fas fa-trash-alt"></i>
		</td>
		<td class="p-2 w-8">
			<i class="fas fa-edit"></i>
		</td>
		<td class="p-2 w-8">
			<i class="fas fa-arrows-alt"></i>
		</td>
		<td class="p-2 border-l">
			Narrow
		</td>
		<td class="border-l p-2">
			All of the above<br />
			...who voted in at least one of G16, G12, G08
		</td>
		<td class="border-l p-2">
			<span class="text-blue"></span>
		</td>
		<td class="border-l p-2">
			<span class="text-red">- 12,020</span>
		</td>
	</tr>

	<tr class="border-b bg-grey-lighter">
		<td class="p-2 text-center" colspan="4">
			<button class="bg-blue rounded-full text-white px-2 py-1 text-sm">New Rule</button>
		</td>
		<td class="p-2 border-l font-bold text-right" colspan="1">
			Current Universe
		</td>
		<td class="border-l p-2 text-center" colspan="2">
			<span class="font-bold">21,000 Voters</span>
		</td>
	</tr>

</table>



<div class="flex p-3 my-4 rounded-lg pt-2">
	<div class="flex-1 flex-initial">
	<b>Attention:</b>
	</div>
	<div class="flex-1">
	<ul>
		<li>Your voter file was updated by FluencyBase on 5/1/20.</li>
		<li>You imported data from your Virtual Office on 5/1/20.</li>
	</ul>
	</div>

	<div class="flex-1 ml-10 flex-initial">
	<button class="bg-orange-dark rounded-full text-white px-2 py-1 text-sm">Recalcuate Universe</button>
	</div>
</div>


<br />
@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {

	$("#search").keyup(function(){
		getSearchData(this.value);

	});
	function getSearchData(v) {
		var mode = '{{ (isset($mode_all)) ? 'constituents_all' : 'constituents' }}';
		$.get('/campaign/'+mode+'_search/'+v, function(response) {
			$('#list').replaceWith(response);
		});
	}
	$("#search").focus();
});
</script>
@endsection
