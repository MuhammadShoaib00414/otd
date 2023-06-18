<div class="step">
    <div class="row justify-content-around align-items-center">
        <div class="col-12">
            <div id="groupsContainer">
            <h6 class="title-decorative mb-2">@lang('messages.step-x', ['step' => ($max_count) ])</h6>

              <h4 class="mb-2">@lang('messages.groups-prompt')</h4>
              @if(config('app.url') == 'https://todayisagoodday.onthedotglobal.com')
               <p ><b>Choose the Miracle Parent Network as well as the location you reside in and/or where your baby is located.</b></p>
              @endif
              @each('onboarding.partials.grouplisting', $groups, 'group')

            </div>
            <div class="d-flex justify-content-between mt-4">
                <button class="btn btn-outline-secondary previous-step-button" type="button">@lang('messages.previous-step')</button>
                <button dusk="next" class="btn btn-primary next-step-button groups-next-button" type="submit"{{ $authUser->groups->count() ? ' ' : ' disabled' }}>@lang('messages.next-step')</button>
            </div>
        </div>
        <!--end of col-->
    </div>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        $('input[name="groups[]"]').on('change', function (e) {
          if ($('input[name="groups[]"]:checked').length)
            $('.groups-next-button').attr('disabled', false);
          else
            $('.groups-next-button').attr('disabled', true);
        });
      });
    </script>
</div>