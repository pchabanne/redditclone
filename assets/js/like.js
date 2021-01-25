function onClickBtnLike(event){
    event.preventDefault();
    const url = this.href;
    const token = document.querySelector('#token').dataset.token;
    const spanCount =  this.parentNode.querySelector(".count-likes");
    const iconeUp = this;
    const iconeDown = this.parentNode.querySelector(".js-dislike-link");
    axios.post(url, {token:token}).then(function(response){
        spanCount.textContent = response.data.count
        const message = response.data.message;
        if(message=='like added'){
            iconeUp.classList.add("is-liked");
        }
        if(message=='like deleted'){
            iconeUp.classList.remove("is-liked");
        }
        if(message=='dislike deleted like added'){
            iconeUp.classList.add("is-liked");
            iconeDown.classList.remove("is-liked");
        }
    }).catch(error => {
        window.location.href= '/login';
    })
}

function onClickBtnDislike(event){
    event.preventDefault();
    const url = this.href;
    const token = document.querySelector('#token').dataset.token;
    const spanCount =  this.parentNode.querySelector(".count-likes");
    const iconeUp = this.parentNode.querySelector(".js-like-link");
    const iconeDown = this;
    axios.post(url, {token:token}).then(function(response){
        spanCount.textContent = response.data.count
        const message = response.data.message;
        if(message=='dislike added'){
            iconeDown.classList.add("is-liked");
        }
        if(message=='dislike deleted'){
            iconeDown.classList.remove("is-liked");
        }
        if(message=='like deleted dislike added'){
            iconeUp.classList.remove("is-liked");
            iconeDown.classList.add("is-liked");
        }
    }).catch(error => {
        window.location.href= '/login';
    })
}




function setLink(){
    document.querySelectorAll('a.js-like-link').forEach(function(link){
        link.addEventListener('click', onClickBtnLike)
    });;
    document.querySelectorAll('a.js-dislike-link').forEach(function(link){
        link.addEventListener('click', onClickBtnDislike)
    });;
}

setLink();

module.exports = {
    setLink
}
