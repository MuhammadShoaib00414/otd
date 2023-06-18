@if(getsetting('is_localization_enabled'))
<table class="table table-borderless" id="{{ $name }}table">
  <thead>
    <tr>
      <th style="width: 1px;"></th>
      <th style="font-weight: normal;">@lang('messages.english')</th>
      <th style="font-weight: normal;">@lang('messages.spanish')</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><b>{{ $label }}</b></td>
      <td>
        <textarea id="{{ $name }}" class="form-control" name="{{ $name }}" {{ isset($required) ? 'required' : '' }} {{ isset($rows) ? 'rows='.$rows : '' }}>{{ isset($value) ? $value : '' }}</textarea>
      </td>
      <td>

        <textarea id="{{ $name }}_es" class="form-control" name="localization[es][{{ $name }}]" {{ isset($rows) ? 'rows='.$rows : '' }}>{{ (isset($localization) && isset($localization['es']) && isset($localization['es'][$name])) ? $localization['es'][$name] : '' }}</textarea>
      </td>
    </tr>
  </tbody>
</table>
@else
<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    <textarea class="form-control" id="{{ $name }}" name="{{ $name }}" {{ isset($required) ? 'required' : '' }} {{ isset($rows) ? 'rows='.$rows : '' }}>{{ isset($value) ? $value : '' }}</textarea>
</div>
@endif