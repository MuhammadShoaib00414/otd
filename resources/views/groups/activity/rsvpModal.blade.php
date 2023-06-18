<span class="btn btn-sm btn-primary" data-toggle="modal" data-target="#event{{ $id }}">
  @lang('activity.view users')
</span>

<div class="modal fade" id="event{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">@lang('groups.Users that viewed') {{ $name }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table">
          <thead>
            <tr>
              <td class="text-left"><b>@lang('general.user')</b></td>
              <td></td>
              <td><b>@lang('general.Views')</b></td>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $name => $data)
            <tr>
              <td class="text-left">{{ $name }}</td>
              <td>
                @if(!isset($data['rsvp']))
                @elseif($data['rsvp']->response == 'yes')
                  <span class="badge" style="background-color: rgba(93 234 104 / 44%);"><i style="font-size: 0.85em; margin-right: 0.3em;" class="fa fa-check"></i>@lang('groups.rsvp')</span>
                @elseif($data['rsvp']->response == 'no')
                  <span class="badge" style="background-color: rgba(255 1 1 / 31%)"><i style="font-size: 0.85em; margin-right: 0.3em;" class="fa fa-times"></i>@lang('groups.rsvp')</span>
                @endif
              </td>
              <td>{{ $data['count'] }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>