<span class="btn btn-sm btn-primary" data-toggle="modal" data-target="#exampleModal">
  view users
</span>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Users that clicked on {{ $name }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ul>
          @foreach($users as $name => $rsvp)
            <li class="text-left">
              {{ $name }}
              @if(!isset($rsvp))
              @elseif($rsvp->response == 'yes')
                <span class="badge" style="background-color: rgba(93 234 104 / 44%);"><i style="font-size: 0.85em; margin-right: 0.3em;" class="fa fa-check"></i>rsvp</span>
              @elseif($rsvp->response == 'no')
                <span class="badge" style="background-color: rgba(255 1 1 / 31%)"><i style="font-size: 0.85em; margin-right: 0.3em;" class="fa fa-times"></i>rsvp</span>
              @endif
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</div>