let like = require("./like")

function formatDate(date){
    var newdate = '';
    if(date.getDate()<10){
        newdate = newdate+'0'+date.getDate()+'/';
    }else{
        newdate = newdate+date.getDate()+'/';
    }
    if(date.getMonth()<10){
        newdate = newdate+'0'+date.getMonth()+'/';
    }else{
        newdate = newdate+date.getMonth()+'/';
    }
    newdate = newdate+date.getFullYear()+' ';
    if(date.getHours()<10){
        newdate = newdate+'0'+date.getHours()+':';
    }else{
        newdate = newdate+date.getHours()+':';
    }
    if(date.getMinutes()<10){
        newdate = newdate+'0'+date.getMinutes();
    }else{
        newdate = newdate+date.getMinutes();
    }

    return newdate;
}

$(document).ready(function () {
    var should = true;

    $(window).scroll(function () {

        var position = $(window).scrollTop();
        var bottom = $(document).height() - $(window).height();
        var response = [];
        if (Math.ceil(position)+400 >= bottom && should==true) {
            should=false;
            var row = $('.card').length;
            $.ajax({
                url: '/get/posts',
                type: 'get',
                data: {
                    first: row,
                    limit: 5
                },
                success: function (response) {
                    $.each(response, function (key, value) {
                        var date = new Date(value['date']);
                        var newdate = formatDate(date);
                        var div = $('.card').first().clone();
                        div.find('.subreddit-title').html(value['subreddit']);
                        div.find('.subreddit-title').attr('href', '/r/' + value['subreddit']);
                        div.find('.username').html(value['user']);
                        div.find('.username').attr('href', '/user/' + value['user']);
                        div.find('.post-title').html(value['title']);
                        div.find('.post-title').attr('href', '/r/' + value['subreddit']+'/'+value['slug']);
                        div.find('.post-content').html(value['content']+'...');
                        div.find('.date').html(newdate);
                        div.find('.collapse').attr('id', value['id']);
                        div.find('.btn-collapse').attr('data-target', '#'+value['id']);
                        div.find('.count-likes').html(value['count']);
                        div.find('.js-like-link').removeClass('is-liked');
                        div.find('.js-dislike-link').removeClass('is-liked');
                        div.find('.js-like-link').attr('href', '/post/'+value['id']+'/like')
                        div.find('.js-dislike-link').attr('href', '/post/'+value['id']+'/dislike')
                        if(value['isLiked']){
                            div.find('.js-like-link').addClass('is-liked');
                        }
                        if(value['isDisliked']){
                            div.find('.js-dislike-link').addClass('is-liked');
                        }
                        
                        $('.container').append(div);
                    })
                    like.setLink();
                    should=true;
                }
            });
        }

    });

});