<div class="modal" tabindex="-1" role="dialog" id="welcome-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">@lang('messages.welcome')!</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h1 class="text-center">@lang('messages.you-made-it-overenthusiastic')!</h1>
        <p>{!! nl2br(App\Setting::where('name', '=', 'onboarding_popup')->first()->value) !!}</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('messages.lets-go')</button>
      </div>
    </div>
  </div>
</div>