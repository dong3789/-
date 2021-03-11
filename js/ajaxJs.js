

function onLoggin(){

    //const email = document.getElementById("email");
    //const password = document.getElementById('pw')
    axios({        
        method:"get",
        url: 'http://158.247.210.10/test/data',
        data:{
            "data" : 6666634
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
      //  throw new Error(error);
    });
}

onLoggin();
