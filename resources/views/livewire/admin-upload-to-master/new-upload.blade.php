<div class="w-full py-4">
    
    <div class="text-sm text-grey-darker w-full border-b-2 uppercase">
        @if ($step_1)
            <div class="float-right">

                @if ($import)
                    @if ($import->municipality)
                        {{ $import->municipality->name }}
                    @endif
                @endif
            </div>
            <span class="text-blue font-bold">1. Upload File <i class="fa fa-check-circle"></i></span>
        @else
            1. Upload File
        @endif
    </div>
    <div class="pt-4 pr-8">

        @if (!$import_id)

        <form action="/admin/uploads" method="post" enctype="multipart/form-data" class="flex-wrap w-3/4 items-center">
            @csrf

                <div class="flex">

                    <select wire:model="state" name="state">
                        <option value="">-- CHOOSE A STATE --</option>
                        @foreach($available_states as $state_option)
                            <option value="{{ $state_option }}">
                                ({{ $state_option }}) x_voters_{{ $state_option }}_master
                            </option>
                        @endforeach
                    </select>

                    @if($state)

                        <div class="flex">

                            <input wire:model="municipality_lookup" id="municipality_lookup" type="text" placeholder="Type to Lookup" class="p-2 border rounded mx-2" />

                            <select wire:model="municipality_id" name="municipality_id" placeholder="Municipality" class="p-1 border rounded-lg w-64 h-8 {{ ($municipality_lookup) ? 'font-bold' : '' }}">
                                <option value="">- Select Municipality -</option>
                                @foreach ($municipalities as $municipality)
                                    <option value="{{ $municipality['id'] }}">
                                        {{ $municipality['name'] }}
                                    </option>
                                @endforeach
                            </select>

                        </div>

                    @endif

                </div>

            @if($state && $municipality_id)
                <div class="flex">

                    <div class="py-2 mr-2">
                        <input type="file" name="fileToUpload" id="fileToUpload" class="border-b">
                        <input type="hidden" name="import_id" value="" />
                    </div>

                    <div class="text-center mx-2 pt-1">
                        <input type="submit" value="Upload" name="submit" class="bg-blue text-white cursor-pointer rounded-full py-2 px-4 text-sm">
                    </div>

                </div>
            @endif

        </form>

        @else

            <div class="text-sm">
                <a target="_blank" href="/admin/uploads/{{ $import->id }}/download">
                    <b>{{ $import->file }}</b>
                </a>
                <br>            
                {{ print_r(fgetcsv(fopen(storage_path().'/app/'.$import->file, 'r'))) }}
            </div>

        @endif



    </div>

    @if ($step_1)
    <div class="text-sm text-grey-darker w-full border-b-2 uppercase mt-8">
        @if ($step_2)
            <span class="text-blue font-bold">2. Match Fields <i class="fa fa-check-circle"></i></span>


        @else
            2. Match Fields
        @endif
    </div>


    <div class="pt-2 pr-8 flex">
        <div class="text-xl text-grey-dark w-1/6"></div>

        <div class="w-5/6">
            @if (!$step_2)


            @if($delimiter)
                <div class="rounded border p-2 px-3 text-blue-dark bg-blue-lightest font-normal whitespace-no-wrap mb-4">
                    Delimiter "{{ $delimiter }}" detected, <span class="font-bold text-lg">Chief.</span>

                    <div class="float-right">
                        <label for="first_has_fields" class="font-normal text-sm">
                            <input type="checkbox" wire:model="first_has_fields" id="first_has_fields" /> First row contains headers
                        </label>
                    </div>

                </div>
            @endif

            <table class="w-full">
                <tr>
                    <th class="pr-4 h-2"><div class="border-b pb-2 mb-2">Database Field</div></th>
                    <th class="pr-4 h-2">
                        <div class="border-b pb-2 mb-2">
                            <div wire:click="guessFields()" class="float-right text-xs text-white text-white bg-blue px-3 py-1 rounded-full cursor-pointer">
                                Guess Fields
                            </div>
                            File Field
                        </div>
                    </th>
                    <th class="pr-4 h-2">
                        <div class="border-b pb-2 mb-2 flex">

                            <div>
                                First Record
                            </div>

                        </div>
                    </th>
                </tr>
                @php
                    ksort($field_map)
                @endphp
                @foreach ($field_map as $field => $val)
                    <tr>
                        <td class="pr-4">
                            {{ $field }}
                            @if (in_array($field, $required))
                                <span class="text-red">*</span>
                            @endif
                        </td>
                        <td class="pr-4">
                            <select wire:model="field_map.{{ $field }}" class="w-full">
                                <option value="">-- DO NOT INSERT --</option>
                                @foreach ($firstrow as $file_field)
                                    <option value="{{ $file_field }}">{{ $file_field }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="pr-4 text-blue-dark">
                            @isset($firstrecord[$field_map[$field]])
                                {{ $firstrecord[$field_map[$field]] }}
                            @endisset
                        </td>
                    </tr>
                @endforeach
            </table>
            <div wire:click="matchFields()" class="cursor-pointer float-right m-2 bg-blue text-white px-3 py-2 rounded-full">
                Match Fields & Save
            </div>
            @else
                <div wire:click="unmatch()" class="cursor-pointer float-right m-2 bg-blue text-white px-3 py-2 rounded-full">
                    Unmatch
                </div>
            @endif
        </div>
    </div>
    @endif

    @if ($step_2)
    <div class="text-sm text-grey-darker w-full border-b-2 uppercase mt-8">
        @if ($step_3)
            <span class="text-blue font-bold">3. Run Import <i class="fa fa-check-circle"></i></span>
        @else
            3. Run Import
        @endif
    </div>
    <div class="pt-4 pr-8 flex">
        <div class="text-xl text-grey-dark w-1/6"></div>
        <div class="w-5/6">

            @if (!$step_3)
            <table class="text-xs w-full">
                <tr>
                    <th>DB Field</th>
                    <th>Record #1</th>
                    <th>Record #2</th>
                    <th>Record #3</th> 
                    <th>Record #4</th>
                    <th>Record #5</th>
                </tr>
                @foreach ($field_map as $field => $val) 
                    <tr class="border-t">
                        <td class="p-1">{{ $field }}</td>
                        @foreach ($firstfive as $row)
                            <td class="text-blue-dark">
                                <div style="opacity:0;animation:fadeIn 1s;animation-delay:{{ ($loop->iteration -1) /2 }}s;animation-fill-mode: forwards;">
                                    @if(!$val)
                                        <div class="bg-grey-lighter p-1 text-red-dark text-xs font-bold">SKIP</div>
                                    @endif
                                    @isset($row[$field])
                                        {{ $row[$field] }}
                                    @endisset
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </table>
            @else
                {{ number_format($import->imported_count) }} Records Imported {{ $import->imported_at->format('n/j/Y g:ia') }}.
            @endif

            <div class="flex w-full items-center mt-6">
                <div class="w-1/6">
                    <div wire:click="clearImport()" class="cursor-pointer
                        @if (!$import->started_at)
                            hidden 
                        @endif
                     float-right m-2 bg-red-light text-white px-3 py-1 rounded-full whitespace-no-wrap">
                        Clear Import
                    </div>
                    @if (!$import->started_at)
                        <div wire:click="startImport()" class="cursor-pointer 
                            @if ($step_3)
                                hidden
                            @endif
                        float-right m-2 bg-blue text-white px-3 py-1 rounded-full">
                            @if (!$failed_import_jobs)
                                Start Import
                            @else
                                Try Again
                            @endif
                        </div>
                    @endif
                   
                </div>
                @if (!$import->imported_at)
                    <div wire:poll.1500ms class="flex w-5/6 items-center">

                        <div class="w-4/5 rounded-full h-4 border">
                            <div class="bg-blue-light h-4 rounded-full" style="width: {{ $percent_imported }}%;"></div>
                        </div>
                        <div class="w-1/5 pl-6 text-blue-light font-bold text-2xl">
                            {{ $percent_imported }}%
                        </div>

                    </div>

                @endif
                
            </div>

            @if($failed_import_jobs)
                @foreach($failed_import_jobs as $key => $failed)

                    <div class="text-red">
                        <div class="py-2 font-bold">
                            Failed at {{ $failed->failed_at }}
                        </div>
                        <div class="text-xs ml-10 text-grey-darker">
                            {!! explode("\n", $failed->exception)[0] !!}
                        </div>
                    </div>

                @endforeach
            @endif

        </div>
    </div>

    @endif

    @if ($step_3)

        <div class="text-sm text-grey-darker w-full border-b-2 uppercase mt-8">
            @if ($step_4)
                <span class="text-blue font-bold">4. Verify Data <i class="fa fa-check-circle"></i></span>
            @else
                4. Verify Data
            @endif
        </div>
        <div class="pt-4 pr-8 flex">
            <div class="text-xl text-grey-dark w-1/6"></div>
            <div class="w-5/6">
            </div>
        </div>

        @if (!$step_4)
            <div class="flex w-full">
                <div class="">
                    {!! $voters->links() !!}
                </div>
                <div class="">
                    <input type="text" wire:model="voters_filter"  class="border" />
                </div>
            </div>
            <table class="text-xs w-full">
                <tr>
                    <th class='px-1'>Voter ID</th>
                    <th class='px-1'>First</th>
                    <th class='px-1'>M</th>
                    <th class='px-1'>Last</th>
                    <th class='px-1'>Gender</th>
                    <th class='px-1'>Party</th>
                    <th class='px-1'>Address</th>
                    <th class='px-1'>House</th>
                    <th class='px-1'>Senate</th>
                    <th class='px-1'>Gov</th>
                    <th class='px-1'>Cong</th>
                </tr>
                @foreach ($voters as $voter) 
                    <tr class="border-t text-grey-dark">
                        <td style="padding: 0px 1px;">{{ $voter->id }}</td>
                        <td style="padding: 0px 1px;">{{ $voter->first_name }}</td>
                        <td style="padding: 0px 1px;">{{ $voter->middle_name }}</td>
                        <td style="padding: 0px 1px;">{{ $voter->last_name }}</td>
                        <td style="padding: 0px 1px;">{{ $voter->gender }}</td>
                        <td style="padding: 0px 1px;">{{ $voter->party }}</td>
                        <td style="padding: 0px 1px;">{{ $voter->full_address }}</td>
                        <td style="padding: 0px 1px;">
                            @if ($voter->houseDistrict)
                                {{ $voter->houseDistrict->name }}
                            @endif
                        </td>
                        <td style="padding: 0px 1px;">
                            @if ($voter->senateDistrict)
                                {{ $voter->senateDistrict->name }}
                            @endif
                        </td>
                        <td style="padding: 0px 1px;">{{ $voter->governor_district }}</td>
                        <td style="padding: 0px 1px;">{{ $voter->congress_district }}</td>
                    </tr>
                @endforeach
            </table>
        @else
            Verified, <span class="font-bold text-lg">Chief.</span>
        @endif

        <div class="flex w-full items-center mt-2">
            <div class="w-1/6">
                <div wire:click="clearVerify()" class="cursor-pointer
                    @if (!$step_4)
                        hidden
                    @endif
                 float-right m-2 bg-red-light text-white px-3 py-1 rounded-full whitespace-no-wrap">
                    Clear Verify
                </div>
                @if (!$import->verified_at)
                    <div wire:click="startVerify()" class="cursor-pointer 
                        @if ($step_4)
                            hidden
                        @endif
                    float-right m-2 bg-blue text-white px-3 py-1 rounded-full">
                        @if (!$failed_verify_jobs)
                            Start Verify
                        @else
                            Try Again
                        @endif
                    </div>
                @endif
               
            </div>
            @if (!$import->verified_at)
                <div wire:poll.1500ms class="flex w-5/6 items-center">

                    <div class="w-4/5 rounded-full h-4 border">
                        <div class="bg-blue-light h-4 rounded-full" style="width: {{ $percent_verified }}%;"></div>
                    </div>
                    <div class="w-1/5 pl-6 text-blue-light font-bold text-2xl">
                        {{ $percent_verified }}%
                    </div>

                </div>

            @endif
            
        </div>

        

    @endif

    @if ($step_4)

        <div class="text-sm text-grey-darker w-full border-b-2 uppercase mt-8">
            @if ($step_4)
                <span class="text-blue font-bold">5. Add to Voter Master File <i class="fa fa-check-circle"></i></span>
            @else
                5. Add to Voter Master File
            @endif
            
        </div>
        <div class="pt-4 pr-8 flex">
            <div class="text-xl text-grey-dark w-1/6"></div>
            <div class="w-5/6">
            </div>
        </div>

        @if (!$step_5)
            <div class="flex w-full mt-6">

                <div class="w-1/6">
                    
                    @if (!$import->completed_at && !$import->reverting)
                        <div wire:click="startInsert()" class="cursor-pointer 
                            @if ($step_5)
                                hidden
                            @endif
                        float-right m-2 bg-blue text-white px-3 py-1 rounded-full">
                            @if (!$failed_insert_jobs)
                                Start Insert
                            @else
                                Try Again
                            @endif
                        </div>
                    @endif
                   
                </div>

                @if (!$import->completed_at)

                    <div wire:poll.1500ms>

                        @if($import->reverting)
                            <div class="p-2 font-bold text-red">Reverting...</div>
                        @endif

                        <div class="flex w-5/6 items-center mb-4">
                            <div class="w-4/5 rounded-full h-4 border">
                                <div class="{{ (!$import->reverting) ? 'bg-blue-light' : 'bg-red' }} h-4 rounded-full" style="width: {{ $percent_inserted }}%;"></div>
                            </div>
                            <div class="w-1/5 pl-6 {{ (!$import->reverting) ? 'text-blue-light' : 'text-red' }} font-bold text-2xl">
                                {{ $percent_inserted }}%
                            </div>
                        </div>


                        <!-- LITTLE SUMMARY TABLE (SHOW WHILE INSERTING) -->
                        <div class="table {{ ($percent_inserted > 0) ? 'opacity-100' : 'opacity-25' }}">
                            <div class="table-row">
                                <div class="table-cell border-b p-2 w-24 text-right font-bold">
                                    {{ number_format($import->new_count) }} 
                                </div>
                                <div class="table-cell border-b p-2">
                                    were added to <b>{{ $import->municipality->name }}</b>.
                                </div>
                            </div>
                            <div class="table-row">
                                <div class="table-cell border-b p-2 w-24 text-right font-bold">
                                    {{ number_format($import->updated_count) }} 
                                </div>
                                <div class="table-cell border-b p-2">
                                    were existing in <b>{{ $import->municipality->name }}</b>.

                                    <div class="flex">
                                        <div class="p-2 w-24 text-right font-bold">
                                            {{ number_format($import->changed_count) }} 
                                        </div>
                                        <div class="p-2">
                                            were updated with changes
                                        </div>
                                    </div>

                                    <div class="flex">
                                        <div class="p-2 w-24 text-right font-bold">
                                            {{ number_format($import->updated_count - $import->changed_count) }} 
                                        </div>
                                        <div class="p-2">
                                            were updated with no changes (replaced)
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="table-row">
                                <div class="table-cell border-b p-2 w-24 text-right font-bold">
                                    {{ number_format($import->new_count + $import->updated_count) }} 
                                </div>
                                <div class="table-cell border-b p-2">
                                    Total <b>{{ $import->municipality->name }}</b>.
                                </div>
                            </div>
                        </div>
                        <!-- END LITTLE SUMMARY TABLE -->

                    </div>

                @endif
                
            </div>
        @else

       <!-- FINAL SUMMARY TABLE -->
        <div class="table">
            <div class="table-row">
                <div class="table-cell border-b p-2 w-24 text-right font-bold">
                    {{ number_format($import->new_count) }} 
                </div>
                <div class="table-cell border-b p-2">
                    were added to <b>{{ $import->municipality->name }}</b>.
                </div>
            </div>
            <div class="table-row">
                <div class="table-cell border-b p-2 w-24 text-right font-bold">
                    {{ number_format($import->updated_count) }} 
                </div>
                <div class="table-cell border-b p-2">
                    were existing in <b>{{ $import->municipality->name }}</b>.

                    <div class="flex">
                        <div class="p-2 w-24 text-right font-bold">
                            {{ number_format($import->changed_count) }} 
                        </div>
                        <div class="p-2">
                            were updated with changes.
                            
                            <button wire:click('show_changes') class="rounded-lg bg-grey-lighter text-xs border px-2 py-1">
                                Show Changes
                            </button>

                            <span class="text-xs text-blue-dark">
                                Not working but we could do it with _changed table
                            </span>

                        </div>
                    </div>

                    <div class="flex">
                        <div class="p-2 w-24 text-right font-bold">
                            {{ number_format($import->updated_count - $import->changed_count) }} 
                        </div>
                        <div class="p-2">
                            were updated with no changes (replaced)
                        </div>
                    </div>

                </div>
            </div>
            <div class="table-row">
                <div class="table-cell border-b p-2 w-24 text-right font-bold">
                    {{ number_format($import->new_count + $import->updated_count) }} 
                </div>
                <div class="table-cell border-b p-2">
                    Total <b>{{ $import->municipality->name }}</b>.
                </div>
            </div>
            <div class="table-row">
                <div class="table-cell border-b p-2 w-24 text-right font-bold">
                    <span class="text-blue">{{ number_format($newly_created_count) }}</span>
                </div>
                <div class="table-cell border-b p-2">
                    VoterMaster: created_at >= <span class="ext-gray">{{ $import->started_at }} (started_at)</span>
                     <!-- + <span class="text-grey-dark">city_code = {{ $import->municipality_id }}</span> -->
                </div>
            </div>
            <div class="table-row">
                <div class="table-cell border-b p-2 w-24 text-right font-bold">
                    <span class="text-blue">{{ number_format($newly_updated_count) }}</span>
                </div>
                <div class="table-cell border-b p-2">
                    VoterMaster: updated_at >= <span class="text-gray">{{ $import->started_at }} (started_at)</span> + created_at < {{ $import->started_at }}
                </div>
            </div>
        </div>
        <!-- END FINAL SUMMARY TABLE -->


            <div wire:click="revertInsert()" class="cursor-pointer
                @if (!$step_5)
                    hidden
                @endif
             float-right m-2 bg-red-light text-white px-3 py-1 rounded-full whitespace-no-wrap">
                Revert Insert
            </div>
            <br clear="all" />

        @endif

    @endif

    @if ($step_5)

    <div class="text-sm text-grey-darker w-full border-b-2 uppercase mt-4">6. Update Slices</div>
    <div class="pt-4 pr-8 flex">
        <div class="text-xl text-grey-dark w-1/6"></div>
        <div class="w-5/6">
        </div>
    </div>

    @endif

    @if (request('debug'))
    <pre>
        {{ print_r($firstrow) }}
        {{ print_r($field_map) }}
    </pre>
    @endif

</div>
