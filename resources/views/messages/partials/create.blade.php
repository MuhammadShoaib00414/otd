<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createMessage">
  @lang('messages.Message users')
</button>


<div class="modal fade bd-example-modal-lg" id="createMessage" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 9999;">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">@lang('messages.Create group message')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/messages/new" method="get" class="d-inline-block pl-5 pr-2">
      <input name="createIndividually" value="true" type="hidden">
      <div class="modal-body text-left">
       <div class="row">
       
        @foreach($users as $userToMessage)
        @if($userToMessage->is_hidden == 0)
        <div class="col-md-2">
              <input checked class="form-check-input" type="checkbox" id="userToMessage{{ $userToMessage->id }}" name="users[]" value="{{ $userToMessage->id }}">
              <label class="form-check-label" for="userToMessage{{ $userToMessage->id }}">
                {{ $userToMessage->name }}
              </label>
            </div>
            @endif
        @endforeach
        <
       </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang('general.create')</button>
      </div>
    </form>
    </div>
  </div>
</div>