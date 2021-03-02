const btn = document.querySelector('.contentBtn');
const wordBtn = document.querySelector('.wordBtn');
const div = document.querySelectorAll('.contents');
const h4 = document.querySelectorAll('h4');
const tagbr = "<input type='text'>";
let addInput = document.createElement('div');
addInput.innerHTML = "hi";
const line = "<br>";

btn.addEventListener('click', function(){
    // if(btn.innerText=="보임") {
    //     div.forEach(e => e.style.display = 'none');
    //    // div.forEach(e => {[btn.innerText="내용", e.style.display = 'block', wordBtn.disabled=false]});
    // }else{
    //     div.forEach(e => e.style.display = 'bl');
    //     //div.forEach(e => btn.innerText="내용", e.style.display = 'block', wordBtn.disabled=false);
    // }
        div.forEach(e => e.style.visibility != 'hidden' ? [ btn.innerText="보임", e.style.visibility = 'hidden', wordBtn.disabled='disabled' ] : [ btn.innerText="내용", e.style.visibility = 'visible', wordBtn.disabled=false ]);
});        
wordBtn.addEventListener('click', function(){
    // if(wordBtn.innerText=="보임") {
    //     h4.forEach(e => e.style.visibility = 'hidden');
    //    // div.forEach(e => {[btn.innerText="내용", e.style.display = 'block', wordBtn.disabled=false]});
    // }else{
    //     h4.forEach(e => e.style.visibility = 'visible');
    //     //div.forEach(e => btn.innerText="내용", e.style.display = 'block', wordBtn.disabled=false);
    // }
    h4.forEach(e => e.style.visibility != 'hidden' ? [ wordBtn.innerText="보임", e.style.visibility = 'hidden', btn.disabled='disabled' ] : [ wordBtn.innerText="단어", e.style.visibility = 'visible', btn.disabled=false ]);
});

h4.forEach(
    e => e.addEventListener('click', function(){
        this.nextElementSibling.style.visibility != 'hidden' ? this.nextElementSibling.style.visibility = 'hidden' : this.nextElementSibling.style.visibility = 'visible';
    })
);

div.forEach(
    e => e.addEventListener('click', function(){
        this.previousElementSibling.style.visibility != 'hidden' ? this.previousElementSibling.style.visibility = 'hidden' : this.previousElementSibling.style.visibility = 'visible';
    })
);


function onLoggin(){

    const email = document.getElementById("email");
    const password = document.getElementById('pw')
    axios({
        method:"POST",
        url: 'https://reqres.in/api/login',
        data:{
            "email": email.value,
            "password": password.value
        }
    }).then((res)=>{
        if(res.status == 200){
            alert("야호 성공");
        }else{
            alert("에러다 에러");
        }
        console.log(res);
    }).catch(error=>{
        console.log(error);
        throw new Error(error);
    });
}
