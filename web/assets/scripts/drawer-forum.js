const drawerForum = document.querySelector(".forum--cont--drawer");
const drawerForumButton = document.querySelector('.forum--cont--actions--cat-show--btn');
const drawerForumClose = document.querySelector('.forum--cont--drawer--close');


drawerForumButton.addEventListener("click",() => {
    drawerForum.classList.add('open')
})

drawerForumClose.addEventListener('click', () => {
    drawerForum.classList.remove('open');
})