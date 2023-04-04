@extends('campaign.base')

@section('title')
    @lang('Universe')
@endsection

@section('breadcrumb')
    <a href="/u"> @lang('Home')</a> > &nbsp;<b> @lang('Import from Community Database')</b>
@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

<div class="flex w-full">
	<div class="text-2xl font-sans border-b pb-2 w-full">
		 @lang('Community Database - Quick Import')
	</div>
</div>

<div class="p-4 text-grey-darker border-b">

	<center>
	<div class="w-2/3">
	Here you can import constituent data from your Community Database into your Campaign Database and flag the source as <span class="rounded-full bg-blue-dark border cursor-pointer px-2 py-1 text-white mx-1 text-xs"><i class="fas fa-landmark mr-1"></i> Community
			</span>
	</div>
	</center>

</div>

<div class="p-4 text-grey-darker border-b">

	<center>
	<div class="w-2/3">
		<div class="p-1 font-normal"><label><input type="checkbox" checked /> Click here to maintain separation between the Community Database and the Campaign Database.</label></div>

		   <a class="hover:font-bold px-2 py-1 rounded-full" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
    					Why would I want to?
  			</a>

		    <div class="collapse mt-4" id="collapseExample">

		    Most states require that public employees not engage in political activity, which can include maintaining campaign databases, while working in their public positions. FluencyBase therefore allows users to separate constituent data from campaign data if they choose.
			<br /><br />
		    If this option is checked, campaign data will be available to users working with the Community Database, but constituent data must be selectively imported into the Campaign Database.
		    <br /><br />
		    Keep in mind that such separation may not be required in all cases. Check the ethics rules of your state for more information.

			</div>


	</div>

	<div id="state_rules" class="hidden">a</div>
	</center>

</div>






<div class="mt-4 flex">

<div class="ml-8 mb-4 flex-1 flex-initial">
<div class="border-b">Columns:</div>
<div class="p-1"><label><input type="checkbox" disabled checked /> Name</label></div>
<div class="p-1"><label><input type="checkbox" disabled checked /> Address</label></div>
<div class="p-1"><label><input type="checkbox" checked /> Emails</label></div>
<div class="p-1"><label><input type="checkbox" checked /> Phones</label></div>
<div class="p-1"><label><input type="checkbox" checked /> Groups / Issues</label></div>
</div>

<div class="ml-8 mb-4 flex-1 flex-initial">
<div class="border-b">...that were Updated By:</div>
@foreach(Auth::user()->team->users()->get() as $theuser)
	<div class="p-1"><label><input type="checkbox" checked /> {{ $theuser->name }}
		<div class="text-sm text-grey font-normal uppercase">{{ $theuser->title }}</div>
</label></div>
@endforeach
</div>

<div class="flex-1 flex-initial ml-8">
<div class="border-b">Import:</div>
<br />
<button class="bg-orange-dark rounded-full text-white px-4 py-1 text-sm">Quick Import Now</button>
</div>

</div>


<div class="flex w-full mt-8">
	<div class="text-2xl font-sans border-b pb-2 w-full">
		 (Example of Import Process -> Save to Log)
	</div>
</div>

	<table class="text-xs">
		<tr class="border-b">
			<td class="p-2">Person ID 1044</td>
			<td class="p-2">Faye Allen</td>
			<td class="p-2">Matched with Voter ID 01AFE2856000</td>
			<td class="p-2">Does not exist in CampaignPeople</td>
			<td class="p-2">Created</td>
		</tr>
		<tr class="border-b">
			<td class="p-2">Person ID 973</td>
			<td class="p-2">Fred Arakelian</td>
			<td class="p-2">Matched with Voter ID 11AFK18610000</td>
			<td class="p-2">Exists</td>
			<td class="p-2">Already exists: Email<br />Added new: Phones</td>
		</tr>
		<tr class="border-b">
			<td class="p-2">Person ID 800</td>
			<td class="p-2">Colleen Burke</td>
			<td class="p-2">No Voter ID Found</td>
			<td class="p-2">No similar names found</td>
			<td class="p-2">Created</td>
		</tr>
		<tr class="border-b">
			<td class="p-2">Person ID 98</td>
			<td class="p-2">Shafiq Khan</td>
			<td class="p-2">No Voter ID Found</td>
			<td class="p-2">100% String Match to CampaignPerson ID 431</td>
			<td class="p-2">Added new data: Emails, Phones</td>
		</tr>
	</table>

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
