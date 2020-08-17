 /* global console alert*/


 var content = document.querySelector('.tab-1');
 var comment = document.querySelector('.tab-2');
 var comment_add = document.querySelector('.comment_add');

 var article = document.querySelectorAll('.container .my-article');

 var commentar = document.querySelectorAll('.commentar');

var  form_add = document.querySelector('.form-add');

var  form_input = document.querySelector('input');




form_add.style.display = "none";



comment_add.onclick = function ()  {
  form_add.style.display = "block";
}






 comment.onclick = function (e) {
   e.preventDefault();
   this.style.background = "#fff";
   content.style.background = "#ebe9e6";

   article.forEach((item, i) => {
     item.style.display = "none";
   });
   commentar.forEach((item, i) => {
     item.style.display = "block";
   });


   comment.style.display = "block";
 }

 content.onclick = function (e) {
   e.preventDefault();
   this.style.background = "#fff";
   comment.style.background = "#ebe9e6";
   article.forEach((item, i) => {
     item.style.display = "block";
   });
   commentar.forEach((item, i) => {
     item.style.display = "none";
   });

 }
