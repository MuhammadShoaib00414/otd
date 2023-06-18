<template>
	<div>
		<div class="mb-2 d-flex pl-2 pt-2">
            <router-link to="/home">{{ $__.messages['Dashboard'] }}</router-link><span class="px-1">></span>
            <router-link :to="'/groups/'+$route.params.slug">{{ group.name }}</router-link><span class="px-1">></span>
            <router-link :to="'/groups/'+$route.params.slug+'/discussions'">{{ $__.discussions['discussions'] }}</router-link>
        </div>
        <div class="row px-2">
            <div class="col-3 d-none d-md-block">
                <group-menu :group="group" :isUserAdminOfGroup="isUserAdminOfGroup"></group-menu>
            </div>
            <div class="col-12 col-md-6">
            	<div class="d-block d-md-none">
                    <button class="btn btn-primary w-100 mb-2" @click="showGroupMenu = !showGroupMenu">Group Menu</button>
                    <group-menu :group="group" :isUserAdminOfGroup="isUserAdminOfGroup" v-show="showGroupMenu" @click="showGroupMenu = false"></group-menu>
                </div>
				<h4 class="mb-2">{{ discussion.name }}</h4>
				<discussion-feed v-if="isDiscussionLoaded" :posts="posts" :type="'discussion'" :discussion="$route.params.discussionSlug" :isLoadingPosts="isLoadingPosts"></discussion-feed>

				<div id="reply">
		            <p class="mb-1"><b>{{ $__.discussions['Write a reply'] }}</b></p>
		            <div>
		                <!-- <textarea class="form-control mb-2" name="body" rows="4" id="newPostBody" required></textarea> -->
		                <quill-editor
		                	style="background-color:white"
						    ref="myQuillEditor"
						    v-model="newPostBody"
						    :options="configOptions"
						  />

                         <p v-if="validated" id="autoHide" class="mt-2 text-red display-7"> <b>Sorry!  You can't post with empty reply </b></p>

		                <div class="text-right mb-5">
		                    <button type="submit" class="btn btn-secondary mt-2"  id="postReplyButton" @click="postReply()">{{ $__.messages['post'] }}</button>
		                </div>
		            </div>
		        </div>
            </div>
        </div>
	</div>
</template>
<script>
	import Vue from 'vue'
	import 'quill/dist/quill.core.css' // import styles
	import 'quill/dist/quill.snow.css' // for snow theme
	import { quillEditor } from 'vue-quill-editor'
	import GroupMenu from '../_components/GroupMenu'
	import moment from 'moment'
	import DiscussionFeed from './_components/DiscussionFeed'

	export default {
		props: [],
		data() {
			return {
				showGroupMenu: false,
				isLoadingPosts: true,
				posts: [],
				discussion: {
					name: '',
				},
				group: {

				},
				configOptions: {
					modules: {
					    toolbar: [
					      [{'header': 1}, {'header': 2}],
					      ['bold', 'italic', 'link'],
					      ['blockquote', {'list': 'ordered'}, {'list': 'bullet'}],
					      ['image']
					    ],
					    clipboard: {
					      matchVisual: false
					    }
				  	}
		        },
				newPostBody: '',
				isUserAdminOfGroup: false,
				isDiscussionLoaded: false,
                isValidationAllowed: false,


			}
		},
        computed: {
            validated() {
            return this.isValidationAllowed && !this.newPostBody
            },

        },
		methods: {

			postReply() {
                if(this.newPostBody.length  == 0){
                   this.isValidationAllowed = true;
                   setTimeout(function() { $("#autoHide").fadeOut(1500); }, 5000);
                  return;
                }else{
                     var body = $('.ql-editor').html();
                    if(body == '')
                        return;
                    axios.post('/api/discussions/'+this.$route.params.discussionSlug, {
                        body: body,
                    }).then((response) => {
                        this.posts.push(response.data);
                        $('.ql-editor').html('');
                    });
                     $('#postReplyButton').prop('disabled', true);
                        setTimeout(function() {
                            $('#postReplyButton').prop('disabled', false);
                        }, 2000);
                    this.isValidationAllowed = false;
                }
			},
		},
		created() {
			axios.get('/api/discussions/'+this.$route.params.discussionSlug)
				.then((response) => {
                    this.discussion = response.data;
                    this.isDiscussionLoaded = true;
                 })

			axios.get('/api/groups/'+this.$route.params.slug)
				.then((response) => {
                    this.group = response.data;
                    this.$usersGroups.forEach((group) => {
                        if (group.id == this.group.id && group.pivot.is_admin) {
                            this.isUserAdminOfGroup = true;
                        }
                    })
                 })

            axios.get('/api/feed?discussion='+this.$route.params.discussionSlug)
                 .then((response) => {
                 	setTimeout(() => {
                 		this.posts = response.data.data;
                 		this.isLoadingPosts = false;
                 	},1000)
                 });
                localStorage.clear();
		},
		components: {
			GroupMenu,
			DiscussionFeed,
			quillEditor,
		}
	}
</script>
