<template>
  <div class="cards">
     <div class="card-body p-0">
         <form @submit.prevent="addComment" class="add-form">
             <div class="form-group mb-2 pt-2">
                 <textarea  class="form-control"  v-model="comment.text" placeholder="Add a comment…" @keyup="validComment()" rows="2" name="comment" id="message" required="" ></textarea>
             </div>
             <div class="row">
              <div class="col-md-8">
                <!-- <div class="input-group-addon text-danger text-sm-12" v-if="(comment.text.length > 199)"><b>You have exceeded the maximum character limit.</b></div> -->
              </div>
              <div  class="col-md-4 text-right mb-1" id="replyButton">
                 <button type="submit" class="btn btn-primary btn-sm" :disabled="isDisabled" @click="spninner()">Add Comment</button>
              </div>  
             </div>
            
         </form>
         <!-- get first five comments -->
         
         <div class="d-flex my-1 pt-2" v-for="(comment, index) in comments" :key="index"  :id="'delete_'+comment.id">
             <a
                 class="d-sm-block mt-1 mr-2"
                 v-if="comment.user.photo_path"
                 href="#"
                 v-bind:style="{ backgroundImage: 'url(' + comment.user.photo_path + ')' }"
                 style="width: 2.5em; height: 2.5em; border-radius: 50%; background-size: cover; background-position: center; overflow: hidden;"
             >
             </a>
             <a
                 class="d-sm-block mt-1 mr-2"
                 v-else-if="!comment.user.photo_path"
                 href="#"
                 style="width: 2.5em; height: 2.5em; border-radius: 50%; background-size: cover; background-position: center; overflow: hidden; background-image: url('/images/profile-icon-empty.png');"
             >
             </a>
             <div class="ml-0 ml-xs-3" style="flex: 1; word-break: break-word;">
                 <div class="row">
                     <div class="col-10 col-md-11" >
                         <a  class="mt-2" style="color: #343a40;"><b>{{comment.user.name}}</b> </a>
                         <span v-if="!readMore[comment.id]">
                          {{comment.text.substring(0, 100) + ""}}
                          <span v-if="comment.text.length > textLimit"> <span @click="showMore(comment.id)" v-if="!readMore[comment.id]" class="btnstyle">Show more</span></span>
                         </span>  
                         <span v-if="readMore[comment.id]">{{comment.text}} <span @click="showLess(comment.id)" v-if="readMore[comment.id]" class="btnstyle">Show less</span></span> 
                         <div class="pt-1 text-sm-12">
                          {{calculateTimeAgo(comment.created_at)}}
                       
                         </div>
                       
                        </div>
                     <div class="col-2 col-md-1">
                         <span class="d-block text-muted text-center text-sm-11 pt-1" style="position: absolute;left: -12px;">
                          <LikeCommentButton :comment="comment" :post="post"></LikeCommentButton>
                        </span>
                        <!-- || comment.user.id == post.post.user_id -->
                        <div style="position: absolute; top: 0em; right: 0em;" class="dropdown" v-if="comment.user.is_admin == true || comment.user.id == post.post.user_id">
                            <button class="btn btn-sm btn-light" @click="toggleDropdown(comment.id + '-' + post.post.id)">
                                <i class="fas fa-caret-down"></i>
                            </button> 
                            <div class="dropdown-menu dropdown-menu-right p-0 m-0" style="height: 35px;" :class="{'show': showDropdownMenu == (comment.id + '-' + post.post.id)}" aria-labelledby="dropdownMenuButton" @click="closeDropdown()">
                          
                                <button  class="dropdown-item cursor-pointer"  @click.prevent="Commentdelete(index,comment.id)">Delete</button>
                               
                            </div> 
                        </div>
                     </div>

                    
                 </div>
            </div>
         </div>
         <div class="" v-if="loaderShow">
             <comment-skeleton v-for="i in 5" :key="i"></comment-skeleton>
         </div>
         <div class="loadMoreStyle text-sm-12 mb-2" v-if="totalComments > commentsCount">
             <span  @click="showComment(nextPage)">Load more comments</span>
         </div>
     </div>
 </div>
 
    
   </template>
  
  <script>
      import TimeAgo from 'javascript-time-ago'
      import en from 'javascript-time-ago/locale/en'
      import Reported from './Reported'
      import PostActions from './PostActions'
      import LikeCommentButton from './CommentLikeButton'
      import CommentSkeleton from './../../skeleton/CommentSkeleton.vue'


      TimeAgo.addDefaultLocale(en)
      const timeAgo = new TimeAgo('en-US')
      export default {
          props: ['post','user'],
          components: {
              Reported,
              PostActions,
              LikeCommentButton,
              CommentSkeleton
          },
          data() {
              return {
                  textLimit: 150,
                  readMore: {},
                  isDisabled: true,
                  totalcharacter: 1,
                  loaderShow: true,
                  max: 200,
                  comments: [],
                  comment: {
                      text: '',
                      postId: this.post.id
                  },
                  post_id: {},
                  userinfo: [],
                  postId: this.post.id,
                  commentsCount: 0,
                  nextPage: 0,
                  showDropdownMenu: false,
                  totalComments: 0,

              }
          },
          mounted: function () {
              this.totalComments = this.$parent.post.comments_count;
          },
          methods: {
            Commentdelete(index,commentid) {
            
			if(!confirm('Are you sure you want to delete this comment?'))
				return;
			axios.delete('/api/delete-comment/'+commentid, {
			})
                .then((response) => {
                    // $('#delete_'+commentid).hide();
                    // $('#delete_'+commentid).remove();
                    this.comments.splice(index, 1);
                    this.records = this.comments.filter(index => index.id !== commentid);
                })
            },
            
              calculateTimeAgo(date) {
                  return timeAgo.format(new Date(date), 'round');
              },
              showMore(id) {
                  this.$set(this.readMore, id, true);

              },
              showLess(id) {
                  this.$set(this.readMore, id, false);
              },
              validComment() {
                  if (this.comment.text.length > 0) {
                      this.isDisabled = false;
                  } else {
                      this.isDisabled = true;
                  }
              },
              addComment() {
                    this.isDisabled = true;
                  let context = this;
                  axios.post('/api/comment-save', this.comment).then(function (response) {
                      context.showComment(null, 'updated');
                      context.$parent.post.comments_count++;
                      context.comment.text = '';
                      context.isDisabled = false;
                  });


              },
              showComment(page = null, action = null) {
                  this.loaderShow = true;
                  let context = this;
                  axios.get('/api/get-comment/' + this.post.id, {
                      params: {
                          page: page
                      }
                  })
                  .then((response) => {
                      context.nextPage = response.data.current_page + 1;
                      if (action == 'updated') {
                        context.comments = response.data.data;
                        context.commentsCount = response.data.data.length;
                      }
                      else {
                          context.comments = context.comments.concat(response.data.data);
                          context.commentsCount += response.data.data.length;
                      }
                      context.loaderShow = false
                  })
              },
              spninner() {
                  this.loaderShow = !this.loaderShow;
              },
              readmoreBtn() {
                  this.readMore = !this.readMore;
              },
              toggleDropdown(id) {
                    if (id == this.showDropdownMenu) {
                        this.showDropdownMenu = '';
                    } else {
                        this.showDropdownMenu = id;
                    }
                },
                closeDropdown() {
                    this.showDropdownMenu = '';
                },

          },
          created() {
              this.showComment();
          },
          filters: {
              humanReadableDateTime: function (value) {
                  if (!value) return ''
                  return moment(value).local().format('M/D/YY h:mma')
              },
          },

      }
  </script>
  