@extends(Auth::user()->team->app_type.'.base')

@section('title')
    Cases
@endsection


@section('breadcrumb')

	<a href="/{{ Auth::user()->team->app_type }}">Home</a>

    @if ($status == 'resolved')
		> <a href="/{{ Auth::user()->team->app_type }}/cases/list/{{ $scope }}/{{ $status }}">Cases</a>
		> <b>{{ $case_breadcrumb }}</b>

	@elseif ($status == 'open')
		> <a href="/{{ Auth::user()->team->app_type }}/cases/list/{{ $scope }}/{{ $status }}">Cases</a>
		> <b>{{ $case_breadcrumb }}</b>

	@elseif ($status == 'held')
		> <a href="/{{ Auth::user()->team->app_type }}/cases/list/{{ $scope }}/{{ $status }}">Cases</a>
		> <b>{{ $case_breadcrumb }}</b>

	@else
		> <b>Cases</b>
	@endif

@endsection 

@section('style')

	<style>

		.pagination {
			margin: 0px;
		}
		.pagination>li>a, 
		.pagination>li>span {
			border: 0;
		}

	</style>

@endsection


@section('main')

	<div class="flex border-b-4 border-blue pb-2">
		<div class="w-1/4">
			<div class="text-2xl font-sans">
				@if(!$user_id)
					Cases
				@else
					Assigned to <span class="text-blue">{{ App\User::find($user_id)->name }}</span>
				@endif
			</div>
		</div>
		<div class="w-3/4">



			<div class="float-right">

			@if(Auth::user()->permissions->developer)
				<div class="float-left mr-2">
					<a href="?livewire=true">
						<button class="rounded-lg bg-blue text-white px-2 py-1 text-xs">
							Dev-Only: New
						</button>
					</a>
				</div>
			@endif

				<?php 
					function urlLook($u, $a, $b) {
						return (!empty(strpos(url()->current(),$u))) ? $a : $b;
					}
					function urlEnds($u, $a, $b) {
						return Str::endsWith(url()->current(), $u) ? $a : $b;
					}
				?>

				<div class="flex text-sm">


					<div class="pt-1 text-sm border-r-2 pr-2 ">

						<!-- <span class="text-grey-darker">Case Type:</span> -->

						<select id="search_type" class="border">

							<option value="">
								-- Any Case Type --
							</option>

							@foreach(\App\WorkCase::StaffOrPrivateAndMine()
												  ->where('team_id', Auth::user()->team->id)
												  ->select('type')
												  ->whereNotNull('type')
												  ->groupBy('type')
												  ->orderBy('type')
												  ->pluck('type')
												  as $type)

								@if(isset($_GET['type']) && ($type == $_GET['type']))
									<option selected value="{{ $type }}" >
								@else
									<option value="{{ $type }}" >
								@endif

									{{ $type }}
								</option>

							@endforeach

						</select>
						
					</div>


					<div class="border-r-2 pr-2 pl-2 flex">
						<a class="top_link mx-1" href="/{{ Auth::user()->team->app_type }}/cases{{ (isset($_GET['type'])) ?  '?type='.$_GET['type'] : '' }}">
							<div class="flex-1 flex-initial rounded-lg whitespace-no-wrap px-2 py-1 text-grey-darker {{ urlEnds('/cases','bg-blue-darker text-white','') }} {{ urlLook('/list/0/0','bg-blue-darker text-white','') }}">
									All Cases
							</div>
						</a>
					</div>


					<div class="flex pl-2 border-r">

						<a class="top_link mx-1" href="/{{ Auth::user()->team->app_type }}/cases/list/all/{{ $status }}{{ (isset($_GET['type'])) ?  '?type='.$_GET['type'] : '' }}">
						<div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ urlLook('/all','bg-blue-darker text-white','') }}">
								Team
						</div>
						</a>

						<a class="top_link mx-1" href="/{{ Auth::user()->team->app_type }}/cases/list/mine/{{ $status }}{{ (isset($_GET['type'])) ?  '?type='.$_GET['type'] : '' }}">
						<div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ urlLook('/mine','bg-blue-darker text-white','') }}">
								Mine
						</div>
						</a>

					</div>

					<div class="flex pl-2">

						<a class="top_link mx-1" href="/{{ Auth::user()->team->app_type }}/cases/list{{ urlLook('/mine','/mine','/all') }}/open{{ (isset($_GET['type'])) ?  '?type='.$_GET['type'] : '' }}">
						<div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ urlLook('/open','bg-blue-darker text-white','') }}">
								Open
						</div>
						</a>

						<a class="top_link mx-1" href="/{{ Auth::user()->team->app_type }}/cases/list{{ urlLook('/mine','/mine','/all') }}/held{{ (isset($_GET['type'])) ?  '?type='.$_GET['type'] : '' }}">
						<div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ urlLook('/held','bg-blue-darker text-white','') }}">
								Held
						</div>
						</a>

						<a class="top_link mx-1" href="/{{ Auth::user()->team->app_type }}/cases/list{{ urlLook('/mine','/mine','/all') }}/resolved{{ (isset($_GET['type'])) ?  '?type='.$_GET['type'] : '' }}">
						<div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ urlLook('/resolved','bg-blue-darker text-white','') }}">
								Resolved
						</div>
						</a>

					</div>

				</div>

			</div>
		</div>
	</div>



	@if($cases->first())

		@if($cases instanceof \Illuminate\Pagination\LengthAwarePaginator)

			@if($cases->lastPage() > 1)

				<div class="w-full uppercase text-sm border-b py-2 bg-grey-lightest" id="paginate-div">


					<div class="w-full flex text-grey-dark">

						<div class="w-1/4 text-left px-2">
							Showing <span class="font-bold text-black">{{ $cases->firstItem() }}-{{ $cases->lastItem() }}</span> of 
							<span class="font-bold text-black">{{ $cases->total() }}</span>
						</div>

						<div class="w-1/2 text-center">
							{{ $cases->links() }}
						</div>

						<div class="w-1/4 text-right px-2">
							Page <span class="text-black font-bold">{{ $cases->currentPage() }}</span> of <span class="text-black font-bold">{{ $cases->lastPage() }}</span>
						</div>

					</div>

				</div>

			@endif

		@endif
	
	@endif



	<div class="inline-flex w-full">

		<div class="border-r pr-8 text-right w-5/6 pt-4">

			@if($cases->first())

				<form autocomplete="off">

					<input id="search" autocomplete="off" type="text" placeholder="&#xf002; @lang('Search Cases')" style="font-family:Font Awesome\ 5 Free, Arial" data-toggle="dropdown" class="w-2/3 appearance-none px-6 py-3 bg-grey-lighter border-2 border-grey text-black focus:border-2 text-lg" />

				</form>

				<div class="text-right pl-12 py-2 text-grey-darker text-xs italic">
					Use <span class="font-bold text-black">"for:"</span> to narrow down searches with a name, as in: <span class="font-bold text-black">"MassHealth for:{{ Auth::user()->first_name }}"</span>
				</div>

				<div class="text-right pl-12 pb-2 text-grey-darker text-xs italic">
					Or, use <span class="font-bold text-black">"group:"</span> to narrow down searches to people who belong to a certain group.
				</div>

			@endif

		</div>

		<div class="w-1/2 pt-4 pb-2">

			<a href="/{{ Auth::user()->team->app_type }}/cases/new">

				<button class="rounded-lg bg-blue text-sm uppercase text-white px-4 py-2 ml-8 hover:bg-blue-dark">
					Start New Case
				</button>
				
			</a>

			@if($cases->first())
				<div class="p-1 mt-2 ml-8">
					<div class="mt-1 uppercase text-sm">
					    <a href="" data-target="#case-report-modal" data-toggle="modal">
					    	<i class="far fa-file-alt mr-2 text-base"></i> Cases Report
					    </a>
					</div>
					<div class="mt-2 uppercase text-sm">
					    <a target="_blank" href="/{{ Auth::user()->team->app_type }}/cases/export/{{ (!$scope) ? 0 : $scope }}/{{ (!$status) ? 0 : $status }}" >
					    		<i class="fa fa-file-csv mr-2 text-base"></i> Export CSV
					    </a>
					</div>
		    	</div>
		    @endif

		</div>



	</div>



@if(!$cases->first())

	<div class="py-2 w-full text-grey-dark">No cases yet.</div>

@else

<div class="flex w-full border-t pt-4">
	
	<div id="list" class="mt-4 mb-4 w-4/5 pr-4">

		@include('shared-features.cases.list-cases')

	</div>

	<div class="ml-4 w-1/5">

		@if($cases_count > 0)

		<div class="text-sm uppercase mt-4 text-center font-bold mb-2 mt-4">
			By User
		</div>

		<table class="w-full text-sm">
			@foreach($cases_unpaginated->groupBy('user_id')->sortBy('id') as $usercases)
			<tr class="{{ (!$loop->last) ? 'border-b border-dashed' : 'border-b-2 border-blue' }}">

				<td class="py-1 pr-2 w-1/5 text-right">
					{{ number_format($usercases->count()) }}
				</td>

				<td class="p-1 text-left w-4/5">
					<a href="/{{ Auth::user()->team->app_type }}/cases/list/{{ $scope }}/{{ $status }}/{{ $usercases->first()->user_id }}">
						{{ \App\User::find($usercases->first()->user_id)->name }}
					</a>
				</td>

			</tr>
			@endforeach
			<tr class="font-bold">

				<td class="p-1 text-right w-1/5">
					{{ number_format($cases_count) }}
				</td>

				<td class="p-1 text-left w-4/5">
					<a href="/{{ Auth::user()->team->app_type }}/cases/list/{{ $scope }}/{{ $status }}">
						Team
					</a>
				</td>

			</tr>
		</table>

			@if($cases_unpaginated->where('resolved',false)->count() >0)

				<div class="text-sm uppercase mt-4 text-center font-bold mb-3 mt-8">
					Unresolved
				</div>


				<div class="pl-6">

						@foreach($cases_unpaginated->where('resolved',false)->sortByDesc('date')->splice(0,5) as $case)

							<div class="text-sm mb-2">
								<a href="/{{ Auth::user()->team->app_type }}/cases/{{ $case->id }}">

									<div class="whitespace-no-wrap text-black text-left font-bold text-xs">
										{{ \Carbon\Carbon::parse($case->created_at)->diffForHumans() }}
									</div>

									<div class="text-left ml-4 border-l-2 pl-2">
										{{ $case->subject }}
									</div>

								</a>
							</div>

						@endforeach

				</div>

			@endif

		@endif

<!-- 		<div class="text-lg font-sans mt-4 border-b pb-2">
			Options
		</div>

		<div class="p-1 mt-2">
	      <a href="" data-target="#case-report-modal" data-toggle="modal">
	      	<i class="far fa-file-alt mr-2"></i> Cases Report
	      </a>
	    </div>

		<div class="p-1 mt-2">
	      <a target="_blank" href="/{{ Auth::user()->team->app_type }}/cases/export/{{ (!$scope) ? 0 : $scope }}/{{ (!$status) ? 0 : $status }}"><i class="fa fa-file-csv mr-2"></i> Export CSV</a>
	    </div> -->

	    <div id="case-report-modal" class="modal fade" role="dialog">
	    	<div class="modal-dialog">

	    		<!-- Modal content-->
	    		<div class="modal-content">
	    			<form target="_blank" action="/{{ Auth::user()->team->app_type }}/cases/report" method="GET" id="report-form">

	    				<div class="modal-header">
	    					<button type="button" class="close" data-dismiss="modal">&times;</button>
	    					<h4 class="modal-title">Case Report</h4>
	    				</div>
	    				<div class="modal-body">

	    					<br>
	    					<b>Whose Cases?</b>
	    					<select name="owner" class="form-control">
								<option value="mine">My Cases</option>
								<option value="all">All {{ Auth::user()->team->name }} Cases</option>
	    					</select>

							<br>
	    					<b>What Status?</b>
	    					<select name="status" class="form-control">
								<option value="resolved">Resolved</option>
								<option value="open">Open</option>
								<option value="held">Held</option>
								<option value="">Any</option>
	    					</select>

							<br>
	    					<b>Case Type?</b>

							<select name="type" class="form-control">

								<option value="">
									-- Any Case Type --
								</option>

								@foreach(\App\WorkCase::StaffOrPrivateAndMine()
													  ->where('team_id', Auth::user()->team->id)
													  ->select('type')
													  ->whereNotNull('type')
													  ->groupBy('type')
													  ->orderBy('type')
													  ->pluck('type')
													  as $type)

									@if(isset($_GET['type']) && ($type == $_GET['type']))
										<option selected value="{{ $type }}" >
									@else
										<option value="{{ $type }}" >
									@endif

										{{ $type }}
									</option>

								@endforeach

							</select>

							<br>
	    					<b>Which Time Frame?</b>
	    					<div class="flex">
	    						<div class="w-1/3 pr-2">
	    							1. Resolved Month
									<select name="resolved_month" class="form-control">
										<option value=""></option>
										@for ($i=0; $i < 12; $i++)
											@php
												$tempdate = \Carbon\Carbon::today()->startOfmonth()->subMonth($i)
											@endphp
											<option value="{{ $tempdate->format('Y-m-d') }}">
												{{ $tempdate->format('F Y') }}
											</option>
										@endfor
			    					</select>
	    						</div>
	    						<div class="w-1/3 pr-2">
									2. Opened Month
									<select name="opened_month" class="form-control">
										<option value=""></option>
										@for ($i=0; $i < 12; $i++)
											@php
												$tempdate = \Carbon\Carbon::today()->startOfmonth()->subMonth($i)
											@endphp
											<option value="{{ $tempdate->format('Y-m-d') }}">
												{{ $tempdate->format('F Y') }}
											</option>
										@endfor
			    					</select>
	    						</div>
	    						<div class="w-1/3">
									3. Custom Dates
									<div class="flex">
										<div class="w-1/2 pr-1">
											<input class="datepicker form-control" name="custom_from_date" placeholder="From" type="text" />
										</div>
										<div class="w-1/2 pl-1">
											<input class="datepicker form-control" name="custom_to_date" placeholder="To" type="text" />
										</div>
									</div>
	    						</div>
	    					</div>
	    					
	    					<br>
	    					<b>Show Contact Notes?</b>
	    					<br />
    						<label for="show_notes" class="font-normal whitespace-no-wrap">
    							<input type="checkbox" checked name="show_notes" id="show_notes" /> Yes
    						</label>

	    				</div>
	    				<div class="modal-footer">
	    					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	    					<button type="submit" id="run-report" class="opacity-25 btn btn-primary">Run Report</button>
	    				</div>
	    			</form>
	    		</div>

	    	</div>
	    </div>

	    

	</div>

</div>

	@endif




@endsection

@section('javascript')
<script type="text/javascript">

	var alert_test_once;

	function getSearchData(v) {

		var v_type = $('#search_type').val();

		$('.top_link').each(function(){
			url = $(this).attr('href');
			if (url.indexOf('?') > 0) {
				url = url.substring(0, url.indexOf('?'));
			}
			$(this).attr('href', url + '?type=' + v_type);
		});
		// url = $('#link_allcases').attr('href');
		// url = url.substring(0, url.indexOf('?'));
		// $('#link_allcases').attr('href', url + '?type=' + v_type);
		
		// alert(s);

		// += '?GET=' + v_type;
		// alert(url);

		v = v + ' type:' + v_type;

		if (v.trim() == '') {
			$v = '';
			$('#paginate-div').removeClass('hidden');
		} else {
			$('#paginate-div').addClass('hidden');
		}

		var scope = '{{ ($scope) ? $scope : 0 }}';
		if (scope) scope += '/';

		var status = '{{ ($status) ? $status : 0 }}';
		if (status) status += '/';

		var user_id = '{{ ($user_id) ? $user_id : 0 }}';
		if (user_id) user_id += '/';

		$.get('/{{ Auth::user()->team->app_type }}/cases/search/'+scope+status+user_id+v, function(response) {
			$('#list').html(response);
		});
	}


	$(document).ready(function() {

		$('#case-report-modal').on('show.bs.modal', function() {
		    $('.form-control').change();
		});

		$('.form-control').change( function () {
			app_type = '{{ Auth::user()->team->app_type }}';
			url = '/' + app_type + '/cases/report/count?' + $('#report-form').serialize();
			$.get(url, function(response) {
				response = response.trim();
				if (response) {
					if (response > 0) {
						$('#run-report').removeClass('opacity-25');
						$('#run-report').html('Run Report <b>on ' + response + '</b> Cases');
						

					} else {
						$('#run-report').addClass('opacity-25');
						$('#run-report').html('No Cases Found');
						
					}
				}
			});
		});

		
		$("#search").focus();

		$("#search").keyup(function(){
			getSearchData(this.value);
		});

		$("#search_type").change(function(){
			getSearchData($("#search").val());
		});

	    $(".clickable").click(function() {
	        window.location = $(this).data("href");
	    });

	});

</script>
@endsection
