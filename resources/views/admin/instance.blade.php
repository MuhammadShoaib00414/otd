@extends('admin.layout')

@section('page-content')
  <h5>Instance settings</h5>
  <form action="/admin/instance-settings" method="post">
    @method('put')
    @csrf
    <div class="ml-2">
      <div class="form-check mt-3">
        <input {{ $is_management_chain_enabled->value == '1' ? 'checked' : '' }} class="form-check-input" type="checkbox" name="is_management_chain_enabled" id="is_management_chain_enabled">
        <label class="form-check-label" for="is_management_chain_enabled">
          Enable management chain
        </label>
      </div>
      <div class="form-check my-3">
        <input {{ $is_departments_enabled->value == '1' ? 'checked' : '' }} class="form-check-input" type="checkbox" name="is_departments_enabled" id="is_departments_enabled">
        <label class="form-check-label" for="is_departments_enabled">
          Enable departments
        </label>
      </div>
      <div class="form-check my-3">
        <input {{ $is_localization_enabled->value == '1' ? 'checked' : '' }} class="form-check-input" type="checkbox" name="is_localization_enabled" id="is_localization_enabled">
        <label class="form-check-label" for="is_localization_enabled">
          Enable multi-language support
        </label>
      </div>
      <div class="form-check my-3">
        <input {{ $is_sequence_enabled->value == '1' ? 'checked' : '' }} class="form-check-input" type="checkbox" name="is_sequence_enabled" id="is_sequence_enabled">
        <label class="form-check-label" for="is_sequence_enabled">
          Enable learning modules
        </label>
      </div>

      <div class="form-check my-3">
        <input {{ $is_gdpr_enabled->value == '1' ? 'checked' : '' }} class="form-check-input" type="checkbox" name="is_gdpr_enabled" id="is_gdpr_enabled">
        <label class="form-check-label" for="is_gdpr_enabled">
          Enable GDPR Onboarding Prompt
        </label>
      </div>

      <div class="form-check my-3">
        <input {{ $is_stripe_enabled->value == '1' ? 'checked' : '' }} class="form-check-input" type="checkbox" name="is_stripe_enabled" id="is_stripe_enabled">
        <label class="form-check-label" for="is_stripe_enabled">
          Enable Stripe Payments
        </label>
      </div>

      <div class="form-check my-3">
        <input {{ $is_pages->value == '1' ? 'checked' : '' }} class="form-check-input" type="checkbox" name="is_pages" id="is_pages">
        <label class="form-check-label" for="is_pages">
          Enable Pages Module
        </label>
      </div>

      <span>Enable SMS notifications for...</span>

      <div class="ml-2 mb-3">
        <div class="form-check my-1">
          <input {{ $is_discussion_sms_notifications_enabled->value == '1' ? 'checked' : '' }} class="form-check-input" type="checkbox" name="is_discussion_sms_notifications_enabled" id="is_discussion_sms_notifications_enabled">
          <label class="form-check-label" for="is_discussion_sms_notifications_enabled">
            Discussions
          </label>
        </div>

        <div class="form-check my-1">
          <input {{ $is_post_sms_notifications_enabled->value == '1' ? 'checked' : '' }} class="form-check-input" type="checkbox" name="is_post_sms_notifications_enabled" id="is_post_sms_notifications_enabled">
          <label class="form-check-label" for="is_post_sms_notifications_enabled">
            Text Posts
          </label>
        </div>

        <div class="form-check my-1">
          <input {{ $is_event_sms_notifications_enabled->value == '1' ? 'checked' : '' }} class="form-check-input" type="checkbox" name="is_event_sms_notifications_enabled" id="is_event_sms_notifications_enabled">
          <label class="form-check-label" for="is_event_sms_notifications_enabled">
            Events
          </label>
        </div>

        <div class="form-check my-1">
          <input {{ $is_ideation_sms_notifications_enabled->value == '1' ? 'checked' : '' }} class="form-check-input" type="checkbox" name="is_ideation_sms_notifications_enabled" id="is_ideation_sms_notifications_enabled">
          <label class="form-check-label" for="is_ideation_sms_notifications_enabled">
            Ideations
          </label>
        </div>

        <div class="form-check my-1">
          <input {{ $is_introduction_sms_notifications_enabled->value == '1' ? 'checked' : '' }} class="form-check-input" type="checkbox" name="is_introduction_sms_notifications_enabled" id="is_introduction_sms_notifications_enabled">
          <label class="form-check-label" for="is_introduction_sms_notifications_enabled">
            Introductions
          </label>
        </div>

        <div class="form-check my-1">
          <input {{ $is_message_sms_notifications_enabled->value == '1' ? 'checked' : '' }} class="form-check-input" type="checkbox" name="is_message_sms_notifications_enabled" id="is_message_sms_notifications_enabled">
          <label class="form-check-label" for="is_message_sms_notifications_enabled">
            Messages
          </label>
        </div>

        <div class="form-check my-1">
          <input {{ $is_shoutout_sms_notifications_enabled->value == '1' ? 'checked' : '' }} class="form-check-input" type="checkbox" name="is_shoutout_sms_notifications_enabled" id="is_shoutout_sms_notifications_enabled">
          <label class="form-check-label" for="is_shoutout_sms_notifications_enabled">
            Shoutouts
          </label>
        </div>
      </div>
    <button type="submit" class="btn btn-primary">@lang('general.save')</button>
  </form>
@endsection