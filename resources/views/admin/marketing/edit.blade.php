@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')

@endsection

@section('style')
  <!-- Alpine -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

    <!-- New version Tailwind: -->
    <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet" />

      <style>
        [x-cloak] {
            display: none;
        }

        .duration-300 {
            transition-duration: 300ms;
        }

        .ease-in {
            transition-timing-function: cubic-bezier(0.4, 0, 1, 1);
        }

        .ease-out {
            transition-timing-function: cubic-bezier(0, 0, 0.2, 1);
        }

        .scale-90 {
            transform: scale(.9);
        }

        .scale-100 {
            transform: scale(1);
        }
    </style>
@endsection

@section('main')


<div class="w-full pb-2">

   
    <form method="POST" id="contact_form" action="/admin/marketing/{{ $candidate->id }}/update">
        
        @csrf

	    <div class="flex">

	        <div class="w-1/2 mr-2">
	        	@include('admin.marketing.edit-basics')
	    	</div>
	    	<div class="w-1/2 ml-8">
	        	@include('admin.marketing.edit-contacts')
	    	</div>

		</div>

		<div class="mt-4 text-right w-full">
		    <input type="submit" name="save" value="Update" class="inline mr-2 rounded-lg bg-grey-darker hover:bg-grey-dark text-white text-sm px-8 py-2 mt-1 shadow ml-2" />

		    <input type="submit" name="save_and_close" formaction="/admin/marketing/{{ $candidate->id }}/update/close" value="Save & Close" class="inline rounded-lg bg-blue hover:bg-blue-dark text-white text-sm px-8 py-2 mt-1 shadow" />
		</div>

    </form>

</div>


<br />
<br />
@endsection

@section('javascript')

 
<script type="text/javascript">
    
    $('.datepicker').datepicker();

</script>
 @endsection