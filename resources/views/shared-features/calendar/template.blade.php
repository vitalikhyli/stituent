<script id="calendar-template" type="text/template">

	<div class="controls flex text-center w-full items-center">
	    <div class="w-1/5 clndr-previous-button py-2 text-grey hover:text-grey-darkest">
	    	<i class="fa fa-arrow-left"></i>
	    </div>
	    <div class="w-3/5 month text-xl py-2 tracking-wide font-sans uppercase text-black">
	    	
	    	<span class="font-bold"><%= month %></span>
	    	<%= year %>
	    		
	    </div>
	    <div class="w-1/5 clndr-next-button  py-2 text-grey hover:text-grey-darkest">
	    	<i class="fa fa-arrow-right"></i>
	    </div>
	</div>

	<div class="clndr-grid w-full border-2 border-b-0 text-center border-grey-light">
	  <div class="days-of-the-week clearfix flex w-full h-12 items-center border-b pr-1">
	    <% _.each(daysOfTheWeek, function(day) { %>
	      <div class="header-day flex-1"><%= day %></div>
	    <% }); %>
	  </div>

	  <div class="pl-2 days clearfix flex w-full flex-wrap items-center text-grey-dark pb-3">
	    <% _.each(days, function(day) { %>
	      <div style="width: 14.2%;" class="mt-2 <%= day.classes %>" id="<%= day.id %>">
	        <div class="rounded-full w-10 h-10 day-number relative">
	        	<%= day.day %>


	        	<!-- Add Events as dots here, ideally on edge of circle -->
				<!-- 
				<% _.each(day.events, function(event, index) { %>
			      <div class="absolute text-5xl text-grey-dark" style="top: <%= -32 + (index * 10) %>px; 
			                                   left: <%= 16 + index * 10 %>px;">
					&middot;
			      </div>
			    <% }); %> 
				-->
	        </div>
	      </div>
	    <% }); %>
	  </div>
	</div>

</script>