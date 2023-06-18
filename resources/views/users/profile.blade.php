@extends('layouts.app')

@push('stylestack')
<style>
  .optionInput:checked + .box {
    background-color: {{ getThemeColors()->accent['200'] }} !important;
  }
</style>
@endpush

@section('content')

  <div class="main-container bg-lightest-brand" id="account1">
    <section class="bg-white space-sm pb-4">
        <div class="container">
            <div class="row justify-content-center align-items-center" style="position: relative;">
                <div class="colf-auto">
                    <h1 class="h2">@lang('messages.your-profile')</h1>
                </div>
                <a href="/home" class="d-inline-block mb-2" style="font-size: 14px; position: absolute; top: -55%; left: 0; transform: translateY(-50%);"><i class="icon-chevron-small-left"></i> @lang('messages.my-dashboard')</a>
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <section class="flush-with-above pt-4 height-80 d-block">
      <div class="container">
        <div class="row mb-4 justify-content-center">
          <div class="col-12 col-md-8">
              <h5>@lang('messages.account-details')</h5>
              @if ($errors->any())
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
              @endif
          </div>
        </div>
      </div>

      <form method="post" enctype="multipart/form-data">
      @csrf

      <div class="container-fluid">
        <div class="row justify-content-center">
            <!--end of col-->
             <div class="col-12 col-md-8 order-md-1">
                <div class="col-12">
                    <div class="form-group">
                        <label for="name">@lang('messages.full-name')
                            <span class="text-red">*</span>
                        </label>
                        <input class="form-control form-control-lg" type="text" name="name" id="name" value="{{ $authUser->name }}" />
                    </div>
                </div>
                @if($setting['is_gender']->value == 'on')
                <div class="col-12">
                    <div class="form-group">
                        <label for="gender_pronouns">@lang('messages.gender-pronouns')</label>
                        <div class="row px-2">
                          <select id="gender_pronouns_select" class="custom-select" name="gender_pronouns" onchange="toggleGenderTextBox()" style=""width:100%!important;>
                              <option value="" {{ $authUser->gender_pronouns == "" ? 'selected' : '' }}>@lang('messages.none')</option>
                              <option value="He/Him/His" {{ $authUser->gender_pronouns == "He/Him/His" ? 'selected' : ''}}>@lang('messages.he-him')</option>
                              <option value="She/Her/Hers" {{ $authUser->gender_pronouns == "She/Her/Hers" ? 'selected' : ''}}>@lang('messages.she-hers')</option>
                              <option value="They/Them" {{ $authUser->gender_pronouns == "They/Them" ? 'selected' : ''}}>@lang('messages.they-them')</option>
                              <option {{ $authUser->has_other_gender_pronoun ? 'selected' : ''}}>@lang('messages.other')</option>
                          </select>
                          <input id="gender_pronouns_text" type="text" class="form-control d-none w-50" style="display:none" value="{{ $authUser->gender_pronouns }}" placeholder="Gender Pronouns">
                        </div>
                    </div>
                </div>
                @endif
                @if(getsetting('is_company_enabled'))
                <div class="col-12">
                    <div class="form-group">
                        <label for="company">@lang('messages.company')
                        </label>
                        <input class="form-control form-control-lg" type="text" name="company" id="company" value="{{ $authUser->company }}"/>
                    </div>
                </div>
                @endif
                @if(getsetting('is_job_title_enabled'))
                <div class="col-12">
                    <div class="form-group">
                        <label for="job_title">@lang('messages.job-title')
                        </label>
                        <input class="form-control form-control-lg" type="text" name="job_title" id="job_title" value="{{ $authUser->job_title }}"/>
                    </div>
                </div>
                @endif
                <div class="col-12">
                    <div class="form-group">
                        <label for="location">@lang('messages.location')</label>
                        <input class="form-control form-control-lg" type="text" name="location" id="location" value="{{ $authUser->location }}" />
                        <small>ex. Austin, TX</small>
                    </div>
                </div>
              </div>
            <!--end of col-->
        </div>
        <!--end of row-->
        <hr>

        <div class="row justify-content-center">
            <!--end of col-->
            <div class="col-12 col-md-8 order-md-1">
              <div class="row">
                @if(getSetting('is_superpower_enabled'))
                  <div class="col-12">
                      <div class="form-group">
                          <label for="superpower">{{ getSetting('superpower_prompt') }}</label>
                          <textarea class="form-control form-control-lg" rows="3" type="text" name="superpower" id="superpower" placeholder="helping others tell their story."/>{{ $authUser->superpower }}</textarea>
                          <small>@lang('messages.limit-x-characters', ['characters' => '90'])</small>
                      </div>
                  </div>
                @endif

                @if(getSetting('is_about_me_enabled'))
                  <div class="col-12">
                      <div class="form-group">
                          <label for="summary">{{ getsetting('summary_prompt') }}</label>
                          <textarea class="form-control form-control-lg" rows="6" type="text" name="summary" id="summary" />{{ $authUser->summary }}</textarea>
                          <small>@lang('messages.limit-x-characters', ['characters' => 90])</small>
                      </div>
                  </div>
                  @endif
                </div>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
        <hr>
        <div class="row justify-content-center" id="image">
            <div class="col-12 col-md-8">
              <div class="row">
                  @if($authUser->photo_path)
                    <div class="col-12">
                      <p><b>@lang('messages.current-image')</b></p>
                      <div class="mb-2 mt-1" style="background-color: #f3f3f3;width: 12em;height: 12em;border-radius: 50%;background-image: url('{{ $authUser->photo_path }}');background-size: cover;background-position: center center; /* overflow: hidden; */">
                </div>
                    </div>
                    <hr>
                  @endif
                  <div class="col-12 mt-3">
                  <p class="text-muted text-small pt-2">Maximum upload file size: 50MB</p>
                    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#changeImage" aria-expanded="false" aria-controls="collapseExample">
                      Change
                    </button>
                    <div class="collapse mt-3" id="changeImage">
                        <div class="form-group">
                          <label for="photoUpload">@lang('change-image')</label>
                          <input class="form-control-file form-control-lg" name="photo" id="photoUpload" type="file" />
                        </div>
                    </div>
                  </div>
                </div>
            </div>
            <!--end of col-->
        </div>

        <hr>
        @if(getSetting('is_ask_a_mentor_enabled'))
          <div class="row justify-content-center mb-4">
            <div class="col-12 col-md-8">
              <div class="form-check pl-0">
                <input class="custom-checkbox" type="checkbox" value="true" name="is_mentor" id="is_mentor" {{ (!$authUser->is_mentor) ?: 'checked=""' }}>
                <label class="form-check-label ml-1" for="is_mentor" style="font-size: 16px;">
                  @lang('messages.mentor-prompt')
                </label>
              </div>
            </div>
          </div>
        @endif

        @foreach(App\Taxonomy::editable()->sortBy('profile_order_key') as $taxonomy)
          <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <div class="mb-2">
                  <label>{{ $taxonomy->name }}
                    <span class="text-red">*</span>
                  </label>
                  @if($taxonomy->is_public)
                    <small>@lang('messages.public-selection')</small>
                  @else
                    <small>@lang('messages.private-selection')</small>
                  @endif
                </div>
                <div class="categories_container">
                  @foreach($taxonomy->groupedOptionsWithOrderKey('profile', false) as $groupName => $cats)
                    <div class="mb-2">
                        <p class="font-weight-bold mb-2">{{ $groupName }}</p>
                        @foreach ($cats as $option)
                            <label for="option{{ $option->id }}" class="checkable-tag">
                              <input type="checkbox" id="option{{ $option->id }}" name="options[]" value="{{ $option->id }}" class="optionInput" {{ ($authUser->hasOption($option->id)) ? 'checked' : '' }}>
                              <div class="box">
                                @if($option->icon && $option->taxonomy->is_badge)
                                  <div class="d-inline-flex flex-column">
                                    <img class="pt-1 mx-auto" style="height: 3em; width: 3em;" src="{{ $option->icon }}">
                                    <span>{{ $option->name }}</span>
                                  </div>
                                @else
                                  <span>{{ $option->name }}</span>
                                @endif
                              </div>
                            </label>
                        @endforeach
                    </div>
                  @endforeach
                </div>
                <br>
                @if($taxonomy->is_customer_option == 1)
                @if($taxonomy->is_user_editable)
                  <div class="pl-3 mb-3" style="border-left: 3px solid #f29181;">
                      <label class="font-weight-bold" for="custom_category">@lang('messages.or-add-your-own')</label>
                      <p>@lang('messages.custom-option-prompt')</p>
                      <div class="w-50">
                          <p class="alert alert-success d-none optionSuccess">@lang('general.Thanks for submitting! Your community admins will review your category for approval shortly.')</p>
                          <p class="d-none category_error" style="color:red">@lang('messages.field-required')</p>
                      </div>
                      <div class="input-group" style="max-width: 500px;">
                          <input maxlength="70" type="text" class="form-control custom_category">
                          <input type="hidden" name="taxonomy_id" class="taxonomy_id" value="{{ $taxonomy->id }}">
                          <div class="input-group-append">
                              <button onclick="return false;" class="btn btn-primary custom_category_submit">@lang('general.submit')</button>
                          </div>
                      </div>
                      <span class="text-muted text-small">@lang('messages.custom-option-description')</span>
                  </div>
                @endif
                @endif
            </div>
          </div>
          <hr>
        @endforeach

        @if($questions->count())
        <div class="row justify-content-center">
      
          <div class="col-12 col-md-8">
          
            <div class="card">
           
              <div class="card-body">
                <p class="font-weight-bold">{{ getSetting('question_prompt', request()->user()->locale) }}</p>
                <p class="text-muted">{{ $settings }}</p>
                <p class="text-muted">
                  {{ getSetting('questions_prompt_description') }}
                </p>
                <div>
                    @each('partials.question', $questions, 'question')
                </div>
              </div>
            </div>
          </div>
        </div>
        <hr>
        @endif

        <div class="row justify-content-center">
          <div class="col-12 col-md-6">
              <div class="form-group">
                  <label for="twitter">@lang('messages.twitter-handle')</label>
                  <div class="input-group input-group-lg">
                    <div class="input-group-prepend">
                      <span class="input-group-text">@</span>
                    </div>
                    <input class="form-control form-control-lg" type="text" name="twitter" id="name" value="{{ $authUser->twitter }}" placeholder="onthedot"/>
                  </div>
              </div>
          </div>

          <div class="col-12 col-md-6">
              <div class="form-group">
                  <label for="facebook">@lang('messages.facebook-profile')</label>
                  <input class="form-control form-control-lg" type="text" name="facebook" id="name" value="{{ $authUser->facebook }}" placeholder="ex. https://www.facebook.com/onthedotwoman"/>
              </div>
          </div>

          <div class="col-12 col-md-6">
              <div class="form-group">
                  <label for="instagram">@lang('messages.instagram-handle')</label>
                  <div class="input-group input-group-lg">
                    <div class="input-group-prepend">
                      <span class="input-group-text">@</span>
                    </div>
                    <input class="form-control form-control-lg" type="text" name="instagram" id="name" value="{{ $authUser->instagram }}" placeholder="onthedot"/>
                  </div>
              </div>
          </div>

          <div class="col-12 col-md-6">
              <div class="form-group">
                  <label for="linkedin">@lang('messages.linkedin-profile')</label>
                  <input class="form-control form-control-lg" type="text" name="linkedin" id="name" value="{{ $authUser->linkedin }}" placeholder="ex. https://www.linkedin.com/company/on-the-dot-woman/"/>
              </div>
          </div>

          <div class="col-12">
              <div class="form-group">
                  <label for="website">@lang('messages.website')</label>
                  <input class="form-control form-control-lg" type="text" name="website" id="name" value="{{ $authUser->website }}" placeholder="ex. www.myblog.com"/>
              </div>
          </div>
        </div>

        <hr>
      </div>

      <div class="container">
        @if(!$authUser->is_event_only)
        <div class="row justify-content-center">
          <div class="col-12 col-md-8" id="groupsContainer">
            <div class="mb-2">
              <label>@lang('messages.groups-prompt')
              </label>
              <small>@lang('messages.choose-at-least-one')</small>
            </div>

            @foreach($groups as $group)

              @include('users.partials.groupCheckbox', ['group' => $group, 'checked' => $group->users()->where('user_id', request()->user()->id)->exists()])

            @endforeach
            
          </div>
        </div>
        <hr>
        @endif

        <div class="row justify-content-center">
            <div class="col-12 col-md-8 order-md-1">
              <div class="row">
                  <div class="col-12" id="account2">
                      <div class="form-group">
                          <button class="btn btn-primary" type="submit">@lang('general.save_changes')</button>
                      </div>
                  </div>
              </div>
            </div>
        </div>
      </div>
      <div style="position: fixed; bottom: 10px;" class="col-10">
        <div class="d-flex justify-content-end">
          <button type="submit" class="btn btn-lg btn-primary">@lang('general.save_changes')</button>
        </div>
      </div>
    </form>

    </section>
  </div>
  <style>
  .custom-control-input {
    position: absolute;
    z-index: 0;
    opacity: 1;
    left: 10px;
    top: 3px;
  }
  .custom-control-label {
      left: 2px;
  }
  .custom-control-label:after, .custom-control-label:before {
                left: -0.5rem;

            }
  </style>
  @endsection

  @section('scripts')
  <script>

    $(document).ready(function()
    {
      checkSelectedGroups();
      if('{{ $authUser->has_other_gender_pronoun }}')
      {
        toggleGenderTextBox();
      }
    });

    $('.groupInput').change(function(e) {
      if($(this).is(':checked'))
        checkParentBox(this);
      else
        uncheckChildren(this);
    });

    function checkParentBox(el)
    {
      if($(el).data('parent'))
      {
        $('.groupInput[value="'+ $(el).data('parent') +'"]').prop('checked', true);
        checkParentBox($('.groupInput[value="'+ $(el).data('parent') +'"]'));
      }
    }

    function uncheckChildren(el)
    {
      $('.groupInput[data-parent="'+ $(el).val() +'"]').each(function(index, child) {
        $(child).prop('checked', false);
        if($('.groupInput[data-parent="'+ $(child).val() +'"]').length)
          uncheckChildren(child);
      });
    }

    // $('input[name="categories[]"]').on('click', function (event) {
    //   if($('input[name="categories[]"]:checked').length > 3 && $(this).is(':checked'))
    //       event.preventDefault();
    // });

    //the following function is to prevent front-end group validation from only selecting one checkbox.
    $("#groupsContainer div input").change(function(event) {
        checkSelectedGroups();
    });

    function checkSelectedGroups()
    {
      var selectedGroupsCount = $('#groupsContainer input:checked').length;
      if(selectedGroupsCount > 0)
          $('#groupsContainer div input').prop('required', false);
      else
          $('#groupsContainer div input').prop('required', true);
    }

    function toggleGenderTextBox()
    {
      var selectElement = $('#gender_pronouns_select');
      var selectedValue = selectElement.children("option:selected").val();
      if(selectedValue == "Other...")
      {
        //make the text box visible
        $('#gender_pronouns_select').attr('name', '');
        $('#gender_pronouns_select').removeClass('w-100');
        $('#gender_pronouns_select').addClass('w-50');
        $('#gender_pronouns_text').addClass('w-50');
        $('#gender_pronouns_text').removeClass('d-none');
        $('#gender_pronouns_text').focus();
        $('#gender_pronouns_text').attr('name', 'gender_pronouns');
      }
      else
      {
        //hide the text box
        $('#gender_pronouns_text').attr('name', '');
        $('#gender_pronouns_select').attr('name', 'gender_pronouns');
        $('#gender_pronouns_select').addClass('w-100');
        $('#gender_pronouns_text').addClass('w-50');
        $('#gender_pronouns_text').addClass('d-none');
      }
    }

    $(".custom_category_submit").click(function(e){
      e.preventDefault();
      var taxonomyDiv = $(this).parent().parent().parent().parent();

      if(taxonomyDiv.find('.custom_category').val() == '')
      {
        taxonomyDiv.find('.custom_category').addClass('alert alert-danger');
        taxonomyDiv.find('.category_error').removeClass('d-none');
        return false;
      }

      $.ajax('/options', { 
        type: 'POST',
        data: {
          'taxonomy_id': taxonomyDiv.find('.taxonomy_id').val(),
          'value': taxonomyDiv.find('.custom_category').val(),
          '_token': '{{ csrf_token() }}'
        },
        success: function(response){
          taxonomyDiv.find('.custom_category').val('');
          taxonomyDiv.find('.categories_container').append('<label id="category'+response.id+'" class="checkable-tag"><input type="checkbox" name="options[]" value="'+response.id+'" checked><div class="box">'+response.name+'</div></label>');
          taxonomyDiv.find('.optionSuccess').removeClass('d-none');
          if(!taxonomyDiv.find('.category_error').hasClass('d-none'))
            taxonomyDiv.find('.category_error').addClass('d-none');
          if(taxonomyDiv.find('.custom_category').hasClass('alert alert-danger'))
            taxonomyDiv.find('.custom_category').removeClass('alert alert-danger');
        },
        error: function(response){
            if(!taxonomyDiv.find('.optionSuccess').hasClass('d-none'))
              taxonomyDiv.find('.optionSuccess').addClass('d-none');
            taxonomyDiv.find('.custom_category').addClass('alert alert-danger');
            taxonomyDiv.find('.category_error').removeClass('d-none');
        },
      });
    });

    if(window.location.hash == '#image')
    {
      $('html, body').animate({
        scrollTop: $("#image").offset().top
      },1000);
    }

    $('#does_work').change(function() {
      if(this.checked)
      {
        $('#non-work').addClass('d-none');
        $('#work').removeClass('d-none');
      }
      else
      {
        $('#work').addClass('d-none');
        $('#non-work').removeClass('d-none');
      }
    });
  </script>
      <script>
        document.addEventListener("DOMContentLoaded", function() {
            $('.question:has(.react-to-parent)').find('select').on('change', function (e) {
                var answer = $(this).find('option:selected').val().toLowerCase();
                $(this).parent().parent().find('.react-to-parent').removeClass('d-block').addClass('d-none');
                $(this).parent().parent().find('.react-to-parent[data-show="'+answer+'"]').removeClass('d-none').addClass('d-block');
            });
            $('.question:has(.react-to-parent)').find('.form-check-input').on('change', function (e) {
                var answer = $(this).is(':checked') ? $(this).val().toLowerCase() : '';
                $(this).parent().parent().parent().find('.react-to-parent').removeClass('d-block').addClass('d-none');
                $(this).parent().parent().parent().find('.react-to-parent[data-show="'+answer+'"]').removeClass('d-none').addClass('d-block');
            });
            $('.question:has(.react-to-parent)').find('input[type=text]').on('keyup', function (e) {
                var answer = $(this).val().toLowerCase();
                $(this).parent().parent().find('.react-to-parent').removeClass('d-block').addClass('d-none');
                $(this).parent().parent().find('.react-to-parent').each(function (index, item) {
                  var item = $(item)
                  if (answer.toLowerCase().includes(item.attr('data-show').toLowerCase()))
                    item.removeClass('d-none').addClass('d-block');
                });
            });
            $('.question:has(.react-to-parent)').find('input[type=radio]').change(function() {
                var answer = $(this).val().toLowerCase();
                $(this).parent().parent().parent().find('.react-to-parent').removeClass('d-block').addClass('d-none');
                $(this).parent().parent().parent().find('.react-to-parent[data-show="'+answer+'"]').removeClass('d-none').addClass('d-block');
            });
            $('.date').datepicker({
              uiLibrary: 'bootstrap4',
              format: 'mm/dd/yy'
            });
        });
    </script>
  @endsection