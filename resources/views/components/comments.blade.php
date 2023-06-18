<div class="cards card-body">
    <div class="card-body p-0">
        <div class="form-group mb-2 pt-2">
             <textarea placeholder="Add a comment…" rows="2" name="comment" id="message" required="required" class="comment_text form-control"></textarea>
        </div>
        <div class="row">
            <div class="col-md-8"><!----></div>
            <div id="replyButton" class="col-md-4 text-right mb-1 add_comment"  data-post-id="{{$post->id}}">
                <button type="submit" class="btn btn-primary btn-sm disabled" id="submit">Add Comment</button>
            </div>
        </div>
        <div id="postSpinner_{{$post->id}}" class="spinner my-3"></div>
        <div id="comment_{{$post->id}}">
            
        </div>
        <div class="loadMoreStyle text-sm-12 mb-2 loadMoreBtn" id="loadMoreBtn_{{$post->id}}" data-post-id="{{$post->id}}"><span>Load more comments</span></div>
    </div>
</div>

@once
	@push('scriptstack')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.17.0/TweenLite.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.17.0/TimelineLite.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.17.0/easing/EasePack.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.17.0/plugins/CSSPlugin.min.js"></script>
	<script type="module" src="/assets/js/like.js"></script>
	<script>
      

	</script>
	@endpush
    @endonce