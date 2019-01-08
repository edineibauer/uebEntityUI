$(function () {
    getJSON(HOME + "get/tpl/sites").then(tpl => {
        if(tpl.response === 1 && tpl.data.sites !== "") {
            dbLocal.exeRead("sites").then(sites => {
                if(typeof sites === "object" && sites !== null && sites.length > 0) {
                    $.each(sites, function (i, e) {
                        $("#sites").prepend(Mustache.render(tpl.data.sites, e));
                    })
                }
            });
        }
    });
});