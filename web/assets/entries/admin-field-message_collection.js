import "../styles/field/admin-field-message_collection.scss";

const messagesCont = document.querySelector('.admin-field-message-collection--cont');
const postId = document.querySelector('meta[name="postOfList"]').attributes.getNamedItem('value').value;

const fromJsonToElement = (value,index,array) => {

    var elem = document.createElement("div");
    elem.classList.add("admin-field-message-collection");

    var elemAuthor = document.createElement("div");
    elemAuthor.classList.add("admin-field-message-collection--author");
    elemAuthor.textContent = "De "+value.author;

    var elemUrl = document.createElement("a");
    elemUrl.classList.add("admin-field-message-collection--link");
    elemUrl.setAttribute("href",value['urlDetail']);
    elemUrl.textContent = "Voir le message";

    elemAuthor.appendChild(elemUrl);

    var elemContent = document.createElement("div");
    elemContent.classList.add('admin-field-message-collection--content');
    elemContent.innerHTML = value.content;

    var elemDate = document.createElement("div");
    elemDate.classList.add('admin-field-message-collection--date');
    elemDate.innerHTML = value.createdAt;


    elem.appendChild(elemAuthor);
    elem.appendChild(elemContent);
    elem.appendChild(elemDate);

    messagesCont.appendChild(elem);
}

const managePagination = (paginationData) => {

    var elem = document.createElement("div");
    elem.classList.add("admin-field-message-collection--pagination");

    if(paginationData['isPrev'])
    {
        var elemPrev = document.createElement("div");
        // elemPrev.setAttribute("data-page",paginationData['prev']);
        elemPrev.innerHTML = "<i class='fas fa-chevron-left'></i>";

        elemPrev.addEventListener("click",(e) => {
            getData(postId,paginationData['prev'])
        })

        elem.appendChild(elemPrev);
    }

    if(paginationData['isNext'])
    {
        var elemNext = document.createElement("div");
        // elemNext.setAttribute("data-page",paginationData['next']);
        elemNext.innerHTML = "<i class='fas fa-chevron-right'></i>";

        elemNext.addEventListener("click",(e) => {
            getData(postId,paginationData['next'])
        })

        elem.appendChild(elemNext);
    }

    messagesCont.appendChild(elem);

};

const getData = (postId,page = 1) => {

    const pageText = page > 1 ? "/"+page : "";

    fetch("/admin/list_message/"+postId+pageText).then(
        res => res.json()
    ).then(
        res  => {
            messagesCont.innerHTML = "";
            res['list'].map(fromJsonToElement)
            managePagination(res['pagination'])
        }
    );

}

getData(postId);



