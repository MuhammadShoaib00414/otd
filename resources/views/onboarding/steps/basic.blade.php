<div class="step">
    <div>
         <h6 class="title-decorative mb-2">@lang('messages.step-x', ['step' => $max_count ])</h6>
        <h4 class="mb-2">{{ $settings['basic']['title'] }}</h4>
        <p>{{ $settings['basic']['prompt'] }}</p>
        <div class="row">
            <div class="form-group col-sm-6">
                <label><strong>{{$setting['is_name_lable']->value}} <span style="color: red;">*</span></strong></label>
                <input id="name" type="text" name="name" class="form-control form-control-lg firstStepInput" value="{{ $authUser->name }}" />
            </div>
            @if($setting['is_location']->value == 'on')
            <div class="form-group col-sm-6">
                <label><strong>@lang('messages.location') @if(getsetting('is_location_required') == 'on')<span style="color: red;">*</span>@endif</strong></label>
                <input id="location" type="text" name="location" class="form-control form-control-lg firstStepInput" placeholder="ex. Austin, TX" value="{{ $authUser->location }}"  onkeyup="requiredfield(event)" />
                <small>@lang('messages.city-state')</small>
            </div>
            @endif
            @if(getsetting('is_company_enabled'))
            <div class="form-group col-sm-6">
                <label><strong>@lang('messages.company')</strong></label>
                <input type="text" name="company" class="form-control form-control-lg" value="{{ $authUser->company }}"/>
            </div>
            @endif
            @if(getsetting('is_job_title_enabled'))
            <div class="form-group col-sm-6">
                <label><strong>@lang('messages.job-title')</strong></label>
                <input type="text" name="job_title" class="form-control form-control-lg" value="{{ $authUser->job_title }}" />
            </div>
            @endif
            @if($setting['is_gender']->value == 'on')
            @if(getsetting('enable_onboarding_pronouns'))
            <div class="form-group col-sm-6">
                <label for="gender_pronouns"><strong>@lang('messages.gender-pronouns')</strong></label>
                <div class="row px-2">
                  <select id="gender_pronouns_select" class="custom-select form-control-lg" name="gender_pronouns" onchange="toggleGenderTextBox()">
                      <option value="" {{ $authUser->gender_pronouns == "" ? 'selected' : '' }}>@lang('messages.none')</option>
                      <option value="He/Him/His" {{ $authUser->gender_pronouns == "He/Him/His" ? 'selected' : ''}}>@lang('messages.he-him')</option>
                      <option value="She/Her/Hers" {{ $authUser->gender_pronouns == "She/Her/Hers" ? 'selected' : ''}}>@lang('messages.she-hers')</option>
                      <option value="They/Them" {{ $authUser->gender_pronouns == "They/Them" ? 'selected' : ''}}>@lang('messages.they-them')</option>
                      <option {{ $authUser->has_other_gender_pronoun ? 'selected' : ''}}>@lang('messages.other')</option>
                  </select>
                  <input id="gender_pronouns_text" type="text" class="form-control d-none w-50" value="{{ $authUser->gender_pronouns }}" placeholder="Gender Pronouns">
                </div>
            </div>
            @endif
            @endif
      
     
    </div>
  </div>
  <div class="text-right">
  
  </div>
  <div class="d-flex justify-content-between mt-4">
    <button class="btn btn-outline-secondary previous-step-button" type="button">@lang('messages.previous-step')</button>
    <button dusk="next2" id="firstNextStep" class="btn btn-primary next-step-button" {{ (empty($authUser->name)) ? 'disabled' : '' }} type="button">@lang('messages.next-step')</button>
  </div>
  <script>
    function requiredfield(event) {
      var checkLocation = '<?php echo $setting['is_location_required']->value ?>';
      var checkauthUser = '<?php echo $authUser->location ?>';


      if (checkLocation != 'on') {
        $("#firstNextStep").removeAttr("disabled", true);
      } else {
        if ($('#location').val().length >= 1) {

          $("#firstNextStep").removeAttr("disabled");
        } else if ($('#location').val().length == 0) {
          $("#firstNextStep").attr("disabled", true);
        }
      }


    }

    function toggleGenderTextBox() {
      var selectElement = $('#gender_pronouns_select');
      var selectedValue = selectElement.children("option:selected").val();
      if (selectedValue == "Other...") {
        //make the text box visible
        $('#gender_pronouns_select').attr('name', '');
        $('#gender_pronouns_select').removeClass('w-100');
        $('#gender_pronouns_select').addClass('w-50');
        $('#gender_pronouns_text').addClass('w-50');
        $('#gender_pronouns_text').removeClass('d-none');
        $('#gender_pronouns_text').focus();
        $('#gender_pronouns_text').attr('name', 'gender_pronouns');
      } else {
        //hide the text box
        $('#gender_pronouns_text').attr('name', '');
        $('#gender_pronouns_select').attr('name', 'gender_pronouns');
        $('#gender_pronouns_select').addClass('w-100');
        $('#gender_pronouns_text').addClass('w-50');
        $('#gender_pronouns_text').addClass('d-none');
      }
    }
  </script>
</div>