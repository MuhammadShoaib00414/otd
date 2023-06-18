<div class="step">
     <div class="row justify-content-around align-items-center">
        <div class="col-12">
            <h6 class="title-decoratived mb-2">Welcome</h6>
            <div class="col-md-8 mx-md-auto">
                {!! $settings['embed_video'] !!}
            </div>
            <div class="d-flex justify-content-between mt-4">
                <button class="btn btn-outline-secondary previous-step-button" type="button">@lang('messages.previous-step')</button>
                <button dusk="next" class="btn btn-primary next-step-button" type="submit">@lang('messages.next-step')</button>
            </div>
        </div>
    </div>
</div>