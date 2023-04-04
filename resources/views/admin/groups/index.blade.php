@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')

	{!! Auth::user()->Breadcrumb('Groups', 'groups_index', 'level_1') !!}

@endsection

@section('main')




				



<div class="text-xl mb-4 border-b bg-orange-lightest p-2">
	Group Presets
</div>
   

<div class="text-center">
<input id="accountInput" type="text" class="rounded-lg border p-4 text-lg w-1/2 mb-4 font-bold" placeholder="Type Here to Filter" />
</div>

<table class="border w-full font-normal text-sm">
<tr class="border-b">
	<td class="border p-2 align-top bg-grey-lighter">
		App
	</td>
	<td class="border p-2 align-top bg-grey-lighter">
		Category
	</td>
	<td class="border p-2 align-top bg-grey-lighter">
		Group
	</td>
</tr>

<tbody id="accountTable">
@foreach($presets as $thepreset)


<tr class="border-b">
	<td class="border p-1 align-top uppercase font-normal" rowspan="{{ $cats->where('preset', $thepreset->preset)->count()+1 }}">
		{{ $thepreset->preset }}
	</td>
</tr>

		@foreach($cats->where('preset', $thepreset->preset) as $thecat)
			
		<tr>
			<td class="border p-1 align-top">
			<b class="uppercase">{{ $thecat->name}}</b>

				<div class="p-2 text-grey-darker text-sm">
					{!! str_replace(")","",str_replace("(","",str_replace('=>','=><br />',str_replace('stdClass Object','',print_r($thecat->data_template, true))))) !!}
				</div>
			</td>
		
			<td class="border p-1 align-top">
				<ul>
			@foreach($groups->where('category_id', $thecat->id)->where('preset') as $thegroup)

				<li>{{ $thegroup->name}}</li>
			@endforeach
				</ul>
			</td>
		</tr>
		@endforeach
	
@endforeach
</tbody>
</table>


@section('javascript')

<script>
$(document).ready(function(){
  $("#accountInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#accountTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

</script>

@endsection


@endsection