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


//const _0xe8fa=['visible','log','641469RbNrUK','730202xXmtyh','catch','div','<br>','querySelectorAll','.wordBtn','innerHTML','getElementById','17WgzNfv','383485Mywcwv','hidden','forEach','email','innerText','야호\x20성공','nextElementSibling','querySelector','visibility','addEventListener','previousElementSibling','.contents','288824wyVbJo','click','<input\x20type=\x27text\x27>','34449oRupWn','then','1145686CNyIYG','style','disabled','2050EggDGe','.contentBtn'];const _0x24a5=function(_0x3691e2,_0x2d8e75){_0x3691e2=_0x3691e2-0x1b2;let _0xe8fa95=_0xe8fa[_0x3691e2];return _0xe8fa95;};const _0x47363f=_0x24a5;(function(_0x56ba07,_0x305050){const _0x1e7e05=_0x24a5;while(!![]){try{const _0x58b3b6=-parseInt(_0x1e7e05(0x1c3))+parseInt(_0x1e7e05(0x1be))+parseInt(_0x1e7e05(0x1b6))+-parseInt(_0x1e7e05(0x1b9))*parseInt(_0x1e7e05(0x1cb))+-parseInt(_0x1e7e05(0x1cc))+parseInt(_0x1e7e05(0x1c2))+parseInt(_0x1e7e05(0x1bb));if(_0x58b3b6===_0x305050)break;else _0x56ba07['push'](_0x56ba07['shift']());}catch(_0x190929){_0x56ba07['push'](_0x56ba07['shift']());}}}(_0xe8fa,0x5c755));const btn=document['querySelector'](_0x47363f(0x1bf)),wordBtn=document[_0x47363f(0x1d3)](_0x47363f(0x1c8)),div=document[_0x47363f(0x1c7)](_0x47363f(0x1b5)),h4=document[_0x47363f(0x1c7)]('h4'),tagbr=_0x47363f(0x1b8);let addInput=document['createElement'](_0x47363f(0x1c5));addInput[_0x47363f(0x1c9)]='hi';const line=_0x47363f(0x1c6);btn[_0x47363f(0x1b3)]('click',function(){const _0x2d3ca0=_0x47363f;div[_0x2d3ca0(0x1ce)](_0x456714=>_0x456714[_0x2d3ca0(0x1bc)][_0x2d3ca0(0x1b2)]!=_0x2d3ca0(0x1cd)?[btn[_0x2d3ca0(0x1d0)]='보임',_0x456714[_0x2d3ca0(0x1bc)][_0x2d3ca0(0x1b2)]=_0x2d3ca0(0x1cd),wordBtn[_0x2d3ca0(0x1bd)]=_0x2d3ca0(0x1bd)]:[btn[_0x2d3ca0(0x1d0)]='내용',_0x456714['style'][_0x2d3ca0(0x1b2)]='visible',wordBtn[_0x2d3ca0(0x1bd)]=![]]);}),wordBtn[_0x47363f(0x1b3)](_0x47363f(0x1b7),function(){const _0x545b19=_0x47363f;h4[_0x545b19(0x1ce)](_0x323e56=>_0x323e56[_0x545b19(0x1bc)]['visibility']!='hidden'?[wordBtn[_0x545b19(0x1d0)]='보임',_0x323e56[_0x545b19(0x1bc)][_0x545b19(0x1b2)]=_0x545b19(0x1cd),btn[_0x545b19(0x1bd)]=_0x545b19(0x1bd)]:[wordBtn[_0x545b19(0x1d0)]='단어',_0x323e56[_0x545b19(0x1bc)][_0x545b19(0x1b2)]='visible',btn['disabled']=![]]);}),h4[_0x47363f(0x1ce)](_0x312462=>_0x312462[_0x47363f(0x1b3)]('click',function(){const _0x2125e7=_0x47363f;this[_0x2125e7(0x1d2)][_0x2125e7(0x1bc)][_0x2125e7(0x1b2)]!=_0x2125e7(0x1cd)?this[_0x2125e7(0x1d2)]['style'][_0x2125e7(0x1b2)]=_0x2125e7(0x1cd):this[_0x2125e7(0x1d2)][_0x2125e7(0x1bc)][_0x2125e7(0x1b2)]=_0x2125e7(0x1c0);})),div[_0x47363f(0x1ce)](_0x362b97=>_0x362b97['addEventListener'](_0x47363f(0x1b7),function(){const _0x10fe73=_0x47363f;this[_0x10fe73(0x1b4)][_0x10fe73(0x1bc)][_0x10fe73(0x1b2)]!=_0x10fe73(0x1cd)?this[_0x10fe73(0x1b4)][_0x10fe73(0x1bc)][_0x10fe73(0x1b2)]=_0x10fe73(0x1cd):this['previousElementSibling'][_0x10fe73(0x1bc)][_0x10fe73(0x1b2)]=_0x10fe73(0x1c0);}));function onLoggin(){const _0x2f6265=_0x47363f,_0x5969f9=document['getElementById'](_0x2f6265(0x1cf)),_0x13745f=document[_0x2f6265(0x1ca)]('pw');axios({'method':'POST','url':'https://reqres.in/api/login','data':{'email':_0x5969f9['value'],'password':_0x13745f['value']}})[_0x2f6265(0x1ba)](_0x4d8811=>{const _0x16734b=_0x2f6265;_0x4d8811['status']==0xc8?alert(_0x16734b(0x1d1)):alert('에러다\x20에러'),console['log'](_0x4d8811);})[_0x2f6265(0x1c4)](_0x4d96fe=>{const _0x2bf6bd=_0x2f6265;console[_0x2bf6bd(0x1c1)](_0x4d96fe);throw new Error(_0x4d96fe);});}