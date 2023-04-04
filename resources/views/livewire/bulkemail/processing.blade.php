<div>
    <div wire:poll
    	 class="text-blue">

    	@if($processing > 0)

    		<i class="fas fa-sync fa-spin"></i> Emails processing: {{ $processing }}

    	@endif

    </div>
</div>
