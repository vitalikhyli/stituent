@extends('admin.base')

@section('title')
    Admin Data Jobs
@endsection

@section('breadcrumb')


	{!! Auth::user()->Breadcrumb('Data Jobs', 'data_jobs', 'level_1') !!}


@endsection

@section('style')

@endsection

@section('main')



<div class="text-xl mb-4 border-b bg-orange-lightest p-2 ">
    Data Jobs
</div>

<table class="font-normal text-sm w-full">
    <tr class="border-b bg-grey-lighter text-xs">
        <td class="p-1">
            Done?
        </td>
        <td class="p-1">
            ID
        </td>
        <td class="p-1">
            Updated
        </td>
        <td class="border-l border-grey px-2 p-1">
            Type
        </td>
        <td class="border-l border-grey px-2 p-1 text-right">
            Count
        </td>
        <td class="border-l border-grey px-2 p-1 text-right">
            Duration
        </td>
        <td class="border-l border-grey px-2 p-1 text-right">
            Rate / second
        </td>
        <td class="border-l border-grey px-2 p-1 text-right">
            Est time 4.5 M
        </td>
        <td class="border-l border-grey px-2 p-1 text-right">
            Ready?
        </td>
        <td class="border-l border-grey px-2 p-1 text-right">
            Deployed?
        </td>
    </tr>


@foreach ($imports as $theimport)
    @if ($theimport->jobs->count() < 1)
        @continue
    @endif
    <tr class="border-b-2 border-blue text-blue">
        <td class="p-1 pt-4" colspan="10">
            {{ $theimport->name }}
        </td>
    </tr>
    @foreach ($theimport->jobs as $thejob)
        <tr id="#job_{{ $thejob->id }}" class="border-b hover:bg-orange-lightest cursor-pointer">
            <td class="p-1">
                @if(!$thejob->done)
                    <i class="fas fa-cog text-orange-dark fa-spin "></i>
                @else
                    <i class="fas fa-check-circle text-blue"></i>
                @endif
            </td>
            <td class="p-1 text-grey-dark">
                {{ $thejob->id }}
            </td>
            <td class="p-1 text-grey-dark">
                {{ \Carbon\Carbon::parse($thejob->updated_at)->diffForHumans() }}
            </td>
            <td class="p-1">
                {{ $thejob->type }}
            </td>
            <td class="p-1 text-right">
                {{ number_format($thejob->count,0,'.',',') }}
            </td>
            <td class="p-1 text-right">
                {{ $thejob->duration/1000 }}
            </td>
            <td class="p-1 text-right">
                {{ number_format($thejob->rate,0,'.',',') }}
            </td>
            <td class="p-1 text-right">
                @if($thejob->rate > 0)
                    {{ number_format(4500000/$thejob->rate/60/60,2,'.',',') }} hrs
                @else
                    -
                @endif
            </td>
            <td class="p-1 text-right text-grey-dark">
                {{ ($thejob->import->ready) ? 'Ready' : '' }}
            </td>
            <td class="p-1 text-right text-grey-dark">
                @if($thejob->import->deployed)
                    Deployed
                @else
                    @if (
                        ($thejob->type == 'enrich') ||
                        ($thejob->type == 'createHouseholds') ||
                        ($thejob->type == 'createHouseholdsBySlice')
                        )
                        <form action="/admin/jobs/rollback/{{ $thejob->id }}" method="POST">
                            @csrf
                           <!--  <button type="submit" class="ml-2 border rounded-full text-xs text-grey-dark hover:text-white hover:bg-blue px-2 py-1"><i class="fas fa-undo"></i> Rollback</button> -->
                            <button type="submit" data-toggle="tooltip" data-placement="top" title="You Look Like a Big Easter Egg" class="ml-2 border rounded-full text-xs text-grey-dark hover:text-white hover:bg-blue px-2 py-1"><i class="fas fa-undo"></i> DO IT AGAIN</button>

                        </form>
                    @endif
                @endif
            </td>
        </tr>
    @endforeach
@endforeach
</table>

@endsection



@section('javascript')



@endsection