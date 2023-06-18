@once
	@push('stylestack')
	<style>
		.likeButton {
			color: #fd00009e;
			cursor: pointer;
			font-size: 1.2em;
			width: 26px;
			margin-top: -3px;
			margin-bottom: -3px;
		}

		.likeCount {
			font-size: 0.7em;
			margin-left: 4px;
			cursor:pointer;
		}
		.likeCount:hover {
			text-decoration:underline;
		}

		.userWhoLikedRow {
			display: flex;
			flex-wrap: nowrap;
			justify-content: space-between;
			text-align: left;
			align-items: center;
			text-decoration: none;
		}

		.userWhoLikedImg {
			height: 2.5em; 
			width: 2.5em; 
			border-radius: 50%; 
			background-size: cover; 
			background-position: center;
		}

		.userWhoLikedName {
			color: #0000009e;
		}
	</style>
	<link href="/assets/css/like.css" rel="stylesheet" type="text/css" media="all" />
	@endpush
@endonce

<div data-postable-id="{{ $post->id }}" data-postable-type="{{ get_class($post) }}" class="d-flex align-items-center pr-2">
	@if($authUser->hasLiked(get_class($postable), $postable->id))
	<i class="likeButton icon-heart"></i>
	@else
	<i class="likeButton icon-heart-outlined"></i>
	@endif

	<span class="likeCount {{ $post->likes()->exists() ? '' : 'd-none' }}" data-toggle="modal" data-target="#likesModal"><span class="likeNumber">{{ $post->likes()->count() }}</span> <span class="likeCountLabel">{{ str_plural('', $post->likes()->count()) }}</span></span>
</div>

@once
<div class="modal" tabindex="-1" role="dialog" id="likesModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">People who have liked this post</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<input type="hidden" id="like_modal_postable_type">
      	<input type="hidden" id="like_modal_postable_id">
        @include('components.spinner', ['spinnerId' => 'likeSpinner'])
        <div id="emptyUserWhoLiked" class="d-none">
        	<a class="userWhoLikedRow" href="" target="_blank">
        		<div class="d-inline-flex align-items-center">
		        	<div class="mr-3 userWhoLikedImg"></div>
		        	<div>
		                <span class="d-block font-weight-bold userWhoLikedName" style="line-height: 1;"></span>
	              	</div>
              	</div>
              	<span class="float-right">view</span>
        	</a>
        	<hr class="my-2">
        </div>
        <div id="usersWhoLikedContainer" class="d-none">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endonce

@once
	@push('scriptstack')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.17.0/TweenLite.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.17.0/TimelineLite.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.17.0/easing/EasePack.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.17.0/plugins/CSSPlugin.min.js"></script>
	<script type="module" src="/assets/js/like.js"></script>
	<script>
		$('.likeButton').on('click', function() {
			if($(this).hasClass('icon-heart'))
			{
				$(this).removeClass('icon-heart');
				$(this).addClass('icon-heart-outlined');
				var likesChangedBy = -1;
			}
			else
			{
				$(this).removeClass('icon-heart-outlined');
				$(this).addClass('icon-heart');
				$(this).parent().find('.likeCount').removeClass('d-none');
				var likesChangedBy = 1;
			}

			var postable_type = $(this).parent().data('postable-type');
			var postable_id = $(this).parent().data('postable-id');
			var element = this;

			$.ajax({
	            url: "/toggle-like", 
	            type : "PUT",
	            data : 
	            {
	                "postable_id": postable_id,
	                "postable_type": postable_type,
	                "_token": "{{ csrf_token() }}",
	            },
	            success: function(response) {
	            	updateLikeNumber(element, likesChangedBy);
	            }
	        });
		});

		$('.likeCount').click(function() {
			$('#like_modal_postable_type').val($(this).parent().data('postable-type'));
			$('#like_modal_postable_id').val($(this).parent().data('postable-id'));

			getUsersWhoHaveLikedThisPost();
		});

		$('#likesModal').on('hidden.bs.modal', function () {
		    $('#usersWhoLikedContainer').empty();
		    $('#likeSpinner').removeClass('d-none')
		});

		function getUsersWhoHaveLikedThisPost()
		{
			$.ajax({
	            url: "/likes", 
	            type : "GET",
	            data : 
	            {
	                "postable_id": $('#like_modal_postable_id').val(),
	                "postable_type": $('#like_modal_postable_type').val(),
	                "_token": "{{ csrf_token() }}",
	            },
	            success: function(response) {
	            	updateLikesModal(response);
	            }
	        });
		}

		function updateLikesModal(data)
		{
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
		}

		function updateLikeNumber(element, amount)
		{
			var likeNumberElement = $(element).parent().find('.likeNumber');
			var newLikeCount = parseInt($(likeNumberElement).html()) + amount;
			$(likeNumberElement).html(newLikeCount);

			if(newLikeCount == 0)
				$(element).parent().find('.likeCount').addClass('d-none');
			// if(newLikeCount == 1)
				// $(element).parent().find('.likeCountLabel').html('like');
			// else
				// $(element).parent().find('.likeCountLabel').html('likes');
		}
	</script>
	@endpush
@endonce