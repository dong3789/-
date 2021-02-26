const btn = document.querySelector('.contentBtn');
const wordBtn = document.querySelector('.wordBtn');
const div = document.querySelectorAll('.contents');
const h4 = document.querySelectorAll('h4');
const tagbr = "<input type='text'>";
let addInput = document.createElement('div');
addInput.innerHTML = "hi";
const line = "<br>";

btn.addEventListener('click', function(){
    div.forEach(e => e.style.display != 'none' ? [ btn.innerText="보임", e.style.display = 'none' ] : [ btn.innerText="내용", e.style.display = 'block' ]);
});        
wordBtn.addEventListener('click', function(){
    
    h4.forEach(e => e.style.display != 'none' ? [ wordBtn.innerText="보임", e.style.display = 'none' ] : [ wordBtn.innerText="안보임", e.style.display = 'block' ]);
});

h4.forEach(
    e => e.addEventListener('click', function(){
        this.nextElementSibling.style.display != 'none' ? this.nextElementSibling.style.display = 'none' : this.nextElementSibling.style.display = 'block';
    })
);

div.forEach(
    e => e.addEventListener('click', function(){
        this.previousElementSibling.style.display != 'none' ? this.previousElementSibling.style.display = 'none' : this.previousElementSibling.style.display = 'block';
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
