<template>
	<div>
		<i @click="toggleCommentLike()" class="likeButton" :class="{ 'icon-heart': comment.hasUserLiked, 'icon-heart-outlined': !comment.hasUserLiked }" style="cursor:pointer"></i>
		<div  v-if="comment.likes_count > 0" style="font-size: 0.8em;" @click="openLikesModal()">{{ comment.likes_count }} {{ $__.posts['likes'] }}</div>
	</div>
</template>

<script>
export default {
	data() {
		return {
			// 
		}
	},
	props: ['comment', 'post'],
	methods: {
		toggleCommentLike() {
			axios.put('/toggle-like', {
				postable_type: 'App\\Comments',
				postable_id: this.comment.id,
			})
			.then((response) => {
				localStorage.clear();
				this.comment.hasUserLiked = !this.comment.hasUserLiked;
				if(this.comment.hasUserLiked)
					this.comment.likes_count++;
				else
					this.comment.likes_count--;
			})
		}
	},
	mounted() {
	}
}
</script>