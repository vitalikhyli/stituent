<div class="modal-content">
        <form id="call-log-connect" action="/call-log/{{ $call->id }}/update-connections" method="POST">
                @csrf
                <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Connect a Log Entry</h4>
                </div>
                <div class="modal-body p-6">

                        
<!--                         <div class="mt-2 font-bold text-lg">
                                Connected Constituents
                        </div>

 -->
                        <div class="text-center my-2">
                          <input id="search_in_modal" data-call-id="{{ $call->id }}" class="rounded-lg p-2 border w-3/4" placeholder="Search for Constituents" />
                        </div>

                        <div id="call_log_search_list" class="text-sm mb-4">
                        </div>

                        @if($call->people->first())
                        
                                <table class="table text-sm mb-4" id="connected_people_table">
                                        <!-- <tbody> -->
                                        @foreach($call->people as $theperson)
                                                <tr>
                                                    <td>
                                                        <label>
                                                            <input
                                                                   type="checkbox" 
                                                                   checked="checked" 
                                                                   name="people[]"
                                                                   value="{{ $theperson->id }}" />
                                                            <span class="ml-2">
                                                              {{ $theperson->full_name }}
                                                            </span>
                                                        </label>
                                                    </td>
                                                    <td>{{ $theperson->full_address }}</td>
                                                </tr>
                                        @endforeach
                                      <!-- </tbody> -->
                                </table>
                              
                        @else
                        
                          <table class="table text-sm mb-4" id="connected_people_table">
                          </table>
                               

                        @endif


                        







                        @if(json_decode($call->suggested_people))
                                <div class="mt-2 font-bold text-lg">
                                        Suggested People from Scanning Text
                                </div>
                                <table class="table text-sm">
                                    @foreach(json_decode($call->suggested_people) as $suggested)

                  <?php 
                  if(IDisPerson($suggested->id)) {
                    $suggested_person = \App\Person::where('id',$suggested->id)->first();
                  }
                  if(IDisVoter($suggested->id)) {
                    $suggested_person = \App\Voter::where('id',$suggested->id)->first();
                  }
                  ?>
                                            <tr>
                                                    <td>
                                                      <label>
                                                            <input type="checkbox" 
                                                                   name="people[]"
                                                                   value="{{ $suggested_person->id }}" />
                                                    {{ $suggested_person->full_name }}
                                                  </label>
                                                  </td>
                                                    <td>{{ $suggested_person->full_address }}</td>
                                            </tr>
                                    @endforeach
                                </table>
                        @endif










                    @if(Auth::user()->team->app_type == 'u')

                        <div class="mt-2 font-bold text-lg">
                                Connected Organizations
                        </div>


                        <div class="text-center my-2">
                          <input id="search_in_modal_entities" data-call-id="{{ $call->id }}" class="rounded-lg p-2 border w-3/4" placeholder="Search for Organizations" />
                        </div>

                        <div id="call_log_search_list_entities" class="text-sm">
                        </div>

                        @if($call->entities->count() > 0)
                        
                                <table class="table text-sm" id="connected_entities_table">
                                        <!-- <tbody> -->
                                        @foreach($call->entities as $theentity)
                                                <tr>
                                                    <td>
                                                        <label>
                                                            <input
                                                                   type="checkbox" 
                                                                   checked="checked" 
                                                                   name="entities[]"
                                                                   value="{{ $theentity->id }}" />
                                                            <span class="ml-2">
                                                              {{ $theentity->name }}
                                                            </span>
                                                        </label>
                                                    </td>
                                                    <td>{{ $theentity->address }}</td>
                                                </tr>
                                        @endforeach
                                      <!-- </tbody> -->
                                </table>
                              
                        @else
                          <table class="table text-sm" id="connected_people_table">
                          </table>
                                <div class="text-grey text-xl text-center w-full">
                                        No Connected Organizations
                                </div>

                        @endif

                    @endif
                        




                <div class="modal-footer">
                        
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>

                </div>
        </form>
</div>
