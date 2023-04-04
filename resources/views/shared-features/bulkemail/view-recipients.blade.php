<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title">All {{ $recipients->count() }} Recipients</h4>
	</div>
	<div class="modal-body">
		<table class="w-full text-sm">
			@foreach ($recipients as $recipient)
				<tr>
					<td class="p-1 border-t">{{ $loop->iteration }}</td>
					<td class="p-1 border-t">{{ $recipient->name_title }} {{ $recipient->name }}</td>
					<td class="p-1 border-t">{{ $recipient->email }}</td>
					<td class="p-1 border-t">{{ $recipient->address }}</td>
				</tr>
			@endforeach
		</table>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	</div>
</div>