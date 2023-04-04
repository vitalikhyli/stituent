@extends('office.base')

@section('title')
  Assign Groups
@endsection

@section('breadcrumb')

  {!! Auth::user()->Breadcrumb($person->name, 'Group', 'level_2') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')

<form method="POST" action="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/instance/save">
    {{ csrf_field() }}


<div class="text-2xl font-sans border-b-4 border-blue pb-2">

	<i class="fas fa-flag mr-2"></i>Assign Group to {{ $person->full_name }}
</div>



<table class="w-full">

  <tr class="border-b">
    <td class="bg-grey-lighter p-2 w-32 whitespace-no-wrap text-sm"">
      Category
    </td>
    <td class="p-2">
      <span class="capitalize">{{ $category->name }}</span>
    </td>
  </tr>

  @if(!isset($mode_external))

  @if($group_pivots_existing->first())
      <tr class="border-b">
        <td class="bg-grey-lighter p-2 align-top w-32 whitespace-no-wrap text-sm">
          Current Groups
        </td>
        <td class="p-2 align-top">

            @foreach($group_pivots_existing as $the_group_pivot)

                <div id="{{ $the_group_pivot->group->id }}_line" class="group_option_line {{ ($loop->last) ? '' : 'border-b' }} p-2 flex bg-orange-lightest">

                  <div class="w-1/3">
                    <label for="{{ $the_group_pivot->group->id }}">
                      <input checked type="checkbox" id="{{ $the_group_pivot->group->id }}" class="group_option" name="keep_group_{{ $the_group_pivot->group->id }}" value="true" />
                      <span class="ml-4 font-normal">{{ $the_group_pivot->group->name }}</span>
                      <div class="ml-8 text-xs text-grey-dark font-normal">
                        {{ \Carbon\Carbon::parse($the_group_pivot->created_at)->toDateString() }}
                        @if($the_group_pivot->created_by)
                          by {{ $the_group_pivot->created_by_name }}
                        @endif
                      </div>
                    </label>

                    <input class="hidden" name="existing_group_{{ $the_group_pivot->group->id }}" value="true" />

                  </div>

                  <div class="w-2/3 flex" id="pivot_{{ $the_group_pivot->group->id }}">
                    
                      @if($the_group_pivot->group->cat->has_position)
                         <div class="w-1/3 px-1">
                            <select name="position_{{ $the_group_pivot->group->id }}" class="w-full">
                               <option {{ ($the_group_pivot->position=="") ? 'selected' : '' }} value="">(Position)</option>

                               <option {{ (strtolower($the_group_pivot->position)=="supports") ? 'selected' : '' }} value="Supports">Supports</option>

                               <option {{ (strtolower($the_group_pivot->position)=="undecided") ? 'selected' : '' }} value="Undecided">Undecided</option>

                               <option {{ (strtolower($the_group_pivot->position)=="concerned") ? 'selected' : '' }} value="Concerned">Concerned</option>

                               <option {{ (strtolower($the_group_pivot->position)=="opposed") ? 'selected' : '' }} value="Opposed">Opposed</option>

                            </select>
                         </div>
                      @endif

                      @if($the_group_pivot->group->cat->has_title)
                         <div class="w-1/3 px-1">
                            <input value="{{ ($the_group_pivot->title) ? $the_group_pivot->title : '' }}" type="text" name="title_{{ $the_group_pivot->group->id }}" class="w-full rounded-lg border p-2 mr-2" placeholder="Title" />
                         </div>
                      @endif

                      @if($the_group_pivot->group->cat->has_notes)
                         <div class="w-2/3 px-1">
                            <input value="{{ ($the_group_pivot->notes) ? $the_group_pivot->notes : '' }}" type="text" name="notes_{{ $the_group_pivot->group->id }}" class="w-full rounded-lg border p-2" placeholder="Notes" />
                         </div>
                      @endif

                  </div>

                </div>
            @endforeach

        </td>
      </tr>
      @endif


    @if($group_pivots_existing_archived->first())
      <tr class="border-b">
        <td class="bg-grey-darkest text-white p-2 align-top w-32 whitespace-no-wrap text-sm">
          Archived Groups
        </td>
        <td class="p-2 align-top bg-grey-darker text-white">

            <div id="toggle_archived_groups" class="text-sm cursor-pointer">
                Show / Hide
            </div>

            <div id="archived_groups" class="hidden mt-2">
            @foreach($group_pivots_existing_archived as $the_group_pivot)

                <div id="{{ $the_group_pivot->group->id }}_line" class="group_option_line {{ ($loop->last) ? '' : 'border-b' }} p-2 flex">

                  <div class="w-1/3">
                    <label for="{{ $the_group_pivot->group->id }}">
                      <input checked type="checkbox" id="{{ $the_group_pivot->group->id }}" class="group_option" name="keep_group_{{ $the_group_pivot->group->id }}" value="true" />
                      <span class="ml-4 font-normal">{{ $the_group_pivot->group->name }}</span>
                    </label>

                    <input class="hidden" name="existing_group_{{ $the_group_pivot->group->id }}" value="true" />

                  </div>

                  <div class="w-2/3 flex" id="pivot_{{ $the_group_pivot->group->id }}">
                    
                      @if($the_group_pivot->group->cat->has_position)
                         <div class="w-1/3 p-1">
                            <select name="position_{{ $the_group_pivot->group->id }}" class="w-full">
                               <option {{ ($the_group_pivot->position=="") ? 'selected' : '' }} value="">(Position)</option>

                               <option {{ (strtolower($the_group_pivot->position)=="supports") ? 'selected' : '' }} value="Supports">Supports</option>

                               <option {{ (strtolower($the_group_pivot->position)=="undecided") ? 'selected' : '' }} value="Undecided">Undecided</option>

                               <option {{ (strtolower($the_group_pivot->position)=="concerned") ? 'selected' : '' }} value="Concerned">Concerned</option>

                               <option {{ (strtolower($the_group_pivot->position)=="opposed") ? 'selected' : '' }} value="Opposed">Opposed</option>

                            </select>
                         </div>
                      @endif

                      @if($the_group_pivot->group->cat->has_title)
                         <div class="w-1/3 px-1">
                            <input value="{{ ($the_group_pivot->title) ? $the_group_pivot->title : '' }}" type="text" name="title_{{ $the_group_pivot->group->id }}" class="w-full rounded-lg border p-2 bg-transparent" placeholder="Title" />
                         </div>
                      @endif

                      @if($the_group_pivot->group->cat->has_notes)
                         <div class="w-2/3 px-1">
                            <input value="{{ ($the_group_pivot->notes) ? $the_group_pivot->notes : '' }}" type="text" name="notes_{{ $the_group_pivot->group->id }}" class="w-full rounded-lg border p-2 bg-transparent" placeholder="Notes" />
                         </div>
                      @endif

                  </div>

                </div>
            @endforeach
            </div>

        </td>
      </tr>
      @endif

  @endif


  @if($groups_available->count() > 0)
  <tr class="border-b">
    <td class="bg-grey-lighter p-2 align-top w-32 whitespace-no-wrap text-sm">
      Add Groups
    </td>
    <td class="p-2 align-top">

        @foreach($groups_available as $thegroup)
            <div id="{{ $thegroup->id }}_line" class="group_option_line {{ ($loop->last) ? '' : 'border-b' }} p-2 flex">

              <div class="w-1/3">
                <label for="{{ $thegroup->id }}">
                  <input type="checkbox" id="{{ $thegroup->id }}" class="group_option" name="group_{{ $thegroup->id }}" value="true" />
                  <span class="ml-4 font-normal">{{ $thegroup->name }}</span>
                </label>
              </div>

              <div class="w-2/3 flex hidden" id="pivot_{{ $thegroup->id }}">
                
                  @if($thegroup->cat->has_position)
                     <div class="w-1/3 p-2">
                        <select name="position_{{ $thegroup->id }}" class="w-full">
                           <option value="">(Position)</option>
                           <option value="Supports">Supports</option>
                           <option value="Undecided">Undecided</option>
                           <option value="Concerned">Concerned</option>
                           <option value="Opposed">Opposed</option>
                        </select>
                     </div>
                  @endif

                  @if($thegroup->cat->has_title)
                     <div class="w-1/3">
                        <input type="text" name="title_{{ $thegroup->id }}" class="w-full rounded-lg border p-2 mr-2" placeholder="Title" />
                     </div>
                  @endif

                  @if($thegroup->cat->has_notes)
                     <div class="w-2/3">
                        <input type="text" name="notes_{{ $thegroup->id }}" class="w-full rounded-lg border p-2" placeholder="Notes" />
                     </div>
                  @endif

              </div>

            </div>
        @endforeach

    </td>
  </tr>
  @endif
 

</table>

 
    <div class="float-right text-base mt-2">

      <input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 bg-blue text-white text-center"/>

      <input formaction="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/instance/save/close" type="submit" name="update" value="Save & Close" class="rounded-lg px-4 py-2 bg-blue-darker text-white text-center ml-2"/>

      <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}">
        <button type="button" name="update" class="rounded-lg px-4 py-2 border bg-grey-dark text-white text-center ml-2"/>
          Cancel
        </button>
      </a>

    </div>

</form>

@endsection

@section('javascript')

<script type="text/javascript">

    $(document).on("click", "#toggle_archived_groups", function() {
        $('#archived_groups').toggleClass('hidden');
    });

    $(document).on("click", ".group_option", function() {

        var id = $(this).attr('id');

        // $('.group_option_line').removeClass('bg-orange-lightest');


        if ($("[id="+id+"]").is(":checked")) {

            $('#'+id+'_line').addClass('bg-orange-lightest');
            $('#pivot_'+id).removeClass('hidden');

        } else {

            $('#'+id+'_line').removeClass('bg-orange-lightest');
            $('#pivot_'+id).addClass('hidden');
            // $('#position_'+id).val("");
            // $('#title_'+id).val("");
            // $('#notes_'+id).val("");

        }
    });

</script>

@endsection
