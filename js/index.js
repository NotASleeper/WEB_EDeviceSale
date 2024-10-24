let btn_bar = document.querySelector('#menu-btn');
let menu_bar = document.querySelector('.header-container .flex .menu-bar');
btn_bar.addEventListener('click',() => {
    profile_div.classList.remove('active');
    menu_bar.classList.toggle('active');

});

let btn_user = document.querySelector('#user-btn');
let profile_div = document.querySelector('.header-container .flex .profile');
btn_user.addEventListener('click',() =>{
    profile_div.classList.toggle('active');
    menu_bar.classList.remove('active');
})

window.onscroll = () => {
    menu_bar.classList.remove('active');
    profile_div.classList.remove('active');
}