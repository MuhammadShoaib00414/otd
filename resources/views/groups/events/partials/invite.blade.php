<button id="inviteUserButton" type="button" class="w-100 btn btn-outline-secondary invite-users" data-toggle="collapse" data-target="#inviteUser"><i class="icon-plus"></i> Invite users</button>

<div id="inviteUser" class="collapse mt-3">
  <div class="form-group">
      <multiselect v-model="selected" id="ajax" label="name" track-by="id" placeholder="Type to search" open-direction="bottom" :options="options" :multiple="true" :searchable="true" :loading="isLoading" :internal-search="true" :clear-on-select="true" :close-on-select="true" :options-limit="300" :limit="3" :max-height="600" :show-no-results="true" :hide-selected="true">
        <template slot="tag" slot-scope="{ option, remove }"><span class="custom__tag"><span>@{{ option.name }}</span><span class="custom__remove" @click="remove(option)">&times;</span></span></template>
        <template slot="clear" slot-scope="props">
          <div class="multiselect__clear" v-if="selected.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
        </template><span slot="noResult">No elements found. Consider changing the search query.</span>
      </multiselect>
  </div>
</div>