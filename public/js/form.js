/*global console */

var tab1 = document.getElementById('tab-1');

var tab2 = document.getElementById('tab-2');


var sign_in = document.querySelector(".sign-in-htm");


var sign_up = document.querySelector(".sign-up-htm");


  console.log(tab1);

sign_up.style.display = "none";
tab1.onclick  = function (e) {
  sign_in.style.display = "block";
  sign_up.style.display = "none";
}

tab2.onclick  = function (e) {
  sign_in.style.display = "none";
  sign_up.style.display = "block";

}
