@isset($current_room)

<div id="basechat-room-content" class="px-4" style="min-height: 600px;">

	<div class="px-4 relative w-full mt-1 border-grey-darkest text-grey text-sm transition">

		<div class="w-full pb-6">

			<div class="text-right border-b border-grey-darkest font-bold text-orange p-2">
				Today - {{ date('l, F jS, Y', time()) }}
			</div>

			<table class="w-full">

				@php
					$lastdate = \Carbon\Carbon::today();
				@endphp
				@foreach ($current_room->messages as $message) 

					@if ($lastdate->format('Y-m-d') != $message->created_at->format('Y-m-d'))

						<tr>
							<td colspan="100" class="w-full">
								<div class="w-full text-right border-b border-grey-darkest font-bold text-orange p-2">
									{{ $message->created_at->format('l, F jS, Y') }}
								</div>
							</td>
						</tr>

					@endif
					<tr>
						<td class="pr-4 py-1 align-top">
							<span class="text-grey-dark text-xs ml-1">
								{{ $message->created_at->format('g:ia') }}
							</span>
						</td>
						<td class="pr-4 py-1 align-top whitespace-no-wrap">
							<div class="font-bold text-grey-dark">
								{{ '@'.$message->user->name }} 
							</div>
						</td>
						<td class="py-1 align-top text-white w-2/3">
						    {{ $message->message }}
						</td>
					</tr>
					@php
						$lastdate = $message->created_at;
					@endphp

				@endforeach
			</table>

			<!-- <div class="py-2">
				<div class="font-bold text-white">
					Lazarus Morrison <span class="text-grey-darkest text-xs ml-1">2:35pm</span>
				</div>
				<div class="">
				    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum iure consequuntur obcaecati saepe, doloribus repellat commodi. Ipsam excepturi distinctio eum vel, ad natus vero, iure iste saepe voluptate neque.
				</div>
			</div>

			<div class="py-2">
				<div class="font-bold text-white">
					Jane Doe  <span class="text-grey-darkest text-xs ml-1">2:35pm</span>
				</div>
				<div class="">
				    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum iure consequuntur obcaecati saepe, doloribus repellat commodi. Ipsam excepturi distinctio eum vel, ad natus vero, iure iste saepe voluptate neque.
				</div>
			</div>

			<div class="py-2">
				<div class="font-bold text-white">
					Jane Doe  <span class="text-grey-darkest text-xs ml-1">2:35pm</span>
				</div>
				<div class="">
				    I got a good idea then
				</div>
			</div>

			<div class="text-right border-b border-grey-darkest font-bold text-orange p-2">
				Wednesday, April 17th, 2019
			</div>

			<div class="py-2">
				<div class="font-bold text-white">
					Lazarus Morrison <span class="text-grey-darkest text-xs ml-1">2:35pm</span>
				</div>
				<div class="">
				    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum iure consequuntur obcaecati saepe, doloribus repellat commodi. Ipsam excepturi distinctio eum vel, ad natus vero, iure iste saepe voluptate neque.
				</div>
			</div>

			<div class="py-2">
				<div class="font-bold text-white">
					Jane Doe  <span class="text-grey-darkest text-xs ml-1">2:35pm</span>
				</div>
				<div class="">
				    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum iure consequuntur obcaecati saepe, doloribus repellat commodi. Ipsam excepturi distinctio eum vel, ad natus vero, iure iste saepe voluptate neque.
				</div>
			</div>

			<div class="py-2">
				<div class="font-bold text-white">
					Jane Doe  <span class="text-grey-darkest text-xs ml-1">2:35pm</span>
				</div>
				<div class="">
				    I got a good idea then
				</div>
			</div>

			<div class="py-2">
				<div class="font-bold text-white">
					Lazarus Morrison <span class="text-grey-darkest text-xs ml-1">2:35pm</span>
				</div>
				<div class="">
				    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum iure consequuntur obcaecati saepe, doloribus repellat commodi. Ipsam excepturi distinctio eum vel, ad natus vero, iure iste saepe voluptate neque.
				</div>
			</div>

			<div class="text-right border-b border-b border-grey-darkest font-bold text-orange p-2">
				Tuesday, April 16th, 2019
			</div>

			<div class="py-2">
				<div class="font-bold text-white">
					Jane Doe  <span class="text-grey-darkest text-xs ml-1">2:35pm</span>
				</div>
				<div class="">
				    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum iure consequuntur obcaecati saepe, doloribus repellat commodi. Ipsam excepturi distinctio eum vel, ad natus vero, iure iste saepe voluptate neque.
				</div>
			</div>

			<div class="py-2">
				<div class="font-bold text-white">
					Jane Doe  <span class="text-grey-darkest text-xs ml-1">2:35pm</span>
				</div>
				<div class="">
				    I got a good idea then
				</div>
			</div>
			<div class="py-2">
				<div class="font-bold text-white">
					Lazarus Morrison <span class="text-grey-darkest text-xs ml-1">2:35pm</span>
				</div>
				<div class="">
				    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum iure consequuntur obcaecati saepe, doloribus repellat commodi. Ipsam excepturi distinctio eum vel, ad natus vero, iure iste saepe voluptate neque.
				</div>
			</div>

			<div class="py-2">
				<div class="font-bold text-white">
					Jane Doe  <span class="text-grey-darkest text-xs ml-1">2:35pm</span>
				</div>
				<div class="">
				    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum iure consequuntur obcaecati saepe, doloribus repellat commodi. Ipsam excepturi distinctio eum vel, ad natus vero, iure iste saepe voluptate neque.
				</div>
			</div>

			<div class="py-2">
				<div class="font-bold text-white">
					Jane Doe  <span class="text-grey-darkest text-xs ml-1">2:35pm</span>
				</div>
				<div class="">
				    I got a good idea then
				</div>
			</div> -->
		</div>
	     
	</div>
</div>
@endisset