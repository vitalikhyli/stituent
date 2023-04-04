<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

        <script src="https://cdn.tailwindcss.com"></script>

        <title>Community Fluency :: {{ $webform->name }}</title>

    </head>
    <body>
    	@if ($message)
    		<div class="w-full text-center text-3xl text-blue-500 pt-16">
    			{{ $message }}
    		</div>

    		<div class="w-full text-center mt-8">
	    		<a href="/web-forms/{{ $webform->unique_id }}" class="bg-blue-400 rounded-full text-white hover:bg-blue-500 w-full text-center py-2 px-4">
			        Add Another
			    </a>
			</div>
    	@else
	        <form action="/web-forms/{{ $webform->unique_id }}" method="POST">
	        	@csrf
	        	<div class="w-full">
		        	<input class="w-full border text-lg p-2 text-gray-500" type="text" name="name" placeholder="Name (required)" required />
		        	<input class="w-full border text-lg mt-2 p-2 text-gray-500" type="text" name="email" placeholder="Email" />
		        	<input class="w-full border text-lg mt-2 p-2 text-gray-500" type="text" name="location" placeholder="Street, Town" />
		        	<textarea rows="3" class="w-full mt-2 border text-lg p-2 text-gray-500" name="note" placeholder="Note"></textarea>

		        	<div class="flex flex-wrap uppercase text-sm text-gray-500">
				        @foreach ($webform->options['volunteers'] as $vo)
				        	<div class="w-1/2 hover:text-gray-900 transition pt-1">
				        		
					        		<input id="{{ $vo }}" name="volunteer[]" value="{{ $vo }}" type="checkbox" /> 
					        	<label class="cursor-pointer" for="{{ $vo }}">
					        		{{ str_replace(['volunteer', '_'], ["", ' '],$vo) }}
					        	</label>
				        	</div>
				        @endforeach
				    </div>

		        	<button class="bg-blue-400 rounded-full text-white hover:bg-blue-500 w-full text-center py-2 mt-2" type="submit">
		        		@if ($webform->button)
		        			{{ $webform->button }}
		        		@else
		        			Submit
		        		@endif
		        	</button>
		        </div>
		        
	        </form>
        @endif
    </body>
</html>
