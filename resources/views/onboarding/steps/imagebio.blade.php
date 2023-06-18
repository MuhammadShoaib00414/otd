<div class="step">
    <div class="row justify-content-around align-items-center">
        <div class="col-12">
            <div>
                 <h6 class="title-decorative mb-2">@lang('messages.step-x', ['step' => $max_count ])</h6>

                <h4 class="mb-3">{{ $settings['imagebio']['title'] }}</h4>
                
                <div class="form-group mb-3">
                    <label for="name" class="fontSize">{{ $settings['imagebio']['prompt'] }}</label>
                    <input class="form-control-file form-control-lg" name="photo" type="file" />
                </div>

                @if(getsetting('is_superpower_enabled'))
                <div class="form-group">
                    <label><b>{{ getSetting('superpower_prompt') }}</b></label>
                    <textarea class="form-control form-control-lg" type="text" name="superpower" rows="4" id="superpower" placeholder="ex: I love helping others tell their story using social media." />{{ $authUser->superpower }}</textarea>
                    <small>@lang('messages.limit-x-characters', ['characters' => 90])</small>
                </div>
                @endif
            </div>
            <div class="d-flex justify-content-between mt-4">
                <button class="btn btn-outline-secondary previous-step-button" type="button">@lang('messages.previous-step')</button>
                <button dusk="next3" class="btn btn-primary next-step-button" type="button">@lang('messages.next-step')</button>
            </div>
        </div>
        <!--end of col-->
    </div>
</div>