<template>
	<div class="d-flex align-items-center">
		<i @click="toggleLike()" class="likeButton" :class="{ 'icon-heart': post.hasUserLiked, 'icon-heart-outlined': !post.hasUserLiked }"></i>
		<div v-if="post.likes_count > 0" style="font-size: 0.8em;cursor: pointer;margin-left: 6px;font-weight: 500" @click="openLikesModal()">{{ post.likes_count }}</div>
	</div>
</template>

<script>
	import bus from './../../../bus'

	export default {
		props: ['post'],
		data() {
			return {};
		},
		methods: {
			toggleLike() {
				
				axios.put('/toggle-like', {
				    postable_type: this.post.post_type ? this.post.post_type : 'App\\Post',
				    postable_id: this.post.id,
				})
				.then((response) => {
					localStorage.clear();
				    this.post.hasUserLiked = !this.post.hasUserLiked;
				    if(this.post.hasUserLiked)
				    	this.post.likes_count++;
				    else
				    	this.post.likes_count--;
				})
				
			},
			updateLikesModal(data) {
				data.forEach(function(user, index, array) {
					var row = $('#emptyUserWhoLiked').html();
					row = $(row).attr('href', '/users/' + user.id);
					row.find('.userWhoLikedImg').css({
						'background-image': "url("+user.photo_path+")"
					});
					row.find('.userWhoLikedName').html(user.name);
					$('#usersWhoLikedContainer').append(row);
				});
				$('#likeSpinner').addClass('d-none');
				$('#usersWhoLikedContainer').find('hr:last-of-type').remove();
				$('#usersWhoLikedContainer').removeClass('d-none');
			},
			openLikesModal() {
				bus.$emit('openLikesModal', this.post.id);
			}
		}
	}
</script>