@extends(Auth::user()->team->app_type.'.base')

@section('title')
   	User Uploads
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> > 
    <a href="/campaign/useruploads">User Uploads</a> >
    <b>Edit</b>
@endsection

@section('styles')

	@livewireStyles

@endsection

@section('main')


	@livewire('user-upload-to-master.edit-user-upload', [
											'upload_id' => $upload->id,
											'preview' => $preview,
											'preview_count' => $preview_count
											])
@endsection


@section('javascript')

 	@livewireScripts

	<script type="text/javascript">
		
		$(document).ready(function() {

			$('#add-tag').on('shown.bs.modal', function () {
			    $('#name').focus();
			})  

		});

	</script>

@endsection