$(document).ready(function () {
    $('.create-post').click(function(){
        
        var subredditTitleTag = document.querySelector('.create-post');
        var isUserActive = subredditTitleTag.dataset.activeUser;
        if(isUserActive == ""){
            window.location.href = '/login';
        }
        else{
            var subredditTitle = subredditTitleTag.dataset.subredditTitle
            if(subredditTitle == ""){
                window.location.href = '/submit';
            }else{
                window.location.href = '/r/'+subredditTitle+'/submit';
            }
        }
    })
});