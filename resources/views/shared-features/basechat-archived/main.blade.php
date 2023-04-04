

<div id="basechat-main">
	<div style="display:none;">
		<div id="basechat-rooms-hidden">
			@include('shared-features.basechat.rooms')
		</div>
	</div>
	<div class="text-grey-lightest" style="padding-left: -150px;">
		<div class="flex">
			<div class="w-2/3 p-6 bg-black-light" style="min-height: 800px;">

				<div id="basechat-room">
					@include('shared-features.basechat.room-header')
					@include('shared-features.basechat.room')
				</div>

			</div>
			<div class="w-1/3 p-6 text-sm text-grey-dark ">
				
				<div class="font-bold text-white">
					<span class="text-3xl">Conversations</span>
					
				</div>

				<div class="text-grey-dark text-sm">
					Use Conversations to discuss your cases, communicate with your team, organize events, and take office notes.
				</div>
				
				<!-- <div class="rounded-full border-2 border-grey-dark hover:bg-grey-darkest hover:text-white inline-block cursor-pointer px-4 py-2 mt-2">
					Settings
				</div> -->

				<div id="chat-quick-access">
				
					@include('shared-features.basechat.quick-access')

				</div>
				

			</div>
		</div>

	</div>
</div>







