<span class="btn btn-sm btn-primary" data-toggle="modal" data-target="#contentModal{{ $id }}">
  view users
</span>

<div class="modal fade" id="contentModal{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">@lang('groups.Users that viewed') {{ $name }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="redactor-output text-center mb-2">
          {!! $post->post->content !!}
        </div>
        <table class="table">
          <thead>
            <tr>
              <td class="text-left"><b>@lang('general.user')</b></td>
              <td><b>@lang('groups.Views')</b></td>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $name => $data)
            <tr>
              <td class="text-left">{{ $name }}</td>
              <td>{{ $data['count'] }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>