// INIT params
var now = new Date();
var timeString = window.localStorage.getItem('cookie');
var timeObject = new Date(timeString);
var days = Math.abs(now.getTime() - timeObject.getTime());
var diffDays = Math.ceil(days / (1000 * 3600 * 24));

// ACTIONS
if (diffDays > 6) {
    var element = document.getElementById("pig-nation");
    element.removeAttribute("style")
}

function myCookie() {
    localStorage.setItem('cookie', now);
    var element = document.getElementById("pig-nation");
    element.parentNode.removeChild(element);
}