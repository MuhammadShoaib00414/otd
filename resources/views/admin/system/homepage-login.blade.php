@extends('admin.layout')

@push('stylestack')
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">
    <style>
      .select-picker > .dropdown-toggle { border: 1px solid #ced4da; }
    </style>
@endpush

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Homepage & Login Settings' => '',
    ]])
    @endcomponent

    <div class="mb-5">

        @foreach($errors->all() as $message)
            <div class="alert alert-dismissible alert-danger" style="max-width:450px;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{!! $message !!}</strong>
            </div>
        @endforeach

        <h5>Homepage &amp; Login Settings</h5>

        <hr>

        <form method="post" action="/admin/system/homepage-login" enctype="multipart/form-data" id="form">
            @csrf

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
                      <button type="button" class="btn btn-primary"  data-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
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


            <hr>
            <button type="submit" class="btn btn-info">Save changes</button>
        </form>
    </div>
@endsection


@push('scriptstack')
    @if(Session::has('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
        <script>
            Swal.fire({
              title: 'Success!',
              text: 'Changes saved.',
              type: 'success',
              confirmButtonText: 'Close'
            })
        </script>
    @endif
@endpush