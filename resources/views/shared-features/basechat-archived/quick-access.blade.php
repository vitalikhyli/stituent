<div class="text-grey-dark uppercase border-b border-grey-darkest pb-2 mt-8">
	Unread Messages
</div>

<div class="pl-4">

	@if (count($chatusermemory->unread_messages) > 0)

		@foreach ($chatusermemory->unread_messages as $room_id => $messages)
			@php
				$room = \App\Models\BaseChat\ChatRoom::find($room_id)
			@endphp

			<div room-id="{{ $room->id }}" class="room rounded-full cursor-pointer pl-3 pr-2 py-1 hover:bg-grey-darkest hover:text-grey-light mt-1">
				<div class="float-right">{{ $room->member_count }}</div>
				#{{ $room->slug }}
				<span class="bg-red px-1 py-0 text-white rounded-full text-xs mt-2 -mr-2">
		            {{ count($messages) }}
		        </span>
			</div>

			<!-- <div class="rounded-full cursor-pointer px-4 py-1 hover:bg-grey-darkest hover:text-grey-light mt-1">
				<div class="float-right">{{ $room->member_count }}</div>
				#{{ $room->slug }}
				<span class="bg-red px-1 py-0 text-white rounded-full text-xs mt-2 -mr-2">
		            {{ count($messages) }}
		        </span>
				
			</div> -->
		@endforeach

	@else
		<div class="pt-2">
			<i>No Unread Messages</i>
		</div>
	@endif
</div>

<!-- <div class="text-grey-dark uppercase border-b border-grey-darkest pb-2 mt-4">
	Recent Rooms
</div>

<div class="pl-4">

	@if (count($chatusermemory->recent_rooms) > 0)
		<div class="rounded-full cursor-pointer bg-grey-light text-blue-darker px-4 py-1 mt-1">
			<div class="float-right">6</div>
			#northeastern-university
		</div>

		<div class="rounded-full cursor-pointer px-4 py-1 hover:bg-grey-darkest hover:text-grey-light mt-1">
			<div class="float-right">8</div>
			@henry-jenkins
		</div>
		<div class="rounded-full cursor-pointer px-4 py-1 hover:bg-grey-darkest hover:text-grey-light mt-1">
			<div class="float-right">2</div>
			#rep-jones
			<span class="bg-red px-1 py-0 text-white rounded-full text-xs mt-2 -mr-2">
	            1
	        </span>
			
		</div>
		<div class="rounded-full cursor-pointer px-4 py-1 hover:bg-grey-darkest hover:text-grey-light mt-1">
			<div class="float-right">4</div>
			#marlboro-cases
		</div>
		<div class="rounded-full cursor-pointer px-4 py-1 hover:bg-grey-darkest hover:text-grey-light mt-1">
			<div class="float-right">32</div>
			#western-mass
			<span class="bg-red px-1 py-0 text-white rounded-full text-xs mt-2 -mr-2">
	            2
	        </span>
		</div>
	@else

		<div class="pt-2">
			<i>No Recent Rooms</i>
		</div>
	@endif
</div> -->