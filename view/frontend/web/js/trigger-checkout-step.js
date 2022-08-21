window.addEventListener('popstate', ()=>{
    var registry = require('uiRegistry'),
        progressBar = registry.get('index = progressBar'),
        reviewAndPaymentsStep = progressBar.steps()[0];
    if (progressBar.isProcessed(reviewAndPaymentsStep))
        jQuery.ajax({
            url: window.location.origin + '/rest/all/V1/usercom-analytics/order/step2',
            data: JSON.stringify({ userKey : getCookie("userKey") }),
            type: "POST",
            dataType: 'json',
            contentType:"application/json"
        });
});

function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
