<div id="human-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <form action="/sandbox/u" method="post" id="report-form">

                @csrf

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Please prove you are not a robot</h4>
                </div>
                <div class="modal-body">

                    <i class="fas fa-graduation-cap text-5xl"></i>

                    What three letter word describes this icon?

                    <input type="hidden" name="question" value="{{ base64_encode('graduation') }}" />

                    <div class="mt-4">
                        <input type="text" name="answer" id="answer" placeholder="Answer here" class="fancy rounded p-2 text-4xl border" />
                    </div>



                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="run-report" class="btn btn-primary">
                       Go
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>