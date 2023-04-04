<div class="relative">


    <div class="font-sans font-bold absolute pin-t pin-r 
        @if(!isset($business->occupation))
            -mt-3
        @endif
        ">
        <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/business/edit">
            <button class="text-sm px-2 py-1">
                Edit
            </button>
        </a>
    </div>

  
  @if (isset($business->name))

    <table class="w-full">



        @if($business->occupation)
            <tr class="">
                <td class="p-1 text-grey-dark uppercase text-xs whitespace-no-wrap">
                    Occupation
                </td>
                <td class="p-1">
                    {{  $business->occupation }}
                </td>
            </tr>
        @endif

        @if($business->name)
            <tr class="border-t">
                <td class="p-1 text-grey-dark uppercase text-xs whitespace-no-wrap">
                    Employer
                </td>
                <td class="p-1">

                    
                    
                        {{ $business->name }}

                        <div class="text-grey-dark text-sm">

                            {{ $business->address_1 }}

                            {{ $business->address_2 }}

                            {{ $business->city }} 

                            {{ $business->state  }}

                            {{ $business->zip }}

                            @if($business->zip4)
                            -{{ $business->zip4 }}
                            @endif
                            
                        </div>
                    
                    
                </td>
            </tr>
        @endif


        @if($business->work_phone)
            <tr class="border-t">
                <td class="p-1 text-grey-dark uppercase text-xs whitespace-no-wrap">
                    Work Phone
                </td>
                <td class="p-1">
                    {{ $business->work_phone }}
                    @if($business->work_phone_ext)
                        ext. {{ $business->work_phone_ext }}
                    @endif
                </td>
            </tr>
        @endif

        @if($business->fax)
            <tr class="border-t">
                <td class="p-1 text-grey-dark uppercase text-xs whitespace-no-wrap">
                    Fax
                </td>
                <td class="p-1">
                    {{ $business->fax }}
                </td>
            </tr>
        @endif

        @if($business->web)
            <tr class="border-t">
                <td class="p-1 text-grey-dark uppercase text-xs whitespace-no-wrap">
                    Web
                </td>
                <td class="p-1">
                    {{ $business->web }}
                </td>
            </tr>
        @endif

    </table>

    @endif

</div>