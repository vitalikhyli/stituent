<div id="call-log-content" class="text-sm text-grey-darker flex flex-wrap mt-8 border-t-4 border-blue">

@if(!$logs->first())

	<div class="text-base py-2 text-grey-dark">
		No results found
	</div>

@else

	<div class="table">
	@foreach ($logs as $thecall)
		<div class="{{ (!$loop->last) ? 'border-b' : 'mb-6' }} table-row w-full flex text-sm group p-1 hover:bg-blue-lightest">


			<div class="table-cell w-24 text-grey-darker text-right pr-3 font-bold whitespace-no-wrap">

				{{ $thecall->created_at->format('Y-m-d') }}

			</div>

			<div class="table-cell w-48 text-grey-darkest text-right pr-3 whitespace-no-wrap">
				<span class="px-2">

				@if (isset($thecall->user))
					{{ $thecall->user->name }}
				@endif
				</span>

			</div>

			<div class="table-cell w-1/2">
					
				<span class="font-bold text-black text-sm leading-none">

				@if($thecall->type)
					<span class="text-blue">
						{{ ucwords($thecall->type) }} 
						@if($thecall->subject)
						 > 
						@endif
					</span>
				@endif

				@if ($thecall->subject)
					{!! preg_replace("/".preg_quote($v)."/i", '<b class="bg-orange-lighter">$0</b>', $thecall->subject) !!}
					<br />
				@endif
				
				@if($thecall->followup)
					<span class="cursor-pointer font-normal text-sm {{ ($thecall->followup_done) ? 'text-grey line-through ' : 'text-red' }}">
						<i class="fas fa-hand-point-right ml-1"></i> Follow up
						@if($thecall->followup_on)
							on {{ $thecall->followup_on }}
						@endif
					</span>
				@endif
				@if ($thecall->private)
					<span class="cursor-pointer">
						<i class="fa fa-lock text-grey-dark ml-1"></i>
					</span>
				@endif
				</span>

				@if ($thecall->notes)

					{!! nl2br(preg_replace("/".preg_quote($v)."/i", '<b class="bg-orange-lighter">$0</b>', $thecall->notes)) !!}

				@endif

			</div>


			<div class="table-cell text-grey text-left">

				@if($thecall->entities->count() >0)
					@foreach($thecall->entities as $theentity)
						<a href="{{ $theentity->team->app_type }}/entities/{{ $theentity->id }}">
						<button class="hover:bg-blue hover:text-white bg-grey-lighter border rounded-full m-1 px-2 py-1 text-sm text-black truncate">
							<i class="fas fa-building mr-1"></i> {{ $theentity->name }}
						</button>
						</a>
					@endforeach
				@endif


				@if($thecall->people->count() >0)
					@foreach($thecall->people as $theperson)
						<a href="/{{ $theperson->team->app_type }}/constituents/{{ $theperson->id }}">
						<button class="hover:bg-blue hover:text-white bg-grey-lighter border rounded-full m-1 px-2 py-1 text-sm text-black truncate">
							<i class="fas fa-user mr-1"></i>

							{!! preg_replace("/".preg_quote($v)."/i", '<b class="bg-orange-lighter">$0</b>', $theperson->full_name) !!}

						</button>
						</a>
					@endforeach
				@else

					<span class="text-grey-dark">Not connected to a person</span>
				@endif

				@if(json_decode($thecall->suggested_people))
					@foreach(json_decode($thecall->suggested_people) as $suggested)

						<?php
							if(IDisPerson($suggested->id)) {
								$suggested_person = \App\Person::where('id',$suggested->id)->first();
							}
							if(IDisVoter($suggested->id)) {
								$suggested_person = \App\Voter::where('id',$suggested->id)->first();
							}
						?>

						<button type="button" class="connect_suggested hover:bg-blue hover:text-white bg-red-lightest text-black border rounded-full m-1 px-2 py-1 text-sm text-black" data-href="/{{ Auth::user()->team->app_type }}/contacts/{{ $thecall->id }}/connect/{{ $suggested_person->id }}" data-toggle="tooltip" data-placement="top" title="Connect This Person to This">
							<i class="fas fa-question-circle mr-2 "></i>{{ $suggested_person->full_name }}
						</button>

					@endforeach
				@endif
				
				@if(json_decode($thecall->suggested_entities))
					@foreach(json_decode($thecall->suggested_entities) as $suggested)

						<?php
							$suggested_entity = \App\Entity::find($suggested->id);
						?>

						<button type="button" class="connect_suggested hover:bg-blue hover:text-white bg-red-lightest text-black border rounded-full m-1 px-2 py-1 text-sm text-black" data-href="{{ Auth::user()->team->app_type }}/contacts/{{ $thecall->id }}/connect_entity/{{ $suggested_entity->id }}" data-toggle="tooltip" data-placement="top" title="Connect This Entity to This">
							<i class="fas fa-question-circle mr-2 "></i>{{ $suggested_entity->name }}
						</button>

					@endforeach
				@endif
			</div>


			<div class="table-cell flex whitespace-no-wrap">

				<div class="py-2 text-right w-full">
						<a data-toggle="tooltip" data-placement="top" title="Connections" href="/call-log/{{ $thecall->id }}/connect" class="group-hover:opacity-100 opacity-0 one_contact remote-modal border hover:bg-blue-dark hover:border-blue-dark rounded-full bg-white hover:text-white px-4 py-1 text-sm" target="#call-log-connect-modal"><i class="fas fa-link"></i></a>

						<a data-toggle="tooltip" data-placement="top" title="Edit Note" href="/call-log/{{ $thecall->id }}/edit" class="group-hover:opacity-100 opacity-0 one_contact remote-modal border hover:bg-blue-dark hover:border-blue-dark rounded-full bg-white hover:text-white px-4 py-1 text-sm" target="#call-log-edit-modal"><i class="fas fa-edit"></i></a>
				</div>

				<div class="ml-1 py-2 text-right w-full text-xs">
					@if(!$thecall->case)
						<a data-toggle="tooltip" data-placement="top" title="Convert to Case" href="/{{ Auth::user()->team->app_type }}/contacts/{{ $thecall->id }}/convert_to_case" class="border hover:bg-orange-dark hover:border-text-white rounded-full bg-white hover:text-white px-3 py-1"><i class="fas fa-folder-open"></i>
							Case
						</a>
					@elseif($thecall->case->resolved)
						<a data-toggle="tooltip" data-placement="top" title="Go to Case" href="/{{ Auth::user()->team->app_type }}/cases/{{ $thecall->case_id }}" class="hover:bg-grey-darker hover:text-white rounded-full bg-grey-light text-grey-darker px-3 py-1 whitespace-no-wrap"><i class="fas fa-folder-open mr-1"></i>
							Resolved
						</a>
					@else
						<a data-toggle="tooltip" data-placement="top" title="Go to Case" href="/{{ Auth::user()->team->app_type }}/cases/{{ $thecall->case_id }}" class="hover:bg-blue-darkest hover:text-white rounded-full bg-blue text-white px-3 py-1 whitespace-no-wrap"><i class="fas fa-folder-open mr-1"></i>
							Open
						</a>
					@endif
				</div>	


			</div>


		</div>
	@endforeach

@endif

</div>
