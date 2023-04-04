<script type="text/javascript">
	
	$(document).ready(function() {


        $(document).on('click', ".modal_export_button", function () {
        	
       		list_id = $(this).data("list-id");

			$('#modal_export_list_id').val(list_id);

       		$('#exportModal').modal('show');

    	});

        $(document).on('click', "#modal_export_submit", function () {

       		$('#exportModal').modal('hide');

    	});

    });

</script>