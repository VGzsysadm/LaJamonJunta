// INIT params
var now = new Date();
var timeString = window.localStorage.getItem('alert');
var timeObject = new Date(timeString);
var days = Math.abs(now.getTime() - timeObject.getTime());
var diffDays = Math.ceil(days / (1000 * 3600 * 24));
// ACTIONS
if (timeString == null) {
    $('#alert-modal').modal(Option)
    localStorage.setItem('alert', now);
} else if (diffDays > 1) {
    $('#alert-modal').modal(Option)
    localStorage.setItem('alert', now);
}