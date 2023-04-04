@extends('campaign.base')

@section('title')
    {{ $list->name }}
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> 
    > <a href="/campaign/lists">Campaign Lists</a>
    > <a href="/campaign/lists/{{ $list->id }}">"{{ $list->name }}"</a>
    > &nbsp;<b>Assign Volunteers</b>
@endsection

@section('style')
	@livewireStyles
@endsection

@section('main')

	<div class="text-3xl font-bold border-b-4 pb-2 flex">

		<div class="truncate text-2xl flex-grow">

			<span class="text-blue">
				<i class="fas fa-user mr-1"></i> Assign to Volunteers:
			</span>

			{{ $list->name }}

		</div>

		<div class="text-lg mt-2 text-blue">
			{{ number_format($list->count()) }} Voters
		</div>


	</div>

	<div class="py-2">

		@livewire('list-assign', ['list' => $list])

	</div>

@endsection


@section('javascript')

 	@livewireScripts

<script type="text/javascript">
    
    function copyToClipboard(the_id) {
      var copyText = document.getElementById(the_id);
      copyText.select(); 
      copyText.setSelectionRange(0, 99999); /*For mobile devices*/
      document.execCommand("copy");
    }

</script>
 				
@endsection