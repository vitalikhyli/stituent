<div>

	@php(define('COMMON', 'livewire._paste-includes.'))

	@if(!$model)
	
		<div class="py-1">
			@include('livewire.admin-upload-to-master.paste-model-selector')
		</div>

	@else

		@include(COMMON.'errors')
		@include(COMMON.'top')
	    @include(COMMON.'textarea-chunk')
		
		<div class="py-1">
			Component-specific options go here?
		</div>
		
		@include(COMMON.'filters')
		@include(COMMON.'go-halt-poll')
		@include(COMMON.'report')

	@endif

</div>
