@extends('campaign.base')

@section('title')
    Voter Groups
@endsection

@section('breadcrumb')
    <a href="/u">Home</a> > &nbsp;<b>Voter Groups</b>
@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

<div class="text-3xl font-sans">
	Manage Groups
</div>


@include('elements.generic_groups')

@endsection

@section('javascript')

<script type="text/javascript">

$('.switchform').click(function() {
  var id = $(this).attr('id');
  var m = $(this).attr('data-model');
  var c = $(this).attr('data-column');
  $.get('/switchform/'+m+'/'+c+'/'+id, function(response) {
		$('#'+id).replaceWith(response);
  });
});


$(window).bind('scroll', function () {
    if ($(window).scrollTop() > 200) {
        $('#menu').addClass('fixed');
        $('#menu').css({display: 'block'});
    } else {
        $('#menu').removeClass('fixed');
        $('#menu').css({top: '10px'});
    }
});

$(window).scroll(function() {
  sessionStorage.scrollTop = $(this).scrollTop();
});
$(document).ready(function() {
  if (sessionStorage.scrollTop != "undefined") {
    $(window).scrollTop(sessionStorage.scrollTop);
  }
});

</script>

@endsection
