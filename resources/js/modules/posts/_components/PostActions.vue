<template>
	<div style="position: absolute; top: 0em; right: 0em;" class="dropdown">
		<button class="btn btn-sm btn-light" @click="toggleDropdown()">
			<i class="fas fa-caret-down"></i>
		</button> 
		<div class="dropdown-menu dropdown-menu-right" :class="{'show': showDropdownMenu}" aria-labelledby="dropdownMenuButton" @click="closeDropdown()">
			<button @click="reportUser()" class="dropdown-item">{{ $__.posts['Report'] + ' ' + $__.general['user'] }}</button>
			<!-- <button v-if="isThisPostPinnable" class="dropdown-item hover-hand" @click="pin()">{{ $__.posts['pin'] }}</button> -->
			<button  v-if="post.is_pinned == true" class="dropdown-item hover-hand refresh" @click="pin()">{{ $__.posts['unpin'] }}</button>
			<button  v-else="post.is_pinned" class="dropdown-item hover-hand refresh" @click="pin()">{{ $__.posts['pin'] }}</button>
			<button v-if="type == 'group' && canBeMovedUp && !post.is_pinned" class="dropdown-item hover-hand" @click="movePostUp()">{{ $__.posts['move up'] }}</button>
			<button v-if="type == 'group' && !post.is_pinned" class="dropdown-item hover-hand" @click="movePostDown()">{{ $__.posts['move down'] }}</button>
	         	<button v-if="this.$user.is_admin == true || this.$user.id == post.post.user_id" type="submit" class="dropdown-item hover-hand deleteButton" @click.prevent="deletePost()">{{ $__.posts['delete'] }}</button>
				 <a v-if="this.$user.is_admin == true || this.$user.id == post.post.user_id" :href="'/posts/'+post.id+'/edit'" class="dropdown-item hover-hand editButton">{{ $__.posts['edit'] }}</a>
		    	<button v-if="!post.is_reported && !doesCurrentUserOwnPost" @click="report()" class="dropdown-item reportButton">{{ $__.posts['Report'] }}</button>
		    	<button v-else-if="post.is_reported && !doesCurrentUserOwnPost && isCurrentUserAdmin" @click="resolve()" class="dropdown-item">{{ $__.posts['Dismiss Report'] }}</button>
		</div> 
	</div>
</template> 

<script>
	import bus from './../../../bus'			

export default {
	props: ['post', 'type', 'canBeMovedUp'],
	data() {
		return {
			showDropdownMenu: false,
		
			
		};
	},
	created() {
		
		localStorage.clear();
	},
	computed: {
		isThisPostPinnable() {
			return this.type == 'group' && !this.is_pinned;
		},
		doesCurrentUserOwnPost() {
			return this.post.post.user_id == this.$user.id;
		},
		isCurrentUserAdmin() {
			return this.$user.is_admin;
		},
	},
	methods: {
		movePostUp() {
			bus.$emit('movePostUp', this.post.id);
		},
		movePostDown() {
			bus.$emit('movePostDown', this.post.id);
		},
		pin() {
			setTimeout(function() {
          $(".refresh").reload(true);;
        }, 2000);

		
			bus.$emit('pin', this.post.id);
		},
		report() {
			if(!confirm('Report this post?'))
				return;

			axios.post('/api/posts/'+this.post.id+'/report', {
					"_method": 'put',
				})
                .then((response) => {
                     this.post.is_reported = true;
                })
		},
		resolve() {
			if(!confirm('Are you sure you want to resolve this reported post?'))
				return;

			axios.post('/api/posts/'+this.post.id+'/resolve', {
					"_method": 'put',
				})
                .then((response) => {
                     this.post.is_reported = false;
                })
		},
		toggleDropdown() {
		
			this.showDropdownMenu = !this.showDropdownMenu;
		},
		closeDropdown() {
			this.showDropdownMenu = false;
		},
		deletePost() {
			if(!confirm('Are you sure you want to delete this post?'))
				return;

			axios.post('/api/posts/'+this.post.id, {
					"_method": 'delete',
				})
                .then((response) => {
					
					// delete post from feed
					let parent = this.$parent;
					while(parent.$vnode.componentOptions.tag != 'feed') {
						parent = parent.$parent;
					}
					parent.deletePost(this.post.id);
                    //  this.$emit('deletePost', this.post.id)
                })

		},
		reportUser() {
			if(!confirm('Report this user?'))
				return;
			window.location.href = '/report-user/'+this.post.id;
		},
	},
}
</script>