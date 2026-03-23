//import $ from "../js/jquery-3.3.1.slim.min.js";

const SetLanguage = () => {  
    let l = GetLanguage();

    if(l==null||l=='en') {
        window.localStorage.setItem('ActiveLanguage','ka');
    }
    else{
        window.localStorage.setItem('ActiveLanguage','en');
    }
    window.location.reload();
}

const GetLanguage=()=>{
    return window.localStorage.getItem('ActiveLanguage');
}

async function getData(){
    let lang = GetLanguage();

    let langData;

    switch(lang){
        case "en":
            langData = await fetch('../langs/en.json')
                .then(response=>response.json())
                .then(data=>langData=data);
            break;
        default:
            langData = await fetch('../langs/ka.json')
                .then(response=>response.json())
                .then(data=>langData=data);
            break;
    }
    //console.log(langData)
    
    return langData;
}

async function ChangeData(){
    let data = await getData();
    for(const[Key,Value] of Object.entries(data)){
        var html = document.querySelector('html');
        var walker = document.createTreeWalker(html, NodeFilter.SHOW_TEXT);
        var node;
        while (node = walker.nextNode()) {
            node.nodeValue = node.nodeValue.replace(`${Key}`, `${Value}`)
        }
    }
}

ChangeData();