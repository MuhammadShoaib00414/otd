<template>
	<div ref="feedDiv" id="feedDiv">

		<post-card v-for="(post, index) in posts" :canBeMovedUp="!((index == 0) || (index == 1 && posts[0].is_pinned))"
			:post="post" :key="post.id" :type="type" @deletePost="deletePost"></post-card>
		<div v-if="isLoadingPosts">
			<post-skeleton v-for="i in 5" :key="i">
			</post-skeleton>
		</div>

		<div class="modal" :class="{ 'd-block': showLikesModal }" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">{{ $__.posts['People who have liked this post'] }}</h5>
						<button type="button" class="close" @click="closeLikesModal()" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div v-show="usersWhoLikedPost.length == 0" class="text-center">
							<div class="spinner my-3" id="postSpinner"></div>
						</div>
						<div>
							<a class="userWhoLikedRow" v-for="user in usersWhoLikedPost" :href="'/users/' + user.id"
								target="_blank">
								<div class="d-inline-flex align-items-center">
									<div class="mr-3 userWhoLikedImg"
										:style="'height: 2.25em; width: 2.25em; min-height: 32px; min-width: 32px; border-radius: 50%; background-image: url(' + user.photo_path + '); background-size: cover; background-position: center;'">
									</div>
									<div>
										<span class="d-block font-weight-bold userWhoLikedName"
											style="line-height: 1;">{{ user.name }}</span>
									</div>
								</div>
								<span class="float-right">view</span>
							</a>
							<hr class="my-2">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary btn-sm" @click="closeLikesModal()">{{
								$__.messages['close']
						}}</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import bus from './../../bus'
import PostCard from './PostCard'
import Vue from 'vue'
import moment from 'moment'
import PostSkeleton from './../skeleton/PostSkeleton'

export default {
	props: ['group', 'type', 'discussion', 'parentPosts'],
	components: {
		PostCard,
		PostSkeleton
	},
	data() {
		return {
			posts: [],
			isLoadingPosts: false,
			pagination: {
				start: 1,
				end: 20,
				page: 1,
				per_page: 20,
				total: 0,
				last_page: 1,
			},
			showLikesModal: false,
			usersWhoLikedPost: [],
		}
	},
	methods: {
		movePostUp(postId) {
			var indexOfThisPost = false;
			var thisPost = false;
			for (var index = 0; index < this.posts.length; index++) {
				if (this.posts[index].id == postId) {
					indexOfThisPost = index;
					thisPost = this.posts[index];
					break;
				}
			}
			if (!thisPost || !indexOfThisPost)
				return;

			var postAboveThisPost = this.posts[index - 1];
			Vue.set(this.posts, index - 1, thisPost);
			Vue.set(this.posts, index, postAboveThisPost);
		},
		movePostDown(postId) {
			var indexOfThisPost = false;
			var thisPost = false;
			for (var index = 0; index < this.posts.length; index++) {
				if (this.posts[index].id == postId) {
					indexOfThisPost = index;
					thisPost = this.posts[index];
					break;
				}
			}
			if (!thisPost || (!indexOfThisPost && indexOfThisPost != 0))
				return;

			var postBelowThisPost = this.posts[index + 1];
			Vue.set(this.posts, index + 1, thisPost);
			Vue.set(this.posts, index, postBelowThisPost);
		},
		pinPost(postId) {
			var index = this.getPostIndex(postId);
			var post = this.posts[index];
			Vue.set(this.posts[index], 'is_pinned', true);
			if (this.posts[0].is_pinned) {
				//this next line should probably be changed at some point
				Vue.set(this.posts, index, this.posts[0]);
				Vue.set(this.posts[0], 'is_pinned', false);
				// this.posts[0].is_pinned = false;
				Vue.set(this.posts, 0, post);
			} else {
				this.posts.unshift(post);
			}
		},
		getPostIndex(postId) {
			for (var index = 0; index < this.posts.length; index++) {
				if (this.posts[index].id == postId)
					return index;
			}
			return false;
		},
		scrollHandler() {
			let context = this;
			$(document).on('scroll', function () {
				var element = document.getElementById('feedDiv');
				var offset = element.getBoundingClientRect().top - element.offsetParent.getBoundingClientRect().top;
				const top = window.pageYOffset + window.innerHeight - offset;

				var bottomOfFeed = false;

				if (top > element.scrollHeight) {
					bottomOfFeed = true;
				}

				if (bottomOfFeed && !this.isLoadingPosts && context.pagination.page != context.pagination.last_page) {
					context.pagination.page++;
					console.log('getting new pots');
					context.getPosts();
				}
			});
		},
		deletePost(id) {
			for (var i = 0; i < this.posts.length; i++) {
				var thisPost = this.posts[i];
				if (thisPost.id != id)
					continue;

				this.posts.splice(i, 1);
				break;
			}
			// delete item from local storage
			this.isLoadingPosts = true;
			if (this.type == 'group' && this.group)
				var params = "?group=" + this.group + '&page=' + this.pagination.page;
			else if (this.type == 'discussion')
				var params = "?discussion=" + this.discussion;
			else
				var params = '?page=' + this.pagination.page;
			var localStorageKey = 'page1-posts-for:' + params;

			const posts = JSON.parse(localStorage.getItem(localStorageKey)).posts;
			posts.forEach((post, index) => {
				if (post.id == id) {
					posts.splice(index, 1);
				}
			});
			localStorage.setItem(localStorageKey, JSON.stringify({
				posts: posts,
				loadedOn: JSON.parse(localStorage.getItem(localStorageKey)).loadedOn,
			}))
		},
		getPosts() {
			if (this.parentPosts)
				return;

			this.isLoadingPosts = true;
			if (this.type == 'group' && this.group)
				var params = "?group=" + this.group + '&page=' + this.pagination.page;
			else if (this.type == 'discussion')
				var params = "?discussion=" + this.discussion;
			else
				var params = '?page=' + this.pagination.page;
			var localStorageKey = 'page1-posts-for:' + params;

			if (this.pagination.page == 1 && localStorage.getItem(localStorageKey)) {
				var storedData = JSON.parse(localStorage.getItem(localStorageKey))
				// if statement below checks if the posts stored in localStorage
				// were pulled less than three hours ago. If so, we show them,
				// and then request posts added since. If not, load an entirely
				// new batch of posts, ignoring those in localStorage
				if (moment.utc(storedData.loadedOn, 'YYYY-MM-DD HH:mm:ss').add(3, 'hours').isAfter()) {
					this.posts = storedData.posts;
					params = params + '&since=' + storedData.loadedOn;
				}
			}

			axios.get('/api/feed' + params)
				.then((response) => {
					setTimeout(() => {
						if (params.indexOf('since') == -1) // if we're just loading new posts, put them at the end of the posts array
							this.posts = this.posts.concat(response.data.data);
						else // if we are getting posts *since* the localStorage posts were grabbed, put those at the BEGINNING of the posts array
							this.posts = response.data.data.concat(this.posts);
						this.pagination = {
							start: response.data.from,
							end: response.data.to,
							page: response.data.current_page,
							per_page: response.data.per_page,
							total: response.data.total,
							last_page: response.data.last_page,
						}
						if (this.pagination.page == 1) {
							this.scrollHandler();
							localStorage.setItem(localStorageKey, JSON.stringify({
								posts: this.posts,
								loadedOn: moment().utc().format('YYYY-MM-DD HH:mm:ss')
							}))
						}

						this.isLoadingPosts = false;
					}, 1000)
				});
		},
		closeLikesModal() {
			this.showLikesModal = false;
			this.usersWhoLikedPost = [];
		}
	},
	created() {
		this.getPosts();
		bus.$on('openLikesModal', (postId) => {
			this.showLikesModal = true;
			axios.get('/api/posts/' + postId + '/likes')
				.then((response) => {
					this.usersWhoLikedPost = response.data
				})
		});
		bus.$on('movePostUp', (postId) => {
			this.movePostUp(postId);
			axios.post('/api/groups/' + this.group + '/posts/' + postId + '/moveUp');
		});
		bus.$on('movePostDown', (postId) => {
			this.movePostDown(postId);
			axios.post('/api/groups/' + this.group + '/posts/' + postId + '/moveDown');
		});
		bus.$on('pin', (postId) => {
			this.pinPost(postId);
			axios.post('/api/groups/' + this.group + '/posts/' + postId + '/pin');
		});

	},
	beforeRouteUpdate(to, from, next) {
		this.posts = [];
		next();
		this.getPosts();
	},
	// remove scroll event listener when component is destroyed
	beforeDestroy() {
		window.removeEventListener('scroll', this.scrollHandler);
	},
}
</script>