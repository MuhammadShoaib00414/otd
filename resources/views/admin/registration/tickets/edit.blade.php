@extends('admin.registration.layout')

@push('stylestack')
  <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
  <style>
    .custom__tag {
      background-color: #f1f1f1;
      padding: 0.2em 0.5em;
      border-radius: 4px;
      margin-right: 0.25em;
    }
    .custom__remove {
      font-size: 20px;
      line-height: 3px;
      position: relative;
      top: 2px;
      padding-left: 0.1em;
    }
    .custom__remove:hover {
      cursor: pointer;
    }
    .multiselect__option--highlight {
      background: #7da5d6;
      color: #000;
    }
    .multiselect__option--highlight::after {
      background: #587597;
      color: #000;
    }
  </style>
@endpush

@section('inner-page-content')

<form method="post" action="/admin/registration/{{ $page->id }}/tickets/{{ $ticket->id }}" class="col-md-6 mx-auto" id="app">
  @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    @endif
	@csrf
  @method('put')
	<h4>Edit Ticket</h4>
	<div class="form-group mt-3">
		<label for="name">Name</label>
		<input type="text" name="name" id="name" class="form-control" required value="{{ $ticket->name }}" maxlength="250">
	</div>
	<div class="form-group mt-3">
		<label for="price">Price <small class="text-muted">(Will not charge if total is less than $1)</small></label>
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text">$</span>
			</div>
			<input type="text" name="price" id="price" class="form-control" required value="{{ $ticket->price }}" maxlength="6">
		</div>
	</div>

  <div class="form-group">
    <label for="description">Description</label>
    <textarea rows="2" id="description" name="description" class="form-control" maxlength="500">{{ $ticket->description }}</textarea>
  </div>

  <label for="ajax">Assign purchasing user to groups</label>
	<multiselect select-label="@lang('messages.Press enter to select')" v-model="selected" id="ajax" label="name" track-by="id" placeholder="@lang('messages.Type to search')" open-direction="bottom" :options="options" :multiple="true" :searchable="true" :loading="isLoading" :internal-search="false" :clear-on-select="true" :close-on-select="true" :options-limit="300" :limit="3" :max-height="600" :show-no-results="true" :hide-selected="true" @search-change="asyncFind">
      <template slot="tag" slot-scope="{ option, remove }"><span class="custom__tag"><span>@{{ option.name }}</span><span class="custom__remove" @click="remove(option)">&times;</span></span></template>
      <template slot="clear" slot-scope="props">
        <div class="multiselect__clear" v-if="selected.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
      </template><span slot="noResult">@lang('messages.No elements found. Consider changing the search query.')</span>
    </multiselect>
    <button type="submit" @click.prevent="submitForm()" class="btn btn-primary mt-3">Save</button>
</form>
<form method="post" action="/admin/registration/{{ $page->id }}/tickets/{{ $ticket->id }}" class="col-md-6 mx-auto mt-4">
  @csrf
  @method('delete')
  <button type="submit" onclick="confirm('Are you sure you want to delete this ticket? Users who purchased this ticket will still be able to see their receipts.');" class="btn btn-sm btn-danger float-right">Delete Ticket</button>
</form>

@endsection

@section('scripts')
<script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script>
  $('#copyLink').click(function() {
		$('#link').focus().select();
		document.execCommand('copy');
		$(this).html('Copied!');
	});
	$('#price').on('keyup', function(e) {
		e.preventDefault();
		$(this).val($(this).val().replace(/[^0-9\.]/g,''));
	});
	Vue.component('multiselect', window.VueMultiselect.default);

      var app = new Vue({
        el: '#app',
        data: {
          selected: [
            @if($ticket->add_to_groups)
              @foreach($ticket->groups as $group)
                {
                  name: "{{ $group->name }}",
                  id: {{ $group->id }},
                },
              @endforeach
            @endif
          ],
          options: [],
          isLoading: false,
          timeout: null,
        },
        methods: {
          asyncFind: function (query) {
            var vthis = this;
            clearTimeout(this.timeout);
            this.timeout = setTimeout(function () {
              this.isLoading = true;
              $.ajax({
                url: '/api/groups/search/',
                data: { q: query },
                success: function (response) {
                  vthis.isLoading = false;
                  vthis.options = response;
                }
              });
            }, 100);
          },
          clearAll: function () {
            this.selected = [];
          },
          submitForm: function () {
            var vthis = this;
            $.each(this.selected, function(index, recipient) {
              $('<input>').attr({
                type: 'hidden',
                name: 'groups[]',
                value: recipient.id
              }).appendTo('#app');
            });

            $('#app').submit();
          }
        }
      })
</script>
@endsection
