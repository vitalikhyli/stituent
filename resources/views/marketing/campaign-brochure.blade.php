@extends('blank')

@section('title')
    Campaign Brochure
@endsection

@section('style')

<style>

	.ratio {
	  padding-top: 77.27%; /* 1:1 Aspect Ratio */
	  position: relative; /* If you want text inside of it */
	}

	/* If you want text inside of the container */
	.text {
	  position: absolute;
	  top: 0;
	  left: 0;
	  bottom: 0;
	  right: 0;
	}
	body {
		background: #667;
	}

	g.highlighted .st1, 
	g.highlighted .st0 {
		stroke: #ddd;
		fill:#2d4e6b;
	}


</style>


	
@endsection

@section('javascript')

	<script type="text/javascript">

		// var y = document.getElementsByTagName("g");
		// var i;
		// for (i = 0; i < y.length; i++) {
		//   y[i].className += " highlighted";
		// }

		document.querySelectorAll('svg g')
		  .forEach((span) => {
		  	if (Math.random() < .03) {
			    span.classList.add('highlighted');
			}
		  });

	</script>

@endsection

@section('main')

<div class="pb-16">

	<div class="font-bold text-xl text-center w-full text-white pt-4">
		Outside
	</div>

	<div class="w-3/5 mt-4 mx-auto shadow-lg">

		<div class="ratio w-full overflow-hidden">

			<div class="absolute w-full h-full flex pin-t">

				<div class="w-1/3 bg-white p-8 relative">

					<div class="uppercase text-xl mt-16">
						<b>Get elected</b> with powerful, easy to use campaign tools. 

						<br><br>
						<b>Stay elected</b> with world-class constituent service tools.
					</div>

					<div class=" absolute pin-b mr-6 mb-6 text-sm text-grey-darker test-justify italic">

						Community Fluency is already the fastest, most powerful voter database and constituent management system in Massachusetts. We are now introducing these additional campaign tools on top of our state-of-the-art constituent service platform.
					</div>
					
				</div>
				
				<div class="w-2/3 bg-grey-light relative">

					

					<div class="w-full relative bg-cover" style="background-image: url(/images/bg-cover.jpg); height: 66%;">

						
						

						<div class="absolute pin-b">
							<img src="/images/swoop.png" />
						</div>
					</div>

					<div class="absolute h-full w-full flex pin-t font-sans">

						<div class="w-1/2 relative">

							<div class="h-32"></div>
							<div class="px-8 py-2 text-grey-lighter font-thin text-lg text-center">
								CONTACT US
							</div>


							<div class="text-xs mt-2 text-white px-8 text-center">

								<b>peri</b>@communityfluency.com<br>
								617.699.4553<br><br>
								PO Box 8703<br>
								Boston, MA 02114
							</div>
							

							<div class="absolute h-full border-r pin-r border-black opacity-25"></div>
						</div>

						<div class="w-1/2 relative">
							<div class="h-32"></div>
							<img class="w-1/3 mx-auto" src="/images/cf_logo_white.svg" />
							<div class="text-center w-full text-white text-lg font-thin tracking-wide mt-4">
								campaign<span class="font-bold">fluency</span>
							</div>

							<div class="absolute pin-b p-6 w-full text-center text-xl pb-12">
								
								Introducing<br>
								Powerful, Fast, Effective
								
								<br>
								<b>Campaign Tools</b><br>
								<span class="text-sm">for Massachusetts Political Pros</span>
							</div>

						</div>

					</div>
					

				</div>
				

			</div>

		</div>

	</div>

	<div class="font-bold text-xl text-center w-full text-white pt-8">
		Inside
	</div>

	<div class="w-3/5 mt-4 mx-auto shadow-lg">

		<div class="ratio w-full">

			<div class="absolute w-full h-full flex pin-t overflow-hidden">

				<div class="w-1/3 bg-blue-darker p-4 relative">
					
					<!-- include('marketing.crowd-svgs') -->


					

					<div class="relative h-full">
						<div class="absolute h-full w-full border border-grey-darker text-white text-sm p-6">

							<b>Every Address, GIS Mapped.</b>
							<div class="p-1">
								<i class="fa fa-star pr-2"></i> Build Walking Lists
								<br>
								<i class="fa fa-star pr-2"></i> Visualize your support
								<br>
								<i class="fa fa-star pr-2"></i> Reveal district insights
								<br>
								<i class="fa fa-star pr-2"></i> Generate Household mailing lists
							</div>
							
						</div>
					</div>
				</div>
				
				<div class="w-2/3 bg-grey-light relative">

					

					<div class="w-full relative" style="height: 50%;">

					</div>
					<div class="w-full relative bg-cover" style="background: bottom center no-repeat url(/images/statehouse.svg); height: 50%; background-size: 120%;">

					</div>

					<div class="absolute h-full w-full flex pin-t">

						<div class="w-1/2 relative p-6 text-xs text-grey-darker">
							<div class="absolute h-full border-r pin-r border-black" style="opacity:10%;"></div>

							<div class="font-bold text-lg mb-6">
								Keep it Simple & Win With:
							</div>
							

							<div class="font-bold text-sm uppercase text-black mt-3">
								Unlimited Voter Lists
							</div>
							Create targeted voter lists based on age, voting history, town, district, neighborhood, street, party, and more.

							

							<div class="font-bold text-sm uppercase text-black mt-3">
								Useful Metrics
							</div>
							Visualize your progress with your voter IDs and outreach over time.

							<div class="font-bold text-sm uppercase text-black mt-3">
								Organized Volunteers
							</div>
							Know who's got signs, who's making calls, and who goes where on election day.

							<div class="font-bold text-sm uppercase text-black mt-3">
								Tracked Contributions
							</div>
							Track contributions, verify your data, and one-click export to <b>OCPF format</b>.

							<div class="font-bold text-sm uppercase text-black mt-3">
								Dedicated Support
							</div>
							Our experienced staff will help you and your team get started and make progress.

							<div class="font-bold text-xs uppercase text-black mt-3 italic">
								Bulk Emailing, Endorsement Survey Management, mailings, walking lists, Maps, Events, and more...
							</div>


						</div>

						<div class="w-1/2 p-6 text-xs text-grey-darker">

							<div class="font-bold text-lg mb-6">
								We Are...
							</div>
							
							<div class="font-bold text-xs uppercase text-black mt-3">
								Fully Non-Partisan
							</div>
							We make the tools, but <b>YOU</b> own the data, including all contacts, volunteers, notes, and your donor list.

							<div class="font-bold text-xs uppercase text-black mt-3">
								Made In Massachusetts
							</div>
							Our team has over 30 years of experience working in the Massachusetts State House.

							<div class="font-bold text-xs uppercase text-black mt-3">
								Loyal to You
							</div>
							Our Loyalty Program means once you are elected, we won't offer this tool to your opponents.

							<div class="font-bold text-xs uppercase text-black mt-3">
								Trusted By Your Colleagues
							</div>
							Over 30 of your House and Senate colleagues are signed up for the Commonwealth's best constituent service tools. Ask them and their staff about Community Fluency!

							<div class="text-blue mt-4">
								<div class="font-bold text-xs uppercase mt-3 text-right">
									...Competitively Priced!
								</div>
							</div>
						</div>

					</div>
					

				</div>

			</div>

		</div>

	</div>

</div>

@endsection