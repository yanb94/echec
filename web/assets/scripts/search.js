const searchButton = document.getElementById('searchButton');
const searchForm = document.getElementById('searchForm');
const searchInput = document.getElementById('searchInput');
const closeSearch = document.querySelector('.search-phone--close');
const searchMobile = document.querySelector('.search-phone');
const body = document.querySelector('body');

const isPhone = window.matchMedia("(max-width: 576px)");


searchButton.addEventListener('click', () => {

    if(isPhone.matches)
    {
        return onPhoneSearch();
    }

    return onBigScreenSearch();
})


closeSearch.addEventListener("click", () => {
    searchMobile.classList.remove('open');
    body.classList.remove('search-phone-open');
});

const onPhoneSearch = () => {
    scrollToTop();
    searchMobile.classList.add('open')
    body.classList.add('search-phone-open');
};

const onBigScreenSearch = () => {
    if(searchForm.classList.contains('open'))
    {
        if(searchInput.value != "")
            return searchForm.submit();
        
        return searchForm.classList.remove('open');
    }
    
    return searchForm.classList.add('open');
}

function scrollToTop() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}