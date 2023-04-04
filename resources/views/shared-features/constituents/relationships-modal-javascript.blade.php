<script type="text/javascript">

	$(document).ready(function() {

		function getSearchData(v) {

			if (v == '') {
				$('#modal-list').addClass('hidden');
			}

			$.get('/{{ Auth::user()->team->app_type }}/entities/'+{!! $entity->id !!}+'/lookup/'+v, function(response) {
				if (response == '') {
					$('#modal-list').addClass('hidden');
				} else {
					$('#modal-list').html(response);
					$('#modal-list').removeClass('hidden');
				}
			});

		}

		$("#relationship_person").keyup(function(){
			getSearchData(this.value);
		});

		$(document).on("click", ".common_relationship_button", function() {
	    	text = $(this).html().trim();
	    	$('#relationship_type').val(text);
		});

		$(document).on("click", ".link_person_id", function() {
	    	id = $(this).data('person_id');
	    	name = $(this).data('person_name');
	    	$('#relationship_person_id').val(id);
	    	$('#relationship_person').val(name);
	    	$('#modal-list').addClass('hidden');
		});

        $(document).on('click', ".edit_relationship_modal_button", function () {

			full_name = $(this).data("full_name");
        	person_id = $(this).data("person_id");
        	type = $(this).data("type");


          	$('#edit_relationship_modal_person_id').val(person_id);
          	$('#edit_relationship_modal_type').val(type);
          	$('#edit_relationship_modal_full_name').html(full_name)
          	$('#edit-relationship-modal').modal('show');
         
        });

		$('#edit-relationship-modal').on('shown.bs.modal', function () {
		    $('#edit_relationship_modal_type').select();
		})  

        $(document).on('click', ".add_relationship_modal_button", function () {

          	$('#add-relationship-modal').modal('show');
          	$('#edit_relationship_modal_type').focus();
            
        });

		$('#add-relationship-modal').on('shown.bs.modal', function () {
		    $('#relationship_person').focus();
		})  


    });

</script>