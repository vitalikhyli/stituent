@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')


	{!! Auth::user()->Breadcrumb('Import', 'import', 'level_1') !!}


@endsection

@section('style')

@endsection

@section('main')



<div class="text-xl border-b-4 border-red py-2">

    <div x-data="{ open: false }" class="p-2 text-right float-right text-base">

        <button type="button" @click="open = !open" class="text-red">
            <span x-show="!open">Delete Slice</span>
            <span x-show="open">Cancel</span>
        </button>

        <div
            x-show="open"
            @click.away="open = false"
            class="text-left bg-white border-red border-2 p-4 absolute -mt-12"
            style="margin-left:-350px;width:300px;"
        >
            <div>
                <span class="font-bold">Are you sure?</span>
                This will not delete the voter file table, just the row in the slices table.
            </div>

            <div class="pt-2">

                <a href="/admin/slices/{{ $slice->id }}/delete">
                    <button class="rounded-lg bg-red text-white px-2 py-1 text-sm">Yes, Delete</button>
                </a>

                <button class="rounded-lg bg-grey-lighter text-black px-2 py-1 text-sm border mt-2"
                        @click="open = false">No, Cancel</button>

            </div>

        </div>
    </div>

    Edit Slice
</div>  


<div class="w-full">

    <form action="/admin/slices/{{ $slice->id }}/update"
          method="post">

        @csrf

        <div class="flex border-b">

            <div class="p-4 w-32 text-grey-darker border-r">

                Name

            </div>

            <div class="p-2 flex-grow">

                <div class="flex">

                    <div class="flex-shrink p-2 pt-4">
                        x_
                    </div>
                    <div class="flex-shrink">
                        <input type="text"
                           class="border p-3 w-16 text-blue"
                           name="state"
                           value="{{ substr($slice->name, 2, 2) }}" />
                    </div>
                    <div class="flex-shrink p-2 pt-4">
                        _
                    </div>
                    <div class="flex-shrink">
                        <input type="text"
                           class="border p-3 text-blue"
                           name="name"
                           value="{{ substr($slice->name, 5) }}" />
                    </div>

                </div>

            </div>

        </div>


        <div class="flex border-b">

            <div class="p-2 px-4 w-32 text-grey-darker border-r">

                SQL

            </div>

            <div class="p-2 flex-shrink whitespace-no-wrap">

                SELECT * FROM 

            </div>

            <div class="p-2 w-64">

                <select name="master_table"
                        class="w-full text-blue border">

                    <option value=""
                            {{ ($slice->master_table == '') ? 'selected' : '' }}>
                        --</option>

                    @foreach($master_table_list as $table)

                        <option value="{{ $table }}"
                                {{ ($slice->master == $table) ? 'selected' : '' }}>
                            {{ $table }}</option>

                    @endforeach

                </select>


                @if($slice->master && (str_replace('_master', '', str_replace('x_voters_', '', $slice->master)) != substr($slice->name, 2, 2)))

                    <div class="px-4 py-2 border-4 border-red text-red mt-2">
                        <i class="fas fa-exclamation-triangle mr-1"></i> <span class="font-bold">Warning:</span> Master Voter Table State does not match Slice State.
                    </div>

                @endif

            </div>

            @if($slice->master)

                <div class="p-2  whitespace-no-wrap flex-shrink">

                    WHERE

                </div>

                <div class="p-2 flex-grow">

                    <textarea type="text"
                           class="border p-3 w-full text-blue"
                           name="sql">{{ $slice->sql }}</textarea>

                </div>

            @endif

        </div>

        @if(!$slice->master)

            <div class="px-4 py-2 border-2 border-blue bg-blue-lightest my-2">
                This is a standalone table, not a slice of a master table.
            </div>

        @endif

        <div class="flex border-b">

            <div class="p-4 w-32 text-grey-darker border-r">

                Command

            </div>

            <div class="flex-grow">

                <div class="bg-black text-yellow font-mono  p-4">

                    php artisan cf:populate_slices --slice={{ $slice->name}} <span class="text-blue-light">--overwrite</span>
                
                </div>

            </div>

        </div>

        <div class="text-right p-2 py-4 mt-2 border-t-2 border-grey-lighter">

            <button class="rounded-lg bg-grey-lighter text-black px-2 py-1">
                Save
            </button>

            <button class="rounded-lg bg-blue text-white px-2 py-1"
                    formaction="/admin/slices/{{ $slice->id }}/update/close">
                Save and Close
            </button>

        </div>

    </form>

    <table class="w-full text-gray-800">
        <tr>
            <th>Municipality</th>
            <th>Code</th>
            <th>Voter Count</th>
        </tr>
        @foreach (\App\Municipality::where('state', 'MA')->orderBy('name')->get() as $m)
            <tr>
                <td class="border-t">
                    {{ $m->name }}
                </td>
                <td class="border-t">
                    {{ $m->code }}
                </td>
                <td class="border-t text-right">
                    {{ number_format($m->voter_count) }}
                </td>
            </tr>
        @endforeach
    </table>

</div>



@endsection



@section('javascript')


<script type="text/javascript">


</script>

@endsection