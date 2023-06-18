<div class="step">
    <div>
      <h6 class="title-decorative mb-2">@lang('messages.step-x', ['step' => ($max_count) ])</h6>
        <h4 class="mb-2">Profile Visibility</h4>
        <p class="fontSize">{{ getsetting('gdpr_prompt') }}</p>
        <div class="d-flex align-items-center">
            <div class="form-check ml-3">
                <input type="hidden" name="is_visible" value="0">
		        <input type="checkbox" class="form-check-input" name="is_visible" id="is_visible" value="1" {{ (!$user->is_hidden) ? 'checked' : '' }}>
		        <label for="is_visible">{{ getsetting('gdpr_checkbox_label') }}</label>
            </div>
        </div>
    </div>
    <div class="text-right">
        <button dusk="next2" id="firstNextStep" class="btn btn-primary next-step-button" type="button">@lang('messages.next-step')</button>
    </div>
</div>