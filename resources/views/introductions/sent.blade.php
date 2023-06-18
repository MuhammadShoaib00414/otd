@extends('introductions.layout')

@section('inner-content')

  @if(Session::has('message'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    {!! Session::get('message') !!}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif

  <div class="card">
    <table class="table mb-0">
      <tr class="d-none d-sm-table-row">
        <td><b>@lang('introductions.you-introduced'):</b></td>
        <td><b>@lang('introductions.to'):</b></td>
        <td></td>
      </tr>
      @foreach($introductions as $introduction)
        @if($introduction->other_user && $introduction->users->count() == 2)
        <tr>
          <td class="d-flex">
            <div class="d-flex">
              <a href="/users/{{ $introduction->users[0]->id }}" style="border-bottom: 0; margin-right: 1em;">
                <div style="width: 2.5em; height: 2.5em; border-radius: 50%; background-image: url('{{ $introduction->users[0]->photo_path }}'); background-size: cover; background-position: center; overflow: hidden;">
                </div>
              </a>
              <div>
                <b>{{ $introduction->users[0]->name }}</b><br>
                {{ $introduction->users[0]->job_title }}<br>
                <a href="/messages/new?user={{ $introduction->users[0]->id }}"><i class="icon-mail"></i> @lang('general.message')</a> 
                @if($introduction->hasUserSentMessage($introduction->users[0]->id))<i class="icon-check"></i>@endif
              </div>
            </div>
          </td>
          <td class="d-flex d-sm-table-cell">
            <div class="d-flex align-items-start">
              <a href="/users/{{ $introduction->users[1]->id }}" style="border-bottom: 0; margin-right: 1em;">
                <div style="width: 2.5em; height: 2.5em; border-radius: 50%; background-image: url('{{ $introduction->users[1]->photo_path }}'); background-size: cover; background-position: center; overflow: hidden;">
                </div>
              </a>
              <div>
                <b>{{ $introduction->users[1]->name }}</b><br>
                {{ $introduction->users[1]->job_title }}<br>
                <a href="/messages/new?user={{ $introduction->users[1]->id }}"><i class="icon-mail"></i> @lang('general.message')</a>
                @if($introduction->hasUserSentMessage($introduction->users[1]->id))<i class="icon-check"></i>@endif
              </div>
            </div>
          </td>
          <td style="vertical-align: middle;" class="py-0">
            <span style="font-size: 0.7em; position:absolute; transform: translateY(-165%);" class="d-block text-muted">{{ $introduction->created_at->diffForHumans() }}</span>
            <div class="flex flex-col justify-content-around h-100">
              <a class="mt-2" href="/introductions/{{ $introduction->id }}"><button class="btn btn-outline-secondary mx-md-auto w-100">@lang('general.view')</button></a>
            </div>
          </td>
        </tr>
        @endif
      @endforeach
    </table>

    @if($introductions->isNotEmpty() && $introductions->hasMorePages() )
     <div class="card-body">
      <div class="d-flex justify-content-center">
        {{ $introductions->links() }}
      </div>
    </div>
    @endif

    @if($introductions->isEmpty() && !isset($_GET['s']))
     <div class="card-body">
      <span class="d-block text-center my-5"><b>@lang('introductions.empty')</b><br>@lang('introductions.empty-prompt-1')<br> <i>@lang('introductions.make-an-introduction')</i> @lang('introductions.empty-prompt-2')</span>
     </div>
    @elseif($introductions->isEmpty() && isset($_GET['s']))
     <div class="card-body">
      <span class="d-block text-center my-5"><b>@lang('introductions.empty-search')</b><br>@lang('introductions.empty-search-2')
      </span>
     </div>
    @endif
  </div>
         
  @endsection