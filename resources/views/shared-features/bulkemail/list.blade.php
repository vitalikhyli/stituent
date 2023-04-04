@extends('office.base')
<?php if (!defined('dir')) define('dir','/office'); ?>

@section('title')
    Bulk Emailer
@endsection

@section('breadcrumb')

    {!! Auth::user()->Breadcrumb('Bulk Email', 'bulkemail_index', 'level_1') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')



<!-- Basic test -->

	<div class="text-xl font-sans w-full border-b-4 border-blue pb-2">

		<a href="{{dir}}/email">
			<button type="button" class="float-right bg-grey-darkest text-white px-4 py-2 rounded-lg text-sm ml-2 hover:bg-blue-dark text-">
				Back
			</button>
		</a>

		<i class="fas fa-users mr-2"></i> List: "{{ $list->name }}"
	</div>


	<table class="w-full text-sm cursor-pointer mb-6">
		<tr class="bg-grey-lighter border-b">
			<td class="p-1 w-8">
				#
			</td>
			<td class="p-1">
				Email
			</td>
		</tr>
		@foreach($emails as $theemail)
		<tr class="clickable border-b hover:bg-orange-lightest" data-href="">

			<td class="p-1 text-blue-dark">
				{{ $loop->iteration }}
			</td>

			<td class="p-1 text-blue-dark">
				{{ $theemail->email }}
			</td>
			

		</tr>
		@endforeach
	</table>



<br />
<br />
@endsection

@section('javascript')
<script type="text/javascript">

	$(document).ready(function() {

        var reloadTimer = setInterval(function(){

          var elements = document.getElementsByClassName('status');
          remaining = elements[0].innerHTML.length;
          remaining += 1;

          var newstring = '.'.repeat(remaining);
          if(remaining == 5){
          	newstring = '';
          }

          	for (i = 0; i < elements.length; i++) {
			  elements[i].innerHTML = newstring;
			}

        }, 1000);

	    $(".clickable").click(function() {
	        window.location = $(this).data("href");
	    });

	});
</script>
@endsection
