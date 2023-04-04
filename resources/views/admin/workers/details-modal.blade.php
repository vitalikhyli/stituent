<div class="modal-content">

      <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Worker {{ $worker->id }}</h4>
      </div>
      <div class="modal-body p-6">
          Started: <b>{{ $worker->created_at->format('Y-m-d g:ia') }}</b>
          
          <div class="flex text-xs">
            <div class="w-2/5">
              <b>Jobs</b><br>
              @foreach ($worker->jobs as $job_id => $job_start)
                <a href="/admin/jobs#job_{{ $job_id }}">
                  {{ $job_id }} - {{ $job_start }}
                </a>
                <br>
              @endforeach
              </div>
              <div class="w-3/5">
                <b>Log</b><br>
                {!! nl2br($worker->log) !!}
              </div>
          </div>
      </div>
      <div class="modal-footer">           
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>

</div>
