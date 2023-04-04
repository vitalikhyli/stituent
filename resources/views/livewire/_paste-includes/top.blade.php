<div class="flex w-full">

	<div class="text-blue font-bold text-2xl pb-2 flex-grow">

		{{ $model->created_at->toDateString() }} - {{ $model->name }}

		@if($model_class == 'App\UserUpload' && $model->lines()->first())
			<span class="text-black font-normal">({{ number_format($model->lines()->count()) }})</span>
		@endif

	</div>

	<div class="">

		<button class="rounded-lg bg-red-dark text-white px-3 py-1 text-sm font-normal"
				wire:click="unselectModel()">
			Use a Different One
		</button>

	</div>

</div>