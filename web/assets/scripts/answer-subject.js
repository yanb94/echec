const answerCheckboxes = document.querySelectorAll('.answer-checkbox');
const followCheckBox = document.getElementById('follow-checkbox');

const check = async (e) => {
    const target = e.target;
    const msg = target.dataset['msg'];

    window.location.replace('/forum/answer-sbuject/'+msg);
};

answerCheckboxes.forEach(answerCheckbox => {
    answerCheckbox.addEventListener("click",check);
});

if(followCheckBox)
{
    followCheckBox.addEventListener("click",(e) => {
        const target = e.target;
        const subject = target.dataset['subject'];

        window.location.replace('/forum/follow-subject/'+subject);
    })
}