<?php if (!defined('dir')) define('dir','/office'); ?>

<div id="entity_contactinfo" class="entity-tab-content  {{ ($tab != 'contactinfo') ? 'hidden' : '' }}" style="min-height: 700px;">

    <div class="flex">

        <div class="border-t p-2 text-base w-1/2">
            <div class="text-xl">
                Contact
            </div>

        <table class="text-base mt-2 w-full border-t-4 border-blue">


            <tr class="border-t">
                <td class="p-2 bg-grey-lighter w-16">
                    Emails
                </td>
                <td class="p-2">

                @if((!$entity->email) || (empty(json_decode($entity->email, true))))
                    <span class="text-grey-dark">None</span>
                @else
                    @foreach(json_decode($entity->email) as $theemail)

                        <div class="">
                            {!! ($theemail->main) ? '<i class="fas fa-star mr-2 w-4 text-orange"></i>' : '<i class="fas fa-envelope mr-2 w-4"></i>' !!}{{$theemail->email}}
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
                @if((!$entity->phone) || (empty(json_decode($entity->phone, true))))
                    <span class="text-grey-dark">None</span>
                @else
                    @foreach(json_decode($entity->phone) as $thephone)
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



        </table>
        </div>

        <div class="border-t p-2 text-base w-1/2">
            <div class="text-xl">
                Social
            </div>
            <table class="text-base mt-2 w-full border-t-4 border-blue">
                <tr class="border-t">
                    <td class="p-2 bg-grey-lighter w-1/5">
                        Twitter
                    </td>
                    <td class="p-2">
                        {{ $entity->social_twitter }}
                    </td>
                </tr>
                <tr class="border-t">
                    <td class="p-2 bg-grey-lighter w-1/5">
                        Facebook
                    </td>
                    <td class="p-2">
                        {{ $entity->social_facebook }}
                    </td>
                </tr>
            </table>
        </div>

    </div>



</div>
