var jQueryScriptOutputted = false;
function initJQuery() {
included = true;
if (typeof(jQuery) == 'undefined') {
if (! jQueryScriptOutputted) {
jQueryScriptOutputted = true;
document.write('<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></'+'script>');
}
setTimeout("initJQuery()", 50);
} else {

var oid = $("#ty_related_script").attr("oid");
var ty_url = "//shopiapps.in/test_upsell/admin/thank_you.php?o_id="+oid+"&shop="+Shopify.shop;
$.ajax({
type: 'GET',
url: ty_url,
dataType : 'html',
success: function(data){

$( data ).insertAfter( $( "#ty_related_script" ) );
}
});
}
}
if (included == undefined) {
var included = false;
if (included == false) {
initJQuery();
}
}


