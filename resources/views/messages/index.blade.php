@extends('layouts.app')

@section('content')

<main class="main" role="main">
    <div class="pb-5 pt-3 bg-lightest-brand">
        <div class="container">
            <div class="row">
                <div class="col-md-9 mx-auto">
                    <a href="/home" class="d-inline-block mb-3" style="font-size: 14px;"><i
                            class="icon-chevron-small-left"></i> @lang('messages.my-dashboard')</a>
                    @if ($errors->any())
                    <div class="alert alert-success">
                        @foreach ($errors->all() as $error)
                        <span>{{ $error }}</span>
                        @endforeach
                    </div>
                    @endif
                    @if(Session::has('message'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="auto-hide">
                        <strong> Successfully! </strong>  {{ Session::get('message') }} <a
                            href="/messages/{{ Session::get('id') }}/undo-delete" style="position: absolute;right: 7%;">
                            <i class="fa fa-undo" aria-hidden="true" style="font-size:13px"> </i> Undo</a>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h4 class="card-title">@lang('messages.messages')</h4>
                        <a href="/messages/new" class="btn btn-sm btn-secondary">@lang('messages.new')</a>
                    </div>

                    <div class="card">
                        <div class="card-body pl-1">
                            @foreach($threads as $thread)
                           @php
                            $thread = App\MessageThread::with(['participants', 'messages'])->find($thread->id);
        
                            $subject = App\Message::select('subject')->where('message_thread_id', '=', $thread->id)->where('subject','!=',null)->first();
                           
      
       @endphp
                            @if($thread->latestMessage->author != null &&
                            $thread->messagesFor(request()->user()->id)->count())
                            <a dusk="message{{ $loop->iteration }}" href="/messages/{{ $thread->id }}#last"
                                class="d-flex flex-wrap justify-content-between align-items-center no-underline light-hover-bg">
                                @if($thread->isUnread)
                                <div class="bg-brand mr-2" style="width: 10px; height: 10px; border-radius: 50%;"></div>
                                @else
                                <div class="bg-white mr-2" style="width: 10px; height: 10px; border-radius: 50%;"></div>
                                @endif
                                <div class="ml-1 ml-md-2 mr-2 my-1 d-flex align-items-center" style="flex: 1;">
                                    <div style="border-bottom: 0; position: relative; width: 2.5em;">
                                        @if($thread->otherUsers->count() < 3) @foreach($thread->otherUsers as $user)
                                            <div
                                                style="width: 2.2em; height: 2.2em; border-radius: 50%; background-image: url('{{ $user->photo_path }}'); background-size: cover; background-position: center; overflow: hidden; position: absolute; top: 0; left: {{ $loop->index * 12 }}px; transform: translateY(-50%);">
                                            </div>
                                            @endforeach
                                            @else
                                            <div
                                                style="width: 2.2em; height: 2.2em; border-radius: 50%; background-color: #eee; text-align: center; font-size: 1.4em; line-height: 2.2em;">
                                                {{ $thread->otherUsers->count() }}
                                            </div>
                                            @endif
                                    </div>
                                    
                                    <div class="ml-3" style="flex: 1;">
                                        <div class="d-flex justify-content-between w-100">
                                            @if($thread->event)
                                            <span class="d-block"><b>{{ $thread->event->name }} </b><small
                                                    class="text-muted">({{ ucfirst($thread->type) }})</small></span>
                                            @else
                                            <span class="d-block"><b>
                                                    @if(empty($subject->subject))
                                                        @if($thread->otherUsers->count() > 5)
                                                            {{ $thread->otherUsers->take(3)->implode('name', ', ') }} &amp;
                                                            {{ $thread->otherUsers->count() - 3 }} others
                                                            @else
                                                             {{ $thread->otherUsers->implode('name', ', ') }}
                                                        @endif
                                                    @else
                                                        {{$subject->subject}}
                                                    @endif
                                                </b></span>
                                            @endif
                                            <span
                                                class="d-none d-xs-block text-muted">{{ $thread->updated_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span
                                                class="d-block text-muted"><i>{{ $thread->latestMessage->author->name }}:</i>
                                                {!! getLatestMessage($thread->latestMessage) !!}</span>
                                            <span class="d-none d-xs-block">@lang('messages.open')</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span
                                                class="d-block d-xs-none text-muted">{{ $thread->latestMessage->created_at->diffForHumans() }}</span>
                                            <span class="d-block d-xs-none">@lang('messages.open')</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <hr class="my-1">
                            @endif
                            @endforeach

                            @if ($threads->isNotEmpty())
                            <div class="d-flex justify-content-center">
                                {{ $threads->links() }}
                            </div>
                            @endif

                            @if ($threads->isEmpty())
                            <span class="d-block text-center my-5"><b>You have no messages.</b><br>You can message
                                someone by clicking <i>message</i> button on their profile.</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    jQuery(function () {
        setTimeout(function () {
            $("#auto-hide").fadeOut(1500);
        }, 5000)
    })
</script>
@endsection