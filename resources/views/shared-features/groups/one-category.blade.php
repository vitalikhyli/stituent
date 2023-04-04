<div category-id="{{ $thecategory->id }}" class="category-div mb-6">

  
   




      <a name="{{ $thecategory->id }}"></a>

      <form method="POST" action="/{{ Auth::user()->team->app_type }}/groups/new">
        {{ csrf_field() }}

        <div>
          <div class="flex text-sm tracking-wide text-left uppercase pb-1 border-b-2 border-grey">
            <div class="flex-1">
              {{ $thecategory->name }}
              ({{ $thecategory->groups_count }})

            </div>


            @if($thecategory->can_edit)
                <a href="/{{ Auth::user()->team->app_type }}/categories/{{ $thecategory->id }}/edit">
                    <button type="button" class="text-xs rounded-lg bg-grey-lighter hover:bg-blue hover:text-white text-grey-dark px-2 py-1">
                      Edit Category
                    </button>
                </a>
            @endif

            <a onClick="return confirm('Are you sure you want to archive all {{ $thecategory->groups_count }} Groups in {{ $thecategory->name }}?')" href="/{{ Auth::user()->team->app_type }}/categories/{{ $thecategory->id }}/archive">
                    <button type="button" class="text-xs rounded-lg bg-grey-lighter hover:bg-blue hover:text-white text-grey-dark px-2 py-1 mx-2">
                      Archive All
                    </button>
                </a>


              <button type="button" data-cat="{{ $thecategory->id }}" class="add-group-to-cat bg-grey-light px-2 py-1 rounded-lg text-xs ml-2 hover:bg-blue-dark">
                Add Group
              </button>


          </div>


            <div id="{{ $thecategory->id }}" class="hidden text-left uppercase text-center py-2 bg-grey-lighter px-2 border-b">
              <input type="text" placeholder="New" name="name" class="rounded-lg border p-2" />
              <input type="hidden" name="category_id" value="{{ $thecategory->id }}" />
              <input type="submit" class="bg-blue text-white p-2 rounded-lg ml-1" value="Add New" />
            </div>
         
        </div>

        </form>

        @foreach($thecategory->subCategories() as $thesub)
         <div class="ml-8 mt-4">
            @include('shared-features.groups.one-category', ['thecategory' => $thesub])
         </div>
      @endforeach

        <div class="mt-2 text-sm">
          @if($thecategory->groups_count <= 0)

            <span class="category-div p-2 text-grey">There are no active groups in this category.</span>

          @else

            @foreach ($thecategory->groups as $thegroup)

              <div data-category="{{ $thecategory->id }}" class="legislation group-div ml-2 pt-2 flex hover:bg-grey-lightest group" year="{{ $thegroup->created_at->format('Y') }}">

                <div class="flex w-full">
                  <div class="w-12 mr-2 whitespace-no-wrap">
                    <a href="/{{ Auth::user()->team->app_type }}/groups/{{ $thegroup->id }}/edit" class="text-grey-darker">
                      <button type="button" class="border text-grey px-2 py-1 rounded-lg text-xs ml-2 hover:bg-blue-dark hover:text-white mr-2 ">
                        Edit
                      </button>
                    </a>
                  </div>
                  <div class="group-name-div flex w-full mr-2">
                    <div class="w-1/2">
                      <a href="/{{ Auth::user()->team->app_type }}/groups/{{ $thegroup->id }}">
                        {{ $thegroup->name }}
                      </a>
                    </div>
                    <div class="w-1/2 text-grey-dark">
                    

                      @if (!$thegroup->archived_at)
                        <a href="/{{ Auth::user()->team->app_type }}/groups/{{ $thegroup->id }}/archive" class="border rounded-full mx-2 px-2 float-right opacity-0 group-hover:opacity-100 hover:bg-white">
                          Archive
                        </a>
                      @else
                        <a href="/{{ Auth::user()->team->app_type }}/groups/{{ $thegroup->id }}/archive/reverse" class="border rounded-full mx-2 px-2 float-right opacity-0 group-hover:opacity-100 hover:bg-white">
                          Un-Archive
                        </a>
                      @endif
                      @if (Str::contains($thecategory->name, 'Issue'))
                        <a href="/{{ Auth::user()->team->app_type }}/groups/{{ $thegroup->id }}/convert-to-legislation" class="border rounded-full mx-2 px-2 float-right opacity-0 group-hover:opacity-100 hover:bg-white">
                          Convert to Legislation
                        </a>
                      @endif

                    </div>


                  </div>
                  <div class="w-24 whitespace-no-wrap text-right">
                    @if($thegroup->people_count > 0)
                      <a href="/{{ Auth::user()->team->app_type }}/groups/{{ $thegroup->id }}">
                        <button type="button" class="bg-blue text-white px-2 py-1 rounded-lg text-xs hover:bg-blue-dark">
                        <i class="fas fa-search"></i> see all {{ $thegroup->people_count }}  
                        </button>
                      </a>
                    @else
                      <a href="/{{ Auth::user()->team->app_type }}/groups/{{ $thegroup->id }}">
                        <button type="button" class="bg-grey text-white px-2 py-1 rounded-lg text-xs hover:bg-grey-dark">
                        <i class="fas fa-plus"></i> add people  
                        </button>
                      </a>
                    @endif
                  </div>

                </div>


              </div>

              <div class="pl-16 pr-24 whitespace-normal border-b pb-1 text-grey-dark">
                  {{ mb_strimwidth($thegroup->notes, 0, 250, '...') }}
              </div>

            @endforeach



          @endif
        </div>

      
</div>

      

