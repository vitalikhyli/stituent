@extends(Auth::user()->team->app_type.'.no-auth')

@section('title')
    Inactive Notice
@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a>

@endsection

@section('style')

	

@endsection

@section('main')

<div class="flex border-b-4 pb-2 border-blue">
	<div class="text-2xl font-sans w-full">
		<span class="font-bold text-red uppercase">Notice:</span> 
		Your Account, Team, or User are currently <b>Inactive</b>
	</div>

</div>

<div class="w-full text-3xl text-center">

	<div class="flex h-16">
		<!-- <div class="mx-auto border-l-4"></div>		 -->
	</div>
	<span class="font-bold">
		<!-- Account -->
	</span>
	
	
	<div class="flex items-center w-3/4 mx-auto">
		<div class="w-2/5 text-right text-2xl">
			<b>Account:</b> {{ Auth::user()->team->account->name }}
		</div>
		<div class="w-1/5">
			@if(Auth::user()->team->account->active)
				<div class="text-green border-4 rounded-full w-16 h-16 flex items-center mx-auto">
					<i class="fa fa-check w-full"></i>
				</div>
			@else
				<div class="text-red border-4 rounded-full w-16 h-16 flex items-center mx-auto">
					<i class="fa fa-times w-full"></i>
				</div>
			@endif
		</div>
		<div class="w-2/5 text-left	">
			@if(Auth::user()->team->account->active)
				<span class="text-green">
					Active
				</span>
			@else
				<span class="text-red font-bold">
					Inactive
				</span>
			@endif
		</div>
	</div>

	<div class="flex h-12">
		<div class="mx-auto border-l-4"></div>		
	</div>

	<span class="font-bold">
		<!-- Team -->
	</span>

	

	
	

	<div class="flex items-center w-3/4 mx-auto">
		<div class="w-2/5 text-right text-2xl">
			<b>Team:</b> {{ Auth::user()->team->name }}
		</div>
		<div class="w-1/5">
			@if(Auth::user()->team->active)
				<div class="text-green border-4 rounded-full w-16 h-16 flex items-center mx-auto">
					<i class="fa fa-check w-full"></i>
				</div>
			@else
				<div class="text-red border-4 rounded-full w-16 h-16 flex items-center mx-auto">
					<i class="fa fa-times w-full"></i>
				</div>
			@endif
		</div>
		<div class="w-2/5 text-left	">
			@if(Auth::user()->team->active)
				<span class="text-green">
					Active
				</span>
			@else
				<span class="text-red font-bold">
					Inactive
				</span>
			@endif
		</div>
	</div>

	<div class="flex h-12">
		<div class="mx-auto border-l-4"></div>		
	</div>

	<span class="font-bold">
		<!-- User -->
	</span>

	<div class="flex items-center w-3/4 mx-auto">
		<div class="w-2/5 text-right text-2xl">
			<b>User:</b> {{ Auth::user()->name }}
		</div>
		<div class="w-1/5">
			@if(Auth::user()->active)
				<div class="text-green border-4 rounded-full w-16 h-16 flex items-center mx-auto">
					<i class="fa fa-check w-full"></i>
				</div>
			@else
				<div class="text-red border-4 rounded-full w-16 h-16 flex items-center mx-auto">
					<i class="fa fa-times w-full"></i>
				</div>
			@endif
		</div>
		<div class="w-2/5 text-left	">
			@if(Auth::user()->active)
				<span class="text-green">
					Active
				</span>
			@else
				<span class="text-red font-bold">
					Inactive
				</span>
			@endif
		</div>
	</div>
	<div class="flex h-16">
		<div class="mx-auto border-l-4"></div>		
	</div>

	<div class="w-1/2 border-t-4 p-4 mx-auto text-lg">

		@if(!Auth::user()->team->account->active)
			Your <b>Account</b> may be <b class="text-red">Inactive</b> because you have ended your subscription or have overdue payments.<br><br>
		@endif

		@if(!Auth::user()->team->active)
			Your <b>Team</b> may be <b class="text-red">Inactive</b> because you have ended a campaign or left your elected position.<br><br>
		@endif

		@if(!Auth::user()->active)
			Your <b>User</b> may be <b class="text-red">Inactive</b> because it has been deactivated by an Admin.<br><br>
		@endif

		
		<i class="text-xl">Please call Lazarus at <b>617.888.0545</b> to reactivate your Account, if you are at this screen incorrectly, or if you have any questions.</i>

	</div>


</div>

@endsection

@section('javascript')

@endsection
