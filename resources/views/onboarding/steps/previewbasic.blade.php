
<div class="step">
    <div>
         <h6 class="title-decorative mb-2">@lang('messages.step-x', ['step' => $max_count ])</h6>
        <h4 class="mb-2">{{ $settings['basic']['title'] }}</h4>
        <p>{{ $settings['basic']['prompt'] }}</p>
        <div class="row">
            <div class="form-group col-sm-6">
                <label>{{$setting['is_name_lable']}} <span style="color: red;">*</span></label>
                <input id="name" type="text" name="name" class="form-control form-control-lg firstStepInput" value="{{ $authUser->name }}" />
            </div>
            @if($setting['is_location'] == 'on')
            <div class="form-group col-sm-6">
                <label>@lang('messages.location') <span style="color: red;">*</span></label>
                <input id="location" type="text" name="location" class="form-control form-control-lg firstStepInput" placeholder="ex. Austin, TX" value="{{ $authUser->location }}"  onkeyup="requiredfield(event)" />
                <small>@lang('messages.city-state')</small>
            </div>
            @endif
            @if(getsetting('is_company_enabled'))
            <div class="form-group col-sm-6">
                <label>@lang('messages.company')</label>
                <input type="text" name="company" class="form-control form-control-lg" value="{{ $authUser->company }}"/>
            </div>
            @endif
            @if(getsetting('is_job_title_enabled'))
            <div class="form-group col-sm-6">
                <label>@lang('messages.job-title')</label>
                <input type="text" name="job_title" class="form-control form-control-lg" value="{{ $authUser->job_title }}" />
            </div>
            @endif
            @if($setting['is_location'] == 'on')
            @if(getsetting('enable_onboarding_pronouns'))
            <div class="form-group col-sm-6">
                <label for="gender_pronouns">@lang('messages.gender-pronouns')</label>
                <div class="row px-2">
                  <select id="gender_pronouns_select" class="custom-select" name="gender_pronouns" onchange="toggleGenderTextBox()">
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
        <div class="row">
            <div class="col-md-4">
            <div class="form-group my-3">
            <label for="basic[prompt]">City/State (Show/Hide) </label>
            <label class="switch">
            <input type="checkbox" name="is_location" @if($setting['is_location'] == 'on') checked @endif  data-device-id="{{$setting['is_location']}}">
            <span class="slider round"></span>
        </label>
        </div>
            </div>
            <div class="col-md-4">
            <div class="form-group my-3">
            <label for="basic[prompt]">City/State (Required)</label>
            <label class="switch">
            <input type="checkbox" name="is_location_required" @if($setting['is_gender'] == 'on') checked @endif  data-device-id="{{$setting['is_gender']}}">
            <span class="slider round"></span>
        </label>
        </div>
            </div>
            <div class="col-md-4">
            <div class="form-group my-3">
            <label for="basic[prompt]">Gender (Show/Hide)</label>
            <label class="switch">
              
            <input type="checkbox" name="is_gender" @if($setting['is_gender'] == 'on') checked @endif  data-device-id="{{$setting['is_gender']}}">
            <span class="slider round"></span>
        </label>
        </div>
            </div>
        </div>   
    </div>
    <div class="text-right">
        <button dusk="next2" id="firstNextStep" class="btn btn-primary next-step-button" {{ (!empty($setting['is_location_required'])) ? 'disabled' : '' }} type="button">@lang('messages.next-step')</button>
    </div>
    <script>
    function requiredfield(event) {
       
          if($('#location').val().length >= 1) 
          {
             // alert($('#location').val().length);
            $("#firstNextStep").removeAttr("disabled");
            } else if($('#location').val().length == 0){
             $("#firstNextStep").attr("disabled",true);
            }
       
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
</script>
</div>