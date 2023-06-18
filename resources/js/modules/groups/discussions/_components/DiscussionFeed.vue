<template>
	<div ref="feedDiv">
		<post-card v-for="(post, index) in posts" :canBeMovedUp="!((index == 0) || (index == 1 && posts[0].is_pinned))" :post="post" :key="post.id" :type="'App\\TextPost'" @deletePost="deletePost"></post-card>
		<div v-show="isLoadingPosts" class="text-center">
			<div class="spinner my-3" id="postSpinner"></div>
		</div>

		<div class="modal" :class="{ 'd-block' : showLikesModal }" tabindex="-1" role="dialog">
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
                    <a class="userWhoLikedRow" v-for="user in usersWhoLikedPost" :href="'/users/'+user.id" target="_blank">
                        <div class="d-inline-flex align-items-center">
                            <div class="mr-3 userWhoLikedImg" :style="'height: 2.25em; width: 2.25em; min-height: 32px; min-width: 32px; border-radius: 50%; background-image: url(\'' + $user.photo_path + '\'); background-size: cover; background-position: center;'"></div>
                            <div>
                                <span class="d-block font-weight-bold userWhoLikedName" style="line-height: 1;">{{ user.name }}</span>
                            </div>
                        </div>
                        <span class="float-right">{{ $__.messages['view'] }}</span>
                    </a>
                    <hr class="my-2">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" @click="closeLikesModal()">{{ $__.messages['close'] }}</button>
              </div>
            </div>
          </div>
        </div>
	</div>
</template>

<script>
	import bus from '../../../../bus'
	import PostCard from '../../../posts/PostCard'
	import Vue from 'vue'

	export default {
		props: ['posts', 'isLoadingPosts'],
		components: {
			PostCard,
		},
		data() {
			return {
                showLikesModal: false,
                usersWhoLikedPost: [],
            }
		},
		methods: {
            deletePost(id) {
                for(var i = 0; i < this.posts.length; i++)
                {
                    var thisPost = this.posts[i];
                    if(thisPost.id != id)
                        continue;

                    this.posts.splice(i, 1);
                    break;
                }
            },
            closeLikesModal() {
            	this.showLikesModal = false;
            	this.usersWhoLikedPost = [];
            }
        },
		created() {
			bus.$on('openLikesModal', (postId) => {
                this.showLikesModal = true;
                axios.get('/api/posts/'+postId+'/likes')
                	 .then((response) => {
                	 	this.usersWhoLikedPost = response.data
                	 })
            });
        },
	}
</script>