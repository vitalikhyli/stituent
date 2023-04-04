<div class="modal-content">
     <form id="call-log-edit" action="/call-log/{{ $call->id }}/update" method="POST">
          @csrf
          <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Editing a Log Entry</h4>
          </div>
          <div class="modal-body p-6">

               <div class="flex mb-2">

                <div class="whitspace-no-wrap pr-1 pt-1">
                  Date:
                </div>

                <div class="whitspace-no-wrap w-2/6">

                  <input type="text" name="date" autocomplete="off" placeholder="Date" class="datepicker border px-2 py-1 h-8 text-sm text-black font-bold w-full" value="{{ \Carbon\Carbon::parse($call->date)->format('m/d/Y') }}" />

                  <script type="text/javascript">
                    $('.datepicker').datepicker(); //Need this here because modal
                  </script>

                </div>

                 <div class="px-2 py-1 text-rightwhitspace-no-wrap">
                    Type:
                 </div>

                 <select name="type" class="form-control whitspace-no-wrap w-3/6">


                        <option value="" {{ (!trim($call->type)) ? 'selected' : '' }}>
                          -- None --
                        </option>

                     @foreach(Auth::user()->team->contactTypes() as $key => $type)

                        <option value="{{ $type }}" {{ (strtolower($call->type) == strtolower($type)) ? 'selected' : '' }}>{{ ucwords($type) }}</option>
                        
                     @endforeach

                 </select>

               </div>

              
              <div class="flex mb-2">

                <div class="whitspace-no-wrap pr-1 pt-1">
                  Time:
                </div>

                <div class="whitspace-no-wrap w-1/6">

                  <input type="text" name="time" autocomplete="off" placeholder="{{ \Carbon\Carbon::now()->format('h:i A') }}" class="border px-2 py-1 h-8 text-sm text-black font-bold w-full" value="{{ \Carbon\Carbon::parse($call->date)->format('h:i A') }}" />


                </div>

              </div>


                  <input type="text"
                             placeholder=""
                             name="subject"
                             value="{{ $call->subject }}"
                             autocomplete="off"
                             class="border-2 text-lg px-4 py-3 w-full font-bold bg-grey-lightest" />
                  <textarea 
                             id="call-log-notes"
                             name="notes" 
                             class="border-2 p-4 border-t-0 text-lg w-full bg-grey-lightest"
                             placeholder=""
                             rows="4">{{ $call->notes }}</textarea>

                  <div class="flex items-center text-sm">

                          <div class="w-2/3">

                                  <label class="checkbox ml-8 cursor-pointer font-normal">
                                          <input type="checkbox" 
                                                     name="private"
                                                     @if ($call->private)
                                                     checked="checked"
                                                     @endif
                                                     autocomplete="off">
                                          
                                          Private <span class="font-normal">({{ $call->user->name }} only)</span>
                                          <i class="fa fa-lock ml-1" style="color: #999;"></i>
                                  </label>

                                  <label class="checkbox ml-8 cursor-pointer font-normal">
                                          <input type="checkbox" 
                                                     autocomplete="off"
                                                     @if ($call->followup)
                                                     checked="checked"
                                                     @endif
                                                     name="followup">
                                          
                                          Requires Followup
                                          <i class="fa fa-exclamation-triangle text-red"></i>
                                  </label>

                          </div>
                  </div>

      </form>           


     
</div>

 <div class="modal-footer">
         
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

      <button id="save-call" class="save-or-delete-call btn btn-primary" data-formaction="/call-log/{{ $call->id }}/update">Save</button>

      <button id="ask-delete-call" class="btn btn-danger float-left">Delete</button>

      <div id="delete-call" class="hidden flex float-left">
         <div class="text-sm font-bold p-2">Are you sure?</div>
            <button class="save-or-delete-call btn btn-danger" data-formaction="/call-log/{{ $call->id }}/delete">Yes, Confirm Delete</button>
      </div>

 </div>
