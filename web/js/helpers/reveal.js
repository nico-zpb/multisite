(function(){
    var commentRegexp = /<!-- start ([\s\S]*?)-->/g;
    document.body.innerHTML = document.body.innerHTML.replace(commentRegexp, "<span class='show-art'>$1</span>");
    var arts = document.querySelectorAll(".show-art");
    [].slice.call(arts).forEach(function(el){
        el.style.color = "#ff0000";
    })
})();
