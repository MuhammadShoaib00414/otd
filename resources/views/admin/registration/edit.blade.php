@extends('admin.layout')

@push('stylestack')
  <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
@endpush

@section('page-content')
<div class="col-5">
	<h4 class="mb-3">Edit {{ $page->name }} Registration Page</h4>
	@if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    @endif
	<form action="/admin/registration/{{ $page->id }}" method="post" enctype="multipart/form-data" id="form">
		@csrf
		@method('put')
		<!-- <div class="form-group">
			<label for="name">Name</label>
			<input type="text" name="name" id="name" class="form-control" required>
		</div> -->
		@include('components.multi-language-text-input', ['name' => 'name', 'label' => 'Name', 'required' => true, 'value' => $page->name, 'localization' => $page->localization,  'maxLength' => '50'])
		@include('components.multi-language-text-input', ['name' => 'prompt', 'label' => 'Prompt', 'required' => true, 'value' => $page->prompt, 'localization' => $page->localization,  'maxLength' => '75'])
		@include('components.multi-language-text-area', ['name' => 'description', 'label' => 'Description', 'value' => $page->description, 'localization' => $page->localization])
		<small class="text-muted">This text will appear on the registration page.</small>
		<div class="form-group py-2">
			<label for="slug">Vanity URL</label>
			<input type="text" name="slug" id="slug" class="form-control" required value="{{ $page->slug }}">
		</div>


		<div class="mb-4">
            <span>Event to add to calendar <small class="text-muted">(optional)</small></span>
            <div class="form-group mt-2">
                <label for="event_name">Event Name</label>
                <input type="text" class="form-control" name="event_name" id="event_name" value="{{ $page->event_name }}" maxlength="100">
            </div>
            <div class="form-row mb-3">
              <div class="col form-control-group">
                <label>Event Date</label>
                <input onkeydown="event.preventDefault()" type="text" name="event_date" class="form-control" value="{{ $page->event_date ? $page->event_date->format('m/d/y') : '' }}" placeholder="mm/dd/yy" id="event_date">
              </div>
              <div class="col">
                <label>Event Time <small class="text-muted">({{ request()->user()->timezone }})</small></label>
                <input autocomplete="off" type="text" name="event_time" class="form-control" value="{{ $page->event_date ? $page->event_date->format('g:i a') : '' }}" placeholder="hh:mm pm" id="event_time">
              </div>
            </div>
            <div class="form-row mb-3">
              <div class="col form-control-group">
                <label>Event End Date</label>
                <input onkeydown="event.preventDefault()" type="text" name="event_end_date" class="form-control" value="{{ $page->event_end_date ? $page->event_end_date->format('m/d/y') : '' }}" placeholder="mm/dd/yy" id="event_end_date">
              </div>
              <div class="col">
                <label>Event End Time <small class="text-muted">({{ request()->user()->timezone }})</small></label>
                <input autocomplete="off" type="text" name="event_end_time" class="form-control" value="{{ $page->event_end_date ? $page->event_end_date->format('g:i a') : '' }}" placeholder="hh:mm pm" id="event_end_time">
              </div>
            </div>
        </div>

        <hr>

        <div class="my-3">
            <span>Image</span>
            @include('components.multi-language-image-input', ['name' => 'image_url', 'label' => 'Image', 'value' => $page->image_url, 'localization' => $page->localization, 'maxWidth' => '200px', 'noRemove' => false])
        </div>

		<div class="form-check mt-3">
			<input type="checkbox" class="form-check-input" name="is_welcome_page_accessible" id="is_welcome_page_accessible" value="1" {{ $page->is_welcome_page_accessible ? 'checked' : '' }}>
			<label for="is_welcome_page_accessible">Is accessible from the welcome page</label>
		</div>

        <hr>

        @if(is_stripe_enabled())
        <div class="form-group py-2">
            <label for="addon_prompt">Prompt for Add-ons</label>
            <input type="text" name="addon_prompt" id="addon_prompt" class="form-control" required value="{{ $page->addon_prompt }}"maxlength="100">
        </div>

        <div class="form-group py-2">
            <label for="ticket_prompt">Prompt for Tickets</label>
            <input type="text" name="ticket_prompt" id="ticket_prompt" class="form-control" required value="{{ $page->ticket_prompt }}"maxlength="100">
        </div>

        <hr>

        <p class="font-weight-bold">Warning for users who have already purchased a ticket:</p>
        <div class="form-group">
            <label for="purchased_warning_title">Title</label>
            <input id="purchased_warning_title" name="purchased_warning_title" type="text" class="form-control" required value="{{ $page->purchased_warning_title }}">
        </div>

        <div class="form-group">
            <label for="purchased_warning_message">Message</label>
            <input id="purchased_warning_message" name="purchased_warning_message" type="text" class="form-control" required value="{{ $page->purchased_warning_message }}">
        </div>

        <div class="form-group">
            <label for="purchased_warning_url">Event Url</label>
            <input id="purchased_warning_url" name="purchased_warning_url" type="url" class="form-control" value="{{ $page->purchased_warning_url }}">
        </div>

        <div class="form-group">
            <label for="purchased_warning_button_text">Link Button Text</label>
            <input id="purchased_warning_button_text" name="purchased_warning_button_text" type="text" class="form-control" value="{{ $page->purchased_warning_button_text }}">
        </div>

        <hr>

        <div class="my-2">
            <span>Add-ons</span>
            <table>
                <tr class="my-2" v-for="(item, index) in items">
                  <td>
                    <div class="col">
                      <span v-if="index == 0">Name</span>
                      <input type="text" :name="'addons['+index+'][name]'" class="form-control form-control-sm" v-model="item.name" maxlength="250">
                    </div>
                  </td>
                  <td>
                    <span v-if="index == 0">Price</span>
                    <input type="text" :name="'addons['+index+'][price]'" class="form-control form-control-sm int-input" v-model="item.price" maxlength="6">
                  </td>
                  <td>
                    <span v-if="index == 0">Description</span>
                    <textarea rows="1" :name="'addons['+index+'][description]'" class="form-control form-control-sm" v-model="item.description"  maxlength="500"></textarea>
                  </td>
                  <td style="vertical-align: bottom;">
                      <a href="#" class="btn btn-sm btn-outline-primary" @click.prevent="items.splice(index,1)">&times;</a>
                  </td>
                </tr>
            </table>
            <a href="#" @click.prevent="items.push({name: '', price: ''})" class="btn btn-sm btn-outline-primary mt-2">Add</a>
        </div>

        <hr>

        <div class="my-2">
            <span>Coupon Codes</span>
            <table>
                <tr class="my-2" v-for="(coupon, index) in coupons">
                  <td>
                    <div class="col">
                      <span v-if="index == 0">Code</span>
                      <input type="text" :name="'coupon_codes['+index+'][code]'" class="form-control form-control-sm" v-model="coupon.code">
                    </div>
                  </td>
                  <td>
                    <template v-if="index == 0">
                        <span>Type</span>
                    </template>
                    <select :name="'coupon_codes['+index+'][type]'" class="custom-select custom-select-sm">
                        <option value="percent" :selected="coupon.type == 'percent'">Percentage</option>
                        <option value="fixed" :selected="coupon.type == 'fixed'">Fixed Rate</option>
                    </select>
                  </td>
                  <td>
                    <span v-if="index == 0">Amount</span>
                    <input type="text" :name="'coupon_codes['+index+'][amount]'" class="form-control form-control-sm int-input" v-model="coupon.amount" maxlength="3">
                  </td>
                  <td style="vertical-align: bottom;">
                      <a href="#" class="btn btn-sm btn-outline-primary" @click.prevent="coupons.splice(index,1)">&times;</a>
                  </td>
                </tr>
            </table>
            <a href="#" @click.prevent="coupons.push({amount: '', type: 'percent', amount: ''})" class="btn btn-sm btn-outline-primary mt-2">Add</a>
        </div>

        <hr>
        @endif

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
                  @include('admin.registration.partials.grouplisting', ['group' => $group, 'count' => 0, 'checkedGroups' => $page->assign_to_groups])
              @endforeach
            </tbody>
          </table>
        </div>
        <button type="submit" class="btn btn-primary mb-4">Save</button>
        <br>
	</form>
	<form action="/admin/registration/{{ $page->id }}" method="post" class="mb-3">
    	@method('delete')
    	@csrf
    	<button class="btn btn-sm btn-outline-primary" onclick="return confirm('Are you sure you want to delete this registration page?')">Delete</button>
    </form>
</div>
@endsection

@push('scriptstack')
  <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script>
    var app = new Vue({
        el: '#form',
        data: {
            items: [
            @if($page->addons)
            @foreach($page->addons as $addon)
                {
                    name: "{{ $addon['name'] }}",
                    price: {{ $addon['price'] / 100 }},
                    description: "{{ array_key_exists('description', $addon) ? $addon['description'] : '' }}",
                },
            @endforeach
            @endif
            ],
            coupons: [
                @if($page->coupon_codes)
                    @foreach($page->coupon_codes as $coupon)
                        {
                            code: "{{ $coupon['code'] }}",
                            type: "{{ $coupon['type'] }}",
                            amount: {{ $coupon['amount'] / 100 }},
                        },
                    @endforeach
                @endif
            ],
            isLoading: false,
            timeout: null,
        },
        methods: {
          clearAddons: function () {
            this.items = [];
          },
          submitForm: function () {

            $('#app').submit();
          }
        },
        created: function () {
            console.log(this.items);
        }
      });



	$('#descriptiontable').addClass('mb-0');

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

	  @if($page->is_event_only)
		  $('#event_only').prop('checked', true);
		  $('#assign_to_groups_display').collapse('hide');
		  $('#event_only_groups_display').collapse('show');
		  $('#list_groups').collapse('show');
	  @elseif($page->assign_to_groups)
		  $('#assign_to_group').prop('checked', true);
		  $('#assign_to_groups_display').collapse('show');
		  $('#event_only_groups_display').collapse('hide');
		  $('#list_groups').collapse('show');
	  @endif

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

    // $('.int-input').on('keyup', function(e) {
    //     e.preventDefault();
    //     $(this).val($(this).val().replace(/[^0-9\.]/g,''));
    // });

    $('.admin').change(function(e) {
        if($(this).is(':checked')) {
            $('#group' + $(this).data('group-id')).prop('checked', true);
            checkParentBox($('#group' + $(this).data('group-id')));
        }
    });
</script>
@endpush
