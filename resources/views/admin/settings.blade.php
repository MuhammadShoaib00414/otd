@extends('admin.layout')

@section('page-content')
<div class="d-flex justify-content-between">
  <h5>Settings</h5>
  @if($authUser->is_super_admin)
  <a href="/admin/instance-settings">Instance Settings</a>
  @endif
</div>

<div class="mb-5">
  <form method="post" enctype="multipart/form-data" id="form-submission">
    <div class="row">
      <div class="col-lg-6">
        @csrf
        @include('components.multi-language-text-input', ['label' => 'Organization name', 'name' => 'name', 'value' => $orgName->value, 'localization' => $orgName->localization, 'specificName' => 'true'])

        @include('components.multi-language-text-input', ['label' => 'Send Emails From', 'name' => 'from_email_name', 'value' => $from_email_name->value, 'localization' => $from_email_name->localization, 'specificName' => 'true'])

        <hr>
       
        <div class="mt-3 mb-4 card card-body">
          <div>
            <label class="form-label d-block"><b>Dashboard header type</b></label>
            <div class="mb-3">
              <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="image" name="dashboard_header_type" value="image" class="custom-control-input" {{ (getSetting('is_dashboard_virtual_room_enabled') == 0) ? ' checked' : '' }}>
                <label class="custom-control-label" for="image">Image</label>
              </div>
              <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="virtual_room" name="dashboard_header_type" value="virtual_room" class="custom-control-input" {{ (getSetting('is_dashboard_virtual_room_enabled') == 1) ? ' checked' : '' }}>
                <label class="custom-control-label" for="virtual_room">Interactive Header Image</label>
              </div>
            </div>
            <hr>
          </div>
          <div id="dashboardTypeImage" class="{{ (getSetting('is_dashboard_virtual_room_enabled') == 0) ? '' : 'd-none' }}">
            <div class="form-row">
              @if($dashboard_header_image && $dashboard_header_image->value)
              <img class="mb-4" style="max-width: 450px; max-height: 450px;" src="{{ $dashboard_header_image->value }}">
              <br>
              <div class="col">
                <p class="d-flex">Upload an image to change:</p>
                <input class="mt-2" accept=".png, .jpeg, .jpg" type="file" name="dashboard_header_image" id="dashboard_header_image">
                <br>
                <small class="text-muted">(Recommended size: 1900x450)</small>
              </div>
              @else
              <input class="mt-2 ml-1" accept=".png, .jpeg, .jpg" type="file" name="dashboard_header_image" id="dashboard_header_image">
              @endif
            </div>
            <div class="form-check mt-2">
              <input class="mt-2 image_revert form-check-input" type="checkbox" id="dashboard_header_image_revert" name="dashboard_header_image_revert">
              <label style="vertical-align: middle;" class="form-check-label" for="dashboard_header_image_revert">
                Remove image
              </label>
            </div>
          </div>
          <div id="dashboardTypeVirtualRoom" class="{{ (getSetting('is_dashboard_virtual_room_enabled') == 1) ? '' : 'd-none' }}">
            @if(getSetting('is_dashboard_virtual_room_enabled') == 1)
            <div class="py-3 text-center" style="background-color: #eee;">
              <a href="/admin/virtual-rooms/{{ getSetting('dashboard_virtual_room_id') }}/edit" class="btn btn-sm btn-primary">Edit Interactive Header Image</a>
            </div>
            @else
            <p class="text-center px-2 py-3 text-sm mb-0" style="background-color: #eee;">@lang('general.save') changes to enable interactive header image. You will then be able to edit it from a button in this area.</p>
            @endif
          </div>
        </div>

        <div class="form-group my-3 card card-body">
          <div class="form-col">
            <label class="form-label"><b>Website logo</b></label>
          </div>
          @if($logo && $logo->value)
          @include('components.multi-language-image-input', ['name' => 'logo', 'value' => $logo->value, 'localization' => $logo->localization, 'revert' => 'true'])
          @else
          <input class="mt-2 ml-1" accept=".png, .jpeg, .jpg" type="file" name="logo" id="logo">
          @endif
        </div>

        <div class="form-group my-4 card card-body">
          <div class="form-col">
            <label class="form-label"><b>Dashboard left nav image</b></label>
          </div>
          <div class="form-row">
            @include('components.multi-language-image-input', ['name' => 'dashboard_left_nav_image', 'value' => $dashboard_left_nav_image ? $dashboard_left_nav_image->value : '', 'localization' => $dashboard_left_nav_image ? $dashboard_left_nav_image->localization : '', 'noRemove' => false, 'maxWidth' => '200px'])
          </div>
          <hr>
          <label for="dashboard_left_nav_image_link">Clicking on image opens url:</label>
          <input value="{{ $dashboard_left_nav_image_link->value }}" name="dashboard_left_nav_image_link" type="url" class="form-control" id="dashboard_left_nav_image_link">
          <div class="form-check mt-2">
            <input {{ $does_dashboard_left_nav_image_open_new_tab->value ? 'checked' : '' }} class="mt-2 form-check-input" type="checkbox" name="does_dashboard_left_nav_image_open_new_tab">
            <label style="vertical-align: middle;" class="form-check-label" for="does_dashboard_left_nav_image_open_new_tab">
              Link opens in new tab
            </label>
          </div>
        </div>

        <div class="form-group my-3 card card-body">
          <div class="form-col">
            <label class="form-label">Login page images <small class="text-muted">(Multiple images will result in an image carousel)</small></label>
          </div>
          <div class="py-3 text-center" style="background-color: #eee;">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#home_page_images">
              Edit Login page images
            </button>
          </div>
        </div>

        <div class="modal fade" id="home_page_images" tabindex="-1" role="dialog" aria-labelledby="home_page_images" aria-hidden="true">
          <div style="left: -19%" class="modal-dialog modal-xlg" role="document">
            <div style="width: 70vw" class="modal-content mx-auto">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Login Page Images</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <small class="text-muted mb-2">(Recommended size: 900x600)</small>
                <br>
                @if(getsetting('is_localization_enabled'))
                <div class="row">
                  <div class="col-6">
                    <span class="text-center">English</span>
                    @endif
                    <div id="home_page_images_container">
                      @foreach($home_page_images as $home_page_image)
                      <div class="home_page_image_container {{ $home_page_image->image_url ? '' : 'd-none' }}" id="home_page_image_container{{ $home_page_image->id }}">
                        <div class="card card-body">
                          <div class="d-flex justify-content-between">
                            @if($home_page_image->image_url)
                            <img src="{{ $home_page_image->image_url }}" style="max-width: 70%" id="home_page_image{{ $home_page_image->id }}">
                            @endif
                            <i data-iteration="{{ $home_page_image->id }}" class="fas fa-times deleteHomePageImage {{ $loop->first ? 'd-none' : '' }}" style="max-width: 20%; cursor: pointer;"></i>
                            <input type="hidden" name="home_page_image_remove[{{ $home_page_image->id }}]" id="home_page_image_remove{{ $home_page_image->id }}">
                          </div>
                          <br>
                          <label class="mt-2 btn btn-outline-primary btn-sm" for="home_page_images{{ $home_page_image->id }}">Change...</label>
                          <input type="file" accept="image/png, image/jpg, image/jpeg" class="d-none home_page_image" name="home_page_images[{{ $home_page_image->id }}]" id="home_page_images{{ $home_page_image->id }}" data-iteration="{{ $home_page_image->id }}" value="{{ $home_page_image ?: '' }}">
                          <p class="text-muted d-none" id="home_page_images{{ $home_page_image->id }}confirm"></p>
                        </div>
                        <hr>
                      </div>
                      @endforeach
                    </div>
                    <button type="button" id="addHomePageImage" class="btn btn-sm btn-primary float-right">Add</button>
                    @if(getsetting('is_localization_enabled'))
                  </div>
                  @endif
                  @if(getsetting('is_localization_enabled'))
                  <div class="col-6">
                    <span class="text-center">Spanish</span>
                    <div id="home_page_images_container_es">
                      @foreach($home_page_images_es as $home_page_image)
                      <div class="home_page_image_container_es {{ $home_page_image->image_url ? '' : 'd-none' }}" id="home_page_image_container_es{{ $home_page_image->id }}">
                        <div class="card card-body">
                          <div class="d-flex justify-content-between">
                            @if($home_page_image->image_url)
                            <img src="{{ $home_page_image->image_url }}" style="max-width: 70%" id="home_page_image_es{{ $home_page_image->id }}">
                            @endif
                            <i data-iteration="{{ $home_page_image->id }}" class="fas fa-times deleteHomePageImageEs" style="max-width: 20%; cursor: pointer;"></i>
                            <input type="hidden" name="home_page_image_remove[{{ $home_page_image->id }}]" id="home_page_image_remove_es{{ $home_page_image->id }}">
                          </div>
                          <br>
                          <label class="mt-2 btn btn-outline-primary btn-sm" for="home_page_images_es{{ $home_page_image->id }}">Change...</label>
                          <input type="file" accept="image/png, image/jpg, image/jpeg" class="d-none home_page_image_es" name="home_page_images[{{ $home_page_image->id }}]" id="home_page_images_es{{ $home_page_image->id }}" data-iteration="{{ $home_page_image->id }}" value="{{ $home_page_image ?: '' }}">
                          <p class="text-muted d-none" id="home_page_images_es{{ $home_page_image->id }}confirm"></p>
                        </div>
                        <hr>
                      </div>
                      @endforeach
                    </div>
                    <button type="button" id="addHomePageImage_es" class="btn btn-sm btn-primary float-right">Add</button>
                  </div>
                </div>
                @endif
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group my-3 card card-body">
          <div class="row align-items-center">
            <p class="font-weight-bold col-3">
              Theme Colors
            </p>
            <small class="text-muted col-3">Click to select a color.</small>
          </div>
          <div class="form-row mb-2">
            <div class="col-3">
              <label for="primary_color">Primary Color</label>
            </div>
            <div class="col-3">
              <input type="color" class="form-control form-control-sm" name="primary_color" value="{{ $primary_color->value }}">
            </div>
          </div>
          <div class="form-row mb-2">
            <div class="col-3">
              <label for="accent_color">Accent Color</label>
            </div>
            <div class="col-3">
              <input type="color" class="form-control form-control-sm" name="accent_color" value="{{ $accent_color->value }}">
            </div>
          </div>
          <div class="form-row mb-2">
            <div class="col-3">
              <label for="accent_color">Navbar Color</label>
            </div>
            <div class="col-3">
              <input type="color" class="form-control form-control-sm" name="navbar_color" value="{{ $navbar_color->value }}">
            </div>
          </div>
          <div class="form-row mb-2">
            <div class="col-3">
              <label for="accent_color">Group Header Color</label>
            </div>
            <div class="col-3">
              <input type="color" class="form-control form-control-sm" name="group_header_color" value="{{ $group_header_color }}">
            </div>
          </div>
        </div>

        <hr>

        <div class="form-group my-3 card card-body">
          <span class="d-block">Mobile Navigation Bar</span>
          <div class="py-3 text-center" style="background-color: #eee;">
            <a href="/admin/mobile" target="_blank" class="btn btn-primary btn-sm">
              Edit mobile navigation bar
            </a>
          </div>
        </div>

        <hr>

        <div class="form-group mt-3 d-none">
          <div class="form-check">
            <input type="hidden" name="management_chain" value="0">
            <input {{ $management_chain ? 'checked' : '' }} id="management_chain" class="form-check-input" type="checkbox" value="1" name="management_chain">
            <label class="form-check-label" for="management_chain">
              Enable Management Chain Dashboard
            </label>
          </div>
        </div>

        <div class="form-group">
          @include('components.multi-language-text-input', ['label' => 'My groups page name (For event-only users)', 'name' => 'my_groups_page_name', 'value' => $my_groups_page_name->value, 'localization' => $my_groups_page_name->localization, 'required' => 'true', 'specificName' => true])
        </div>

        <div class="form-group">
          @include('components.multi-language-text-input', ['label' => 'Prompt for Superpower', 'name' => 'superpower_prompt', 'value' => $superpower_prompt->value, 'localization' => $superpower_prompt->localization, 'required' => 'true', 'specificName' => true])
        </div>

        <div class="form-group">
          @include('components.multi-language-text-input', ['label' => 'Prompt for Summary', 'name' => 'summary_prompt', 'value' => $summary_prompt->value, 'localization' => $summary_prompt->localization, 'required' => 'true', 'specificName' => true])
        </div>

        <hr>

        @if(getsetting('is_stripe_enabled'))
        <div class="form-group">
          <label for="stripe_key">Stripe Key <small class="text-muted">(Required for payments under registration pages)</small></label>
          <input type="text" class="form-control" name="stripe_key" id="stripe_key" value="{{ get_stripe_credentials()['key'] }}">
        </div>
        <div class="form-group">
          <label for="stripe_secret">Stripe Secret</label>
          <input type="password" class="form-control" name="stripe_secret" id="stripe_secret" value="{{ get_stripe_credentials()['secret'] }}">
        </div>
        <hr>
        @endif

        @if(getsetting('is_gdpr_enabled'))
        <div class="form-group">
          <label for="gdpr_prompt">GDPR Prompt</label>
          <input type="text" class="form-control" name="gdpr_prompt" id="gdpr_prompt" value="{{ getsetting('gdpr_prompt') }}">
        </div>
        <div class="form-group">
          <label for="gdpr_checkbox_label">GDPR Checkbox Label</label>
          <input type="text" class="form-control" name="gdpr_checkbox_label" id="gdpr_checkbox_label" value="{{ getsetting('gdpr_checkbox_label') }}">
        </div>
        <hr>
        @endif

      
      
      
        <div class="form-group">
          <label for="gdpr_checkbox_label">Pages </label>
          <input type="text" class="form-control" name="pages" id="pages" value="{{ $pages ? $pages : 'Pages' }}">
        </div>
        <div class="form-group">
          <span class="d-block mb-2">General open registration <span class="text-muted">(Link available under registration pages)</span></span>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="or_enabled" name="open_registration" value="1" class="custom-control-input" {{ $open_registration ? 'checked' : '' }}>
            <label class="custom-control-label" for="or_enabled">Enabled</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="or_disabled" name="open_registration" value="0" class="custom-control-input" {{ $open_registration ? '' : 'checked' }}>
            <label class="custom-control-label" for="or_disabled">Disabled</label>
          </div>
        </div>

        <div class="form-group">
          <span class="d-block mb-2">Auto hide new members</span>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="hide_new_members_on" name="hide_new_members" value="1" class="custom-control-input" {{ $hide_new_members ? 'checked' : '' }}>
            <label class="custom-control-label" for="hide_new_members_on">Enabled</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="hide_new_members_off" name="hide_new_members" value="0" class="custom-control-input" {{ $hide_new_members ? '' : 'checked' }}>
            <label class="custom-control-label" for="hide_new_members_off">Disabled</label>
          </div>
        </div>

        <div class="form-group d-none">
          <span class="d-block mb-2">When a user becomes a group admin </span>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="group_admins_hide" name="group_admins" value="hide" class="custom-control-input" {{ $group_admins == 'hide' ? 'checked' : '' }}>
            <label class="custom-control-label" for="group_admins_hide">Auto-hide</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="group_admins_show" name="group_admins" value="show" class="custom-control-input" {{ $group_admins == 'show' ? 'checked' : '' }}>
            <label class="custom-control-label" for="group_admins_show">Auto-show</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="group_admins_nothing" name="group_admins" value="nothing" class="custom-control-input" {{ $group_admins == 'nothing' ? 'checked' : '' }}>
            <label class="custom-control-label" for="group_admins_nothing">Do nothing</label>
          </div>
        </div>

        <div class="form-group">
          <span class="d-block mb-2">Show join button on group pages</span>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="show_join_button_on_group_pages_on" name="show_join_button_on_group_pages" value="1" class="custom-control-input" {{ $show_join_button_on_group_pages ? 'checked' : '' }}>
            <label class="custom-control-label" for="show_join_button_on_group_pages_on">Enabled</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="show_join_button_on_group_pages_off" name="show_join_button_on_group_pages" value="0" class="custom-control-input" {{ $show_join_button_on_group_pages ? '' : 'checked' }}>
            <label class="custom-control-label" for="show_join_button_on_group_pages_off">Disabled</label>
          </div>
        </div>

        <div class="form-group">
          <span class="d-block mb-2">Join group codes</span>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="join_group_codes_on" name="are_group_codes_enabled" value="1" class="custom-control-input" {{ $are_group_codes_enabled ? 'checked' : '' }}>
            <label class="custom-control-label" for="join_group_codes_on">Enabled</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="are_group_codes_enabled_off" name="are_group_codes_enabled" value="0" class="custom-control-input" {{ $are_group_codes_enabled ? '' : 'checked' }}>
            <label class="custom-control-label" for="are_group_codes_enabled_off">Disabled</label>
          </div>
        </div>

        <div class="form-group">
          <span class="d-block mb-2">Ask a Mentor</span>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="ask_a_mentor_on" name="ask_a_mentor" value="1" class="custom-control-input" {{ $ask_a_mentor ? 'checked' : '' }}>
            <label class="custom-control-label" for="ask_a_mentor_on">Enabled</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="ask_a_mentor_off" name="ask_a_mentor" value="0" class="custom-control-input" {{ $ask_a_mentor ? '' : 'checked' }}>
            <label class="custom-control-label" for="ask_a_mentor_off">Disabled</label>
          </div>
        </div>

        <div class="form-group">
          <span class="d-block mb-2">Enable Focus Groups</span>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="is_ideations_enabled_on" name="is_ideations_enabled" value="1" class="custom-control-input" {{ $is_ideations_enabled ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_ideations_enabled_on">Enabled</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="is_ideations_enabled_off" name="is_ideations_enabled" value="0" class="custom-control-input" {{ $is_ideations_enabled ? '' : 'checked' }}>
            <label class="custom-control-label" for="is_ideations_enabled_off">Disabled</label>
          </div>
        </div>

        <div class="form-group">
          <span class="d-block mb-2">Enable Likes</span>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="is_likes_enabled_on" name="is_likes_enabled" value="1" class="custom-control-input" {{ $is_likes_enabled ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_likes_enabled_on">Enabled</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="is_likes_enabled_off" name="is_likes_enabled" value="0" class="custom-control-input" {{ $is_likes_enabled ? '' : 'checked' }}>
            <label class="custom-control-label" for="is_likes_enabled_off">Disabled</label>
          </div>
        </div>

        @if($is_ideations_enabled)
        <div class="form-group">
          <span class="d-block mb-2">Ideation proposal/approval</span>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="is_ideation_approval_enabled_on" name="is_ideation_approval_enabled" value="1" class="custom-control-input" {{ $is_ideation_approval_enabled ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_ideation_approval_enabled_on">Enabled</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="is_ideation_approval_enabled_off" name="is_ideation_approval_enabled" value="0" class="custom-control-input" {{ $is_ideation_approval_enabled ? '' : 'checked' }}>
            <label class="custom-control-label" for="is_ideation_approval_enabled_off">Disabled</label>
          </div>
        </div>
        @endif

        <div class="form-group">
          <span class="d-block mb-2">Is "What is your Superpower?" Prompt Enabled</span>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="is_superpower_enabled" name="is_superpower_enabled" value="1" class="custom-control-input" {{ ($is_superpower_enabled == 1) ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_superpower_enabled">Enabled</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="is_superpower_disabled" name="is_superpower_enabled" value="0" class="custom-control-input" {{ $is_superpower_enabled ? '' : 'checked' }}>
            <label class="custom-control-label" for="is_superpower_disabled">Disabled</label>
          </div>
        </div>

        <div class="form-group">
          <span class="d-block mb-2">Is "Tell us about yourself" Prompt Enabled</span>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="is_about_me_enabled" name="is_about_me_enabled" value="1" class="custom-control-input" {{ ($is_about_me_enabled == 1) ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_about_me_enabled">Enabled</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="is_about_me_disabled" name="is_about_me_enabled" value="0" class="custom-control-input" {{ $is_about_me_enabled ? '' : 'checked' }}>
            <label class="custom-control-label" for="is_about_me_disabled">Disabled</label>
          </div>
        </div>

        <div class="form-group">
          <span class="d-block mb-2">Enable Job Title</span>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="is_job_title_enabled" name="is_job_title_enabled" value="1" class="custom-control-input" {{ $is_job_title_enabled ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_job_title_enabled">Enabled</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="is_job_title_disabled" name="is_job_title_enabled" value="0" class="custom-control-input" {{ $is_job_title_enabled ? '' : 'checked' }}>
            <label class="custom-control-label" for="is_job_title_disabled">Disabled</label>
          </div>
        </div>

        <div class="form-group">
          <span class="d-block mb-2">Enable Company</span>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="is_company_enabled" name="is_company_enabled" value="1" class="custom-control-input" {{ $is_company_enabled ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_company_enabled">Enabled</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="is_company_disabled" name="is_company_enabled" value="0" class="custom-control-input" {{ $is_company_enabled ? '' : 'checked' }}>
            <label class="custom-control-label" for="is_company_disabled">Disabled</label>
          </div>
        </div>

        <div class="form-group">
          <span class="d-block mb-2">Show points to users</span>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="is_points_enabled" name="is_points_enabled" value="1" class="custom-control-input" {{ $is_points_enabled ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_points_enabled">Enabled</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="is_points_disabled" name="is_points_enabled" value="0" class="custom-control-input" {{ $is_points_enabled ? '' : 'checked' }}>
            <label class="custom-control-label" for="is_points_disabled">Disabled</label>
          </div>
        </div>

        <div class="form-group">
          <span class="d-block mb-2">Technical Assistance Link</span>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="is_technical_assistance_link_enabled" name="is_technical_assistance_link_enabled" value="1" class="custom-control-input" {{ $is_technical_assistance_link_enabled ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_technical_assistance_link_enabled">Enabled</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="is_technical_assistance_link_disabled" name="is_technical_assistance_link_enabled" value="0" class="custom-control-input" {{ $is_technical_assistance_link_enabled ? '' : 'checked' }}>
            <label class="custom-control-label" for="is_technical_assistance_link_disabled">Disabled</label>
          </div>
        </div>

        <div class="form-group">
          <label for="technical_assistance_email">Email for Technical Assistance</label>
          <input type="email" name="technical_assistance_email" id="technical_assistance_email" class="form-control" value="{{ $technical_assistance_email }}">
        </div>

      </div>
    </div>

    <div class="form-group">
      <label for="ask_a_mentor_alias">Ask a Mentor Alias</label>
      <input type="text" required name="ask_a_mentor_alias" id="ask_a_mentor_alias" class="form-control w-50" value="{{ $ask_a_mentor_alias->value }}">
    </div>

    <div class="form-group">
      <label for="ask_a_mentor_alias">Find Your People Alias</label>
      <input type="text" required name="find_your_people_alias" id="find_your_people_alias" class="form-control w-50" value="{{ $find_your_people_alias->value }}">
    </div>

    <div class="row">
      <div class="col-lg-8">
        @include('components.multi-language-text-area', ['name' => 'homepage_text', 'label' => 'Login Page Text', 'value' => $homepage_text->value, 'localization' => $homepage_text->localization, 'rows' => 4])
      </div>
      <div class="col-lg-4">
        <p class="mt-4">This is the text that is viewable on the login page to give visitors an overview of what they can do on the platform.</p>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-8">
        @include('components.multi-language-text-area', ['name' => 'account_created_message', 'label' => 'Welcome Text', 'value' => $account_created_message->value, 'localization' => $account_created_message->localization, 'rows' => 4])
      </div>
      <div class="col-lg-4">
        <p class="mt-4">This initial login screen is where you can first greet your community members/attendees. It’s a good place for a positive message, along with any basic guidelines for the community or event. All the text you see here can be customized.</p>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        @include('components.multi-language-text-area', ['name' => 'new_message_text', 'label' => 'New Message Text', 'value' => $new_message_text->value, 'localization' => $new_message_text->localization, 'rows' => 4])
      </div>
      <div class="col-lg-4">
        <p class="mt-5">This will preface any new message a user sends.</p>
      </div>
    </div>

    <button class="btn btn-info">Save</button>
    <div class="modal" tabindex="-1" id="groups_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Limit access to groups</h5>
          </div>
          <div class="modal-body">
            @foreach($groups as $group)
            @include('admin.partials.groupCheckbox', ['group' => $group, 'count' => 0, 'checked' => getsetting('or_event_only_groups') && collect(json_decode(getsetting('or_event_only_groups')))->contains($group->id)])
            @endforeach
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" data-dismiss="modal">Save changes</button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@section('scripts')
<script>
  $('.home_page_image').change(function() {
    $('#home_page_image' + $(this).data('iteration')).addClass('d-none');
    $('#home_page_images' + $(this).data('iteration') + 'confirm').html($(this).val().split(/(\\|\/)/g).pop());
    $('#home_page_images' + $(this).data('iteration') + 'confirm').removeClass('d-none');
  });

  $('.deleteHomePageImage').click(function() {
    $('#home_page_image_container' + $(this).data('iteration')).addClass('d-none');
    $('#home_page_image_container' + $(this).data('iteration')).find('input:first').val('');
    $('#addHomePageImage').removeClass('d-none');
    $('#home_page_image_remove' + $(this).data('iteration')).val('true');
  });

  $('#addHomePageImage').click(function() {
    var imagecontainer = $('#home_page_images_container').children('.d-none').first();
    $(imagecontainer).removeClass('d-none');
    $('#home_page_image_remove' + $(imagecontainer).data('iteration')).val('');
    if (!$('#home_page_images_container').children('.d-none').length)
      $(this).addClass('d-none');
  });

  if (!$('#home_page_images_container').children('.d-none').length)
    $('#addHomePageImage').addClass('d-none');


  $('.home_page_image_es').change(function() {
    $('#home_page_image_es' + $(this).data('iteration')).addClass('d-none');
    $('#home_page_images_es' + $(this).data('iteration') + 'confirm').html($(this).val().split(/(\\|\/)/g).pop());
    $('#home_page_images_es' + $(this).data('iteration') + 'confirm').removeClass('d-none');
  });

  $('.deleteHomePageImageEs').click(function() {
    $('#home_page_image_container_es' + $(this).data('iteration')).addClass('d-none');
    $('#home_page_image_container_es' + $(this).data('iteration')).find('input:first').val('');
    $('#addHomePageImage_es').removeClass('d-none');
    $('#home_page_image_remove_es' + $(this).data('iteration')).val('true');
  });

  $('#addHomePageImage_es').click(function() {
    var imagecontainer = $('#home_page_images_container_es').children('.d-none').first();
    $(imagecontainer).removeClass('d-none');
    $('#home_page_image_remove_es' + $(imagecontainer).data('iteration')).val('');
    if (!$('#home_page_images_container_es').children('.d-none').length)
      $(this).addClass('d-none');
  });

  if (!$('#home_page_images_container').children('.d-none').length)
    $('#addHomePageImage').addClass('d-none');

  $('#or_enabled').change(function() {
    if ($(this).is(':checked'))
      $('#or_open_registration_container').removeClass('d-none');
  });
  $('#or_disabled').change(function() {
    if ($(this).is(':checked'))
      $('#or_open_registration_container').addClass('d-none');
  });

  $('#or_event_only_enabled').click(function() {
    $('#groups_modal').modal('show');
  });

  $('input[type=radio][name=dashboard_header_type]').change(function() {
    if (this.value == 'virtual_room') {
      $('#dashboardTypeVirtualRoom').removeClass('d-none');
      $('#dashboardTypeImage').addClass('d-none');
    } else {
      $('#dashboardTypeVirtualRoom').addClass('d-none');
      $('#dashboardTypeImage').removeClass('d-none');
    }
  });
  $('#form-submission').on('click', function(e) {
    localStorage.clear();
  });
</script>
@endsection