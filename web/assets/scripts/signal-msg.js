const messagesActions = document.querySelectorAll('.post-forum-msg--actions');
const menus = document.querySelectorAll('.post-forum-msg--actions--list');
const body = document.querySelector('body');
const modal = document.querySelector('.post-forum--modal');
const contModal = document.querySelector('.post-forum--modal--cont');
const msgInModal = document.querySelector('.post-forum--modal--cont--msg');
const hiddenMsgField = document.getElementById('signal_message');
const closeModalButton = document.querySelector('.post-forum--modal--cont--close');
const alertMsg = document.querySelector('.post-forum--notice');
const closeAlertMsg = document.querySelector('.post-forum--notice--close');

const closeModal = () => {
    modal.classList.remove('open');
    body.classList.remove('modal-signal');
    hiddenMsgField.removeAttribute('value');
    msgInModal.innerHTML = "";
};

messagesActions.forEach((elem) => {
    elem.addEventListener('click', (e) => {
       const target = e.currentTarget;
       const childMenu = target.children[1];
       childMenu.classList.toggle('show');
    })
})

menus.forEach((elem) => {
    elem.addEventListener('click', (e) => {
        e.stopPropagation();
        modal.classList.add('open');
        body.classList.add('modal-signal');

        const msg = e.currentTarget.dataset.msg;
        const content = document.querySelector('#'+msg+" > .post-forum-msg--content").innerHTML;
        hiddenMsgField.setAttribute('value',msg.split("msg-")[1]);
        msgInModal.innerHTML = content;
    })
})

modal.addEventListener("click", () => {
    closeModal();
})

closeModalButton.addEventListener("click", () => {
    closeModal();
})

contModal.addEventListener("click", (e) => {
    e.stopPropagation();
})

if(alertMsg)
{
    closeAlertMsg.addEventListener("click", () => {
        alertMsg.remove();
    })
}