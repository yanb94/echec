const drawerButton = document.getElementById("drawer-button");
const drawer = document.getElementById('drawer');
const drawerFull = document.getElementById('drawer-full');
const body =  document.querySelector('body');


drawerButton.addEventListener('click', () => {
    scrollToTop();
    drawer.classList.toggle('open');
    drawerFull.classList.toggle('open');
    body.classList.toggle('drawer-open');
})

drawerFull.addEventListener('click', () => {
    drawer.classList.remove('open');
    drawerFull.classList.remove('open');
    body.classList.remove('drawer-open');
})

function scrollToTop() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}