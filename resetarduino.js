var button_reset = document.getElementById("button_reset");
var text = document.getElementById("textthingy");

function change_pin(pin, status) {
	//this is the http request
	var request = new XMLHttpRequest();
	request.open("GET", "gpio.php?pin=" + pin + "&status=" + status, true);
	request.send();
	request.onreadystatechange = function () {
		if (request.readyState == 4 && request.status == 200) {
			return (request.responseText);
		} else if (request.readyState == 4 && request.status == 500) {
			alert("server error");
			return ("fail");
        } else {
            return ("fail");
        }
	}
}

/*button_reset.addEventListener("click", function(){
    var new_status = pin_request(26, 0);
	if (new_status != "fail") {
        button_reset.alt = "on";
        button_reset.src = "data/img/green/green.jpg";
	}
	setTimeout(function () {
		new_status = pin_request(26, 1);
		if (new_status != "fail") {
			button_reset.alt = "off";
			button_reset.src = "data/img/red/red.jpg";
		}
	}, 1000);
    return 0;
});*/

var new_status = change_pin(26, 0);
text.innerHTML = ("Resetting.");
setTimeout(function () {
    text.innerHTML = ("Resetting..");
    setTimeout(function () {
        text.innerHTML = ("Resetting...");
        setTimeout(function () {
            text.innerHTML = ("Resetting... Done!");
            new_status = change_pin(26, 1);
            setTimeout(function () {
                //window.location.href = 'http://picontrol.ddns.net/';
                window.close();
            }, 600);
        }, 800);
    }, 600);
}, 600);