@once
    @push('stylestack')
        <style>
            .pinnedBadge {
                padding-top: 4px;
                padding-bottom: 4px;
                padding-left: 6px;
                padding-right: 6px;
            }
        </style>
    @endpush
@endonce

<div class="d-flex justify-content-end">
    <span class="badge badge-secondary pinnedBadge">
      <i class="icon-pin"></i> @lang('messages.pinned')
    </span>
</div>