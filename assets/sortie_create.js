
$(function (){
    display_info();
})

$("#lieu").on("change",function (){
    display_info();
})

function display_info(){
// console.log($("#lieu :selected").data('lieu'));
    let lieu = $("#lieu :selected").data('lieu');
// console.log(lieu);
    $("#ville_js_sortie").text(lieu.ville)
    $("#rue_js_sortie").text(lieu.rue);
    $("#code_js_sortie").text(lieu.codePostal);
    $("#longitude_js_sortie").text(lieu.longitude);
    $("#latitude_js_sortie").text(lieu.latitude);
}