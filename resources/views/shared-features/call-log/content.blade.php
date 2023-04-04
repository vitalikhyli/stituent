<div id="call-log-content" class="text-sm text-grey-darker flex flex-wrap mt-8">


@include('shared-features.call-log.report-modal')


<table class="w-full">

	@foreach($call_log->contacts_by_date as $date => $callsbydate)

		<tr>
			<td colspan="4">

				@if(\Carbon\Carbon::parse($date) == \Carbon\Carbon::today())
					<div class="w-full text-black bg-blue text-white p-2 font-bold rounded-lg mb-2 text-lg">
						Today
					</div>
				@elseif(\Carbon\Carbon::parse($date)->diffInDays() <= 7 && \Carbon\Carbon::parse($date) < \Carbon\Carbon::now())
					<div class="w-full text-black bg-blue-lightest p-2 border-b-2 border-blue font-bold rounded-t-lg mb-2 text-lg">
						{{ \Carbon\Carbon::parse($date)->format('l') }}
					</div>
				@elseif(\Carbon\Carbon::parse($date)->format('Y') == \Carbon\Carbon::today()->format('Y'))
					<div class="w-full text-black bg-blue-lightest p-2 border-b-2 border-blue font-bold rounded-t-lg mb-2 text-lg">
						{{ \Carbon\Carbon::parse($date)->format('l, F j') }}
					</div>
				@else
					<div class="w-full text-black bg-blue-lightest p-2 border-b-2 border-blue font-bold rounded-t-lg mb-2 text-lg">
						{{ \Carbon\Carbon::parse($date)->format('F jS, Y') }}
					</div>
				@endif

			</td>

		</tr>
			
			@foreach ($callsbydate as $thecall)
				<tr class="{{ (!$loop->last) ? 'border-b' : 'mb-6' }} table-row w-full text-sm group">


					<td class="table-cell whitespace-no-wrap text-grey text-right pr-3 pt-2 pb-4" valign="top">

						<div class="font-medium text-grey-darkest">

							@if ($thecall->private)
								<span class="cursor-pointer">
									<i class="fa fa-lock text-blue mr-1"></i>
								</span>
							@endif

							@if (isset($thecall->user))
								{{ $thecall->user->name }}
							@endif
						</div>

						<div>
							{{ $thecall->date->format('g:i a') }}
						</div>

					</td>



					<td class="table-cell w-1/2 pr-2 pt-2 pb-4" valign="top">
							
						<span class="font-bold text-black text-sm leading-none">

						@if($thecall->type)
							<span class="text-blue">
								{{ ucwords($thecall->type) }} 
								 > 
							</span>
						@endif

						@if($thecall->subject)
							{{ $thecall->subject }}
							<br />
						@endif
						
						@if($thecall->followup)
							<span class="cursor-pointer font-normal text-sm {{ ($thecall->followup_done) ? 'hidden ' : 'text-red' }}">
								<i class="fas fa-hand-point-right ml-1"></i> Follow up
								@if($thecall->followup_on)
									on {{ $thecall->followup_on }}
								@endif
							</span>
						@endif

						</span>

						@if($thecall->notes)
							<span class="">{!! nl2br($thecall->notesRegex) !!}</span>
						@endif

						@if($thecall->emails && count($thecall->emails) > 0)

							<div class="border-blue border-l-2 mt-2">

								<div class="px-2 py-1 text-blue border-b border-blue">
									Detected Emails
									<span class="text-grey float-right">
										+ People
									</span>
								</div>

								<div class="py-2">


									@foreach($thecall->emails as $email)

										<div class="py-2 text-xs bg-white mr-2 uppercase cursor-pointer flex {{ (!$loop->last) ? 'border-b' : '' }}">

										@livewire('call-log.email-link', ['thecall' => $thecall,
																		  'email' 	=> $email],
																		  key(['key' => $thecall->id.'_'. base64_encode($email)]))

										</div>

									@endforeach

								</div>

							</div>
						@endif

						@if($thecall->people->first())
							@foreach($thecall->people as $theperson)

								<div class="ml-6">
									@livewire('constituent-groups', ['person' => $theperson])
								</div>
								
							@endforeach
						@else
							<span class="text-grey ml-2">Not linked to constituent</span>
						@endif

					</td>


					<td class="text-grey text-left w-1/2 pt-2 pb-4" valign="top">

					<div class="float-right whitespace-no-wrap">

						<div class="py-2 text-right w-full">

								@if (Auth::user()->permissions->createconstituents)
									<span class="group-hover:opacity-100 opacity-0 one_contact remote-modal border hover:bg-blue-dark hover:border-blue-dark rounded-full bg-white hover:text-white px-4 py-1 text-xs cursor-pointer" data-toggle="modal" data-target="#new-person-modal-{{ $thecall->id }}">+ Create Voter</span>
								@endif

									<!---------------------------- NEW PERSON MODAL ---------------------------->

									@php
										$first_name = null;
										$last_name = null;
										$arr = explode(' ', $thecall->subject);
										if (count($arr) == 1) 
											$first_name = $arr[0];
										if (count($arr) == 2) {
											$first_name = $arr[0]; $last_name = $arr[1];
									}
									@endphp

									<div id="new-person-modal-{{ $thecall->id }}" class="modal fade text-left whitespace-normal" role="dialog">
										<div class="modal-dialog">

											<form method="POST" id="contact_form" action="/{{ Auth::user()->team->app_type }}/constituents/save">
												@csrf
												<input type="hidden" name="contact_id" value="{{ $thecall->id }}" />
												<!-- Modal content-->
												<div class="modal-content">
													<div class="modal-header">
														<button type="button" class="close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title">Add A New Person to the Database</h4>
													</div>
													<div class="modal-body">
														<p class="p-2 bg-grey-lightest">
															<b>NOTE: Are you sure this person is not in the database already?</b> If you are looking to link this contact to someone who is already in the databse, please use the <b><i class="fas fa-link"></i>Link</b> option.
														</p>

														<table class="text-base w-full border-t">
															<tr class="border-b">
																<td class="p-2 bg-grey-lighter text-right  w-1/4">
																	First Name
																</td>
																<td class="p-2">

																	<input name="first_name" placeholder="First Name" value="{{ ucfirst($first_name) }}" class="border-2 rounded-lg px-4 py-2 w-1/2"/>

																</td>
															</tr>
														
																<tr class="border-b">
																<td class="p-2 bg-grey-lighter text-right  w-1/4">
																	Last Name
																</td>
																<td class="p-2">


																	<input name="last_name"  placeholder="Last Name" value="{{ ucfirst($last_name) }}" class="border-2 rounded-lg px-4 py-2 w-1/2"/>


																</td>
															</tr>
															<tr class="border-b">
																<td class="p-2 bg-grey-lighter text-right  w-1/4">
																	Email
																</td>
																<td class="p-2">


																	<input name="email"  placeholder="Email" value="" class="border-2 rounded-lg px-4 py-2 w-1/2"/>


																</td>
															</tr>
															<tr class="border-b">
																<td class="p-2 bg-grey-lighter text-right  w-1/4">
																	Phone
																</td>
																<td class="p-2">


																	<input name="phone"  placeholder="Phone #" value="" class="border-2 rounded-lg px-4 py-2 w-1/2"/>


																</td>
															</tr>
															<tr class="border-b">
																<td class="p-2 bg-grey-lighter text-right  w-1/4">
																	Contact
																</td>
																<td>
																	<div class="p-4 text-xs text-gray italics">
																		<div class="font-bold">
																			{{ $thecall->subject }}
																		</div>
																		{{ $thecall->notes }}
																	</div>
																</td>
															</tr>

														</table>
														
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
														<button type="submit" class="btn btn-primary">Create & View Constituent</button>
														
													</div>
												</div>

											</form>

										</div>
									</div>
								

								<a data-toggle="tooltip" data-placement="top" title="Connections" href="/call-log/{{ $thecall->id }}/connect" class="group-hover:opacity-100 opacity-0 one_contact remote-modal border hover:bg-blue-dark hover:border-blue-dark rounded-full bg-white hover:text-white px-4 py-1 text-xs" target="#call-log-connect-modal"><i class="fas fa-link"></i>Link Voters</a>

								<a data-toggle="tooltip" data-placement="top" title="Edit Note" href="/call-log/{{ $thecall->id }}/edit" class="group-hover:opacity-100 opacity-0 one_contact remote-modal border hover:bg-blue-dark hover:border-blue-dark rounded-full bg-white hover:text-white px-4 py-1 text-xs" target="#call-log-edit-modal"><i class="fas fa-edit"></i>Edit Note</a>
						</div>


						<div class="ml-1 py-2 text-right w-full text-xs">
							@if(!$thecall->case)
								<a data-toggle="tooltip" data-placement="top" title="Convert to or Link to Case" href="/{{ Auth::user()->team->app_type }}/contacts/{{ $thecall->id }}/convert_to_case" class="border hover:bg-orange-dark hover:border-text-white rounded-full bg-white hover:text-white py-1 px-3"><i class="fas fa-folder-open"></i> Convert/Link Case</a>
							@elseif($thecall->case->resolved)
								<a data-toggle="tooltip" data-placement="top" title="Go to Case" href="/{{ Auth::user()->team->app_type }}/cases/{{ $thecall->case_id }}" class="hover:bg-grey-darker hover:text-white rounded-full bg-grey-light text-grey-darker py-1 whitespace-no-wrap px-3"><i class="fas fa-folder-open"></i> View Case<a>
							@else
								<a data-toggle="tooltip" data-placement="top" title="Go to Case" href="/{{ Auth::user()->team->app_type }}/cases/{{ $thecall->case_id }}" class="hover:bg-blue-darkest hover:text-white rounded-full bg-blue text-white py-1 whitespace-no-wrap px-3"><i class="fas fa-folder-open"></i> View Case</a>
							@endif
						</div>	


					</div>

					<div>
						@if($thecall->entities->first())
							@foreach($thecall->entities as $theentity)
								@if ($theentity->team->app_type == 'office')
									<a href="/{{ $theentity->team->app_type }}/organizations/{{ $theentity->id }}">
								@else
									<a href="/{{ $theentity->team->app_type }}/entities/{{ $theentity->id }}">
								@endif

								<button class="hover:bg-blue hover:text-white bg-grey-lighter border rounded-lg m-1 px-2 py-1 text-xs text-black truncate">
									<i class="fas fa-building mr-1"></i> {{ $theentity->name }}
								</button>
								</a>
							@endforeach
						@endif

						

						@if(json_decode($thecall->suggested_people))
							@foreach(json_decode($thecall->suggested_people) as $suggested)

								<?php
									if(IDisPerson($suggested->id)) {
										$suggested_person = \App\Person::where('id',$suggested->id)->first();
									}
									if(IDisVoter($suggested->id)) {
										$suggested_person = \App\Voter::where('id',$suggested->id)->first();
									}
								?>

								<button type="button" class="connect_suggested hover:bg-blue hover:text-white bg-red-lightest text-black border rounded-full m-1 px-2 py-1 text-sm text-black" data-href="/{{ Auth::user()->team->app_type }}/contacts/{{ $thecall->id }}/connect/{{ $suggested_person->id }}" data-toggle="tooltip" data-placement="top" title="Connect This Person to This">
									<i class="fas fa-question-circle mr-2 "></i>{{ $suggested_person->full_name }}
								</button>

							@endforeach
						@endif
						
						@if(json_decode($thecall->suggested_entities))
							@foreach(json_decode($thecall->suggested_entities) as $suggested)

								<?php
									$suggested_entity = \App\Entity::find($suggested->id);
								?>

								<button type="button" class="connect_suggested hover:bg-blue hover:text-white bg-red-lightest text-black border rounded-full m-1 px-2 py-1 text-sm text-black" data-href="{{ Auth::user()->team->app_type }}/contacts/{{ $thecall->id }}/connect_entity/{{ $suggested_entity->id }}" data-toggle="tooltip" data-placement="top" title="Connect This Entity to This">
									<i class="fas fa-question-circle mr-2 "></i>{{ $suggested_entity->name }}
								</button>

							@endforeach
						@endif
					</div>

					

					</td>



				</tr>
			@endforeach

			

	@endforeach

</table>

	@if ($call_log->total > 50)
		<div class="text-center text-lg text-grey-dark p-6 w-full">
			Showing last 50
			<a href="/{{ Auth::user()->team->app_type }}/contacts" class="m-4 px-6 py-4 font-bold rounded-full border text-blue">
				Show all {{ $call_log->total }}
			</a>
		</div>
	@endif

	

</div>
