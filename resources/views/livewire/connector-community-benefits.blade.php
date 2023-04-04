<div>

	<div class="py-2">

		<div class="whitespace-no-wrap inline-block rounded-lg px-2 py-1 border mb-1 mr-2 bg-white">
		    <input type="text"
		    	   class="p-1"
		    	   wire:model.debounce="search"
		    	   placeholder="Search Organizations" />
		</div>

	@if($entities->first() && $search)

		<div class="fz-10 px-8 py-4 bg-white border-2 border-black shadow-lg cursor-pointer"
			 style="position:fixed;display: block;top: 10px; left: 10px;margin: 0 0 0 0;">

			<div class="-mr-4">
				<i class="fas fa-times ml-2 text-lg text-black float-right" wire:click="$set('search', '')"></i>

				<div class="-ml-4 font-bold capitalize text-lg">
					Choose {{ $link_as }}
				</div>
			</div>

			<div class="mt-2">

			    @foreach($entities as $entity)

			    	<div class="py-2 {{ (!$loop->last) ? 'border-b' : '' }} flex">

			    		<div class="truncate" style="width:500px;">
			    			{{ $entity->name }}
			    		</div>

			    		@if($community_benefit->entities()->where('entity_id', $entity->id)->where($link_as, true)->exists())

							<button type="button"
									class="rounded-lg bg-red text-white hover:bg-grey-darkest hover:text-grey-lightest px-3 py-1 text-xs mr-2 uppercase"
									wire:click="unlink({{ $entity->id }})">
									UnLink
							</button>

						@else

							<button type="button"
									class="rounded-lg hover:bg-blue hover:text-white bg-grey-lighter text-grey-darker px-3 py-1 text-xs mr-2 uppercase"
									wire:click="link({{ $entity->id }})">
									Link
							</button>

						@endif

			    	</div>

			    @endforeach

			</div>

		</div>

	@endif
		@foreach($linked as $entity)
			<div class="mt-1 text-sm">

				<div class="truncate w-full px-2 py-1 border-b border-grey-light border-dashed mr-2 mb-1" wire:key="entity_{{ $entity->id }}">

					<i class="fas fa-times mr-2 text-red" wire:click="unlink({{ $entity->id }})"></i>

					<a href="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}">
						<i class="fas fa-hotel mr-2"></i>{{ $entity->name }}
					</a>


				</div>

			</div>
			
		@endforeach

	</div>


</div>
