@extends('campaign.base')

@section('title')
    	{{ $person->full_name }}
@endsection

@section('breadcrumb')
    <a href="/campaign">Home</a> > <a href="/campaign/constituents">Voters</a> > &nbsp;<b>{{ $person->full_name }}</b>
@endsection

@section('style')

	<style>
		.truncate {
		  width: 100px;
		  white-space: nowrap;
		  overflow: hidden;
		  text-overflow: ellipsis;
		}

	</style>

@endsection

@section('main')

	<div class="text-3xl font-sans">
		{{ $person->full_name }}

		@if(isset($mode_external))
			<span class="rounded-full bg-white border cursor-pointer px-2 py-1 text-grey-darkest m-2 text-xs">
				<i class="fas fa-unlink mr-1"></i>
				not yet imported
			</span>
		@else
			<span class="rounded-full bg-orange-lighter border cursor-pointer px-2 py-1 text-grey-darkest m-2 text-xs">
				<i class="fas fa-user-circle mr-1"></i>
				{{ $person->team->shortname }}
			</span>

			<span class="rounded-full bg-orange-dark border cursor-pointer px-2 py-1 text-white mr-2 text-xs"><i class="fas fa-flag mr-1"></i> Campaign
			</span>
			
		@endif
		


		<button type="button" class="rounded-full bg-blue hover:bg-blue-dark text-white float-right text-sm px-8 py-2 mt-1 shadow">
			EDIT
		</button>
	</div>


		

			<table class="text-base mt-4 w-full">
				<tr class="border-t">
					<td class="p-2 bg-grey-lighter w-32">
						Address
					</td>
					<td class="p-2">
						{{ $person->full_address }}
					</td>
				</tr>
				<tr class="border-t">
					<td class="p-2 bg-grey-lighter">
						Household
					</td>
					<td class="p-2">
						<div class="inline-flex flex-wrap">
						@if($cohabitors->count() <= 0)
							Alone
						@else
						@foreach ($cohabitors as $theperson)
							@if($theperson->external)
								<a href="/campaign/constituents/{{ $theperson->id}}">
								<div class="flex-1 flex-initial m-1 bg-grey-lighter px-2 py-1 text-blue-dark rounded-full mr-2 text-base">
									<i class="fas fa-unlink text-sm mr-2"></i>
									{{ $theperson->full_name }}
								</div>
								</a>
							@else
								<a href="/campaign/constituents/{{ $theperson->id}}">
								<div class="flex-1 flex-initial m-1 bg-orange-lighter px-2 py-1 text-blue-dark rounded-full mr-2 text-base">
									<i class="fas fa-user-circle text-sm mr-2"></i>
									{{ $theperson->full_name }}
								</div>
								</a>
							@endif
						@endforeach
						@endif
						</div>
					</td>
				</tr>
			</table>

		<div class="flex">
			<table class="w-1/2 border-b">
				<tr class="border-t">
					<td class="p-2 bg-grey-lighter  w-32">
						Born
					</td>
					<td class="p-2">
						{{ \Carbon\Carbon::parse($person->born)->format("n/j/Y") }}
						(age {{ \Carbon\Carbon::parse($person->born)->diffInYears() }})
					</td>
				</tr>
				<tr class="border-t">
					<td class="p-2 bg-grey-lighter  w-32">
						Gender
					</td>
					<td class="p-2">
						@if($person->gender)
							{{ $person->gender }}
						@endif
					</td>
				</tr>				
			</table>



			<table class="text-base w-1/2 border-b">
				<tr class="border-t">
					<td class="p-2 bg-grey-lighter w-32">
						Emails
					</td>
					<td class="p-2">
						{{ $person->email }}
					</td>
				</tr>
				<tr class="border-t">
					<td class="p-2 bg-grey-lighter w-32">
						Phones
					</td>
					<td class="p-2">
						{{ $person->phone }}
					</td>
				</tr>
			</table>

		</div>


		<table class="w-full">
			<tr class="border-b">
					<td class="p-2 flex items-center bg-orange-lightest">
						Support in {{ CurrentCampaign()->name }} : 

						<div class="ml-4">
						@if($person->support)
							<a href="/campaign/constituents/{{ $person->id }}/cycle_campaign_support/{{ CurrentCampaign()->id }}" class="text-grey-dark text-sm">

							@if($person->support->campaign_1 == 1)
								<button class="rounded-full px-4 py-2 text-white bg-blue">
								Yes <i class="fas fa-smile-beam ml-4"></i>
								</button>
							@elseif($person->support->campaign_1 == 2)
								<button class="rounded-full px-4 py-2 text-white bg-blue-light">
								Lean Yes <i class="fas fa-thumbs-up ml-4"></i>
								</button>							@elseif($person->support->campaign_1 == 3)
								<button class="rounded-full px-4 py-2 text-black border">
								Undecided <i class="fas fa-question-circle ml-4"></i>
								</button>
							@elseif($person->support->campaign_1 == 4) 
								<button class="rounded-full px-4 py-2 text-white bg-grey-darker">
								No <i class="fas fa-thumbs-down ml-4"></i>
								</button>	
							@elseif($person->support->campaign_1 == 5) 
								<button class="rounded-full px-4 py-2 text-white bg-black">
								Hard No <i class="fas fa-angry ml-4"></i>
								</button>
							@else
								<button class="rounded-full px-4 py-2 bg-grey-lighter border">
								Edit support <i class="fas fa-edit ml-4"></i>
								</button>
							@endif

							</a>
						@else
							<a href="/campaign/constituents/{{ $person->id }}/cycle_campaign_support/{{ CurrentCampaign()->id }}" class="rounded-full px-2 py-1 text-sm">
							<button class="rounded-full px-4 py-2 bg-grey-lighter border">
								Edit support <i class="fas fa-edit ml-4"></i>
								</button>
							</a>
						@endif
						</div>
					</td>
				</tr>
			</table>

		







	<div class="mt-6 flex flex-row-reverse text-sm"> 

		@if(isset($cases))
		@if($cases->where('resolved',1)->count() >0 )
		    <div class="person-tab {{ ($tab == 'cases') ? 'active bg-blue text-white' : 'bg-grey-lighter text-blue-darker hover:text-black hover:bg-grey-lighter' }} h-10 uppercase pt-3 px-4 mr-2 rounded-t-lg cursor-pointer relative border-t border-l border-r " data-target="#person_cases" id="tab_cases">
		       Resolved
		       ({{ $cases->where('resolved',1)->count() }})
		    </div>
		@endif
		@endif

	    <div class="person-tab {{ ($tab == 'massemailings') ? 'active bg-blue text-white' : 'bg-grey-lighter text-blue-darker hover:text-black hover:bg-grey-lighter' }} h-10 uppercase pt-3 px-4 mr-2 rounded-t-lg cursor-pointer relative border-t border-l border-r " data-target="#person_massemailings" id="tab_massemailings">
	      Email
	    </div>

	    <div class="person-tab {{ ($tab == 'otherinfo') ? 'active bg-blue text-white' : 'bg-grey-lighter text-blue-darker hover:text-black hover:bg-grey-lighter' }} h-10 uppercase pt-3 px-4 mr-2 rounded-t-lg cursor-pointer relative border-t border-l border-r " data-target="#person_otherinfo" id="tab_otherinfo">
	       Other Info
	    </div>

	    <div class="person-tab {{ ($tab == 'groups') ? 'active bg-blue text-white' : 'bg-grey-lighter text-blue-darker hover:text-black hover:bg-grey-lighter' }}  h-10 uppercase  pt-3 px-4  mr-2 rounded-t-lg cursor-pointer relative border-t border-l border-r " data-target="#person_groups" id="tab_groups">
	       Groups
	        @if(isset($groups) && ($groups->count() >0))
	       		({{ $groups->count() }})
	        @endif
	    </div>

	   	<div class="person-tab {{ ($tab == 'contacts') ? 'active bg-blue text-white' : 'bg-grey-lighter text-blue-darker hover:text-black hover:bg-grey-lighter' }} relative rounded-t-lg h-10 uppercase pt-3 px-4 mr-2 border-t border-l border-r cursor-pointer" data-target="#person_contacts">
	        Notes
	        @if(isset($contacts) && ($contacts->count() >0))
	       		({{ $contacts->count() }})
	        @endif

	        @if(isset($cases) && ($cases->where('resolved',0)->count() >0))
	        	&amp; Cases
	       		({{ $cases->where('resolved',0)->count() }})
	        @endif
	    </div>    

	</div>

	


    <div class="person-tabs w-full" style="min-height: 700px;">



        <div id="person_contacts" class="border-t person-tab-content text-base w-full  {{ ($tab != 'contacts') ? 'hidden' : '' }}">

		<div class="flex">

        	<div class="mt-2 w-1/2">
				<form method="POST" id="contact_form" action="/campaign/constituents/{{ $person->id }}/add_contact">

					@csrf

					<div class="inline-flex flex-wrap border-t-2 px-4 py-2 w-full bg-orange-lightest">

						<label class="hover:bg-orange-lighter cursor-pointer font-normal rounded-full border px-2 py-1 text-sm bg-white">
							<input type="checkbox" name="include_people[]" value="{{ $person->id }}" checked disabled readonly />
							{{ $person->full_name }}
						</label>

						<label class="ml-2 font-normal rounded-full border px-2 py-1 text-sm bg-white hover:bg-orange-lighter cursor-pointer">
							<div onclick="">
								Include Others
							</div>
						</label>

					</div>

					{{ csrf_field() }}

					<div class="flex">

					<input type="hidden" value="{{ $person->id }}" name="person_id" />

					<input id="contact_date"
						   type="text"
						   placeholder="Short of it"
						   name="date"
						   value="{{ \Carbon\Carbon::now()->toDateString() }}"
						   autocomplete="off"
						   class="focus:shadow-inner focus:bg-blue-lightest w-2/5 border-t-2 border-l-2 border-b-2 text-lg px-4 py-3 font-bold bg-grey-lightest" />

					<input id="contact_subject"
						   type="text"
						   placeholder="Action"
						   name="subject"
						   value=""
						   autocomplete="off"
						   class="focus:shadow-inner focus:bg-blue-lightest w-3/5 border-2 text-lg px-4 py-3 font-bold bg-grey-lightest" />
					</div>
					<textarea 
						   id="contact_notes"
						   name="notes" 
						   class="focus:shadow-inner focus:bg-blue-lightest border-2 p-4 border-t-0 text-lg w-full bg-grey-lightest"
						   placeholder="Description"
						   rows="7"></textarea>

					<div class="flex items-center">
						<div class="w-full -mt-20">
							<input type="submit" class="mr-4 float-right shadow z-10 relative cursor-pointer hover:bg-blue-dark rounded-full float-right py-2 px-4 bg-blue text-white" value="Add" />
						</div>
					</div>

				</form>
			</div>



			<div class="w-1/2 ml-3 mt-2 text-base">
				@if(isset($cases) && ($cases->count() >0))
					@foreach($cases->where('resolved',0) as $thecase)
					<a href="/campaign/cases/{{ $thecase->id }}">
						<div class="border-2 shadow rounded-lg bg-orange-lightest hover:bg-orange-lighter mb-2 px-4 py-2">
							<span class="text-grey-darkest">
							<i class="fas fa-folder-open text-xl mr-2 "></i>
							</span>{{ $thecase->subject}}
							<span class="text-grey-darkest">
							({{$thecase->contacts->count() }} notes)
							</span>
						</div>
					</a>
					@endforeach
				@endif

           		@if(isset($contacts))
						@foreach($contacts as $thecontact)
							<div class="pb-8 cursor-pointer">
								@include('campaign.notes.one-contact')
							</div>
						@endforeach
				@else
					No notes yet
				@endif
			</div>
		</div>
    </div>


    <!-- No Resolved Cases Tab -->


		<div id="person_groups" class="person-tab-content flex {{ ($tab != 'groups') ? 'hidden' : '' }}">
            <div class="border-t py-2 text-base w-full flex">
           		@include('campaign.constituents.tabs')
			</div>
		</div>



        <div id="person_otherinfo" class="person-tab-content  {{ ($tab != 'otherinfo') ? 'hidden' : '' }}">         

			<div class="border-t p-2 text-base w-full">
				<div class="uppercase font-bold w-1/6">
					Social
				</div>
				<table class="text-base mt-4 w-full">
					<tr class="border-t">
						<td class="p-2 bg-grey-lighter w-1/5">
							Twitter
						</td>
						<td class="p-2">
							{{ $person->social_twitter }}
						</td>
					</tr>
					<tr class="border-t">
						<td class="p-2 bg-grey-lighter w-1/5">
							Facebook
						</td>
						<td class="p-2">
							{{ $person->social_facebook }}
						</td>
					</tr>
				</table>
			</div>


			<div class="border-t mt-4 p-2 text-base w-full">
				<div class="uppercase font-bold w-1/6">
					Political
				</div>

				<table class="text-base mt-4 w-full">
					<tr class="border-t">
						<td class="p-2 bg-grey-lighter w-1/5">
							State Voter ID
						</td>
						<td class="p-2">
							{{ $voterRecord->id }}
						</td>
					</tr>
					<tr class="border-t">
						<td class="p-2 bg-grey-lighter w-1/5">
							Registered
						</td>
						<td class="p-2">
							{{ $voterRecord->registered }}
						</td>
					</tr>
					<tr class="border-t">
						<td class="p-2 bg-grey-lighter w-1/5">
							Election History
						</td>
						<td>

				<table class="w-full">
					<tr>
					@foreach($voterRecord->electiondata as $election)
						@if($election->voted)
							<td class="py-2 border-r text-center">
								<i class="fas fa-check-square"></i>
							</td>
						@else
							<td class="py-2 border-r text-center bg-red-lighter">
								X
							</td>
						@endif
					@endforeach
					</tr>
					<tr class="bg-grey-lighter text-center text-xs">
					@foreach($voterRecord->electiondata as $election)
						<td class="p-2 border-r border-t w-16">
						{{ $election->key }}
						</td>
					@endforeach
					</tr>
				</table>
								
							
						</td>
					</tr>
					<tr class="border-t">
						<td class="p-2 bg-grey-lighter w-1/5">
							Party
						</td>
						<td class="p-2">
							{{ $voterRecord->party}}
						</td>
					</tr>
					<tr class="border-t">
						<td class="p-2 bg-grey-lighter w-1/5">
							Ward - Precinct
						</td>
						<td class="p-2">
							{{ $voterRecord->ward }} - {{ $voterRecord->precinct}} 
						</td>
					</tr>
					<tr class="border-t">
						<td class="p-2 bg-grey-lighter w-1/5">
							State House Dist
						</td>
						<td class="p-2">
							{{ $voterRecord->district_house}}
						</td>
					</tr>
					<tr class="border-t">
						<td class="p-2 bg-grey-lighter w-1/5">
							State Sen Dist
						</td>
						<td class="p-2">
							{{ $voterRecord->district_senate}}
						</td>
					</tr>
					<tr class="border-t">
						<td class="p-2 bg-grey-lighter w-1/5">
							Congress Dist
						</td>
						<td class="p-2">
							{{ $voterRecord->district_congress}}
						</td>
					</tr>
				</table>
			</div>





        </div>
        <div id="person_massemailings" class="person-tab-content  {{ ($tab != 'massemailings') ? 'hidden' : '' }}" style="min-height: 700px;">
            

			<div class="border-t p-2 text-base w-full">
				<div class="uppercase font-bold w-1/6">
					Mass Emailings
				</div>

				You better believe info will go here
			</div>

        </div>
    </div>

@endsection

@section('javascript')
<script type="text/javascript">

	$(document).ready(function() {

		$('.switchform').click(function() {
	      var id = $(this).attr('id');
	      var m = 'GroupPerson';
	      var c = 'data';
	      var j = $(this).attr('data-json');
		  $.get('/campaign/get_form_json/'+m+'/'+c+'/'+j+'/'+id, function(response) {
				$('#'+id).replaceWith(response);
		  });
		});

		$('.btn_newgroup').click(function() {
	      var c = $(this).data('category');
	      var f = $(this).data('form');
		  $.get('/campaign/get_form_groups/'+f+'/'+'{!! $person->id !!}'+'/'+c, function(response) {
				$('#'+f).replaceWith(response);
		  }); 
		});

		$(window).scroll(function() {
		  sessionStorage.scrollTop = $(this).scrollTop();
		});
	
	});

	$(document).ready(function() {
		$("#contact_subject").focus();
		$(function() {
		    $("#contact_notes").keypress(function (e) {
		        if(e.which == 13) {
		            e.preventDefault();
		            $('#contact_form').submit();
		        }
		    });
		});
	});

	$(document).ready(function() {
	  if (sessionStorage.scrollTop != "undefined") {
	    $(window).scrollTop(sessionStorage.scrollTop);
	  }
	});

</script>
@endsection