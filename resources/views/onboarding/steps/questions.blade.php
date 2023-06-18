<div class="step">
     <div class="row justify-content-around align-items-center">
        <div class="col-12">
             <h6 class="title-decorative mb-2">@lang('messages.step-x', ['step' => ($max_count) ])</h6>

            <div class="col-md-8 mx-md-auto">
                <p class="font-weight-bold">{{ $settings['questions']['prompt'] }}</p>
                <p class="text-muted" >
                  {{ $settings['questions']['description'] }}
                </p>
                <div>
                    @each('partials.question', $questions, 'question')
                </div>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <button class="btn btn-outline-secondary previous-step-button" type="button">@lang('messages.previous-step')</button>
                <button dusk="next" class="btn btn-primary next-step-button" type="submit">@lang('messages.next-step')</button>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            $('.question:has(.react-to-parent)').find('select').on('change', function (e) {
                var answer = $(this).find('option:selected').val().trim().toLowerCase();
                $(this).parent().parent().find('.react-to-parent').removeClass('d-block').addClass('d-none');
                $(this).parent().parent().find('.react-to-parent[data-show="'+answer+'"]').removeClass('d-none').addClass('d-block');
            });
            $('.question:has(.react-to-parent)').find('input[type=text]').on('keyup', function (e) {
                var answer = $(this).val().trim().toLowerCase();
                $(this).parent().parent().find('.react-to-parent').removeClass('d-block').addClass('d-none');
                $(this).parent().parent().find('.react-to-parent').each(function (index, item) {
                  var item = $(item)
                  if (answer.toLowerCase().includes(item.attr('data-show').toLowerCase()))
                    item.removeClass('d-none').addClass('d-block');
                });
            });
            $('.question:has(.react-to-parent)').find('input[type=radio]').change(function() {
                var answer = $(this).val().trim().toLowerCase();
                $(this).parent().parent().parent().find('.react-to-parent').removeClass('d-block').addClass('d-none');
                $(this).parent().parent().parent().find('.react-to-parent[data-show="'+answer+'"]').removeClass('d-none').addClass('d-block');
            });
            $('.question:has(.react-to-parent)').find('input[type=checkbox]').change(function() {
                var answer = $(this).val().trim().toLowerCase();
                var parent = $(this).parent().parent().parent();
                if($(this).is(':checked'))
                    parent.find('.react-to-parent[data-show="'+answer+'"]').removeClass('d-none').addClass('d-block');
                else
                    parent.find('.react-to-parent[data-show="'+answer+'"]').removeClass('d-block').addClass('d-none');
            });
        });
    </script>
</div>