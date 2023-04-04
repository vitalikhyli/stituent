<?php if (!defined('dir')) define('dir','/u'); ?>

<!-- <div id="person_contactinfo" class="person-tab-content  {{ ($tab != 'contactinfo') ? 'hidden' : '' }}" style="min-height: 700px;">
 -->

<table class="text-base w-full border-b">

    <tr class="border-t">
        <td class="p-2 bg-grey-lighter w-16">
            Emails
        </td>
        <td class="p-2">

        @if($person->primary_email)
            <div class="text-blue font-semibold">
               <i class="fas fa-envelope mr-2 w-4"></i>
               {{ $person->primary_email}}
                <span class="text-grey-dark text-xs">(Primary)</span>
            </div>
        @endif

        @if($person->work_email)
            <div class="">
               <i class="fas fa-envelope mr-2 w-4"></i>
               {{ $person->work_email}}
               <span class="text-grey-dark text-xs">(Work)</span>
            </div>
        @endif

        @if((!$person->other_emails) || (empty(json_decode($person->other_emails, true))))
            <span class="text-grey-dark">None</span>
        @else

            @foreach(json_decode($person->other_emails) as $theemail)
                <div class="">
                    <i class="fas fa-envelope mr-2 w-4"></i>
                    {{$theemail->email}}
                    @if($theemail->notes)
                        <span class="text-grey-dark text-xs">({{$theemail->notes}})</span>
                    @endif
                </div>
            @endforeach
        @endif
        </td>
    </tr>



    <tr class="border-t">
        <td class="p-2 bg-grey-lighter w-16">
            Phones
        </td>
        <td class="p-2">
        @if((!$person->phone) || (empty(json_decode($person->phone, true))))
            <span class="text-grey-dark">None</span>
        @else
            @foreach(json_decode($person->phone) as $thephone)
                <div class="">
                    {!! ($thephone->main) ? '<i class="fas fa-star mr-2 w-4 text-orange"></i>' : '<i class="fas fa-phone mr-2 w-4"></i>' !!}{{$thephone->phone}}
                    @if($thephone->notes)
                    <span class="text-grey-dark text-xs">({{$thephone->notes}})</span>
                    @endif
                </div>
            @endforeach
        @endif
        </td>
    </tr>


    <tr class="border-t">
        <td class="p-2 bg-grey-lighter w-16">

            Email Lists
            
            @if(IDisPerson($person->id))
                @if ($person->groups->where('category_id', $email_list_cat_id)->count() <
                \App\Group::where('category_id', $email_list_cat_id)->where('team_id',Auth::user()->team->id)->count())
                    <a href="{{dir}}/constituents/{{ $person->id }}/category/{{ $email_list_cat_id }}/new">
                        <button class="btn_newgroup rounded-lg bg-blue border text-white text-xs px-2 py-1 my-1 cursor-pointer"><i class="fas fa-plus"></i></button>
                    </a>
                @endif
            @endif

        </td>
        <td class="p-2">

            @if(IDisPerson($person->id))
            @if($person->groups->where('category_id', $email_list_cat_id)->count() <= 0)

                <span class="text-grey-dark">None</span>

            @else

                @foreach($person->groups->where('category_id', $email_list_cat_id) as $thegroup)


                    <a href="{{dir}}/groups/instance/{{ $thegroup->pivot->id }}">
                        <div class="p-2 {{ (!$loop->last) ? 'border-b' :'' }} hover:bg-grey-lightest">

                            <div class="float-right">
                                Edit
                            </div>


                            <i class="fas fa-flag mr-2 w-4"></i> {{ $thegroup->name }}

                            @if(isset($thegroup->pivotdata->position))
                                <div class="ml-8 text-grey-darkest">
                                    Position: <span class="font-bold capitalize">{{ $thegroup->pivotdata->position }}</span>
                                </div>
                            @endif

                            @if(isset($thegroup->pivotdata->notes))
                                <div class="ml-8 text-grey-darker">
                                    {{ $thegroup->pivotdata->notes }}
                                </div>
                            @endif

                        </div>
                    </a>

                @endforeach

            @endif
            @endif
            
           

          

        </td>
    </tr>

    <tr class="border-t">
        <td class="p-2 bg-grey-lighter w-1/5">
            Twitter
        </td>
        <td class="p-2">
            {{ $person->social_twitter }}
        </td>
    </tr>
<!--     <tr class="border-t">
        <td class="p-2 bg-grey-lighter w-1/5">
            Facebook
        </td>
        <td class="p-2">
            {{ $person->social_facebook }}
        </td>
    </tr> -->

</table>
        