@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')


@endsection

@section('style')

  @livewireStyles

@endsection

@section('main')


<div class="w-full mb-4 pb-2">

    <div class="text-xl border-b-4 border-red py-2 flex">

        <div class="flex-grow pt-2">
            Accounts ({{ $accounts->count() }})
        </div>

        <div class="flex">

          <div class="mr-2 flex">

            @foreach($state_options as $state)

              <a href="?state={{ $state }}">
                <div class="text-center rounded text-xl font-bold px-2 py-1 mx-1 w-12 border
                  @if(isset($_GET['state']) && ($_GET['state'] == $state))
                    bg-blue-dark text-white
                  @else
                    bg-grey-lightest text-grey-darker 
                  @endif
                  ">
                  {{ $state}}
                </div>
              </a>

            @endforeach

            <a href="?state=">
              <div class="text-center rounded text-xl font-bold px-2 py-1 mx-1 w-12 border
                @if(!isset($_GET['state']) || ($_GET['state'] == ''))
                  bg-blue-dark text-white
                @else
                  bg-grey-lightest text-grey-darker 
                @endif
                ">
                All
              </div>
            </a>

          </div>


        </div>

        <div class="mr-2">
          <input type="text" id="filter-input" onkeyup="filterTable()" class="border-2 mr-2 rounded-lg p-2 text-base" placeholder="Filter Accounts" />
        </div>

        <a href="/admin/accounts/new">
            <button class="rounded-lg bg-blue float-right text-white px-4 py-2 text-base">
                Create New
            </button>
        </a>

    </div>  



<div x-data="{ open: false }">

  <button class="rounded-lg bg-black text-white px-2 py-1 text-sm float-right mt-2" 
          @click="open = !open">
    Show/Hide Admins
  </button>

<table class="w-full text-sm">

    <tr class="text-xs cursor-pointer border-b-4 bg-grey-lighest">


        <th class="font-normal p-2 w-64" >
            Account
        </th>

        <th class="font-normal p-2 w-8" >
            Users
        </th>

        <th class="font-normal p-2">
            Teams
        </th>

    </tr>

    <tbody id="accountTable">
    @foreach($accounts as $theaccount)
    <tr class="line-div border-b-2 border-grey-light {{ ($theaccount->me) ? 'bg-orange-lightest' : '' }}">


        <td class="p-2 pb-4 align-top whitespace-no-wrap font-bold">
            <a href="/admin/accounts/{{ $theaccount->id }}/edit"><span class="line-name-div text-xl">{{ $theaccount->name }}</span></a>

            <div class="text-sm text-grey font-normal">
              <div>
                {{ $theaccount->address }}
              </div>
              <div>
                {{ ($theaccount->city) ? $theaccount->city.', ' : ''  }}{{ $theaccount->state }} {{ $theaccount->zip }}
              </div>
            </div>

            <div>
              <div class="text-xs mt-2 mb-1 font-normal text-grey-dark">
                Payment link: <span class="font-bold">{{ $theaccount->payment_simple }}</span>
              </div>
              <div>
                <input type="text" value="https://communityfluency.com/payments/{{ $theaccount->payment_simple }}" class="border p-1 font-normal text-xs w-full" />
              </div>
            </div>

            <div class="py-2">
              @livewire('admin.active-switch', ['model_type' => 'Account',
                                                'model_id' => $theaccount->id,
                                                'size' => 6])
            </div>

        </td>


        <td class="p-2 pb-4 align-top w-2/6 whitespace-no-wrap">
            {{ $theaccount->users()->count() }}
        </td>

        <td class="p-2 pb-8 align-top w-2/6 whitespace-no-wrap">

            @if($theaccount->teams->first())
                @foreach($theaccount->teams as $theteam)

                    <div class="{{ (!$loop->last) ? 'border-b' : '' }} border-dashed border-grey-light">

                        <div class="flex">

                            <div class="w-24 p-1">
                              @livewire('admin.active-switch', ['model_type' => 'Team',
                                                                'model_id' => $theteam->id,
                                                                'size' => 4])
                            </div>

                            <div class="w-32 border-l p-1">
                                <span class="mx-2 text-grey-dark w-6">
                                  @if(!$theteam->data_folder_id)
                                    --
                                  @else
                                    {{ $theteam->data_folder_id }}
                                  @endif
                                </span>
                                <span class="">{{ $theteam->app_type }}</span>
                             </div>

                            <div class="flex-grow truncate p-1">
                                <a href="/admin/accounts/{{ $theaccount->id }}/teams/{{ $theteam->id}}/edit">
                                  {{ $theteam->name }}
                                </a>

                                <div class="text-blue py-1" x-show="open">

                                  @php

                                    $admins = $theteam->usersAll->filter(function ($item)  {
                                                if ($item->permissions) {
                                                    return $item->permissions->admin;
                                                }
                                            });
                                  @endphp

                                  @if(!$admins->first())

                                    <span class="py-1 px-2 bg-grey-lighter text-grey-darker rounded text-xs">No Admins</span>

                                  @else

                                    @foreach($admins as $admin)
                                        <div>
                                            <a href="/admin/accounts/{{ $theaccount->id }}/users/{{ $admin->id }}/edit" class="text-blue">
                                                <i class="fas fa-user-cog"></i>
                                                {{ $admin->name }}
                                            </a>
                                        </div>
                                    @endforeach

                                  @endif

                                </div>



                            </div>

                            <div class="w-32 truncate p-1">
                              @if(!$theteam->db_slice)
                                <span class="text-red">No Voter Slice</span>
                              @else
                                <span class="">{{ $theteam->db_slice }}</span>
                              @endif
                            </div>

                        </div>

                    </div>

                @endforeach

                

            @endif
        </td>

    </tr>
    @endforeach
    </tbody>

</table>

</div>



</div>



@endsection

@section('javascript')

  @livewireScripts

<script type="text/javascript">


function filterTable()
{
  input = document.getElementById('filter-input');
  filter_string = input.value.toUpperCase();
  lines = document.getElementsByClassName('line-div');
  group_names = document.getElementsByClassName('line-name-div');

  for (i = 0; i < lines.length; i++) {

    group_name = group_names[i].innerHTML.trim().toUpperCase();

    if (group_name.indexOf(filter_string) > -1) {

      lines[i].style.display = "";

    } else {

      lines[i].style.display = "none";

    }
  }

}


$(document).ready(function() {

    $('#filter-input').focus();

    $(document).on("click", ".add-group-to-cat", function() {
        var id = $(this).attr('data-cat');
        if ($("#"+id).hasClass('hidden')) {
          $("#"+id).removeClass('hidden');
        } else {
          $("#"+id).addClass('hidden');
        }
    });

  $(window).bind('scroll', function () {
      if ($(window).scrollTop() > 200) {
          $('#menu').addClass('fixed');
          $('#menu').css({display: 'block'});
      } else {
          $('#menu').removeClass('fixed');
          $('#menu').css({top: '10px'});
      }
  });

  $(document).on("click", "#add-category", function() {
      $(this).toggleClass('hidden');
      $('#add-category-form').toggleClass('hidden');
      $('#new-category-form-name').focus();
  });

});


</script>


@endsection