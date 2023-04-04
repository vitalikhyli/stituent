<div>

	@php(define('COMMON', 'livewire._paste-includes.'))

	@if(!$model)

		@include(COMMON.'model-selector')
	
	@else

		@include(COMMON.'errors')
		@include(COMMON.'top')
	    @include(COMMON.'textarea-chunk')
		
		<!-- Component-Specific Include -->
		@include('livewire.user-upload-to-master.paste-extra-options')
		
		@include(COMMON.'filters')
		@include(COMMON.'go-halt-poll')
		@include(COMMON.'report')

	@endif

</div>
