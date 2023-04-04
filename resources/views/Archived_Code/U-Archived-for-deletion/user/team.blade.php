@extends('u.base')
<?php if (!defined('dir')) define('dir','/u'); ?>

@section('title')
    Team & Settings
@endsection

@section('breadcrumb')

    {!! Auth::user()->Breadcrumb('Team', 'team_index', 'level_1') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')


	<div class="flex border-b-4 border-blue mb-2 pb-2">
		<div class="w-full">

			@if(Auth::user()->permissions->admin)
			<a href="{{dir}}/users/new">
				<button type="button" class="float-right bg-blue text-white px-4 py-2 rounded-lg text-base ml-2 hover:bg-blue-dark">
					Create New User
				</button>
			</a>
			@endif

			<div class="text-2xl font-sans">
				{{ $team->name }}
			</div>
		</div>
	</div>

@if($team->account->teams->count() > 1)
	<div class="text-blue mb-4">
		{{ $team->account->name }}
	</div>
@endif

<table class="w-full border-t">
	<tr class="border-b-2 border-black bg-grey-lighter text-sm">
		<td class="p-2" colspan="2">
			User
		</td>
		<td class="p-2 text-grey-dark">
			Email
		</td>
		<td class="p-2">
			Title
		</td>
		<td class="p-2 text-xs">
			Dev
		</td>
		<td class="p-2 text-xs">
			Admin
		</td>
		<td class="p-2 text-xs">
			Chat
		</td>
		<td class="p-2 text-xs">
			Reports
		</td>
		<td class="p-2 text-xs">
			Metrics
		</td>
		<td class="p-2 text-xs">
			People
		</td>
	</tr>
	@foreach($users as $theuser)
	<tr class="clickable border-b cursor-pointer hover:bg-orange-lightest" data-href="{{dir}}/users/{{ $theuser->id }}/edit">
		<td class="">
			<button type="button" class="bg-blue text-white px-2 py-1 rounded-lg text-xs hover:bg-blue-dark">
				Edit
			</button>
		</td>
		<td class="p-2">
			<i class="fas fa-user-cog mr-2"></i>{{ $theuser->name }}
		</td>
		<td class="p-2 text-grey-dark">
			{{ $theuser->email }}
		</td>
		<td class="p-2">
			{{ $theuser->permissions->title }}
		</td>
		<td class="p-2 text-green w-12">
			{!! ($theuser->permissions->developer) ? '<i class="fas fa-check-circle"></i>' : '' !!} 
		</td>
		<td class="p-2 text-green w-12">
			{!! ($theuser->permissions->admin) ? '<i class="fas fa-check-circle"></i>' : '' !!}
		</td>
		<td class="p-2 text-green w-12">
			{!! ($theuser->permissions->chat) ? '<i class="fas fa-check-circle"></i>' : '' !!}
		</td>
		<td class="p-2 text-green w-12">
			{!! ($theuser->permissions->reports) ? '<i class="fas fa-check-circle"></i>' : '' !!}
		</td>
		<td class="p-2 text-green w-12">
			{!! ($theuser->permissions->metrics) ? '<i class="fas fa-check-circle"></i>' : '' !!}
		</td>
		<td class="p-2 text-green w-12">
			{!! ($theuser->permissions->constituents) ? '<i class="fas fa-check-circle"></i>' : '' !!}
		</td>
	</tr>
	@endforeach
</table>





@if(Auth::user()->permissions->admin)
	<div class="flex border-b-4 border-blue mb-4 mt-8 pb-2">
		<div class="w-1/2">
			<div class="text-2xl font-sans">
				Settings
			</div>
		</div>
	</div>

	<table class="w-full">
		<tr class="">
			<td class="p-2 bg-grey-lightest border-r w-1/5">
				Dashboard Logo
			</td>

			<td class="p-2">
				@if(!Auth::user()->team->logo_img)
					No logo on file
				@else
				
					@if(strpos(Auth::user()->team->logo_img, "://") !== false)

						<img class="" style="max-height:150px;" src="{{ Auth::user()->team->logo_img }}" />

					@elseif(strpos(Auth::user()->team->logo_img, "/images/logos") !== false)

						<img class="" style="max-height:150px;" src="http://{{ Request::getHost() }}{{ Auth::user()->team->logo_img }}" />
					
					@else

						<img style="max-height:150px;" src="{{dir}}/show_logo/{{ Auth::user()->team->id }}" />

					@endif
					
				@endif
			</td>
			<td class="p-2">

	<div class="text-center">
				<a class="px-2 py-1 rounded-full" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
				<button class="bg-blue hover:bg-blue-dark rounded-full text-white mt-2 px-4 py-1">
					@if(!Auth::user()->team->logo_img)
						Add Logo
					@else
						Change Logo
					@endif
				</button>
				</a>

				    <div class="collapse mt-4 p-4 rounded-lg bg-grey-lighter" id="collapseExample">
						<form disabled id="file_upload_form" action="{{dir}}/upload_logo" method="post" enctype="multipart/form-data">
							
							@csrf

						    <div class="m-2">
						    	

						    	<input type="file" name="fileToUpload" id="fileToUpload" class="opacity-0 z-99 float-right absolute">

						    	<label for="fileToUpload">
						    		<button type="button" class="bg-orange-dark rounded-full text-white px-4 py-2">
						    			Choose File
						    		</button>
						    	</label>

						    </div>

						    <button name="submit" class="bg-blue rounded-full text-white px-4 py-2">
						    Upload <span id="file_selected" class="font-bold"></span><i class="fas fa-file-upload ml-4"></i>
						    </button>
						</form>
					</div>

			</div>


			</td>
		</tr>
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

	    $("input[type=file]").change(function(e){
            var fileName = e.target.files[0].name;
	    	$("#file_selected").text(fileName); 
	    });

		$( "#file_upload_form" ).submit(function( event ) {
			var fileName = $('#fileToUpload').val();
			if (fileName == '') { event.preventDefault(); }
		});

	});
</script>
@endsection
