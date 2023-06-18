<div class="step">
  <div class="row justify-content-around align-items-center">
    <div class="col-12">
      <h6 class="title-decorative mb-2">@lang('messages.step-x', ['step' => $max_count ])</h6>
      <div class="mb-2">
        <label class="font-weight-bold mb-2" style="font-size: 1.3rem;"> {{ $taxonomy->name }}
          <span class="text-red">*</span>
        </label>
        @if($taxonomy->is_public)
        <small>@lang('messages.public-selection')</small>
        @else
        <small>@lang('messages.private-selection')</small>
        @endif
      </div>
      @if(array_key_exists('taxonomies', $settings) && array_key_exists($taxonomy->id, $settings['taxonomies']) && array_key_exists('description', $settings['taxonomies'][$taxonomy->id]) && $settings['taxonomies'][$taxonomy->id]['description'] != '')
      <div class="d-flex mb-3 d-none" style="display:none!important">

        @if($user->localization == 'en')
        <p>{{ $settings['taxonomies'][$taxonomy->id]['description'] }}</p>
        @else
        <p> {{$is_localization_data['taxonomies'][$taxonomy->id]['description']}}</p>
        @endif

      </div>
      @endif
      <div class="categories_container">
        @php
        $opt = App\Option::groupBy('parent')->pluck('parent');

        @endphp
        @foreach($taxonomy->groupedOptionsWithOrderKeyCategories('profile', false)->sortBy('0.parent_order_key') as $groupName => $cats)

        <div class="mb-2">
        <p class="font-weight-bold mb-2">
                {{ $groupName }}
        </p>

          @foreach($cats as $option)
          <label id="option{{ $option->id }}" class="checkable-tag"><input type="checkbox" class="optionInput" id="option{{ $option->id }}" name="options[]" value="{{ $option->id }}" {{ ($authUser->options->contains('id', $option->id)) ? 'checked' : '' }}>
            <div class="box">
              @if($option->icon && $option->taxonomy->is_badge)
              <div class="d-inline-flex flex-column">
                <img class="pt-1 mx-auto" style="height: 3em; width: 3em;" src="{{ ltrim($option->icon , '/') }}">
                @endif
                <span>{{ $option->name }}</span>
                @if($option->icon && $option->taxonomy->is_badge)
              </div>
              @endif
            </div>
          </label>
          @endforeach
        </div>
        @endforeach
      </div>
      <br>
      <!-- && $taxonomy->is_badge -->
      @if($taxonomy->is_customer_option == 1)
      @if($taxonomy->is_user_editable)
      <div class="pl-3 mb-3" style="border-left: 3px solid #f29181;">
        <label class="font-weight-bold" for="custom_category">@lang('messages.or-add-your-own')</label>
        <p>@lang('messages.custom-option-prompt')</p>
        <div class="w-50">
          <p class="alert alert-success d-none optionSuccess">@lang('general.Thanks for submitting! Your community admins will review your category for approval shortly.')</p>
          <p class="d-none category_error" style="color:red">@lang('messages.field-required')</p>
        </div>
        <div class="input-group w-50">
          <input maxlength="70" type="text" class="form-control custom_category">
          <input type="hidden" name="taxonomy_id" class="taxonomy_id" value="{{ $taxonomy->id }}">
          <div class="input-group-append">
            <button onclick="return false;" class="btn btn-primary custom_category_submit">@lang('messages.submit')</button>
          </div>
        </div>
        <span class="text-muted text-small">@lang('messages.custom-option-description')</span>
      </div>
      @endif
      @endif
      <div class="d-flex justify-content-between mt-4">
        <button class="btn btn-outline-secondary previous-step-button" type="button">@lang('messages.previous-step')</button>
        <button dusk="next" class="btn btn-primary next-step-button" type="button">@lang('messages.next-step')</button>
      </div>

    </div>
  </div>
</div>