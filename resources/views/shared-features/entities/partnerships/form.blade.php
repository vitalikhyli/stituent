<form method="POST" id="contact_form" action="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}/partnerships/{{ $partnership->id }}/update">
	@csrf

	<input type="hidden" name="previous_url" value="{{ base64_encode(url()->previous()) }}" />



	<table class="text-base w-full border-t">


		<tr class="border-b">
			<td class="pt-4 pr-2 bg-grey-lighter text-right align-top w-1/6">
				Year
			</td>

			<td class="p-2">

				<div class="flex">

					<select name="partnership_type_id" class="form-control w-1/3">

						<option value="">-- Select a Year --</option>
						@foreach ($partnership_years as $year)
							<option value="{{ \Carbon\Carbon::parse($year)->format("Y-m-d") }}" {{ ($partnership->year == $year) ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($year)->format("Y-m-d") }}</option>

						@endforeach

					</select>


					<div class="p-2">
						~ or ~ 
					</div>

					<input name="new_year" placeholder="New in format: {{ \Carbon\Carbon::now()->format("Y-m-d") }}" value="{{ ($errors->any()) ? old('new_year') : '' }}" class="border-2 rounded-lg px-4 py-2 w-1/3"/>
				</div>

			</td>

		</tr>

		<tr class="border-b">
			<td class="pt-4 pr-2 bg-grey-lighter text-right align-top w-1/6">
				Partnership Type
			</td>
			<td class="p-2">

				<div class="flex">

					<select name="partnership_type_id" class="form-control w-1/3">

						<option value="">-- Select a Type --</option>
						@foreach ($partnership_types as $type)
							@if(!$errors->any())
								<option value="{{ $type->id }}" {{ ($partnership->partnership_type_id == $type->id) ? 'selected' : '' }}>{{ $type->name }}</option>
							@elseif(old('partnership_type_id'))
								<option value="{{ $type->id }}" {{ (old('partnership_type_id') == $type->id) ? 'selected' : '' }}>{{ $type->name }}</option>
							@endif
						@endforeach

					</select>


					<div class="p-2">
						~ or ~ 
					</div>

					<input name="new_type" placeholder="Add a New Type" value="{{ ($errors->any()) ? old('new_type') : '' }}" class="border-2 rounded-lg px-4 py-2 w-1/3"/>
				</div>

			</td>
		</tr>




		<tr class="border-b">
			<td class="pt-4 pr-2 bg-grey-lighter text-right align-top w-1/6">
				Contacts
			</td>
			<td class="p-2">


			<?php $i = 0; ?>
			@if(!$errors->any())
				@if($partnership->contacts)
					@foreach($partnership->contacts as $thecontact)
					<div class="flex w-full mb-2 text-sm">
		
						<div class="w-1/3 pr-2">
							<input name="name_{{ $i }}" placeholder="Name" value="{{ ($errors->any()) ? old('name_'.$i) : $thecontact['name'] }}" class="border-2 rounded-lg px-4 py-2 w-full font-semibold"/>
						</div>

						<div class="w-1/3 pr-2">
							<input name="phone_{{ $i }}" placeholder="Phone" value="{{ ($errors->any()) ? old('phone_'.$i) : $thecontact['phone'] }}" class="border-2 rounded-lg px-4 py-2 w-full "/>
						</div>

						<div class="w-1/3">
							<input name="email_{{ $i++ }}" placeholder="Email" value="{{ ($errors->any()) ? old('email_'.$i++) : $thecontact['email'] }}" class="border-2 rounded-lg px-4 py-2 w-full"/>
						</div>

					</div>
					@endforeach
				@endif
				<div class="flex w-full text-sm">
	
					<div class="w-1/3 pr-2">
						<input name="name_{{ $i }}" placeholder="New Name" value="{{ ($errors->any()) ? old('name_'.$i) : '' }}" class="border-2 rounded-lg px-4 py-2 w-full font-semibold"/>
					</div>

					<div class="w-1/3 pr-2">
						<input name="phone_{{ $i }}" placeholder="New Phone" value="{{ ($errors->any()) ? old('phone'.$i) : '' }}" class="border-2 rounded-lg px-4 py-2 w-full "/>
					</div>

					<div class="w-1/3">
						<input name="email_{{ $i++ }}" placeholder="New Email" value="{{ ($errors->any()) ? old('email_'.$i++) : '' }}" class="border-2 rounded-lg px-4 py-2 w-full"/>
					</div>


				</div>
			@else

				<?php
					$e = 0;
					foreach(old() as $key => $value){
						if (("email_" == substr($key,0,6)) || ("name_" == substr($key,0,6))) {
							$e++;
						}
					}
					$f = ($partnership->contacts) ? count($partnership->contacts) : 0;
					$contacts_count = ($f >= $e) ? $f : $e;
				?>

				@for($num = 0; $num < $contacts_count; $num++)
					<div class="flex w-2/3 mb-2">
		
						<div class="w-1/2 pr-2">
							<input name="name_{{ $i }}" placeholder="Name" value="{{ old('name_'.$num) }}" class="border-2 rounded-lg px-4 py-2 w-full "/>

						</div>
						<div class="w-1/2 pl-2">
							<input name="email_{{ $i++ }}" placeholder="Email" value="{{ old('email_'.$num) }}" class="border-2 rounded-lg px-4 py-2 w-full"/>
						</div>

					</div>
				@endfor

			@endif

			</td>
		</tr>

		<tr class="border-b">
			<td class="pt-4 pr-2 bg-grey-lighter text-right align-top w-1/6">
				Notes
			</td>
			<td class="p-2">

				<textarea rows="6" name="notes" placeholder="Description of this Partnership..." value="" class="border-2 rounded-lg px-4 py-2 w-full">{{ ($errors->any()) ? old('notes') : $partnership->notes }}</textarea>

			</td>
		</tr>

	</table>


	

	<table class="text-base w-full mt-4">
		<tr class="border-b-4 border-blue">
			<td class="p-2" colspan="4">

				<div class="text-xl">

					<i class="fas fa-graduation-cap mr-2"></i>

					Northeastern University
				</div>

			</td>
		</tr>


		<tr class="border-b">

		<tr class="border-b">
			<td class="pt-4 pr-2 bg-grey-lighter text-right align-top w-1/6">
				Program/Course
			</td>
			<td class="p-2">

				<input name="program" id="program" placeholder="i.e. HVT or SPNS 3501 Advanced..." value="{{ ($errors->any()) ? old('program') : $partnership->program }}" class="border-2 rounded-lg px-4 py-2 w-5/6 font-bold"/>


				<div id="list-programs" class="flex-shrink"></div>


			</td>
		</tr>
		
			<td class="pt-4 pr-2 bg-grey-lighter text-right align-top w-1/6">
				Department
			</td>
			<td class="p-2">

				<div class="flex">
					<select name="department_id" class="form-control w-1/2">

						<option value="">-- Select a Department --</option>
						@foreach ($departments as $department)
							<option {{ ($department->id == $partnership->department_id) ? 'selected' : '' }} value="{{ $department->id }}">
								{!! $department->name !!}
							</option>
						@endforeach

					</select>


					<div class="p-2">
						~ or ~ 
					</div>

					<input name="new_department" placeholder="Add a New Department" value="" class="border-2 rounded-lg px-4 py-2 w-1/3"/>
				</div>

			</td>
		</tr>

		<tr class="border-b">
			<td class="pt-4 pr-2 bg-grey-lighter text-right align-top w-1/6">
				Staff 1
			</td>
			<td class="p-2">

				<input name="faculty" placeholder="Name" value="{{ $partnership->data['faculty'] }}" class="border-2 rounded-lg px-4 py-2 w-1/2"/>

			</td>
		</tr>

		<tr class="border-b">
			<td class="pt-4 pr-2 bg-grey-lighter text-right align-top w-1/6">
				Staff 2
			</td>
			<td class="p-2">

				<input name="filer" placeholder="Name" value="{{ $partnership->data['filer'] }}" class="border-2 rounded-lg px-4 py-2 w-1/2"/>

			</td>
		</tr>
	</table>


	<div class="text-right text-lg py-8">

		<input type="submit" name="save" value="Save" class="text-base rounded-lg px-4 py-2 border bg-blue text-white text-center"/>

		<button formaction="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}/partnerships/{{ $partnership->id }}/update/close" name="update" class="text-base rounded-lg bg-grey-darkest text-white px-4 py-2 text-center mr-2"/>
			Save and Close
		</button>



	</div>


</form>