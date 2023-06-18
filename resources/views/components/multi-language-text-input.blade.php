@if(getsetting('is_localization_enabled'))
<table class="table table-borderless">
  <thead>
    <tr>
      <th style="width: 115px;"></th>
      <th style="font-weight: normal;">@lang('messages.english')</th>
      <th style="font-weight: normal;">@lang('messages.spanish')</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><b>{{ $label }}</b></td>
      <td>
        <input id="{{ $name }}" class="form-control" name="{{ $name }}" value="{{ isset($value) ? $value : '' }}" {{ isset($required) ? 'required pattern=.*\S+.*' : '' }} {{ isset($maxLength) ? 'maxlength=' . $maxLength : '' }}>
      </td>
      <td>

        <input id="{{ $name }}_es" class="form-control" name="{{ isset($specificName) ? 'localized_'.$name : 'localization' }}[es][{{ $name }}]" value="{{ (isset($localization) && isset($localization['es']) && isset($localization['es'][$name])) ? $localization['es'][$name] : '' }}" {{ isset($maxLength) ? 'maxlength=' . $maxLength : '' }}>
      </td>
    </tr>
  </tbody>
</table>
@else
<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    <input type="text" class="form-control" id="{{ $name }}" name="{{ $name }}" value="{{ isset($value) ? $value : '' }}" {{ isset($required) ? 'required pattern=.*\S+.*' : '' }} {{ isset($maxLength) ? 'maxlength=' . $maxLength : '' }}>
</div>
@endif