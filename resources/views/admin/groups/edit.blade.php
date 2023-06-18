@extends('admin.groups.layout')

@section('head')
    @parent
  <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
@endsection

@section('inner-page-content')
@foreach($errors->all() as $message)
  <div class="alert alert-dismissible alert-danger" style="max-width:450px;">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>{!! $message !!}</strong>
  </div>
@endforeach
<div class="card col-md-8 px-0">
  <ul class="nav nav-tabs justify-content-start px-3 pt-2" role="tablist" style="background-color: #dedede;">
      <li class="nav-item">
          <a id="generalNav" class="nav-link" href="#general" data-toggle="tab" role="tab" aria-controls="general"><b>General</b></a>
      </li>
      <li class="nav-item">
          <a id="permissionsNav" class="nav-link" href="#permissions" data-toggle="tab" role="tab" aria-controls="permissions"><b>Permissions</b></a>
      </li>
      <li class="nav-item">
          <a id="enabled_featuresNav" class="nav-link" href="#enabled_features" data-toggle="tab" role="tab" aria-controls="enabled_features"><b>Enabled features</b></a>
      </li>
      <li class="nav-item">
          <a id="welcomeMessageNav" class="nav-link" href="#welcomeMessage" data-toggle="tab" role="tab" aria-controls="welcomeMessage"><b>Welcome Message</b></a>
      </li>
      <li class="nav-item">
          <a id="miscNav" class="nav-link" href="#misc" data-toggle="tab" role="tab" aria-controls="misc"><b>Misc</b></a>
      </li>
  </ul>
  <form id="form" method="post" action="/admin/groups/{{ $group->id }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
  <div class="tab-content">



    <div id="general" class="tab-pane fade py-3 px-3" role="tabpanel" aria-labelledby="general-tab">
        @include('components.multi-language-text-input', ['name' => 'name', 'value' => $group->name, 'label' => 'Group name', 'localization' => $group->localization])
        <div class="form-group mb-2" style="max-width: 600px;">
            <label for="slug">Your vanity URL</label>
            @include('partials.subtext', ['subtext' => 'This is the direct link/address for accessing this group'])
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon3">{{ config('app.url') }}/groups/</span>
                </div>
              <input style="min-width: 150px; max-width: 250px;" value="{{ $group->slug }}" type="text" name="slug" id="slug" class="form-control" required aria-describedby="basic-addon3">
            </div>
        </div>

        <div class="form-group mb-3" style="max-width: 450px;">
          <label for="parent_group_id">Parent Group (optional)</label>
          <select class="select-picker form-control" id="parent_group_id" name="parent_group_id" data-live-search="true">
            <option {{ (request()->has('parent')) ? '' : ' selected' }} value="">Select one</option>
            @foreach($groups as $otherGroup)
                <option value="{{ $otherGroup->id }}"{{ ($group->parent_group_id == $otherGroup->id) ? ' selected' : '' }}>{{ $otherGroup->name }}</option>
            @endforeach
          </select>
        </div>

        @if(!$group->parent_group_id)
        <div class="d-flex mb-0 ml-1">
          <div class="form-group w-50">
            <label for="dashboard_header">Menu Header for Group Name</label>
            <select class="select-picker form-control mr-2" id="dashboard_header_dropdown" name="dashboard_header">
              @foreach($existingHeaders as $header)
                <option value="{{ $header->dashboard_header }}"{{ ($group->dashboard_header == $header->dashboard_header) ? ' selected' : '' }}>{{ $header->dashboard_header }}</option>
              @endforeach
              <option value="new_header">- Create new header -</option>
            </select>
          </div>
          @if(getsetting('is_localization_enabled'))
            <div class="form-group ml-3 w-50">
              <label for="localization[es][dashboard_header]">Spanish</label>
              <input type="text" class="form-control" name="localization[es][dashboard_header]" id="localization[es][dashboard_header]" value="{{ isset($group->localization) && isset($group->localization['es']) && isset($group->localization['es']['dashboard_header']) ? $group->localization['es']['dashboard_header'] : '' }}">
            </div>
          @endif
        </div>
        @endif

        <div class="form-group">
          <input id="dashboard_header_custom" style="max-width: 400px;" maxlength="35" class="mt-3 form-control d-none" name="dashboard_header_custom" placeholder="Type new custom header...">
          <small class="text-muted">Categorization of groups displayed on the dashboard</small>
        </div>

        <div class="form-group w-50">
          <label for="join_code">Join code <small class="text-muted">(through profile setup and account page)</small></label>
          <input type="text" name="join_code" id="join_code" class="form-control" value="{{ $group->join_code }}">
        </div>

        <div class="form-check mb-3 mt-3">
          <input type="hidden" name="is_private" value="0">
          <input id="is_private" type="checkbox" class="form-check-input" {{ $group->is_private ? 'checked' : ''}} name="is_private" value="1">
          <label class="form-check-label" for="is_private">Private group</label>
          @include('partials.subtext', ['subtext' => 'Users can only be added or invited to this group by administrators.'])
        </div>

        @if($group->parent)
          <div class="form-check mb-3">
            <input type="hidden" name="publish_to_parent_feed" value="0">
            <input class="form-check-input" type="checkbox" name="publish_to_parent_feed" {{ $group->publish_to_parent_feed ? 'checked' : ''}} value="1">
            <label class="form-check-label" for="publish_to_parent_feed"> Publish group activity (posts/events/content, etc.) to the parent group’s activity feed @include('partials.subtext', ['subtext' => 'Check this box if you want group activity to show up on the parent group\’s activity feed. Keep in mind, if checked, the settings for the parent group will determine where the activity is subsequently posted (to higher-level parent groups and/or personalized dashboards of those group\’s members). If left unchecked, activity will only show up in this group\’s activity feed, and the personal dashboard of this group\’s members.'])</label>
          </div>
        @endif
          <div class="form-check mt-3 mb-3">
            <input type="hidden" name="publish_to_dashboard_feed" value="0">
            <input class="form-check-input" type="checkbox" name="publish_to_dashboard_feed" {{ $group->publish_to_dashboard_feed ? 'checked' : ''}} value="1">
            <label class="form-check-label" for="publish_to_dashboard_feed"> Publish posts to personalized dashboard</label>
            @include('partials.subtext', ['subtext' => 'Check this box if you want group activity to show up in group members\' personalized dashboard feed.'])
          </div>

        
        <div class="{{ $group->parent_group_id ? '' : 'd-none' }}">
          <div class="form-check mb-3">
            <input type="hidden" name="should_display_dashboard" value="0">
            <input type="checkbox" class="form-check-input" name="should_display_dashboard" {{ $group->should_display_dashboard ? 'checked' : ''}} value="1">
            <label class="form-check-label" for="should_display_dashboard">Display on personalized dashboard of group members </label>
            @include('partials.subtext', ['subtext' => 'This option only applies to subgroups, not parent groups. Check this box if you want this group name with a link to show up on the left-hand menu of a user’s personalized dashboard. Think of this as a shortcut, where users can easily access groups they will visit frequently. If selected, be sure to edit the “Menu Header for Group Name” with a name that will help users identify the category for this group.'])
          </div>
          <div class="form-check mb-3" id="is_joinable_input">
            <input type="hidden" name="is_joinable" value="0">
            <input id="is_joinable" type="checkbox" class="form-check-input" name="is_joinable" {{ $group->is_joinable ? 'checked' : ''}} value="1">
            <label class="form-check-label" for="is_joinable">Joinable <span class="text-muted">(through profile setup and onboarding)</span></label>
          </div>
        </div>
        <div class="d-flex my-4">
          <div class="form-group">
            <label>Group Thumbnail Image</label>
            @include('components.upload', ['name' => 'thumbnail_image', 'value' => $group->thumbnail_image_path ? $group->thumbnail_image_path : '', 'noRemove' => false])
            <span class="text-muted text-small">Recommended size: 350x180</span>
          </div>
        </div>
        
        @if(!$group->is_virtual_room_enabled)
        <div id="header_bg_image_container" class="form-group mt-2">
          <label for="name">Header Background Image</label>
          @include('components.upload', ['name' => 'header_bg_image', 'value' => $group->header_bg_image_url ? $group->header_bg_image_url : '', 'noRemove' => false])
          <span class="text-muted text-small">Recommended size: 1900x450</span>
        </div>
        @endif

        <div class="d-flex justify-content-end">
          <button class="btn btn-info" type="submit">@lang('general.save') changes</button>
        </div>

    </div>


    <div id="enabled_features" class="tab-pane fade py-3 px-3" role="tabpanel" aria-labelledby="enabled_features-tab">
        <div class="col-md-6 mb-3 pl-0">
          <div class="row ml-0">
            <p>Select which functions you want active for this group.</p>
          </div>
          
          <div class="row form-check mb-1">
            <input type="hidden" name="is_posts_enabled" value="0">
            <input type="checkbox" name="is_posts_enabled" id="is_posts_enabled" {{ $group->is_posts_enabled ? 'checked' : ''}} value="1">
            <label class="form-check-label ml-2" for="is_posts_enabled"> Posts</label>
          </div>
          <div class="row form-check mb-1">
            <input type="hidden" name="is_events_enabled" value="0">
            <input type="checkbox" name="is_events_enabled" id="is_events_enabled" {{ $group->is_events_enabled ? 'checked' : ''}} value="1">
            <label class="form-check-label ml-2" for="is_events_enabled"> Events</label>
          </div>
          <div class="row form-check mb-1">
            <input type="hidden" name="is_content_enabled" value="0">
            <input type="checkbox" name="is_content_enabled" id="is_content_enabled" {{ $group->is_content_enabled ? 'checked' : ''}} value="1">
            <label class="form-check-label ml-2" for="is_content_enabled"> Content</label>
          </div>
          <div class="row form-check mb-1">
            <input type="hidden" name="is_shoutouts_enabled" value="0">
            <input type="checkbox" name="is_shoutouts_enabled" id="is_shoutouts_enabled" {{ $group->is_shoutouts_enabled ? 'checked' : ''}} value="1">
            <label class="form-check-label ml-2" for="is_shoutouts_enabled"> Shoutouts</label>
          </div>
          <div class="row form-check mb-1">
            <input type="hidden" name="is_files_enabled" value="0">
            <input type="checkbox" name="is_files_enabled" id="is_files_enabled" {{ $group->is_files_enabled ? 'checked' : ''}} value="1">
            <label class="form-check-label ml-2" for="is_files_enabled"> Files</label>
          </div>
          <div class="row form-check mb-1">
            <input type="hidden" name="is_budgets_enabled" value="0">
            <input type="checkbox" name="is_budgets_enabled" id="is_budgets_enabled" {{ $group->is_budgets_enabled ? 'checked' : ''}} value="1">
            <label class="form-check-label ml-2" for="is_budgets_enabled"> Budgets</label>
          </div>
          <div class="row form-check mb-2">
            <input type="hidden" name="is_discussions_enabled" value="0">
            <input type="checkbox" name="is_discussions_enabled" id="is_discussions_enabled" {{ $group->is_discussions_enabled ? 'checked' : ''}} value="1">
            <label class="form-check-label ml-2" for="is_discussions_enabled"> Discussions</label>
          </div>
          <div class="row ml-2">
            <small class="text-muted mb-2">If unchecked, that content type will not show up in this group's feed.</small>
          </div>
        </div>
        <hr>
        <div class="form-check mb-3 mt-2">
          <input type="checkbox" class="form-check-input" id="is_virtual_room_enabled" {{ $group->is_virtual_room_enabled ? 'checked' : ''}} name="is_virtual_room_enabled" value="1">
          <label class="form-check-label" for="is_virtual_room_enabled">Enable Interactive Header Image</label> @include('partials.subtext', ['subtext' => 'This replaces the Header Background image, and will disable the Header Background image when selected.'])
        </div>
        @if(getsetting('is_sequence_enabled'))
        <div class="form-check mb-3 mt-2">
          <input type="checkbox" class="form-check-input" id="is_sequence_enabled" {{ $group->is_sequence_enabled ? 'checked' : ''}} name="is_sequence_enabled" value="1">
          <label class="form-check-label" for="is_sequence_enabled">Enable Learning Modules</label>
        </div>
        @endif

        <div class="form-check mb-3 mt-2">
          <input type="checkbox" class="form-check-input" id="is_lounge_enabled" {{ $group->is_lounge_enabled ? 'checked' : ''}} name="is_lounge_enabled" value="1">
          <label class="form-check-label" for="is_lounge_enabled">Enable Networking Lounge</label>
        </div>

        @if(is_zoom_enabled())

        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" name="enable_zoom" id="enable_zoom" {{ $group->zoom_meeting_id ? 'checked' : '' }}>
          <label class="form-check-label" for="enable_zoom" style="font-size: 1em;">
            @lang('events.Allow Zoom Meeting')
          </label>
        </div>
        <div class="d-none mb-2" id="zoom_details_container">
          <div class="form-group">
            <label for="zoom_meeting_link">Zoom Meeting Invite Link</label>
            <input type="text" name="zoom_meeting_link" id="zoom_meeting_link" class="form-control zoom_details" value="{{ $group->zoom_invite_link }}">
            <small class="text-muted">Host must join through zoom application.</small>
          </div>
        </div>
        @if($group->zoom_meeting_id)
          <div class="form-group videoInput">
            <p class="mb-2">Video conference room</p>
            <div class="form-check">
              <input type="radio" class="form-check-input" name="is_video_room_enabled" id="video_room_enabled" value="1"{{ (optional($group->videoRoom)->is_enabled) ? ' checked': '' }}>
              <label class="form-check-label" for="video_room_enabled">Enabled</label>
            </div>
            <div class="form-check">
              <input type="radio" class="form-check-input" name="is_video_room_enabled" id="video_room_disabled" value="0"{{ (!optional($group->videoRoom)->is_enabled) ? ' checked': '' }}>
              <label class="form-check-label" for="video_room_disabled">Disabled</label>
            </div>
          </div>

          <div class="form-group videoInput">
            <p class="mb-2">Auto open video conference?</p>
            <div class="form-check">
              <input type="radio" class="form-check-input" name="is_video_room_auto_open" id="video_room_auto_open_true" value="1"{{ (optional($group->videoRoom)->auto_open) ? ' checked': '' }}>
              <label class="form-check-label" for="video_room_auto_open_true">Auto open</label>
            </div>
            <div class="form-check">
              <input type="radio" class="form-check-input" name="is_video_room_auto_open" id="video_room_auto_open_false" value="0"{{ (!optional($group->videoRoom)->auto_open) ? ' checked': '' }}>
              <label class="form-check-label" for="video_room_auto_open_false">Prompt to open</label>
            </div>
          </div>
        @endif

        @endif

        <div class="form-group">
          <p class="mb-2">Live chat</p>
          <div class="form-check">
            <input type="radio" class="form-check-input" name="is_chat_room_enabled" id="live_chat_enabled" value="true"{{ (optional($group->chatRoom)->is_enabled) ? ' checked': '' }}>
            <label class="form-check-label" for="live_chat_enabled">Enabled</label>
          </div>
          <div class="form-check">
            <input type="radio" class="form-check-input" name="is_chat_room_enabled" id="live_chat_disabled" value="false"{{ (!optional($group->chatRoom)->is_enabled) ? ' checked': '' }}>
            <label class="form-check-label" for="live_chat_disabled">Disabled</label>
          </div>
        </div>

        <div class="{{ ($group->chatRoom && $group->chatRoom->is_enabled && ($group->virtualRoom || $group->header_bg_image_path)) ? '' : 'd-none' }}">
          <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="should_live_chat_display_below_header_image" name="should_live_chat_display_below_header_image" {{ $group->should_live_chat_display_below_header_image ? 'checked' : '' }}>
            <label class="form-check-label" for="should_live_chat_display_below_header_image">Display live chat below header image</label>
          </div>
        </div>

        <div class="{{ ($group->chatRoom && $group->chatRoom->is_enabled) ? '' : 'd-none' }}" style="max-width: 400px;" id="chatRoomTimeFrame">

          <div class="form-group">
            <p class="font-weight-bold mb-1">Live chat start</p>
            <div class="form-row">
              <div class="col-6">
                <label for="live_chat_start_date">Date</label>
                <input type="text" name="live_chat_start_date" class="form-control" value="{{ ($group->chatRoom && $group->chatRoom->start_at) ? $group->chatRoom->start_at->format('m/d/y') : '' }}" placeholder="mm/dd/yy" id="live_chat_start_date">
              </div>
              <div class="col-6">
                <label for="live_chat_start_date">Time</label>
                 <input type="text" name="live_chat_start_time" class="form-control" value="{{ ($group->chatRoom && $group->chatRoom->start_at) ? $group->chatRoom->start_at->tz(request()->user()->timezone)->format('g:i a') : '' }}" placeholder="hh:mm pm" id="live_chat_start_time">
              </div>
            </div>
          </div>
          <div class="form-group">
            <p class="font-weight-bold mb-1">Live chat end</p>
            <div class="form-row">
              <div class="col-6">
                <label for="live_chat_end_date">Date</label>
                <input type="text" name="live_chat_end_date" class="form-control" value="{{ ($group->chatRoom && $group->chatRoom->end_at) ? $group->chatRoom->end_at->format('m/d/y') : '' }}" placeholder="mm/dd/yy" id="live_chat_end_date">
              </div>
              <div class="col-6">
                <label for="live_chat_end_time">Time</label>
                 <input type="text" name="live_chat_end_time" class="form-control" value="{{ ($group->chatRoom && $group->chatRoom->end_at) ? $group->chatRoom->end_at->tz(request()->user()->timezone)->format('g:i a') : '' }}" placeholder="hh:mm pm" id="live_chat_end_time">
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <button class="btn btn-info" type="submit">@lang('general.save') changes</button>
        </div>
    </div>

    <div id="permissions" class="tab-pane fade py-3 px-3" role="tabpanel" aria-labelledby="permissions-tab">
        <div class="col-md-8 mb-3">
          <div class="row mb-3">
            <b>Permissions</b>
          </div>
          <span class="row ml-0 mb-3">Allow group admins to...</span>
          <div class="row form-check mb-2">
            <input type="hidden" name="is_email_campaigns_enabled" value="0">
            <input type="checkbox" name="is_email_campaigns_enabled" id="is_email_campaigns_enabled" {{ $group->is_email_campaigns_enabled ? 'checked' : ''}} value="1">
            <label class="form-check-label ml-2" for="is_email_campaigns_enabled">Send email campaigns</label>
          </div>
          <div class="row form-check mb-2">
            <input type="hidden" name="can_ga_toggle_content_types" value="0">
            <input type="checkbox" name="can_ga_toggle_content_types" id="can_ga_toggle_content_types" {{ $group->can_ga_toggle_content_types ? 'checked' : ''}} value="1">
            <label class="form-check-label ml-2" for="can_ga_toggle_content_types">Toggle content types within group</label>
          </div>
          <div class="row form-check mb-2">
            <input type="checkbox" name="is_reporting_enabled" id="is_reporting_enabled" {{ $group->is_reporting_enabled ? 'checked' : ''}} value="1">
            <label class="form-check-label ml-2" for="is_reporting_enabled">Access reporting</label>
          </div>
          <div class="row form-check mb-2">
            <input type="checkbox" name="is_reporting_user_data_enabled" id="is_reporting_user_data_enabled" {{ $group->is_reporting_user_data_enabled ? 'checked' : ''}} value="1">
            <label class="form-check-label ml-2" for="is_reporting_user_data_enabled">Access user breakdown in reports</label>
          </div>
          <div class="row form-check mb-2">
            <input type="hidden" name="can_ga_set_live_chat" value="0">
            <input type="checkbox" name="can_ga_set_live_chat" id="can_ga_set_live_chat" {{ $group->can_ga_set_live_chat ? 'checked' : ''}} value="1">
            <label class="form-check-label ml-2" for="can_ga_set_live_chat">Toggle & manage live chat</label>
          </div>
          @if(!$group->is_private)
          <div class="row form-check mb-2">
            <input type="checkbox" name="can_group_admins_invite_other_groups_to_events" id="can_group_admins_invite_other_groups_to_events" {{ $group->can_group_admins_invite_other_groups_to_events ? 'checked' : ''}} value="1">
            <label class="form-check-label ml-2" for="can_group_admins_invite_other_groups_to_events">Invite other groups to events</label>
            @include('partials.subtext', ['subtext' => 'Uncheck this box if you do not want this group to be able to share its events outside this group. This is commonly unchecked when the group is a sponsor/vendor, etc.'])
          </div>
          @endif
          <div class="row form-check mb-2">
            <input type="checkbox" name="can_group_admins_schedule_posts" id="can_group_admins_schedule_posts" {{ $group->can_group_admins_schedule_posts ? 'checked' : ''}} value="1">
            <label class="form-check-label ml-2" for="can_group_admins_schedule_posts">Schedule posts</label>
          </div>
          <div class="row form-check mb-2">
            <input type="checkbox" name="can_ga_order_posts" id="can_ga_order_posts" {{ $group->can_ga_order_posts ? 'checked' : ''}} value="1">
            <label class="form-check-label ml-2" for="can_ga_order_posts">Order posts</label>
          </div>
          <div class="mt-3">
            <span class="row ml-0 mb-3">Allow users to...</span>
            <div class="row form-check mb-2 mt-2">
              <input type="hidden" name="can_users_post_events" value="0">
              <input type="checkbox" name="can_users_post_events" id="can_users_post_events" {{ $group->can_users_post_events ? 'checked' : ''}} value="1">
              <label class="form-check-label ml-2" for="can_users_post_events"> Post Events</label>
            </div>
            <div class="row form-check mb-2 mt-2">
              <input type="hidden" name="can_users_post_shoutouts" value="0">
              <input type="checkbox" name="can_users_post_shoutouts" id="can_users_post_shoutouts" {{ $group->can_users_post_shoutouts ? 'checked' : ''}} value="1">
              <label class="form-check-label ml-2" for="can_users_post_shoutouts"> Post Shoutouts</label>
            </div>
            <div class="row form-check mb-2 mt-2">
              <input type="hidden" name="can_users_post_content" value="0">
              <input type="checkbox" name="can_users_post_content" id="can_users_post_content" {{ $group->can_users_post_content ? 'checked' : ''}} value="1">
              <label class="form-check-label ml-2" for="can_users_post_content"> Post Content</label>
            </div>
            <div class="row form-check mb-2 mt-2">
              <input type="hidden" name="can_users_post_text" value="0">
              <input type="checkbox" name="can_users_post_text" id="can_users_post_text" {{ $group->can_users_post_text ? 'checked' : ''}} value="1">
              <label class="form-check-label ml-2" for="can_users_post_text"> Post Text</label>
            </div>
            <div class="row form-check mb-2 mt-2">
              <input type="hidden" name="can_users_post_discussions" value="0">
              <input type="checkbox" name="can_users_post_discussions" id="can_users_post_discussions" {{ $group->can_users_post_discussions ? 'checked' : ''}} value="1">
              <label class="form-check-label ml-2" for="can_users_post_discussions"> Post Discussions</label>
            </div>
            <div class="row form-check mb-2 mt-2">
              <input type="hidden" name="can_users_upload_files" value="0">
              <input type="checkbox" name="can_users_upload_files" id="can_users_upload_files" {{ $group->can_users_upload_files ? 'checked' : ''}} value="1">
              <label class="form-check-label ml-2" for="can_users_upload_files"> Upload Files</label>
            </div>
            <div class="d-none">
              <div class="row form-check mb-2 mt-2">
                <input type="hidden" name="can_users_invite" value="0">
                <input type="checkbox" name="can_users_invite" id="can_users_invite" {{ $group->can_users_invite ? 'checked' : ''}} value="1">
                <label class="form-check-label ml-2" for="can_users_invite"> Invite Users</label>
              </div>
              <div class="row form-check mb-2 mt-2">
                <input type="hidden" name="can_users_message_group" value="0">
                <input type="checkbox" name="can_users_message_group" id="can_users_message_group" {{ $group->can_users_message_group ? 'checked' : ''}} value="1">
                <label class="form-check-label ml-2" for="can_users_message_group"> Message Group</label>
              </div>
            </div>
          </div>

          <div class="form-group mt-2">
            <label for="join_via_registration_page">Restrict access to non-members via registration page...</label>
            <select class="custom-select" name="join_via_registration_page" id="join_via_registration_page">
              <option value="">None</option>
              @foreach($pages as $page)
              <option value="{{ $page->id }}" {{ $group->join_via_registration_page == $page->id ? 'selected' : '' }}>{{ $page->name }}</option>
              @endforeach
            </select>
            <small class="text-muted">If selected, when a non-member attempts to view this group users will be required to fill out a registration page, tickets and add-ons.</small>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <button class="btn btn-info" type="submit">@lang('general.save') changes</button>
        </div>
    </div>


    <div id="misc" class="tab-pane fade py-3 px-3" role="tabpanel" aria-labelledby="misc-tab">
        @include('components.multi-language-text-input', ['label' => 'Group home page name', 'name' => 'home_page_name', 'value' => $group->home_page, 'localization' => $group->localization, 'required' => 'true', 'maxLength' => 100])

        @include('components.multi-language-text-input', ['label' => 'Posts page name', 'name' => 'posts_page_name', 'value' => $group->posts_page, 'localization' => $group->localization, 'required' => 'true', 'maxLength' => 100])

        @include('components.multi-language-text-input', ['label' => 'Content page name', 'name' => 'content_page_name', 'value' => $group->content_page, 'localization' => $group->localization, 'required' => 'true', 'maxLength' => 100])

        @include('components.multi-language-text-input', ['label' => 'Calendar page name', 'name' => 'calendar_page_name', 'value' => $group->calendar_page, 'localization' => $group->localization, 'required' => 'true', 'maxLength' => 100])

        @include('components.multi-language-text-input', ['label' => 'Shoutouts page name', 'name' => 'shoutouts_page_name', 'value' => $group->shoutouts_page, 'localization' => $group->localization, 'required' => 'true', 'maxLength' => 100])

        @include('components.multi-language-text-input', ['label' => 'Discussions page name', 'name' => 'discussions_page_name', 'value' => $group->discussions_page, 'localization' => $group->localization, 'required' => 'true', 'maxLength' => 100])

        @include('components.multi-language-text-input', ['label' => 'Subgroups page name', 'name' => 'subgroups_page_name', 'value' => $group->subgroups_page_name, 'localization' => $group->localization, 'required' => 'true', 'maxLength' => 100])

        @include('components.multi-language-text-input', ['label' => 'Members page name', 'name' => 'members_page_name', 'value' => $group->members_page, 'localization' => $group->localization, 'required' => 'true', 'maxLength' => 100])

        @include('components.multi-language-text-input', ['label' => 'Files page name', 'name' => 'files_alias', 'value' => $group->files_alias, 'localization' => $group->localization, 'required' => 'true', 'maxLength' => 100])

          <div class="form-group mb-4" id="filesalias">
            <label class="form-check-label" for="embed_code">Embed Code (HTML)</label>
            <textarea class="form-control" name="embed_code" id="embed_code">{!! $group->embed_code !!}</textarea>
          </div>
        <div class="d-flex justify-content-end">
          <button class="btn btn-info" type="submit">@lang('general.save') changes</button>
        </div>
    </div>

    <div id="welcomeMessage" class="tab-pane fade py-3 px-3" role="tabpanel" aria-labelledby="welcomeMessage-tab">
        
        <div class="row form-check mb-3 mt-2">
            <input type="checkbox" name="is_welcome_message_enabled" id="is_welcome_message_enabled" {{ $group->is_welcome_message_enabled ? 'checked' : ''}}>
            <label class="form-check-label ml-2" for="is_welcome_message_enabled"> Send Welcome Message When A User Joins Group Via Access Code</label>
          </div>

        <div class="form-group">
          <label for="welcome_message_sending_user_id">User to send message from</label>
          <select name="welcome_message_sending_user_id" id="welcome_message_sending_user_id" class="form-control">
            <option disabled{{ ($group->welcome_message_sending_user_id == null) ? ' selected' : '' }}>Select one</option>
            @foreach($group->users()->orderBy('name', 'asc')->get() as $user)
              <option value="{{ $user->id }}"{{ ($group->welcome_message_sending_user_id == $user->id) ? ' selected' : '' }}>{{ $user->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="welcome_message">Message</label>
          <textarea name="welcome_message" class="form-control" rows="7">{{ $group->welcome_message }}</textarea>
        </div>

        <div class="d-flex justify-content-end">
          <button class="btn btn-info" type="submit">@lang('general.save') changes</button>
        </div>
    </div>
  </div>
  </form>
</div>
<form action="/admin/groups/{{ $group->id }}" method="post" class="mt-5">
  @csrf
  @method('delete')
  <button type="submit" class="btn btn-sm btn-danger" id="deleteGroup">Delete group</button>
</form>
@endsection

@section('scripts')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
  <script>

    $('#enable_zoom').change(function() {
      if($(this).is(':checked'))
      {
        $('#zoom_meeting_link').prop('required', true);
        $('#zoom_details_container').removeClass('d-none');
      }
      else
      {
        $('#zoom_meeting_link').prop('required', false);
        $('#zoom_details_container').addClass('d-none');
      }
    });
    $(document).ready(function(){
      if(window.location.hash) 
      {
        $(window.location.hash + 'Nav').tab('show');
      }
      else 
      {
        $('.nav-link').removeClass('active');
        $('#general').tab('show');
        $('#generalNav').addClass('active');
      }

      if($('#enable_zoom').is(':checked'))
      {
        $('#zoom_meeting_link').prop('required', true);
        $('#zoom_details_container').removeClass('d-none');
      }
      else
      {
        $('#zoom_meeting_link').prop('required', false);
        $('#zoom_details_container').addClass('d-none');
      }

      if({{ $group->is_private }})
        $('#is_joinable_input').addClass('d-none');
    });
    $('#deleteGroup').on('click', function(event) {
      event.preventDefault();
      if (confirm('Delete this group? This cannot be undone.'))
        $('#deleteGroup').parent().submit();
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

    $('#is_private').change(function(e) {
      if($(this).is(':checked'))
        $('#is_joinable_input').addClass('d-none');
      else
        $('#is_joinable_input').removeClass('d-none');
    });

    $('#form').submit(function(e) {
        var input = $("input[name=slug]");
        input.val(string_to_slug(input.val()));
    });

    $("#slug").keyup(function(e){
        if(e.key != ' ')
            $(this).val(string_to_slug($(this).val()));
    });

    $('#is_virtual_room_enabled').change(function(e) {
      $('#header_bg_image_container').toggleClass('d-none');
    });

    $('#dashboard_header_dropdown').on('change', function (e) {
      if($(this).val() == 'new_header') {
        $('#dashboard_header_custom').removeClass('d-none');
      } else {
        $('#dashboard_header_custom').val('');
        $('#dashboard_header_custom').addClass('d-none');
      }
    })

    function string_to_slug (str) {
        str = str.replace(/^\s+|\s+$/g, ''); // trim
        str = str.toLowerCase();
      
        // remove accents, swap ñ for n, etc
        var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
        var to   = "aaaaeeeeiiiioooouuuunc------";
        for (var i=0, l=from.length ; i<l ; i++) {
            str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
        }

        str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
            .replace(/\s+/g, '-') // collapse whitespace and replace by -
            .replace(/-+/g, '-'); // collapse dashes
            console.log(str);
        return str;
    }
  </script>
@endsection