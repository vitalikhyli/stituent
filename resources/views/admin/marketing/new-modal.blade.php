<!-- https://alpinetoolbox.com/examples/modal-demo.php -->

  <!--Overlay-->
  <div class=" transition-opacity overflow-auto" style="background-color: rgba(0,0,0,0.5)" 
          x-show="open" 
          :class="{ 'block w-full z-30 fixed bottom-0 inset-x-0 px-4 pb-4 sm:inset-0 sm:flex sm:items-center sm:justify-center': open }"
          x-transition:enter="ease-out duration-300"        
          x-transition:enter-start="opacity-0"        
          x-transition:enter-end="opacity-100"        
          x-transition:leave="ease-in duration-200"        
          x-transition:leave-start="opacity-600"         
          x-transition:leave-end="opacity-0"
       >

    <!--Dialog-->
    <div class="transform transition-all bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg py-4 text-left px-6" 
          x-show="open" 
          @click.away="open = false" 
          x-transition:enter="ease-out duration-300" 
          x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
          x-transition:leave="ease-in duration-200" 
          x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
          x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         >

      <!--Title-->
      <div class="flex justify-between items-center pb-3">
        <p class="text-2xl font-bold">Add New Candidate</p>
        <div class="cursor-pointer z-50" @click="open = false">
          <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
            <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
          </svg>
        </div>
      </div>

    
   <!-- content -->
   <div>
    <form action="/admin/marketing" method="post">

      @csrf

      

        <!--Body-->

        <div class="mt-4 flex">
          <div class="w-1/3 mt-1 whitespace-no-wrap">
            Office
          </div>
          <div class="w-2/3">
              
                <select class="text-xl w-full" name="office">
                    <option value="">-- Pick One --</option>
                  	@foreach(\App\Candidate::select('office')->groupBy('office')->get() as $office)
                        <option value="{{ $office->office }}">{{ $office->office }}</option>
                    @endforeach
                </select>
          </div>
        </div>

        <div class="mt-4 flex">
          <div class="w-1/3 mt-1 whitespace-no-wrap">
            District
          </div>
          <div class="w-2/3">
                <select class="text-xl w-full" name="district_id">
                    <option value="">-- Pick One --</option>
                    @foreach(\App\District::all() as $district)
                        <option value="{{ $district->id }}">({{ $district->type }}) {{ $district->name }}</option>
                    @endforeach
                </select>
          </div>
        </div>


        <div class="mt-4 flex">
          <div class="w-1/3 mt-1 whitespace-no-wrap">
            Or City
          </div>
          <div class="w-2/3">
                <select class="text-xl w-full" name="municipality_id">
                    <option value="">-- Pick One --</option>
                    @foreach(\App\Municipality::all() as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
              </select>
          </div>
        </div>

        <div class="mt-4 flex">
          <div class="w-1/3 mt-1 whitespace-no-wrap">
            First Name:
          </div>

          <div class="w-2/3">
              <input type="text" 
                     name="first_name" 
                     placeholder="first_name"
                     class="border-2 px-2 py-1 w-full" />
          </div>

        </div>

        <div class="mt-4 flex">
          <div class="w-1/3 mt-1 whitespace-no-wrap">
            Last Name:
          </div>

          <div class="w-2/3">
              <input type="text" 
                     name="last_name" 
                     placeholder="last_name"
                     class="border-2  px-2 py-1 w-full" />
          </div>
        </div>


        <!--Footer-->

   

        <div class="flex justify-end pt-2">


          <button
            type="button"
            class="px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-100 hover:text-indigo-400 mr-2"
            @click="open = false"
            >Cancel</button>

          <button 
            class="px-4 bg-indigo-500 p-3 rounded-lg text-white hover:bg-indigo-400"
            >Add</button>
          
        </div>

        

      </form>
    </div>



    </div>


    <!--/Dialog -->
  </div><!-- /Overlay -->

</div>

