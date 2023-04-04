<?php if (!defined('dir')) define('dir','/office'); ?>

<div id="list">

@if($entities->count() <= 0)


	@if(isset($search_value))
		<div class="text-center">
			<div class="p-2 text-grey-dark">
				Search found nothing
			</div>

			<a href="{{dir}}/entities/new/{{ $search_value }}">
				<button class="hover:bg-blue-dark bg-blue rounded-lg px-4 py-2 text-white shadow"><i class="fas fa-plus-circle"></i> Create New Entity: {{ $search_value }}
			</button></a>

		</div>

	@else

		<div class="text-grey-dark">No entities yet. Start typing a search to create one.</div>

	@endif

@else

	
	<table class="text-sm w-full">
		<tr class="border-b bg-grey-lighter uppercase">

			<td class="p-2 w-1/3">
				Name
			</td>

			<td class="p-2">
				Address
			</td>

		</tr>

		@if(isset($search_value) && ($search_value != null))
			<tr class="border-b-4 border-blue">
				<td class="p-2 text-right" colspan="4">

					<a href="{{dir}}/entities/new/{{ $search_value }}">
						<button class="bg-blue rounded-lg px-4 py-2 text-white shadow text-base"><i class="fas fa-plus-circle"></i> New Entity "{{ $search_value }}"
					</button></a>

				</td>
			</tr>
		@endif

	@foreach($entities as $entity)

		<tr class="border-b hover:bg-blue-lightest cursor-pointer">

			<td class="p-2 w-10 whitespace-no-wrap" valign="top">
					<a href="{{dir}}/entities/{{ $entity->id }}">
					<span class="hover:bg-blue hover:text-white bg-grey-lighter rounded-full m-1 px-2 py-1 text-sm">

						<i class="fas fa-hotel mr-2"></i> 

						{{ $entity->name }}

					</span>
					</a>
			</td>
			<td class="p-2 w-10 whitespace-no-wrap" valign="top">
				{{ $entity->full_address }}
			</td>
		</tr>

	@endforeach
	</table>

@endif
</div>