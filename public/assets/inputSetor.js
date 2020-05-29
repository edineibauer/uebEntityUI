get("setores").then(setores => {
    $("#setor").html('<option value="" selected="selected" disabled="disabled">selecione</option>');
    for(let setor of setores)
        $("#setor").append('<option value="' + setor + '">' + setor  + '</option>');
});