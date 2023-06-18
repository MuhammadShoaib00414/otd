<!-- Sorry for the naming on this one camron <3 I didn't want to refactor the other invite users modal -->

<button style="min-width:300px; max-width:450px;" id="inviteUserButton" class="btn btn-outline-secondary invite-users" data-toggle="modal" data-target="#inviteUserModal"><i class="icon-plus"></i> @lang('ideations.invite-users')</button>

<div class="modal" tabindex="-1" role="dialog" id="inviteUserModal">
  <div class="modal-dialog modal-dialog-centered" role="document">
  	<div class="modal-content">
	  <div class="modal-header">
	    <h5 class="modal-title">@lang('ideations.invite-users')</h5>
	    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      <span aria-hidden="true">&times;</span>
	    </button>
	  </div>
	  <div class="modal-body">
	    <div class="form-group">
	        <label for="name">@lang('general.user')</label>
	        <multiselect v-model="selected" id="ajax" label="name" track-by="id" placeholder="Type to search" open-direction="bottom" :options="options" :multiple="true" :searchable="true" :loading="isLoading" :internal-search="false" :clear-on-select="true" :close-on-select="true" :options-limit="300" :limit="3" :max-height="600" :show-no-results="true" :hide-selected="true" @search-change="asyncFind">
	          <template slot="tag" slot-scope="{ option, remove }"><span class="custom__tag"><span>@{{ option.name }}</span><span class="custom__remove" @click="remove(option)">&times;</span></span></template>
	          <template slot="clear" slot-scope="props">
	            <div class="multiselect__clear" v-if="selected.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
	          </template><span slot="noResult">@lang('general.empty-search')</span>
	        </multiselect>
	    </div>
	  </div>
	  <div class="modal-footer">
	    <button onclick="return false;" type="button" class="btn btn-primary nonSubmitButton" data-dismiss="modal" @click.prevent="sendInvite">@lang('general.save')</button>
	  </div>
	</div>
  </div>
</div>