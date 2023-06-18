@extends('admin.layout')

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Profile Categories' => '/admin/categories',
        $taxonomy->name => '/admin/categories/'.$taxonomy->id,
        'Edit' => '',
    ]])
    @endcomponent


    <div>
        <div class="d-flex justify-content-between align-items-middle">
            <h5 class="mr-3 mt-1">Edit {{ $taxonomy->name }}</h5>
            @if(\App\Taxonomy::count() > 1)
            <form action="/admin/categories/{{ $taxonomy->id }}" method="post">
              @method('delete')
              @csrf
              <button type="submit" class="btn btn-sm btn-light mr-2" id="deleteTaxonomy">Delete</button>
            </form>
            @endif
        </div>
        <div class="card">
            <div class="card-body">
                <form action="/admin/categories/{{ $taxonomy->id }}" method="post">
                    @csrf
                    @method('put')
                    @include('components.multi-language-text-input', ['label' => 'Name', 'name' => 'name', 'value' => $taxonomy->attributeWithLocale('name', 'en'), 'localization' => $taxonomy->localization, 'required' => true])
                    @include('components.multi-language-text-area', ['label' => 'Description', 'name' => 'description', 'value' => $taxonomy->description, 'localization' => $taxonomy->localization, 'required' => true])
                    <div class="custom-control custom-checkbox mb-2">
                      <input type="checkbox" class="custom-control-input" id="is_enabled" name="is_enabled"{{ ($taxonomy->is_enabled) ? ' checked' : '' }}>
                      <label class="custom-control-label" for="is_enabled">Enabled</label>
                    </div>
                    <div class="custom-control custom-checkbox mb-2">
                      <input type="checkbox" class="custom-control-input" id="is_public" name="is_public"{{ ($taxonomy->is_public) ? ' checked' : '' }}>
                      <label class="custom-control-label" for="is_public">Publicly visible on users profile</label>
                    </div>
                    <div class="custom-control custom-checkbox mb-2">
                      <input type="checkbox" class="custom-control-input" id="is_user_editable" name="is_user_editable"{{ ($taxonomy->is_user_editable) ? ' checked' : '' }}>
                      <label class="custom-control-label" for="is_user_editable">Users can add/remove themselves from these categories</label>
                    </div>
                    <div class="custom-control custom-checkbox mb-2">
                      <input type="checkbox" class="custom-control-input" id="is_visible_in_group_admin_reporting" name="is_visible_in_group_admin_reporting"{{ ($taxonomy->is_visible_in_group_admin_reporting) ? ' checked' : '' }}>
                      <label class="custom-control-label" for="is_visible_in_group_admin_reporting">Show in <i>group admin</i> reports</label>
                    </div>

                    <div class="custom-control custom-checkbox mb-2">
                      <input type="checkbox" class="custom-control-input" id="is_badge" name="is_badge"{{ ($taxonomy->is_badge) ? ' checked' : '' }}>
                      <label class="custom-control-label" for="is_badge">Is a badge</label>
                    </div>
                    
                    <div class="custom-control custom-checkbox mb-2">
                      <input type="checkbox" class="custom-control-input" id="is_customer_option" name="is_customer_option"{{ ($taxonomy->is_customer_option) ? ' checked' : '' }}>
                      <label class="custom-control-label" for="is_customer_option">Enabled customer add option</label>
                    </div>
                    
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">@lang('general.save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    $('#deleteTaxonomy').on('click', function(event) {
      event.preventDefault();
      if (confirm('Delete this category?'))
        $('#deleteTaxonomy').parent().submit();
    });
</script>
@endsection