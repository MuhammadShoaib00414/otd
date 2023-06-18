@extends('admin.layout')

@push('stylestack')
<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
@endpush

@section('page-content')
<div class="col-5">
	<h4 class="mb-3">New Registration Page</h4>
	@if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    @endif
	<form action="/admin/registration" method="post" enctype="multipart/form-data">
		@csrf
		<!-- <div class="form-group">
			<label for="name">Name</label>
			<input type="text" name="name" id="name" class="form-control" required>
		</div> -->
		@include('components.multi-language-text-input', ['name' => 'name', 'label' => 'Name', 'required' => true, 'maxLength' => '50'])
        @include('components.multi-language-text-input', ['name' => 'prompt', 'label' => 'Prompt', 'required' => true, 'maxLength' => '75'])
		@include('components.multi-language-text-area', ['name' => 'description', 'label' => 'Description'])
		<small class="text-muted">This text will appear on the registration page.</small>
		<div class="py-3">
			<div class="form-group">
				<label for="slug">Vanity URL</label>
				<input type="text" name="slug" id="slug" class="form-control" required>
			</div>
		</div>

        <div class="mb-4">
            <span>Event to add to calendar <small class="text-muted">(optional)</small></span>
            <div class="form-group mt-2">
                <label for="event_name">Event Name</label>
                <input type="text" class="form-control" name="event_name" id="event_name">
            </div>
            <div class="form-row mb-3">
              <div class="col form-control-group">
                <label>Event Date</label>
                <input onkeydown="event.preventDefault()" type="text" name="event_date" class="form-control" placeholder="mm/dd/yy" id="event_date">
              </div>
              <div class="col">
                <label>Event Time <small class="text-muted">({{ request()->user()->timezone }})</small></label>
                <input autocomplete="off" type="text" name="event_time" class="form-control" placeholder="hh:mm pm" id="event_time">
              </div>
            </div>
            <div class="form-row mb-3">
              <div class="col form-control-group">
                <label>Event End Date</label>
                <input onkeydown="event.preventDefault()" type="text" name="event_end_date" class="form-control" placeholder="mm/dd/yy" id="event_end_date">
              </div>
              <div class="col">
                <label>Event End Time <small class="text-muted">({{ request()->user()->timezone }})</small></label>
                <input autocomplete="off" type="text" name="event_end_time" class="form-control" placeholder="hh:mm pm" id="event_end_time">
              </div>
            </div>
        </div>


        <div class="my-3">
            <span>Image</span>
            @include('components.multi-language-image-input', ['name' => 'image_url', 'label' => 'Image'])
        </div>

		<div class="form-check mb-2">
			<input type="checkbox" class="form-check-input" name="is_welcome_page_accessible" id="is_welcome_page_accessible" value="1">
			<label for="is_welcome_page_accessible">Is accessible from the welcome page</label>
		</div>

		<div class="form-check mb-3">
          <input class="form-check-input" checked type="radio" name="is_event_only" id="standard" value="0">
          <label class="form-check-label mr-5" for="standard">
            Standard
          </label>
          <input class="form-check-input" type="radio" value="1" name="is_event_only" id="event_only">
          <label class="form-check-label mr-5" for="event_only">
            Limited Access
          </label>
          <input class="form-check-input" type="radio" name="is_event_only" value="0" id="assign_to_group">
          <label class="form-check-label" for="assign_to_group">
            Assign to groups
          </label>
        </div>
        <div id='event_only_groups_display' class='collapse div1 mb-3'>
            <p class="text-muted">Invited users will be limited to these groups.</p>
        </div>
        <div id='assign_to_groups_display' class='collapse div1 mb-3'>
            <p class="text-muted">Invited users will be auto-assigned (but not limited to) selected group.</p>
        </div>
        <div id="list_groups" class="collapse">
          <table class="table col-12">
            <thead>
              <tr>
                <th>Group</th>
                <th class="text-center">Member</th>
              </tr>
            </thead>
            <tbody>
              @foreach($groups as $group)
                  @include('admin.registration.partials.grouplisting', ['group' => $group, 'count' => 0])
              @endforeach
            </tbody>
          </table>
        </div>
        <button type="submit" class="btn btn-primary mb-4">Save</button>
	</form>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script>
    $('#event_time').timepicker({
        timeFormat: 'h:mm p',
        dropdown: true,
    });

    $('#event_date').change(function () {
        if(!$('#event_end_date').val())
            $('#event_end_date').val($(this).val());
    });

    $('#event_date').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'mm/dd/yy'
    });

    $('#event_time').timepicker({
        timeFormat: 'hh:mm p',
        dropdown: true,
    });

    $('#event_end_date').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'mm/dd/yy'
    });

    $('#event_end_time').timepicker({
        timeFormat: 'hh:mm p',
        dropdown: true,
    });

	$('#descriptiontable').addClass('mb-0');

	$("#slug").keyup(function(e){
	    if(e.key != ' ')
	        $(this).val(string_to_slug($(this).val()));
	});

	$('#name').change(function() {
	    $('#slug').val(string_to_slug($(this).val()));
	});

	function string_to_slug (str) {
	    str = str.replace(/^\s+|\s+$/g, ''); // trim
	    str = str.toLowerCase();
	  
	    // remove accents, swap ñ for n, etc
	    var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
	    var to   = "aaaaeeeeiiiioooouuuunc------";
	    for (var i=0, l=from.length ; i<l ; i++) {
	        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
	    }

	    str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
	        .replace(/\s+/g, '-') // collapse whitespace and replace by -
	        .replace(/-+/g, '-'); // collapse dashes
	        console.log(str);
	    return str;
	}

	$('#event_only').change(function (event)
	  {
	    $('#assign_to_groups_display').collapse('hide');
	    $('#event_only_groups_display').collapse('show');
	    $('#list_groups').collapse('show');
	  });
	  $('#assign_to_group').change(function (event)
	  {
	    $('#assign_to_groups_display').collapse('show');
	    $('#event_only_groups_display').collapse('hide');
	    $('#list_groups').collapse('show');
	  });
	  $('#standard').change(function (event)
	  {
	    $('#assign_to_groups_display').collapse('hide');
	    $('#event_only_groups_display').collapse('hide');
	    $('#list_groups').collapse('hide');
	  });

	  function toggleChildren(groupId) {
        var expanded = $('#'+groupId+' > td > i').hasClass('rotated');
        $('#'+groupId+' > td > i').toggleClass('rotated');
        if (expanded)
            $('.belongsTo'+groupId).addClass('d-none');
        else
            $('.belongsTo'+groupId).removeClass('d-none');
    }

    $('.group').on('change', function (e) {
        if ($(this).is(':checked'))
            checkParentBox(this);
        else
        {
        	 $('#admin' + $(this).data('group-id')).prop('checked', false);
            uncheckChildBox(this);
        }
    });

    function checkParentBox(checkboxElement) {
        var parentId = $(checkboxElement).data('parent-group-id');
        var parentCheckbox = $('#group'+parentId);
        if (parentCheckbox.length) {
            parentCheckbox.prop('checked', true);
            checkParentBox(parentCheckbox);
        }
    }
    function uncheckChildBox(checkboxElement) {
        var id = $(checkboxElement).attr('value');
        var childCheckbox = $('input[data-parent-group-id='+id+']');
        if (childCheckbox.length) {
            $(childCheckbox).each(function(index, check) {
                $(check).prop('checked', false);
                $('.admin[data-group-id="' + $(check).data('group-id') +'"]').prop('checked', false);
                uncheckChildBox(check);
            });
        }
    }

    function getSecondPart(str) {
        return str.split('-')[1];
    }

    $('.admin').change(function(e) {
        if($(this).is(':checked')) {
            $('#group' + $(this).data('group-id')).prop('checked', true);
            checkParentBox($('#group' + $(this).data('group-id')));
        }
    });
</script>
@endsection