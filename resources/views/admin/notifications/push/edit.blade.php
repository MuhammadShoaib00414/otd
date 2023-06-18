@extends('admin.layout')

@push('stylestack')
<style>
    @media(min-width: 768px)
    {
        .formContainer {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        #header {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
    }
    #tags {
        float: left;
    }

    #live-notification {
        border: 1px solid black;
        border-radius: 12px;
        padding-left: 20px;
        padding-top: 5px;
        padding-bottom: 5px;
        max-width: 450px;
    }

    #live-logo {
        width: 20px;
        margin-right: 5px;
    }

</style>
@endpush
@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Push Notifications' => '/admin/notifications/push',
        $notification->name => '/admin/push/notifications/'.$notification->id,
    ]])
    @endcomponent

<div class="container">
    <h4>Edit <i> {{ $notification->name }} </i> notification</h4>
    <p class="text-muted">Tip: Push notifications should be as short and concise as possible.</p>
    <form action="/admin/notifications/push/{{ $notification->id }}" method="post">
        @csrf
        @method('put')
        <div class="formContainer mb-2">
            <div class="mr-2">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" class="form-control" style="max-width: 450px;" maxlength="50" value="{{ $notification->title }}" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="body">Body</label>
                    <input type="text" id="body" name="body" class="form-control" style="max-width: 450px;" maxlength="100" value="{{ $notification->body }}" required autocomplete="off">
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="is_enabled" id="is_enabled" value="1" {{ $notification->is_enabled ? 'checked' : '' }}>
                    <label for="is_enabled" class="form-check-label">Is enabled</label>
                </div>
                <button type="submit" class="btn btn-primary my-3">Save changes</button>
            </div>

            <div id="tags">
                <div class="mb-3 col px-0" id="live-demo">
                    <span class="d-flex mb-2">Your notification will look similar to: </span>
                    <div id="live-notification" class="my-4 ml-0">
                        <span class="text-muted"><img src="/logo" id="live-logo">{{ getsetting('name') }}</span>
                        <b class="d-flex" id="live-title">{{ $notification->title }}</b>
                        <p class="d-flex mb-0" id="live-body">{{ $notification->body }}</p>
                    </div>
                    <hr>
                </div>
                <h5 class="mb-3">Available tags</h5>
                @include('admin.notifications.push.tag', ['index' => '@userName', 'description' => 'The person that this notification is being sent to.', 'isLast' => false])
                @foreach($notification->tags as $index => $description)
                    @include('admin.notifications.push.tag', ['index' => $index, 'description' => $description, 'isLast' => $loop->last])
                @endforeach
            </div>
        </div>
    </form> 
</div>
@endsection

@push('scriptstack')
<script>
    $('#title').keyup(function() {
        $('#live-title').html($(this).val());
    });
    $('#body').keyup(function() {
        updateLiveBody();
    });

    function updateLiveBody()
    {
        var body = $('#body').val();
        var tags = [];
        tags['@sender'] =  '(example sender)';
        tags['@introducedTo'] = '(example user)';
        tags['@introducedBy'] = '(example user)';
        tags['@eventName'] = '(example event)';
        tags['@shouter'] = '(example user)';
        tags['@reporter'] = '(example user)';
        tags['@focusGroup'] = '(example focus group)';
        tags['@discussionName'] = '(example discussion)';
        tags['@groupName'] = '(example group)';
        tags['@replier'] = '(example user)';

        for(var tag in tags)
        {
            body = body.replace(tag, tags[tag]);
        }
        body = body.replace('@userName', '({{ $authUser->name }})');
        $('#live-body').html(body);
    }

    updateLiveBody();
</script>
@endpush