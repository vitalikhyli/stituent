<script type="text/javascript">

function callLog_getSearchData(v) {
	if (v == '') {
	  $('#call-list').addClass('hidden');
	}
	$.get('/call-log_lookup/'+v, function(response) {
	  if (response == '') {
	    $('#call-list').addClass('hidden');
	  } else {
	    $('#call-list').html(response);
	    $('#call-list').removeClass('hidden');
	  }
	});
}

function getSearchData_CallLog(v, call) {
    $.get('/call-log/search/'+v+'/'+call, function(response) {
        $('#call_log_search_list').html(response);
    });
}

function getSearchDataEntities_CallLog(v, call) {
    $.get('/call-log/search_entities/'+v+'/'+call, function(response) {
        $('#call_log_search_list_entities').replaceWith(response);
    });
}

$(document).ready(function() {

    $("#call-subject").focusout(function(){
        window.setTimeout(function() {$('#call-list').addClass('hidden'); }, 300);
    });
      
    $("#call-subject").keyup(delay(function (e) {
            callLog_getSearchData(this.value);
    }, 500));

	$(document).on('click', ".call-search-result", function () {

	    name = $(this).data('name');

	    entity_id = $(this).data('entity_id');
	    if (entity_id != "") {
	    	// $("#call-add-entities").val($("#call-add-entities").val() + ',' + entity_id);
	    	$("#call-add").append('<div><input type="checkbox" name="add-entity-'+entity_id +'" id="add-entity-'+entity_id +'" checked><label class="ml-2" for="add-entity-'+entity_id +'" >'+name+'</label></div>');
	    }

	    person_id = $(this).data('person_id');
	    if (person_id != "") {
	    	// $("#call-add-people").val($("#call-add-people").val() + ',' + person_id);
	    	$("#call-add").append('<div><input type="checkbox" name="add-person-'+person_id+'" id="add-person-'+person_id+'" checked><label class="ml-2" for="add-person-'+person_id+'" >'+name+'</label></div>');
	    }

	    $("#call-subject").val(name);
	    $("#call-subject").select();

	});

   $('#call-log').on('submit', '#call-log-search', function(e) {
        e.preventDefault();

        var url = $(this).attr('action');

        $.post(url, $(this).serialize(), function(response) {

            // alert(url);
            // alert($(this).serialize());

            $('#call-log-content').replaceWith(response);
            $('[data-toggle="tooltip"]').tooltip();
            focusCallLog();
            updateLeftCounter();
        });

    });


    $(document).on('click', "#save_as_new_case", function () {
        $('#save_as_new_case_serialize').val('true');
        $('#call-log-add').trigger( "submit" )
    });

   $('#call-log').on('submit', '#call-log-add', function(e) {
        e.preventDefault();

        var url = $(this).attr('action');

        $.post(url, $(this).serialize(), function(response) {

            $('#call-log-content').replaceWith(response);
            $('#call-subject').val('');
            $('#call-log-notes').val('');

			$('#call-add').html('');

            $('[data-toggle="tooltip"]').tooltip();
            focusCallLog();
            updateLeftCounter();
            window.livewire.rescan();
        });
    });

    $("form input[type=submit]").click(function() {
        $("input[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });


    // $('#call-log').on('submit', '#call-log-edit', function(e) {
    //     e.preventDefault();
    //     var that = this;
    //     // To do delete formaction on button
    //     // https://stackoverflow.com/questions/51988827/how-to-get-formaction-in-submit-event
    //     let formAction = e.target.getAttribute("formaction");
    //     let activeElementAction = document.activeElement.getAttribute("formaction");
    //     let action = activeElementAction || formAction;
    //     $.post(action, $(this).serialize(), function(response) {
    //         $('#call-log-edit-modal').modal('hide');
    //         $('#call-log-content').replaceWith(response);
    //         $('[data-toggle="tooltip"]').tooltip();
    //         updateLeftCounter();
    //     });
    // });

    // HAD TO REPLACE ABOVE WITH BELOW, BECAUSE DELETE BUTTON WAS NOT WORKING

    $('#call-log').on('click', '.save-or-delete-call', function(e) {
        let action = $(this).attr('data-formaction');
        $.post(action, $('#call-log-edit').serialize(), function(response) {
            $('#call-log-edit-modal').modal('hide');
            $('#call-log-content').replaceWith(response);
            $('[data-toggle="tooltip"]').tooltip();
            updateLeftCounter();
        });
    });

    $('#call-log').on('click', '#ask-delete-call', function(e) {
        $(this).toggleClass('btn-danger');
        $('#delete-call').toggleClass('hidden');
    });


    $('#call-log').on('submit', '#call-log-connect', function(e) {
        e.preventDefault();
        var that = this;
        // To do delete formaction on button
        // https://stackoverflow.com/questions/51988827/how-to-get-formaction-in-submit-event
        let formAction = e.target.getAttribute("action");
        let activeElementAction = document.activeElement.getAttribute("formaction");
        let action = activeElementAction || formAction;
        $.post(action, $(this).serialize(), function(response) {
            $('#call-log-connect-modal').modal('hide');
            $('#call-log-content').replaceWith(response);
            $('[data-toggle="tooltip"]').tooltip();
            updateLeftCounter();
            // Livewire.emit('refresh')
        });
    });

    $('#call-log').on('click', '.remote-modal', function(e){
        e.preventDefault();
        var target = $(this).attr('target');
        var href = $(this).attr('href');
        $(target).modal('show').find('.modal-dialog').load(href);
    });


    $(document).on("click", "#followup", function() {
        if ($('#followup').is(":checked")) {
            $("#followup_on").removeClass('hidden');
        } else {
            $("#followup_on").addClass('hidden');
        }
    });

    $(document).on("click", ".connect_suggested", function() {
        $.get($(this).attr('data-href'), function(response) {
            $('#call-log-content').replaceWith(response);
        }); 
    });

    $(document).on("click", ".connect_suggested_in_modal", function(e) {
        e.preventDefault();
        var person_id = $(this).attr('data-person_id');
        var person_name = $(this).attr('data-person_name');
        var person_address = $(this).attr('data-person_address');
        
        var row = '<tr class="bg-orange-lightest"> <td> <label> <input  type="checkbox" checked="checked" name="people[]" value="'+person_id +'" /><span class="ml-2">'+person_name +'</span></label></td><td>'+person_address +'</td></tr>';

        $(this).parent().parent().next().prepend(row);
        $(this).parent().remove();

        $('#search_in_modal').val('');
        $('#search_in_modal').focus();
        $('#call_log_search_list').html("");

        return false;
    });

    $(document).on("click", ".connect_entity_in_modal", function(e) {
        e.preventDefault();
        var entity_id = $(this).attr('data-entity_id');
        var entity_name = $(this).attr('data-entity_name');
        var entity_address = $(this).attr('data-entity_address');
        
        var row = '<tr class="bg-orange-lightest"> <td> <label> <input  type="checkbox" checked="checked" name="entities[]" value="'+entity_id +'" /><span class="ml-2">'+entity_name +'</span></label></td><td>'+entity_address +'</td></tr>';

        $(this).parent().parent().next().prepend(row);
        $(this).parent().remove();

        $('#search_in_modal_entities').val('');
        $('#search_in_modal_entities').focus();
        $('#call_log_search_list_entities').html("");

        return false;
    });
    $(document).on("keyup", "#search_in_modal", function() {
        var call = $(this).attr('data-call-id');
        getSearchData_CallLog(this.value,call);
    });

    $(document).on("keyup", "#search_in_modal_entities", function() {
        var call = $(this).attr('data-call-id');
        getSearchDataEntities_CallLog(this.value,call);
    });



    $(document).on('click', "#show_all_contact_types", function () {
        $('#other_contact_types').removeClass('hidden');
        $(this).addClass('hidden');
    });

});

</script>