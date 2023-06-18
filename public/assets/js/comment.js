$(document).ready(function() {
    var regex = new RegExp('[a-zA-Z]+/[0-9]+');
    var url = window.location.href;
    if (url.match(regex)) {
        $('.commentSection').click();
    }
    
});
jQuery('textarea').on('keyup', function () {
    if (this.value.length > 0) {
        $('button').removeClass("disabled");
    } else {
        $('button').addClass("disabled");
    }

});
$(document).on('click', '.loadMoreBtn', function () {
    var postId = $(this).attr("data-post-id");
    var nextPageUrl = $(this).attr('data-next-page');
    if (nextPageUrl != undefined) {
        getUsersComment(nextPageUrl,postId);
    } else {
        $(this).hide();
    }

    
});
$('.add_comment').on('click', function () {
    var postId = $(this).attr("data-post-id");
    var element = $(this).closest('#collapse_' + postId).find('textarea');
    $('#postSpinner_'+postId).show();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: "/api/comment-save",
        type: "POST",
        data: {
            "postId": postId,
            "text": element.val(),
           
        },
        success: function (response) {
            $('#postSpinner_'+postId).hide();
            getUsersComment("/api/get-comment/"+postId, postId);
        }
    });
    element.val('');
    $('button').addClass("disabled");
});

function getUsersComment(nextPageUrl = null, postId = null) {
    $.ajax({
        url: nextPageUrl,
        type: "GET",
        success: function (response) {
            $('#postSpinner_' + postId).hide();
            var html = '';
           
            if (response.data.length > 0) {
                $.each(response.data, function (key, value) {
                    if (value.user.photo_path) {
                        var image_url = value.user.photo_path;
                    } else {
                        var image_url = '/images/profile-icon-empty.png'
                    }
                    if (value.hasUserLiked == true) {
                        var likeButton = 'icon-heart';
                    } else {
                        var likeButton = 'icon-heart-outlined';

                    }
                    html += '<div class="d-flex my-1 pt-2"><a href="#" class="d-sm-block mt-1 mr-2" style="width:2.5em;height:2.5em;border-radius:50%;background-size:cover;background-position:center center;overflow:hidden;background-image:url('+image_url+')"></a><div class="ml-0 ml-xs-3" style="flex:1 1 0%;word-break:break-word"><div class="row"><div class="col col-md-9"><a class="mt-2" style="color:#343a40"><b> '+value.user.name+' </b></a><span class="commentText">  '+value.text+' </span><div class="pt-2">'+timeSince(new Date(value.created_at))+'</div></div><div class="col col-md-3"><span class="d-block text-muted text-right text-sm-11 pt-1"><div id="myid"><i class="likeButton commentLikeButton '+likeButton+'" data-id="' +value.id+'" data-type="App\Comments"  style="cursor:pointer"></i><div style="font-size:.8em" id="like_count_'+value.id+'" data-likes="' + value.likes_count + '"> '+value.likes_count+' likes</div></div></span></div></div></div></div>';
                });
                $('#loadMoreBtn_' + postId).attr('data-next-page', response.next_page_url);
                if (response.current_page == 1) {
                    $("#comment_" + postId).html(html);
                } else {
                    $('#comment_' + postId).append(html);
                }
                if (response.next_page_url != undefined) {
                    $('#loadMoreBtn_'+ postId).show();
                } else {
                    $('#loadMoreBtn_'+postId).hide();
                }
            } else {
                $('#loadMoreBtn_'+postId).hide();
            }
            var commentsText = $('.commentText');
            commentsText.each(function () {
                var words = $(this).text();
                var maxWords = 100;
                if (words.length > maxWords) {
                    html = words.slice(0, maxWords) + '<span class="tdots">...</span><span class="more_text" style="display:none;">' + words.slice(
                            maxWords, words.length) + '</span>' +
                        '<a href="javascrip:;" class="read_more"> <span class="text-decoration-underline text-primary">More</span></a>'
                    $(this).html(html)
                    $(this).find('a.read_more').click(function (event) {
                        $(this).toggleClass("less");
                        event.preventDefault();
                        if ($(this).hasClass("less")) {
                            $(this).html("<span class='text-decoration-underline ms-1 text-primary'>Less</span>")
                            $(this).parent().find(".more_text").show();
                            $('.tdots').hide();
                        } else {
                            $(this).html(" <span class='text-decoration-underline text-primary'>More</span>")
                            $(this).parent().find(".more_text").hide();
                            $('.tdots').show();
                        }
                    })
                }
            });
        }
    });
}
$(document).on('click', '.commentLikeButton', function () {
    if ($(this).hasClass('icon-heart')) {
        $(this).removeClass('icon-heart');
        $(this).addClass('icon-heart-outlined');
        var likesChangedBy = -1;
    } else {
        $(this).removeClass('icon-heart-outlined');
        $(this).addClass('icon-heart');
        $(this).parent().find('.likeCount').removeClass('d-none');
        var likesChangedBy = 1;
    }
    var postable_id = $(this).data("id");
    var postable_type = $(this).data("type");
    var element = this;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: "/toggle-like",
        type: "PUT",
        data: {
            "postable_id": postable_id,
            "postable_type": postable_type,
        },
        success: function (response) {
            var data = $('#like_count_'+postable_id);
            var likes = parseInt(data.attr('data-likes'));
            if (likesChangedBy == 1) {
                data.attr('data-likes', likes + 1);
                data.text(likes + 1 + ' likes');
            } else {
                data.attr('data-likes', likes - 1);
                data.text(likes - 1 + ' likes');
            }
            // updateLikeNumber(element, likesChangedBy);
        }
    });
});

function timeSince(date) {
    var seconds = Math.floor((new Date() - date) / 1000);
    var interval = seconds / 31536000;
    if (interval > 1) {
        return Math.floor(interval) + " years";
    }
    interval = seconds / 2592000;
    if (interval > 1) {
        return Math.floor(interval) + " months";
    }
    interval = seconds / 86400;
    if (interval > 1) {
        return Math.floor(interval) + " days";
    }
    interval = seconds / 3600;
    if (interval > 1) {
        return Math.floor(interval) + " hours";
    }
    interval = seconds / 60;
    if (interval > 1) {
        return Math.floor(interval) + " minutes";
    }
    return Math.floor(seconds) + " seconds";
}
$('.collapse').collapse({
    toggle: false
});

$(document).on('click', '.commentSection', function() {
    var areaExpanded = $(this).attr('aria-expanded');
    var postId = $(this).attr('data-post-id');
    if (areaExpanded) {
        getUsersComment("/api/get-comment/"+postId+"", postId);
    }
})
