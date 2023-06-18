<div class="step">
    <div class="row justify-content-around align-items-center">
        <div class="col-12">
            <div>
                 <h6 class="title-decorative mb-2">@lang('messages.step-x', ['step' => $max_count ])</h6>
                <h4 class="mb-3">{{ $settings['about']['title'] }}</h4>

                 @if(getSetting('is_about_me_enabled'))
                <div class="form-group">
                    <p class="">{{ $settings['about']['prompt'] }}</p>
                    <label><b>{{ getsetting('summary_prompt') }}</b></label>
                    <textarea class="form-control form-control-lg" type="text" name="summary" rows="5" id="summary" placeholder="Write anything! Ex: Hobbies, travels, family, a favorite quote or your life ambitions." />{{ $authUser->summary }}</textarea>
                </div>
                @endif
            </div>
             @if(getSetting('is_ask_a_mentor_enabled'))
             <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="true" name="is_mentor" id="is_mentor" checked>
                    <label class="form-check-label" for="is_mentor" style="font-size: 16px;">
                      @lang('messages.mentor-prompt')
                    </label>
                </div>
             @endif
            <div class="d-flex justify-content-between mt-4">
                <button class="btn btn-outline-secondary previous-step-button" type="button">@lang('messages.previous-step')</button>
                <button dusk="next4" class="btn btn-primary next-step-button" type="button">@lang('messages.next-step')</button>
            </div>
        </div>
        <!--end of col-->
    </div>
</div>