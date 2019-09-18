var count = document.getElementById("numbpics");

function reqListener() {
    console.log(this.responseText);
}

var PicReq = new XMLHttpRequest();

PicReq.onload = function () {
    var str = this.response.replace(/\"/g, '');
    response = parseInt(str, 10);
    if (isNaN(response)){
        count.innerHTML = ("Error: " + this.responseText);
    } else {
        if (response === 0){
            var prefix = "";
        } else {
            var prefix = "~";
        }
        count.innerHTML = (prefix + Math.floor((response*0.1104)+0.5) + " shots");
    }
}

function Sendit(){
    PicReq.open("get", "getPictureCount.php", true);
    PicReq.send();
}

Sendit();
setInterval(Sendit, 5000);