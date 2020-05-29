get("setores").then(setores => {
    $(".inputSetor").each(function(i, e) {
        let $setor = $(e).find("select");
        $setor.html('<option value="" selected="selected" disabled="disabled">selecione</option>');
        let id = $setor.attr("id");
        for(let setor of setores)
            $setor.append('<option value="' + setor + '"' + (!isEmpty(form.data[id]) && setor === form.data[id] ? ' selected="selected"' : '') + '>' + ucFirst(setor.replace("_", " ").replace("_", " "))  + '</option>');
    });
});