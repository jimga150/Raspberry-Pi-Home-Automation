var dhttext = document.getElementById("dht");
function reqListener() {
    console.log(this.responseText);
}
var DHTReq = new XMLHttpRequest();
DHTReq.onload = function () {
    //alert("3362");
    //alert(parseInt("3362", 10));
    //alert(this.response);

    //in playing with this, i found out that this.responsetext actually has the quotes 
    //redundantly included in the string. this next command removes those quotes to 
    //make the string readable by parseInt().
    var str = this.response.replace(/\"/g, '');
    //                               ^^
    //                               this is the escape slash followed by a quote, so that 
    //                               replace() interprets it as 'remove all instances of 
    //                               double quotes'

    response = parseInt(str, 10);//change it to an integer so we can decode it using math
    if (isNaN(response)){
        dhttext.innerHTML = ("Error: "+this.responseText);
    } else {
        var temp = toFar(Math.floor(response / 1000));//extract the temperature, convert from Celsius to F
        var RH = response % 1000;//extract Relative Humidity
        //alert(temp);
        //alert(RH);
        dhttext.innerHTML = ("It is " + temp + " degrees F, with " + RH + "% <a href=\"http://en.wikipedia.org/wiki/Relative_humidity#Definition\" target=\"_blank\">Relative Humidity</a>.");
    }
};
function toFar(input) {
    return ((input * 9) / 5) + 32;//C to F formula
}
function Sendit(){
    DHTReq.open("get", "DHT.php", true);
    DHTReq.send();
}
Sendit();
setInterval(Sendit, 5000);//the average time it takes for this to complete is 2.59 seconds,
                          //so i give each request a breathing room of 4 seconds.