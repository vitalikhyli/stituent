<div x-data="{ open: false }">

  @php
    $cfplus = \App\CFPlus::firstWhere('voter_id', $voter->voter_id);
  @endphp

  <button @click="open=true" type="button" class="cursor-pointer text-white bg-yellow-500 bg-orange rounded-md px-3 py-2 font-bold hover:bg-yellow-600 hover:bg-orange-dark transition shadow">
    CF+ 
    @if ($cfplus)
      Record
    @else
      Info
    @endif
  </button>

  <div @keydown.window.escape="open = false" x-cloak x-init="$watch('open', o => !o &&)" x-show="open" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" x-ref="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-description="Background overlay, show/hide based on modal state." class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false" aria-hidden="true">
          </div>

          <!-- This element is to trick the browser into centering the modal contents. -->
          <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true"></span>
          
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-description="Modal panel, show/hide based on modal state." class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6 border-2 border-yellow-500">
              <div>
                @if ($cfplus)
                  <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" x-description="Heroicon name: outline/check" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                  </div>
                @else
                  <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                  </div>
                @endif
                <div class="mt-3 text-center sm:mt-5">
                  <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                    @if (!$cfplus)
                      No
                    @endif
                    <span class="font-bold text-yellow-600">CF+ Record</span> for {{ $voter->name }}
                  </h3>
                  <div class="mt-2">

                    

                    <p class="text-sm text-gray-500">
                      CF+ consists of hundreds of pieces of commercial information, gathered and compiled from both public and commercial sources. Many of the fields are estimates and best guesses based on available data. This data is purchased from 3rd parties, and Community Fluency is not responsible for the accuracy or usage.
                    </p>

                    @if (!$cfplus)
                    <p class="text-base text-white p-4 bg-yellow-500 rounded-md mt-2">
                      Call or email Peri at 617.699.4553 (<a href="mailto:peri@communityfluency.com">peri@communityfluency.com</a>) to learn more about purchasing CF+ data for your account. You can add any number of voters, at roughly 4 cents per record depending on volume.             
                    </p>
                    @endif
                    
                    <table class="text-left w-full">
                      @if ($cfplus)
                        @foreach ($cfplus->formatted_data as $category => $fields)

                          <tr>
                            <td class="p-1 pt-3 border-b-2 text-sm uppercase font-bold">{{ $category }}</td>
                            <td class="p-1 border-b-2 text-sm text-gray-900"></td>
                          </tr>
                          @foreach ($fields as $field => $value)
                        
                            <tr>
                              <td class="border-t p-1 text-sm text-gray-500">{{ $field }}</td>
                              <td class="border-t p-1 text-sm text-gray-900">{{ $value }}</td>
                            </tr>

                          @endforeach

                        @endforeach
                      @else
                        
                        @foreach (\App\CFPlus::getFieldMap() as $category => $fields)

                          <tr>
                            <td class="p-1 pt-3 border-b-2 text-sm uppercase font-bold">{{ $category }}</td>
                            <td class="p-1 border-b-2 text-sm text-gray-900"></td>
                          </tr>
                          @foreach ($fields as $dud => $field)
                        
                            <tr>
                              <td class="border-t p-1 text-sm text-gray-500">{{ $field }}</td>
                              <td class="border-t p-1 text-sm text-gray-300 italic">
                                No CF+ Record Imported
                              </td>
                            </tr>

                          @endforeach

                        @endforeach

                      @endif
                    </table>
                  </div>
                </div>
              </div>
              <div class="mt-5 sm:mt-6">
                <button type="button" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:text-sm" @click="open = false">
                  Close
                </button>
              </div>
            </div>
          
        </div>
      </div>
    </div>