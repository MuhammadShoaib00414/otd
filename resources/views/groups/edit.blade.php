@extends('groups.layout')

@section('body-class', 'col-md-9')

@section('stylesheets')
  @parent
  <style>
    .pagination {
      justify-content: center;
    }
    .collapsing {
      -webkit-transition: none;
      transition: none;
      display: none;
    }
    .nav-tabs .nav-item .nav-link:not(.active) {
      color: #5e646b;
    }
  </style>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
@endsection

@section('inner-content')
<div class="col-md-12">
  <div class="d-flex align-items-center">
      <h2 class="flex-shrink-0 my-auto mr-2">@lang('groups.edit_title')</h2>
      @include('partials.tutorial', ['tutorial' => \App\Tutorial::named('Group Settings')])
    @if(session()->has('success'))
      <div class="alert alert-success w-100 ml-3">
        {{ session('success') }}
      </div>
    @endif
  </div>
  <ul class="nav nav-tabs justify-content-between px-1" role="tablist">
      <li class="nav-item">
          <a id="generalNav" class="nav-link" href="#general" data-toggle="tab" role="tab" aria-controls="general"><b>@lang('groups.general')</b></a>
      </li>
      <li class="nav-item">
          <a id="permissionsNav" class="nav-link" href="#permissions" data-toggle="tab" role="tab" aria-controls="permissions"><b>@lang('groups.permissions')</b></a>
      </li>
      <li class="nav-item">
          <a id="custom_menusNav" class="nav-link" href="#custom_menus" data-toggle="tab" role="tab" aria-controls="custom_menus"><b>@lang('groups.custom_menus')</b></a>
      </li>
      <li class="nav-item">
          <a id="socialNav" class="nav-link" href="#social" data-toggle="tab" role="tab" aria-controls="social"><b>@lang('groups.social')</b></a>
      </li>
      @if($group->chatRoom)
      <li class="nav-item">
          <a id="chatRoomNav" class="nav-link" href="#chatRoom" data-toggle="tab" role="tab" aria-controls="chatroom"><b>@lang('groups.chat_room')</b></a>
      </li>
      @endif
      @if($group->is_virtual_room_enabled)
      <li class="nav-item">
        <a class="nav-link" href="/groups/{{ $group->slug }}/edit-virtual-room"><b>@lang('groups.interactive_header_image')</b></a>
      </li>
      @endif
      @if($group->is_lounge_enabled)
      <li class="nav-item">
        <a class="nav-link" href="/groups/{{ $group->slug }}/edit-lounge"><b>Networking Lounge</b></a>
      </li>
      @endif
      @if(getsetting('is_sequence_enabled') && $group->is_sequence_enabled)
      <li class="nav-item">
        <a id="sequenceNav" class="nav-link" href="#sequence" data-toggle="tab" role="tab" aria-controls="sequence"><b>{{ $group->sequence()->exists() ? $group->sequence->name : __('groups.Sequence') }}</b></a>
      </li>
      @endif
  </ul>
  <div class="tab-content">
    <div id="general" class="card tab-pane fade" role="tabpanel" aria-labelledby="general-tab">
      <div class="card-body">
        <form method="post" action="/groups/{{ $group->slug }}/edit" enctype="multipart/form-data">
          @method('put')
          @csrf
          <input type="hidden" name="tab" value="general">
          @if(getsetting('is_localization_enabled'))
            <table class="table table-borderless">
              <thead>
                <tr>
                  <th></th>
                  <th>@lang('messages.english')</th>
                  <th>@lang('messages.spanish')</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><b>@lang('groups.group_description')</b></td>
                  <td>
                    <textarea id="description" maxlength="255" class="form-control" name="description">{{ $group->description }}</textarea>
                  </td>
                  <td>
                    <textarea id="description_es" maxlength="255" class="form-control" name="description_es">{{ $group->description }}</textarea>
                  </td>
                </tr>
              </tbody>
            </table>
          @else
            <div class="form-group mb-4">
              <label for="description">@lang('groups.group_description')</label>
              @include('partials.subtext', ['subtext' => __('group.group_description_subtext')])
              <textarea id="description" maxlength="255" class="form-control" name="description">{{ $group->description }}</textarea>
            </div>
          @endif

          <div class="d-flex my-4">
            <div class="form-group">
              <label>@lang('groups.group_thumbnail_image')</label>
              @include('components.upload', ['name' => 'thumbnail_image', 'value' => $group->thumbnail_image_url ? $group->thumbnail_image_url : ''])
              <span class="text-muted text-small">@lang('groups.recommended_size'): 350x180</span>
            </div>
          </div>

          @if(!$group->is_virtual_room_enabled)
          <div id="header_bg_image_container" class="form-group">
            <label for="name">@lang('groups.header_background_image')</label>
            @include('components.upload', ['name' => 'header_bg_image', 'value' => $group->header_bg_image_path ? $group->header_bg_image_url : ''])
            <span class="text-muted text-small">@lang('groups.recommended_size'): 1900x450</span>
          </div>
          @endif

          <hr>
          <div class="form-check mb-3 mt-2">
            <input type="hidden" name="is_virtual_room_enabled" value="0">
            <input type="checkbox" class="form-check-input" id="is_virtual_room_enabled" {{ $group->is_virtual_room_enabled ? 'checked' : ''}} name="is_virtual_room_enabled" value="1">
            <label class="form-check-label" for="is_virtual_room_enabled" style="font-size: 16px;">@lang('groups.enable_interactive_header_image')</label>
          </div>
          
          @if($group->can_ga_set_live_chat)
            <div class="form-group">
              <p class="mb-2">@lang('groups.live_chat')</p>
              <div class="form-check">
                <input type="radio" class="form-check-input" name="is_chat_room_enabled" id="live_chat_enabled" value="true"{{ (optional($group->chatRoom)->is_enabled) ? ' checked': '' }}>
                <label class="form-check-label" for="live_chat_enabled">@lang('groups.enabled')</label>
              </div>
              <div class="form-check">
                <input type="radio" class="form-check-input" name="is_chat_room_enabled" id="live_chat_disabled" value="false"{{ (!optional($group->chatRoom)->is_enabled) ? ' checked': '' }}>
                <label class="form-check-label" for="live_chat_disabled">@lang('groups.disabled')</label>
              </div>
            </div>
            <div class="{{ ($group->chatRoom && $group->chatRoom->is_enabled) ? '' : 'd-none' }}" style="max-width: 400px;" id="chatRoomTimeFrame">

              <div class="form-group">
                <p class="font-weight-bold mb-1">@lang('groups.live_chat_start')</p>
                <div class="form-row">
                  <div class="col-6">
                    <label for="live_chat_start_date">@lang('groups.date')</label>
                    <input type="text" name="live_chat_start_date" class="form-control" value="{{ ($group->chatRoom && $group->chatRoom->start_at) ? $group->chatRoom->start_at->format('m/d/y') : '' }}" placeholder="mm/dd/yy" id="live_chat_start_date">
                  </div>
                  <div class="col-6">
                    <label for="live_chat_start_date">@lang('groups.time')</label>
                     <input type="text" name="live_chat_start_time" class="form-control" value="{{ ($group->chatRoom && $group->chatRoom->start_at) ? $group->chatRoom->start_at->tz(request()->user()->timezone)->format('g:i a') : '' }}" placeholder="hh:mm pm" id="live_chat_start_time">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <p class="font-weight-bold mb-1">@lang('groups.live_chat_end')</p>
                <div class="form-row">
                  <div class="col-6">
                    <label for="live_chat_end_date">@lang('groups.date')</label>
                    <input type="text" name="live_chat_end_date" class="form-control" value="{{ ($group->chatRoom && $group->chatRoom->end_at) ? $group->chatRoom->end_at->tz(request()->user()->timezone)->format('m/d/y') : '' }}" placeholder="mm/dd/yy" id="live_chat_end_date">
                  </div>
                  <div class="col-6">
                    <label for="live_chat_end_time">@lang('groups.time')</label>
                     <input type="text" name="live_chat_end_time" class="form-control" value="{{ ($group->chatRoom && $group->chatRoom->end_at) ? $group->chatRoom->end_at->tz(request()->user()->timezone)->format('g:i a') : '' }}" placeholder="hh:mm pm" id="live_chat_end_time">
                  </div>
                </div>
              </div>
            </div>
          @endif

          <div class="d-flex justify-content-end px-3">
            <button class="btn btn-primary mb-2">@lang('general.save')</button>
          </div>
        </form>
      </div>
    </div>
    <div id="custom_menus" class="card tab-pane fade" role="tabpanel" aria-labelledby="custom-menus-tab">
      <div class="card-body">
        <form id="custom_menu_form" method="post" action="/groups/{{ $group->slug }}/edit" enctype="multipart/form-data">
          @method('put')
          @csrf
          <input type="hidden" name="tab" value="custom_menus">
          <p class="mb-2"><b>@lang('groups.message_an_admin_banner')</b></p>
          <div class="form-row justify-content-between mb-2">
            @include('components.multi-language-text-input', ['label' => __('groups.title'),'name' => 'banner_cta_title', 'value' => $group->banner_cta_title, 'required' => true, 'localization' => $group->localization])
            @include('components.multi-language-text-input', ['label' => __('groups.button_text'),'name' => 'banner_cta_button', 'value' => $group->banner_cta_button, 'required' => true, 'localization' => $group->localization])
          </div>
          <div class="form-row justify-content-between">
            @include('components.multi-language-text-input', ['label' => __('general.description'),'name' => 'banner_cta_paragraph', 'value' => $group->banner_cta_paragraph, 'required' => true, 'localization' => $group->localization])
            <div class="col">
              <label for="users[]">@lang('groups.users_to_message')</label>
              <select class="selectpicker form-control" multiple name="users[]">
                @foreach($group->admins()->orderBy('name', 'asc')->get() as $user)
                  <option {{ in_array($user->id, $group->banner_cta_users) ? 'selected' : '' }} value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <hr>
          <p><b>Custom Menu</b></p>


          <template v-for="(group, index) in groups">
            <div class="p-1 d-flex" style="background-color: #eee;">
              <input type="text" class="form-control form-control-sm w-100" placeholder="@lang('groups.custom_header')" v-model="group.title">
              <button class="btn btn-sm btn-secondary" @click.prevent="deleteGroup(index)">&times;</button>
            </div>
            <div class="mb-3 p-1" style="border: 1px solid #eee;">
              <div v-for="(link, index) in group.links" style="border-bottom: 1px solid #eee;">
                <div class="d-flex align-items-start">
                  <div class="w-100">
                    <div class="row align-items-center">
                      <label class="col-2 text-right font-weight-bold">@lang('general.name')</label>
                      <div class="col-10">
                        <input type="text" v-model="link.title" class="form-control form-control-sm">
                      </div>
                    </div>
                    <div class="row align-items-center">
                      <label class="col-2 text-right font-weight-bold">@lang('general.url')</label>
                      <div class="col-10">
                        <input placeholder="Ex: https://www.domainname.com" type="text" v-model="link.url" class="form-control form-control-sm">
                      </div>
                    </div>
                  </div>
                  <button class="btn btn-sm btn-secondary" @click.prevent="deleteLink(group, index)">&times;</button>
                </div>
              </div>
              <div class="text-center">
                <button class="btn btn-sm btn-secondary" @click.prevent="addLink(group)">@lang('groups.add_link')</button>
              </div>
            </div>
          </template>
          <input type="hidden" id="customMenuJson" name="custom_menu" value="">
          <div class="text-center">
            <button class="btn btn-sm btn-secondary" @click.prevent="addGroup()">@lang('groups.add_link_group')</button>
          </div>

          @if(getsetting('is_localization_enabled'))
          <template v-for="(group, index) in spanishGroups" id="custom_menu_es">
            <div class="p-1 d-flex" style="background-color: #eee;">
              <input type="text" class="form-control form-control-sm w-100" placeholder="@lang('groups.custom_header')" v-model="group.title">
              <button class="btn btn-sm btn-secondary" @click.prevent="deleteGroup(index)">&times;</button>
            </div>
            <div class="mb-3 p-1" style="border: 1px solid #eee;">
              <div v-for="(link, index) in group.links" style="border-bottom: 1px solid #eee;">
                <div class="d-flex align-items-start">
                  <div class="w-100">
                    <div class="row align-items-center">
                      <label class="col-2 text-right font-weight-bold">@lang('general.name')</label>
                      <div class="col-10">
                        <input type="text" v-model="link.title" class="form-control form-control-sm">
                      </div>
                    </div>
                    <div class="row align-items-center">
                      <label class="col-2 text-right font-weight-bold">@lang('general.url')</label>
                      <div class="col-10">
                        <input placeholder="Ex: https://www.domainname.com" type="text" v-model="link.url" class="form-control form-control-sm">
                      </div>
                    </div>
                  </div>
                  <button class="btn btn-sm btn-secondary" @click.prevent="deleteSpanishLink(group, index)">&times;</button>
                </div>
              </div>
              <div class="text-center">
                <button class="btn btn-sm btn-secondary" @click.prevent="addSpanishLink(group)">@lang('groups.add_link')</button>
              </div>
            </div>
          </template>
          <input type="hidden" id="customMenuJsonEs" value="">
          <div class="text-center">
            <button class="btn btn-sm btn-secondary" @click.prevent="addSpanishGroup()">@lang('groups.add_spanish_link_group')</button>
          </div>
          @endif


          <div class="d-flex justify-content-end mt-4">
            <button class="btn btn-primary mb-2" onclick="app.save()">@lang('general.save')</button>
          </div>
        </form>
      </div>
    </div>

    <div id="social" class="card tab-pane fade" role="tabpanel" aria-labelledby="social-tab">
      <div class="card-body">
        <form id="custom_menu_form" method="post" action="/groups/{{ $group->slug }}/edit" enctype="multipart/form-data">
          @method('put')
          @csrf
          <input type="hidden" name="tab" value="social">
          <p class="font-weight-bold">@lang('groups.social_links')</p>
          <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="twitter">@lang('general.twitter_handle')</label>
                    <div class="input-group input-group-lg">
                      <div class="input-group-prepend">
                        <span class="input-group-text">@</span>
                      </div>
                      <input class="form-control form-control-lg" type="text" name="twitter_handle" id="twitter" value="{{ $group->twitter_handle }}" placeholder="onthedot"/>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label for="facebook">@lang('general.facebook_profile')</label>
                    <input class="form-control form-control-lg" type="text" name="facebook_url" id="facebook" value="{{ $group->facebook_url }}" placeholder="ex. https://www.facebook.com/onthedotwoman"/>
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label for="instagram">@lang('general.instagram_handle')</label>
                    <div class="input-group input-group-lg">
                      <div class="input-group-prepend">
                        <span class="input-group-text">@</span>
                      </div>
                      <input class="form-control form-control-lg" type="text" name="instagram_handle" id="instagram" value="{{ $group->instagram_handle }}" placeholder="onthedot"/>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label for="linkedin">@lang('general.linkedin_profile')</label>
                    <input class="form-control form-control-lg" type="text" name="linkedin_url" id="linkedin" value="{{ $group->linkedin_url }}" placeholder="ex. https://www.linkedin.com/company/on-the-dot-woman/"/>
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label for="website">@lang('general.website')</label>
                    <input class="form-control form-control-lg" type="text" name="website_url" id="website" value="{{ $group->website_url }}" placeholder="ex. www.myblog.com"/>
                </div>
            </div>
          </div>
          <div class="d-flex justify-content-end mt-3">
            <button class="btn btn-primary mb-2">@lang('general.save')</button>
          </div>
        </form>
      </div>
    </div>

    <div id="permissions" class="card tab-pane fade" role="tabpanel" aria-labelledby="permissions-tab">
      <div class="card-body">
        <form id="permissions_form" method="post" action="/groups/{{ $group->slug }}/edit" enctype="multipart/form-data">
          @method('put')
          @csrf
          <input type="hidden" name="tab" value="permissions">
          @if($group->can_ga_toggle_content_types)
            <div class="col-md-6 mb-3">
              <div class="row mb-2">
                <b>@lang('general.post')</b>
              </div>
              <div class="row ml-0 mb-1">
                <p>@lang('groups.select_which_functions_you_want_active_for_this_group')</p>
              </div>
              
              <div class="row form-check mb-1">
                <input type="hidden" name="is_posts_enabled" value="0">
                <input type="checkbox" name="is_posts_enabled" {{ $group->is_posts_enabled ? 'checked' : ''}} value="1">
                <label class="form-check-label ml-2" for="is_posts_enabled">@lang('general.posts')</label>
              </div>
              <div class="row form-check mb-1">
                <input type="hidden" name="is_events_enabled" value="0">
                <input type="checkbox" name="is_events_enabled" {{ $group->is_events_enabled ? 'checked' : ''}} value="1">
                <label class="form-check-label ml-2" for="is_events_enabled"> @lang('general.events')</label>
              </div>
              <div class="row form-check mb-1">
                <input type="hidden" name="is_content_enabled" value="0">
                <input type="checkbox" name="is_content_enabled" {{ $group->is_content_enabled ? 'checked' : ''}} value="1">
                <label class="form-check-label ml-2" for="is_content_enabled"> @lang('general.content')</label>
              </div>
              <div class="row form-check mb-1">
                <input type="hidden" name="is_shoutouts_enabled" value="0">
                <input type="checkbox" name="is_shoutouts_enabled" {{ $group->is_shoutouts_enabled ? 'checked' : ''}} value="1">
                <label class="form-check-label ml-2" for="is_shoutouts_enabled"> @lang('general.shoutouts')</label>
              </div>
              <div class="row form-check mb-1">
                <input type="hidden" name="is_files_enabled" value="0">
                <input type="checkbox" name="is_files_enabled" {{ $group->is_files_enabled ? 'checked' : ''}} value="1">
                <label class="form-check-label ml-2" for="is_files_enabled"> @lang('general.files')</label>
              </div>
              <div class="row form-check mb-1">
                <input type="hidden" name="is_budgets_enabled" value="0">
                <input type="checkbox" name="is_budgets_enabled" {{ $group->is_budgets_enabled ? 'checked' : ''}} value="1">
                <label class="form-check-label ml-2" for="is_budgets_enabled"> @lang('general.budgets')</label>
              </div>
              <div class="row form-check mb-2">
                <input type="hidden" name="is_discussions_enabled" value="0">
                <input type="checkbox" name="is_discussions_enabled" {{ $group->is_discussions_enabled ? 'checked' : ''}} value="1">
                <label class="form-check-label ml-2" for="is_discussions_enabled"> @lang('general.discussions')</label>
              </div>
              <div class="row ml-2">
                <small class="text-muted mb-2">@lang('groups.if_unchecked_that_content_will_not_show_up_in_this_groups_feed')</small>
              </div>
            </div>
          @endif
          <div class="col-md-6 mb-3">
            <div class="row mb-2">
              <b>@lang('groups.allow_users_to')</b>
            </div>
            
            <div class="row form-check mb-2 mt-2">
              <input type="hidden" name="can_users_post_events" value="0">
              <input type="checkbox" name="can_users_post_events" {{ $group->can_users_post_events ? 'checked' : ''}} value="1">
              <label class="form-check-label ml-2" for="can_users_post_events"> @lang('groups.post_events')</label>
            </div>
            <div class="row form-check mb-2 mt-2">
              <input type="hidden" name="can_users_post_shoutouts" value="0">
              <input type="checkbox" name="can_users_post_shoutouts" {{ $group->can_users_post_shoutouts ? 'checked' : ''}} value="1">
              <label class="form-check-label ml-2" for="can_users_post_shoutouts"> @lang('groups.post_shoutouts')</label>
            </div>
            <div class="row form-check mb-2 mt-2">
              <input type="hidden" name="can_users_post_content" value="0">
              <input type="checkbox" name="can_users_post_content" {{ $group->can_users_post_content ? 'checked' : ''}} value="1">
              <label class="form-check-label ml-2" for="can_users_post_content"> @lang('groups.post_content')</label>
            </div>
            <div class="row form-check mb-2 mt-2">
              <input type="hidden" name="can_users_post_text" value="0">
              <input type="checkbox" name="can_users_post_text" {{ $group->can_users_post_text ? 'checked' : ''}} value="1">
              <label class="form-check-label ml-2" for="can_users_post_text"> @lang('groups.post_text')</label>
            </div>
            <div class="row form-check mb-2 mt-2">
              <input type="hidden" name="can_users_post_discussions" value="0">
              <input type="checkbox" name="can_users_post_discussions" id="can_users_post_discussions" {{ $group->can_users_post_discussions ? 'checked' : ''}} value="1">
              <label class="form-check-label ml-2" for="can_users_post_discussions"> Post Discussions</label>
            </div>
            <div class="row form-check mb-2 mt-2">
              <input type="hidden" name="can_users_upload_files" value="0">
              <input type="checkbox" name="can_users_upload_files" {{ $group->can_users_upload_files ? 'checked' : ''}} value="1">
              <label class="form-check-label ml-2" for="can_users_upload_files"> @lang('groups.upload_files')</label>
            </div>
            <div class="row form-check mb-2 mt-2 d-none">
              <input type="hidden" name="can_users_invite" value="0">
              <input type="checkbox" name="can_users_invite" {{ $group->can_users_invite ? 'checked' : ''}} value="1">
              <label class="form-check-label ml-2" for="can_users_invite"> @lang('groups.invite_users')</label>
            </div>
            <div class="row form-check mb-2 mt-2 d-none">
              <input type="hidden" name="can_users_message_group" value="0">
              <input type="checkbox" name="can_users_message_group" {{ $group->can_users_message_group ? 'checked' : ''}} value="1">
              <label class="form-check-label ml-2" for="can_users_message_group"> @lang('groups.message_group')</label>
            </div>
          </div>
          <div class="d-flex justify-content-end px-3">
            <button class="btn btn-primary mb-2">@lang('general.save')</button>
          </div>
        </form>
      </div>
    </div>

    <div id="chatRoom" class="card tab-pane fade" role="tabpanel" aria-labelledby="chatroom-tab">
      <div class="card-body">
        <div>
          <p><b>@lang('groups.admin_actions'):</b></p>
          <div class="list-group">
            <form class="list-group-item list-group-item-action p-0" action="/groups/{{ $group->slug }}/chat-room/download" method="post">
              @csrf
              <button class="btn btn-link w-100 text-left p-2">@lang('groups.download_chat_logs')</button>
            </form>
            <form class="list-group-item list-group-item-action p-0" action="/groups/{{ $group->slug }}/chat-room/clear" method="post">
              @csrf
              <button class="btn btn-link w-100 text-left p-2">@lang('groups.clear_chat_history')</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    @if(getsetting('is_sequence_enabled') && $group->is_sequence_enabled)
    <div id="sequence" class="card tab-pane fade" role="tabpanel" aria-labelledby="sequence-tab">
      <div class="card-body">
        <div>
          <p><b>@lang('groups.Sequence'):</b></p>
          @if($group->sequence)
          <form action="/groups/{{ $group->slug }}/sequence" method="post" enctype="multipart/form-data" class="mb-3">
              @csrf
              @method('put')
              <div class="form-group">
                <label for="name">Sequence name</label>
                <input type="text" name="name" value="{{ $group->sequence->name }}" class="form-control" style="max-width: 700px;">
              </div>

              <div class="row form-check">
                <input type="checkbox" name="is_sequence_visible_on_group_dashboard" {{ ($group->is_sequence_visible_on_group_dashboard) ? 'checked' : '' }} id="is_sequence_visible_on_group_dashboard">
                <label class="form-check-label ml-1" for="is_sequence_visible_on_group_dashboard" style="font-size: 0.95em;"> Show sequence carousel on group dashboard</label>
              </div>

              <div class="row form-check">
                <input type="checkbox" name="is_completion_shoutouts_enabled" {{ ($group->sequence->is_completion_shoutouts_enabled) ? 'checked' : '' }} id="is_completion_shoutouts_enabled">
                <label class="form-check-label ml-1" for="is_completion_shoutouts_enabled" style="font-size: 0.95em;"> Create shoutout for users who complete sequence</label>
              </div>

              @if(false)
              <div class="form-row mb-3">
                <div class="col-6">
                  <label for="completed_thumbnail_image_path" class="d-block">Sequence completed thumbnail</label>
                  @if($group->sequence->completed_thumbnail_image_path)
                  <img src="{{ $group->sequence->completed_thumbnail_image_path }}" style="max-width: 250px; margin-bottom: 1em;">
                  <p class="text-sm text-muted">To change, upload a new image:</p>
                  @else
                  <p class="text-sm text-muted">To set, upload an image:</p>
                  @endif
                  <input type="file" name="completed_thumbnail_image_path" id="completed_thumbnail_image_path">
                </div>
              </div>
              <p class="text-small text-muted">Recommended thumbnail size: Ratio 5:3, Minimum size of 500x300px</p>
              @endif

              <div class="form-group">
                <label for="name">Completion badge (optional)</label>
                <select name="completed_badge_id" class="form-control" style="max-width: 700px;">
                  <option value="null"{{ ($group->sequence->completed_badge_id == null) ? ' selected' : '' }}>None</option>
                  @foreach(\App\Taxonomy::where('is_badge', 1)->get() as $taxonomy)
                    <optgroup label="{{ $taxonomy->name }}">
                      @foreach($taxonomy->options as $option)
                        <option value="{{ $option->id }}"{{ ($group->sequence->completed_badge_id == $option->id) ? ' selected' : '' }}>{{ $option->name }}</option>
                      @endforeach
                    </optgroup>
                  @endforeach
                </select>
              </div>

              <button type="submit" class="btn btn-primary">Save</button>
            </form>
            <p>
              <a href="/groups/{{ $group->slug }}/sequence">Edit this group's sequence <i class="fas fa-angle-right"></i></a>
            </p>
          @else
            <p>This group does not have a sequence for users to interact with step-by-step posts.</p>
            <form action="/groups/{{ $group->slug }}/sequence" method="post">
              @csrf
              <button type="submit" class="btn btn-primary">Create sequence</button>
            </form>
          @endif
        </div>
      </div>
    </div>
    @endif
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
<script>
  if(!window.location.hash)
    showNav('#general');
  else if (window.location.hash == 'sequence')
    showNav('#sequence');

  var app = new Vue({
    el: "#custom_menu_form",
    data: {
      groups: {!! ($group->custom_menu) ? "JSON.parse('".addslashes($group->getRawOriginal('custom_menu'))."').groups" : "[]" !!},
      spanishGroups: {!! ($group->customMenuWithLocale('es')) ? "JSON.parse('".addslashes(json_encode($group->customMenuWithLocale('es')))."').groups" : "[]" !!},
    },
    methods: {
      addLink: function (group) {
        group.links.push({
          title: '',
          url: ''
        })
      },
      addSpanishLink: function (group) {
        group.links.push({
          title: '',
          url: ''
        })
      },
      deleteLink: function (group, index) {
        group.links.splice(index, 1);
      },
      deleteSpanishLink: function (group, index) {
        group.links.splice(index, 1);
      },
      addGroup: function () {
        this.groups.push({
          title: '',
          links: [],
        })
      },
      addSpanishGroup: function () {
        this.spanishGroups.push({
          title: '',
          links: [],
        })
      },
      deleteGroup: function (index) {
        this.groups.splice(index, 1);
      },
      deleteGroup: function (index) {
        this.spanishGroups.splice(index, 1);
      },
      save: function () {
        $('#customMenuJson').val(JSON.stringify(this.$data));
        if($('#custom_menu_form').valid())
          $('#custom_menu_form').submit();
      }
    },
    created: function () {
    }
  });

  $('#is_virtual_room_enabled').change(function(e) {
        $('#header_bg_image_container').toggleClass('d-none');
      });
      $(document).ready(function(){
        var str = '{{ $group->banner_cta_url }}';
        str = str.replace(/[quot;&]/g,'');
        str = str.replace('[', '');
        str = str.replace(']', '');
        var users = str.split(',');
        $('.selectpicker').selectpicker('val', users);

        $('.nav-link').click( function(e) {
            showNav($(this).attr('href'));
        });

        if(window.location.hash) 
        {
          showNav(window.location.hash);
        }
        else 
          showNav('#general');
      });

      $('input[name="is_chat_room_enabled"]').on('click', function () {
        if ($('input[name="is_chat_room_enabled"]:checked').val() == "true")
          $('#chatRoomTimeFrame').removeClass('d-none');
        if ($('input[name="is_chat_room_enabled"]:checked').val() == "false")
          $('#chatRoomTimeFrame').addClass('d-none');
      });
      $('#live_chat_start_date').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'mm/dd/yy'
      });
      $('#live_chat_start_time').timepicker({
          timeFormat: 'h:mm p',
          dropdown: true,
      });
      $('#live_chat_end_date').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'mm/dd/yy'
      });
      $('#live_chat_end_time').timepicker({
          timeFormat: 'h:mm p',
          dropdown: true,
      });

      $('.image_revert').change(function () {
        if($(this).prop('checked'))
        {
          $('label[for="'+ $(this).attr('name') +'"]').css('color', '#c71c1c');
        }
        else
        {
          $('label[for="'+ $(this).attr('name') +'"]').css('color', '#363a3d');
        }

      });

      function showNav(id)
      {
        $(id + 'Nav').tab('show');
        $('.nav-link').removeClass('active');
        $(id + 'Nav').addClass('active');
      }
</script>
@endsection