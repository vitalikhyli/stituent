@extends('blank')

@section('title')
    Terms of Service
@endsection


@section('style')
<style>
</style>
@endsection


@section('main')


<center>


	<div class="w-1/3 m-10 border rounded-lg shadow-lg">
		<div class="w-full p-2 border-b bg-orange-lightest">
			<div class="text-blue-darker">
				<!-- <img src="/images/FS_logo_blue.png" class="w-16" /> -->
				<!-- <span class="text-lg font-thin tracking-wide">community</span> -->
				<!-- <span class="text-lg ">fluency</span> -->
			</div>

			<b>Welcome back!</b> &nbsp;Our terms of service and privacy policy have been updated. Questions? Call (617) 000-000.

		</div>
		<div class="p-4">
			<form method="POST" id="tos" action="/tos/agree">

				@csrf

				<label for="agree"> <input type="checkbox" name="agree" id="agree" /> &nbsp;Check here to indicate that you have read and agreed to Community Fluency's terms of service and privacy policy.</label>

				<input type="submit" id="continue" disabled class="opacity-50 bg-blue rounded-lg px-4 py-2 m-2 text-white" value="Continue">

			</form>
		</div>
	</div>







<!------------------------------ TERMS OF SERVICE ------------------------------>
<?php $i = 1; ?>

<div class="w-1/2 text-base leading-normal mt-12 ">

	<div class="p-4 text-center text-xl font-bold uppercase">
		Terms of Service Agreement

	<span class="p-2 text-center text-lg font-bold uppercase bg-orange-lightest border rounded-lg">
		DRAFT
	</span>

	</div>

	<div class="mb-4 text-grey-dark mb-10">
		<i>Last updated: June 30, 2019</i>
	</div>

	<div class="normalcase text-left">
	Fluency Community LLC (“Community Fluency”, "Fluency," “we”, “us” and terms of similar meaning) provides this web site [www.CommunityFluency.com] and the services provided by or through this web site to you subject to these terms and conditions of use. These terms and conditions of use together with the Community Fluency Privacy Policy (collectively, the “Terms”) govern your use of this web site and the services provided.
	<br /><br />
	These terms apply to all customers as well as the persons who are provided access to a Community Fluency account by the customer, whether directly or indirectly, including staff, volunteers, and advisors.
	</div>



	<div class="font-bold text-xl mb-4 text-left mt-8">
		<?php echo $i++; ?>.) Privacy Policy
	</div>
	<div class="normalcase text-left ml-10">
	Please refer to Community Fluency’s privacy policy, available <a href="#privacy" class="text-blue font-bold">on this webpage</a> a for information on how Community Fluency collects, uses and discloses personally identifiable information from its Users. By using the Services you agree to our use, collection and disclosure of personally identifiable information in accordance with the Privacy Policy.
	</div>


	<div class="font-bold text-xl mb-4 text-left mt-8">
		<?php echo $i++; ?>.) Ownership / Intellectual Property Rights
	</div>
	<div class="normalcase text-left ml-10">
	xxxx
	</div>

	<div class="font-bold text-xl mb-4 text-left mt-8">
		<?php echo $i++; ?>.) Termination of Accounts
	</div>
	<div class="normalcase text-left ml-10">
	xxxx
	</div>

	<div class="font-bold text-xl mb-4 text-left mt-8">
		<?php echo $i++; ?>.) Rights to your data
	</div>
	<div class="normalcase text-left ml-10">
	Client owns data
	</div>
	<div class="normalcase text-left ml-10">
	Fluency Community LLC owns any voter data that is uploaded and may make availavle to other clients, etc.
	</div>

	<div class="font-bold text-xl mb-4 text-left mt-8">
		<?php echo $i++; ?>.) User Activity / Use
	</div>
	<div class="normalcase text-left ml-10">
	Community Fluency provides its tools and services on the understanding that they will be used in accordance with local, state and federal law regulating political campaign activity. These include laws governing the times and places where campaign activity may occur. While Community Fluency provides tools to help delineate campaign and non-campaign activity, it is your responsibility to use Community Fluency according to the law.
	<br /><br />
	As Community Fluency provides political and campaign-related tools, you agree that the source of your payments to Community Fluency will be entirely non-public funds.
	</div>



	<div class="font-bold text-xl mb-8 text-left mt-8">
		<?php echo $i++; ?>.) Warranty Disclaimer
	</div>
	<div class="normalcase text-left ml-10">

	Your use of the site, services, software (including without limitation, the application all of the tools Community Fluency contains), the content whether provided by you or by Community Fluency, as well as any third party materials or third party services is entirely at your own risk, and are provided “as is.”
	<br /><br />

	To the maximum extent permitted by applicable law, Community Fluency, its subsidiaries and affiliates, and their third party providers, licensors, distributors or suppliers (collectively “suppliers”) disclaim all warranties and conditions, express or implied, including any warranty or condition that Community Fluency or third party materials or third party services are or will (a) be fit for a particular purpose, (b) be of good title, (c) be of merchantable quality; or they do not or will not interfere with or infringe or misappropriate any intellectual property rights.
	<br /><br />
	Furthermore, the suppliers disclaim all warranties and conditions, express or implied as to the accuracy, reliability, quality of content in or linked to Community Fluency. Community Fluency, its subsidiaries and its affiliates and suppliers do not warrant that Community Fluency is or will be secure, free from bugs, viruses, interruption, data loss, errors, theft or destruction. Some provinces do not allow the exclusion of implied warranties or conditions, so the above exclusions may not apply to you.
	</div>

</div>

<!------------------------------ PRIVACY POLICY ------------------------------>
<?php $i = 1; ?>

<a name="privacy"></a>

<div class="w-1/2 text-base leading-normal mt-12 ">

	<div class="p-4 text-center text-xl font-bold uppercase">
		Privacy Policy
	</div>
	<div class="mb-4 text-grey-dark mb-10">
		<i>Last updated: June 30, 2019</i>
	</div>

	<div class="normalcase text-left">
	xxxx
	https://www.termsfeed.com/blog/caloppa/#How_to_comply_with_CalOPPA
	</div>



	<div class="font-bold text-xl mb-4 text-left mt-8">
		<?php echo $i++; ?>.) Data may been viewed for maintenance / coding purposes
	</div>
	<div class="normalcase text-left ml-10">
	xxx
	</div>

	<div class="font-bold text-xl mb-4 text-left mt-8">
		<?php echo $i++; ?>.) Section
	</div>
	<div class="normalcase text-left ml-10">
	xxx
	</div>

</div>

<!------------------------------ CONTACT ------------------------------>

<div class="w-1/2 text-base leading-normal mt-12 ">

	<div class="p-4 text-center text-xl font-bold uppercase">
		Contact
	</div>


	<div class="normalcase text-left">
	If you have any questions regarding these Terms or your use of the Services, please feel free to contact us at:
	<br /><br />
	Fluency Community LLC<br />
	25 Spruce Dr<br />
	Ashburnham, MA 01430<br />
	contact@CommunityFluency.com<br />
	(617) 000-0000
	</div>


</div>

<!------------------------------ /END ------------------------------>

</center>

@endsection

@section('javascript')

<script type="text/javascript">



$(document).ready(function() {

	$('#agree').click(function() {
        if ($(this).is(':checked')) {
        	$('#continue').removeAttr('disabled');
        	$('#continue').removeClass('opacity-50');
            
        } else {
            $('#continue').attr('disabled', 'disabled');
            $('#continue').addClass('opacity-50');

        }
    });

	// $("#agree").on("change", function(e){
	//   if($("#agree").attr("checked")){
	//   	alert();
	//     $("#continue").button("enable");
	//   } else {
	//     $("#continue").button("disable");
	//   }
	// });
});
</script>
@endsection