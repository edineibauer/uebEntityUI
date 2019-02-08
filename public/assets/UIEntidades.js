var entity = {};
var dicionarios = {};
var info = {};
var dicionariosNomes = {};
var identifier = {};
var defaults = {};
var source_types = {
    "image": ["png", "jpg", "jpeg", "gif", "bmp", "tif", "tiff", "psd", "svg"],
    "video": ["mp4", "avi", "mkv", "mpeg", "flv", "wmv", "mov", "rmvb", "vob", "3gp", "mpg"],
    "audio": ["mp3", "aac", "ogg", "wma", "mid", "alac", "flac", "wav", "pcm", "aiff", "ac3"],
    "document": ["txt", "doc", "docx", "dot", "dotx", "dotm", "ppt", "pptx", "pps", "potm", "potx", "pdf", "xls", "xlsx", "xltx", "rtf"],
    "compact": ["rar", "zip", "tar", "7z"],
    "denveloper": ["html", "css", "scss", "js", "tpl", "json", "xml", "md", "sql", "dll"]
};

function tplObject(obj, $elem, prefix) {
    prefix = typeof(prefix) === "undefined" ? "" : prefix;
    if (typeof(obj) === "object") {
        $.each(obj, function (key, value) {
            if (obj instanceof Array) $elem = tplObject(value, $elem, prefix + key); else $elem = typeof(value) === "object" ? tplObject(value, $elem, prefix + key + ".") : $elem.replace(regexTpl(prefix + key), value)
        })
    } else {
        $elem = $elem.replace(regexTpl(prefix), obj)
    }
    return $elem
}

function regexTpl(v) {
    return new RegExp('__\\s*\\$' + v + '\\s*__', 'g')
}

function copy($elem, $destino, variable, position) {
    $elem = (typeof($elem) === "string" ? $($elem) : $elem);
    $destino = (typeof($destino) === "string" ? $($destino) : $destino);
    $elem = $elem.clone().removeClass("hide").removeAttr("id").prop('outerHTML');
    $elem = tplObject(variable, $elem);
    if (typeof(position) === "undefined") $($elem).prependTo($destino); else if (position === "after") $($elem).insertAfter($destino); else if (position === "before") $($elem).insertBefore($destino); else $($elem).appendTo($destino)
}

function readDefaults() {
    post("entity-ui", "load/defaults", function (data) {
        defaults = data;
    });
}

function readInfo() {
    post("entity-ui", "load/info", function (data) {
        info = data;
    });
}

function readIdentifier() {
    post("entity-ui", "load/identifier", function (data) {
        identifier = data;
    });
}

function readDicionarios() {
    readInfo();
    readIdentifier();
    post("entity-ui", "load/dicionarios", function (data) {
        dicionarios = data;
        $("#entity-space, #relation").html("");

        $.each(dicionarios, function (i, e) {
            dicionariosNomes[i] = i;
            copy("#tpl-entity", "#entity-space", i, true);
            $("#relation").append("<option value='" + i + "'>" + i + "</option>");
        });
    });
}

function entityReset() {
    entity = {
        "name": "",
        "icon": "",
        "autor": "",
        "owner": "",
        "edit": null
    };
}

function entityEdit(id) {
    $("#importForm").addClass("hide");
    if ((typeof(id) === "undefined" && entity.name !== "") || (typeof(id) !== "undefined" && id !== entity.name)) {
        resetAttr();
        entityReset();

        if (typeof(id) !== "undefined") {
            entity.name = id;
            entity.icon = info[id]["icon"];
            entity.autor = info[id]["autor"];
            entity.owner = info[id]["owner"];

            $("#entityIconDemo").text(entity.icon || "");
            $("#haveAutor").prop("checked", entity.autor === 1);
            $("#haveOwner").prop("checked", entity.autor === 2);
        }

        showEntity();
    } else {
        $("#entityName").focus();
    }
}

function uploadEntity() {
    entityReset();
    showEntity();
    $("#importForm").removeClass("hide");
}

function showEntity() {
    $("#entityName").val(entity.name).focus();
    $("#entityIcon").val(entity.icon);
    $("#entityIconDemo").text(entity.icon);
    $("#haveAutor").prop("checked", entity.autor === 1);
    $("#haveOwner").prop("checked", entity.autor === 2);
    $("#entityAttr").html("");

    let maxIndice = 1;
    $.each(dicionarios[entity.name], function (i, e) {
        if (maxIndice < e.indice)
            maxIndice = e.indice;
    });
    maxIndice++;

    for (c = 1; c < maxIndice; c++) {
        $.each(dicionarios[entity.name], function (i, f) {
            if (f && f.indice == c) {
                copy("#tpl-attrEntity", "#entityAttr", [i, f.column], true);
                return;
            }
        });
    }
}

function updateDicionarioIndex(entity) {
    //atualiza lista de dicionarios
    get("dicionarios").then(dicionarios => {
        dbLocal.clear('__dicionario').then(() => {
            dbLocal.exeCreate("__dicionario", dicionarios);
        });
    });

    //atualiza lista de infos
    get("info").then(info => {
        dbLocal.clear('__info').then(() => {
            dbLocal.exeCreate("__info", info);
        });
    });

    setUpdateVersion();

    let t = {};
    t[entity] = 0;
    dbLocal.exeUpdate("__historic", t, 1);
    dbLocal.clear(entity);
}

function saveEntity(silent) {
    if (checkSaveAttr() && entity.name.length > 2 && typeof(dicionarios[entity.name]) !== "undefined" && !$.isEmptyObject(dicionarios[entity.name])) {
        let newName = slug($("#entityName").val(), "_");

        post("entity-ui", "save/entity", {
            "name": entity.name,
            "icon": $("#entityIcon").val(),
            "autor": $("#haveAutor").prop("checked"),
            "owner": $("#haveOwner").prop("checked"),
            "dados": dicionarios[entity.name],
            "id": identifier[entity.name],
            "newName": newName
        }, function (g) {

            updateDicionarioIndex(entity.name);

            if (entity.name !== $("#entityName").val()) {
                dicionarios[newName] = dicionarios[entity.name];
                entity.name = newName;
                readInfo();
                if(typeof(info[entity.name]) !== "undefined")
                    info[entity.name]["icon"] = $("#entityIcon").val();
            }

            if(typeof(silent) === "undefined")
                toast("Salvo", 1500);

            if (g && typeof(silent) === "undefined")
                readDicionarios()
        });
    }
}

function resetAttr(id) {
    entity.edit = typeof(id) !== "undefined" ? id : null;
    $("#atributos, #template, #style, #class, #orientation, .input").val("");
    $(".selectInput").css("color", "#AAAAAA").val("");
    $(".allformat").prop("checked", false);
    $("#format-source, .formato-div, #requireListExtend, .relation_container, #requireListFilter, .relation_creation_container").addClass("hide");
    $("#allowBtnAdd, #spaceValueAllow").removeClass("hide");
    $("#spaceValueAllow, #requireListExtendDiv, #list-filter, #relation_fields_show, #relation_fields_default").html("");

    $(".allformat").prop("checked", false);
    $(".formato-div").addClass("hide");
    $(".file-format").each(function () {
        $(this).prop("checked", false);
        $("." + $(this).attr("id") + "-format").prop("checked", false);
    });
    if (entity.edit !== null)
        $(".selectInput, #relation").attr("disabled", "disabled").addClass("disabled");
    else
        $(".selectInput, #relation").removeAttr("disabled").removeClass("disabled");

    applyAttr(getDefaultsInfo());
    $("#nome").trigger("change");
}

function indiceChange(id, val) {
    let dic = dicionarios[entity.name];
    let $li = $(".list-att-" + id);
    let max = 0;
    let nextId = null;
    let searchNextIndice = val > 0 ? 10000 : 0;
    $.each(dic, function (i, e) {
        if ((val > 0 && e.indice > dic[id].indice && e.indice < searchNextIndice) || (val < 0 && e.indice < dic[id].indice && e.indice > searchNextIndice)) {
            searchNextIndice = e.indice;
            nextId = i;
        }
        max++;
    });

    //retorna caso não tenha alteração
    if (!nextId || (dic[id].indice === 1 && val === -1) || (dic[id].indice === max && val === 1))
        return;

    dic[nextId].indice = dic[id].indice;
    dic[id].indice = searchNextIndice;

    //atualiza o html
    if (val > 0) {
        $li.detach().insertAfter($(".list-att-" + nextId));
    } else {
        $li.detach().insertBefore($(".list-att-" + nextId));
    }
}

function editAttr(id) {
    if (id !== entity.edit) {
        if (checkSaveAttr())
            resetAttr(id);
    }
}

var alert = false;
function checkSaveAttr() {
    var yes = true;
    entity.icon = $("#entityIcon").val();
    entity.autor = $("#haveAutor").prop("checked") ? 1 : ($("#haveOwner").prop("checked") ? 2 : null);

    if (checkRequiresFields()) {
        if (entity.edit === null) {
            if (entity.name === "") {
                let temp = slug($("#entityName").val(), '_');
                $.each(dicionarios, function (nome, data) {
                    if (nome === temp && !alert) {
                        toast("Nome de Entidade já existe", 2000, "toast-warning");
                        alert = true;
                        yes = false;

                        setTimeout(function () {
                            alert = false;
                        },2000);
                    }
                });
                if (yes && allowName(temp, 1)) {
                    entity.name = temp;

                    identifier[entity.name] = 1;
                    dicionarios[entity.name] = {};
                }

                if (yes)
                    yes = checkUniqueNameColumn();
            }
            if (yes) {
                entity.edit = identifier[entity.name];
                identifier[entity.name]++;
            }
        }
        if (yes) {
            saveAttrInputs();
            resetAttr();
            showEntity();
        }
    }
    return yes;
}

function saveAttrInputs() {
    if (typeof(dicionarios[entity.name][entity.edit]) !== "undefined")
        var oldData = dicionarios[entity.name][entity.edit];

    dicionarios[entity.name][entity.edit] = assignObject(defaults.default, defaults[getType()]);

    $.each($(".input"), function () {
        if (!$(this).hasClass("hide"))
            saveAttrValue($(this));
    });

    dicionarios[entity.name][entity.edit]['allow']['options'] = [];

    checkSaveFilter();
    checkSaveSelect();
    checkSaveAssociacaoShowAttr();

    if (dicionarios[entity.name][entity.edit]['format'] === "source")
        checkSaveSource();
    else
        checkSaveAllow();

    if (typeof(oldData) === "undefined" || typeof(oldData['indice']) === "undefined") {
        let lastIndice = 0;
        $.each(dicionarios[entity.name], function (i, e) {
            if (e.indice > lastIndice)
                lastIndice = e.indice;
        });
        dicionarios[entity.name][entity.edit]['indice'] = lastIndice + 1;
    } else {
        dicionarios[entity.name][entity.edit]['indice'] = oldData['indice'];
    }
}

function checkSaveFilter() {
    if ($("#list-filter").html() !== "") {
        $("#list-filter").find(".filterTpl").each(function () {
            var $this = $(this);
            var filter = $this.find(".filter").val();
            var filter_column = $this.find(".filter_column").length > 0 ? $this.find(".filter_column").val() : null;
            var filter_operator = $this.find(".filter_operator").val();
            var filter_value = $this.find(".filter_value").val();

            if (filter !== "" && filter_operator !== "" && filter_value !== "")
                dicionarios[entity.name][entity.edit]['filter'].push(filter + "," + filter_operator + "," + filter_value + "," + filter_column);
        });
    }
}

function checkSaveAssociacaoShowAttr() {
    if ($.inArray(dicionarios[entity.name][entity.edit]['key'], ["extend", "extend_add", "extend_mult", "extend_folder", "list", "list_mult", "selecao", "selecao_mult", "checkbox_rel", "checkbox_mult"]) > -1) {

        if (dicionarios[entity.name][entity.edit]['form'] !== false) {

            if (typeof (dicionarios[entity.name][entity.edit]['form']['fields']) === "undefined" || typeof (dicionarios[entity.name][entity.edit]['form']['defaults']) === "undefined") {
                dicionarios[entity.name][entity.edit]['form']['fields'] = [];
                dicionarios[entity.name][entity.edit]['form']['defaults'] = {};
            }

            $.each($(".relation_fields_show"), function () {
                let id = $(this).attr("rel");
                if (id !== "__$0__") {
                    id = parseInt(id);
                    if ($(this).is(":checked")) {
                        if ($.inArray(id, dicionarios[entity.name][entity.edit].form.fields) === -1)
                            dicionarios[entity.name][entity.edit].form.fields.push(id);
                    } else {
                        dicionarios[entity.name][entity.edit].form.fields = $.grep(dicionarios[entity.name][entity.edit].form.fields, function (value) {
                            return value != id;
                        });
                    }
                }
            });
            $.each($(".relation_fields_default"), function () {
                if ($(this).attr("rel") !== "__$0__") {
                    dicionarios[entity.name][entity.edit].form.defaults[parseInt($(this).attr("rel"))] = $(this).val();
                }
            });
        }
    }
}

function checkSaveSelect() {
    if ($("#requireListExtendDiv").html() !== "") {
        $("#requireListExtendDiv").find("input").each(function () {
            if ($(this).prop("checked"))
                dicionarios[entity.name][entity.edit]['select'].push($(this).attr("id"));
        });
    }
}

function checkSaveSource() {
    $(".file-format").each(function () {
        if ($(this).prop("checked")) {
            $("." + $(this).attr("id") + "-format").each(function () {
                if ($(this).prop("checked")) {
                    dicionarios[entity.name][entity.edit]['allow']['options'].push({'option': $(this).attr("id"), 'name': $(this).attr("id")});
                }
            });
        }
    });
}

function checkSaveAllow() {
    if ($("#spaceValueAllow").html() !== "") {
        $.each($("#spaceValueAllow").find(".allow"), function () {
            saveAllowValue($(this));
        });
    }
}

function saveAttrValue($input) {
    var name = $input.attr("id");
    if (name === "nome")
        dicionarios[entity.name][entity.edit]['column'] = slug($input.val(), "_");

    if (["default", "size"].indexOf(name) > -1 && !$("#" + name + "_custom").prop("checked"))
        dicionarios[entity.name][entity.edit][name] = false;
    else if ("form" === name || "datagrid" === name)
        dicionarios[entity.name][entity.edit][name] = $input.prop("checked") ? {} : false;
    else if (dicionarios[entity.name][entity.edit]['form'] !== false && ["class", "style", "orientation", "template", "atributos", "coll", "cols", "colm", "input", "type"].indexOf(name) > -1)
        dicionarios[entity.name][entity.edit]['form'][name] = $input.val();
    else if (dicionarios[entity.name][entity.edit]['datagrid'] !== false && ["grid_relevant", "grid_class", "grid_style", "grid_template", "grid_relevant_relational", "grid_class_relational", "grid_style_relational", "grid_template_relational"].indexOf(name) > -1)
        dicionarios[entity.name][entity.edit]['datagrid'][name] = $input.val();
    else if ("regexp" === name)
        dicionarios[entity.name][entity.edit]['allow']["regexp"] = $input.val();
    else
        dicionarios[entity.name][entity.edit][name] = ($input.attr("type") === "checkbox" ? $input.prop("checked") : $input.val());
}

function saveAllowValue($input) {
    if ($input.find(".values").val() !== "")
        dicionarios[entity.name][entity.edit]['allow']['options'].push({'option': $input.find(".values").val(), 'name': $input.find(".names").val()});
}

function applyAttr(data) {
    if (typeof (data) !== "undefined" && data !== null) {
        $.each(data, function (name, value) {
            if (typeof(value) === "object")
                applyAttr(value);

            applyValueAttr(name, value);
        });

        checkFieldsOpenOrClose();
    }
}

function applyValueAttr(name, value) {
    var $input = $("#" + name);

    if (name === "options") {
        setAllow(value);
    } else if (name === "filter") {
        $.each(value, function (i, e) {
            addFilter(e);
        });
    } else if (name === "select") {
        checkEntityMultipleFields(value);
    } else {
        if ($input.attr("type") === "checkbox" && ((value !== false && !$input.prop("checked")) || (value === false && $input.prop("checked"))))
            $input.trigger("click");
        else
            checkValuesEspAttr(name, value);
    }
}

function checkValuesEspAttr(name, value) {
    if ((name === "default" || name === "size")) {
        if ((value !== false && !$("#" + name + "_custom").prop("checked")) || (value === false && $("#" + name + "_custom").prop("checked"))) {
            $("#" + name + "_custom").trigger("click");
        }
        $("#" + name).val(value !== false ? value : "");
    } else if (name === "format") {
        setFormat(value);
    } else {
        $("#" + name).val(value);
    }
}

function setAllow(value) {
    if (entity.edit !== null && dicionarios[entity.name][entity.edit]['format'] === "source") {

        //sources
        $.each(value, function (i, e) {
            $.each(source_types, function (n, dados) {
                if (dados.indexOf(e.option) > -1 && !$("#" + n).prop("checked")) {
                    $("#" + n).prop("checked", true);
                    $("#formato-" + n).removeClass("hide");
                }
            });
            $("#" + e.option).prop("checked", true);
        });

    } else {

        //others
        let copia = $("#spaceValueAllow").html() === "";
        $.each(value, function (i, e) {
            if (copia)
                copy('#tplValueAllow', '#spaceValueAllow', '', 'append');

            let $allow = (copia ? $("#spaceValueAllow").find(".allow:last-child") : $("#spaceValueAllow").find(".allow:eq(" + i + ")"));
            $allow.find(".values").val(e.option);
            $allow.find(".names").val(e.name);
        });
    }
}

function setFormat(val) {
    $(".selectInput").css("color", "#AAA").val("");
    getSelectInput(val).css("color", "#000").val(val);
    $("#spaceValueAllow").html("");

    $(".allformat").prop("checked", false);
    $(".formato-div").addClass("hide");
    $(".file-format").each(function () {
        $(this).prop("checked", false);
        $("." + $(this).attr("id") + "-format").prop("checked", false);
    });

    /* Determina campo de tamanho */
    if(['boolean', 'select', 'radio', 'color', 'file', 'information', 'status', 'email', 'cpf', 'cnpj', 'ie', 'rg', 'cep', 'date', 'datetime', 'time', 'passwordRequired',
        'extend', 'extend_add', 'list', 'selecao', 'checkbox_rel'].indexOf(val) > -1) {
        $("#size_field, #size_field_container").addClass("hide");
    } else {
        $("#size_field, #size_field_container").removeClass("hide");
    }

    /* Determina campo de UNIQUE */
    if(['boolean', 'information', 'status', 'passwordRequired',
        'extend', 'extend_add', 'extend_mult', "extend_folder", 'list', 'list_mult', 'selecao', 'selecao_mult', 'checkbox_rel', 'checkbox_mult'].indexOf(val) > -1) {
        $("#unique_field").addClass("hide");
    } else {
        $("#unique_field").removeClass("hide");
    }

    /* Determina campo de NULL */
    if(['information', 'passwordRequired'].indexOf(val) > -1) {
        $("#default_field").addClass("hide");
    } else {
        $("#default_field").removeClass("hide");
    }

    /* Determina campo de UPDATE */
    if(['information', 'extend', 'passwordRequired'].indexOf(val) > -1) {
        $("#update_field").addClass("hide");
    } else {
        $("#update_field").removeClass("hide");
    }

    /* Determina Expressão regular */
    if(['textarea', 'html', 'boolean', 'select', 'radio', 'checkbox', 'color', 'source', 'information', 'status', 'date', 'datetime', 'time', 'passwordRequired',
        'extend', 'extend_add', 'extend_mult', "extend_folder", 'list', 'list_mult', 'selecao', 'selecao_mult', 'checkbox_rel', 'checkbox_mult'].indexOf(val) > -1) {
        $("#regexp_field").addClass("hide");
    } else {
        $("#regexp_field").removeClass("hide");
    }

    /* Transforma campo padrão em Textarea no tipo informação */
    if(val === "information") {
        $("#default_container").css("width", "100%");
        $("#default").replaceWith($('<textarea id="default" class="input" rows="9"></textarea>'));
    } else {
        $("#default_container").css("width", "");
        $("#default").replaceWith($('<input type="text" id="default" class="input" />'));
    }

    /* Determina Orientação */
    if(['checkbox', 'radio', 'checkbox_rel', 'checkbox_mult'].indexOf(val) > -1) {
        $("#orientation_field").removeClass("hide");
    } else {
        $("#orientation_field").addClass("hide");
    }

    /* Determinar opções de entrada */
    $("#allowBtnAdd, #spaceValueAllow").removeClass('hide');
    if(['boolean', 'select', 'radio', 'checkbox', 'source'].indexOf(val) > -1) {
        $("#definirvalores").removeClass("hide");

        if (val === "source") {
            $("#format-source").removeClass("hide");
            $("#allowBtnAdd, #spaceValueAllow").addClass("hide");
            $("#image").prop("checked");
        } else if(val === 'boolean') {
            $("#allowBtnAdd").addClass('hide');
        }

    } else {
        $("#definirvalores").addClass("hide");
    }

    if(['extend_mult', 'extend', 'extend_folder'].indexOf(val) > -1) {
        $("#default_container").addClass("hide");
    } else {
        $("#default_container").removeClass("hide");
    }

    if (val !== "source") {
        $("#format-source, .relation_creation_container, #requireListFilter, .relation_container").addClass("hide");
        if (["extend", "extend_add", "extend_mult", "extend_folder", "list", "list_mult", "selecao", "selecao_mult", "checkbox_rel", "checkbox_mult"].indexOf(val) > -1)
            $(".relation_container, .relation_creation_container").removeClass("hide");
    }

    $(".requireName, #nomeAttr").removeClass("hide");
    $("#nome").focus();
}

function getSelectInput(val) {
    if (["text", "textarea", "html", "number", "float", "boolean", "select", "radio", "checkbox", "range", "color", "source", "information"].indexOf(val) > -1)
        return $("#funcaoPrimary");
    else if (["extend", "extend_add", "extend_mult", "extend_folder", "list", "list_mult", "selecao", "selecao_mult", "checkbox_rel", "checkbox_mult", "publisher", "owner"].indexOf(val) > -1)
        return $("#funcaoRelation");
    else
        return $("#funcaoIdentifier");
}

function checkRequiresFields() {
    var type = getType();
    return (type !== "" && $("#nome").val().length > 1 && $("#nome").val() !== "id" && (["extend", "extend_add", "extend_mult", "extend_folder", "list", "list_mult", "selecao", "selecao_mult", "checkbox_rel", "checkbox_mult"].indexOf(type) < 0 || $("#relation").val() !== null));
}

function checkFieldsOpenOrClose(nome) {
    if(typeof nome === "undefined") {
        $(".requireName").removeClass("hide");
    } else {
        if (allowName(nome, 2)) {
            if (checkRequiresFields())
                $(".requireName").removeClass("hide");
            else
                $(".requireName").addClass("hide");
        }
    }
}

function allowName(nome, tipo) {

    if (typeof nome !== "undefined") {
        //nomes especiais do banco
        if (["add", "all", "alter", "analyze", "and", "as", "asc", "asensitive", "before", "between", "bigint", "binary", "blob", "both", "by", "call", "cascade", "case", "change", "char", "character", "check", "collate", "column", "condition", "connection", "constraint", "continue", "convert", "create", "cross", "current_date", "current_time", "current_timestamp", "current_user", "cursor", "database", "databases", "day_hour", "day_microsecond", "day_minute", "day_second", "dec", "decimal", "declare", "default", "delayed", "delete", "desc", "describe", "deterministic", "distinct", "distinctrow", "div", "double", "drop", "dual", "each", "else", "elseif", "enclosed", "escaped", "exists", "exit", "explain", "false", "fetch", "float", "for", "force", "foreign", "from", "fulltext", "goto", "grant", "group", "having", "high_priority", "hour_microsecond", "hour_minute", "hour_second", "if", "ignore", "in", "index", "infile", "inner", "inout", "insensitive", "insert", "number", "integer", "interval", "into", "is", "iterate", "join", "key", "keys", "kill", "leading", "leave", "left", "like", "limit", "lines", "load", "localtime", "localtimestamp", "lock", "long", "longblob", "longtext", "loop", "low_priority", "match", "mediumblob", "mediumint", "mediumtext", "middleint", "minute_microsecond", "minute_second", "mod", "modifies", "natural", "not", "no_write_to_binlog", "null", "numeric", "on", "optimize", "option", "optionally", "or", "order", "out", "outer", "outfile", "precision", "primary", "procedure", "purge", "read", "reads", "real", "references", "regexp", "rename", "repeat", "replace", "require", "restrict", "return", "revoke", "right", "rlike", "schema", "schemas", "second_microsecond", "select", "sensitive", "separator", "set", "show", "smallint", "soname", "spatia", "specific", "sql", "sqlexception", "sqlstate", "sqlwarning", "sql_big_result", "sql_calc_found_rows", "sql_small_result", "ssl", "starting", "straight_join", "table", "terminated", "then", "tinyblob", "tinyint", "tinytext", "to", "trailing", "trigger", "true", "undo", "union", "unique", "unlock", "unsigned", "update", "usage", "use", "using", "utc_date", "utc_time", "utc_timestamp", "values", "varbinary", "varchar", "varcharacter", "varying", "when", "where", "while", "with", "write", "xor", "year_month", "zerofill"].indexOf(nome) > 0) {
            toast("Este nome é reservado pelo sistema", 3000, "toast-error");
            $(".requireName").addClass("hide");
            return false;
        }

        //tamanho máximo de caracteres
        if (nome.length > 28) {
            if(!alert) {
                alert = true;
                toast("Nome " + (tipo === 1 ? "da Entidade" : "do Campo") + " deve ter no máximo 28 caracteres. [" + nome.length + "]", 3000, "toast-warning");
                setTimeout(function () {
                    alert = false;
                }, 3000);
            }
            $(".requireName").addClass("hide");
            return false;
        }

        //nome repetido
        if (tipo === 2 && nome.length > 2 && (entity.edit < 1 || (entity.edit > 0 && nome !== dicionarios[entity.name][entity.edit]['nome']))) {
            let tt = slug(nome, "_");
            $.each(dicionarios[entity.name], function (i, e) {
                if (tt === e.column) {
                    if(!alert) {
                        alert = true;
                        toast("Nome " + (tipo === 1 ? "da Entidade" : "do Campo") + " já esta em uso", 4500, "toast-warning");
                        setTimeout(function () {
                            alert = false;
                        }, 3000);
                    }
                    $(".requireName").addClass("hide");
                    return false;
                }
            })
        }
    }

    return true;
}

function checkUniqueNameColumn() {
    $.each(dicionarios[entity.name], function (j, k) {
        $.each(dicionarios[entity.name], function (i, e) {
            if (k.column === e.column) {
                if(!alert) {
                    alert = true;
                    toast("Nome do Campo" + k.column + " precisa ser único", 3000, "toast-warning");
                    setTimeout(function () {
                        alert = false;
                    }, 3000);
                }
                return false;
            }
        });
    });

    return true;
}

function deleteAttr(id) {
    if (confirm("Remover Atributo?")) {
        delete dicionarios[entity.name][id];
        resetAttr();
        showEntity();
    }
}

function removeEntity(entity) {
    if (confirm("Excluir esta entidade e todos os seus dados?")) {
        post("entity-ui", "delete/entity", {"name": entity}, function (g) {
            if (g) {
                updateDicionarioIndex(entity);
                toast("Entidade Excluída", 3000, "toast-warning");
                readDicionarios();
            }
        })
    }
}

function sendImport() {
    if ($("#import").val() !== "") {
        var form_data = new FormData();
        form_data.append('file', $('#import').prop('files')[0]);
        $.ajax({
            url: HOME + 'entidadesImport',
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (data) {
                if (data) {
                    if (data === "existe") {
                        toast("Entidade já Existe", 2500, "toast-warning");
                    } else {
                        toast("Rejeitado! Chave Estrangeira Ausente", 4000, "toast-warning");
                        post('entity-ui', 'delete/import', {entity: $('#import').val()}, function (g) {
                        });
                    }
                    $('#import').val("");
                } else {
                    location.reload();
                }
            }
        });
    }
}

function addFilter(value) {
    if(value !== "" && typeof value === "string" && value !== null) {
        var field = "";
        var operator = "";
        var valor = "";
        var column = "null";
        if (typeof (value) !== "undefined") {
            var e = value.split(",");
            field = e[0];
            operator = e[1];
            valor = e[2];
            column = e[3];
        }

        //Copia Cria o Filter
        copy("#tpl-list-filter", "#list-filter", {0: operator, 1: valor}, "append");
        var id = Math.floor(Math.random() * 1000000);
        var $filter = $(".filter").last().attr("id", id).html("");
        var relation = "null";

        //Adiciona as opções de entidade
        $.each(dicionarios[$("#relation").val()], function (i, e) {
            copy("#optionTpl", "#" + id, {
                0: e.column,
                1: e.nome,
                2: (field === e.column ? "\" selected=\"selected" : "")
            }, "append");

            if (field === e.column && ["list", "list_mult", "selecao", "selecao_mult", "checkbox_rel", "checkbox_mult", "extend", "extend_add", "extend_folder", "extend_mult"].indexOf(e.key) > -1)
                relation = e.relation
        });

        //Adiciona as opções de coluna da entidade
        if (column !== "null" && relation !== "null")
            addColumnFilter($filter, relation, column);
    }
}

function checkFilterToApply() {
    if (["list", "list_mult", "selecao", "selecao_mult", "checkbox_rel", "checkbox_mult"].indexOf($("#funcaoRelation").val()) > -1) {
        $("#requireListFilter").removeClass("hide");
        $("#list-filter").html("");
        addFilter();
    } else {
        $("#requireListFilter").addClass("hide");
    }
}

function checkAttrRelationToShow() {
    let dicRelation = dicionarios[$("#relation").val()];
    $("#relation_fields_show, #relation_fields_default").html("");

    //check if fields exist
    if (entity.edit !== null) {
        let dic = dicionarios[entity.name][entity.edit];

        if (dic.form !== !1 && (typeof(dic.form.fields) === "undefined" || typeof(dic.form.defaults) === "undefined")) {
            dic.form.fields = [];
            dic.form.defaults = {};
            $.each(dicRelation, function (i, e) {
                dic.form.fields.push(parseInt(i));
                dic.form.defaults[parseInt(i)] = ""
            });
            $.each(dicRelation, function (i, e) {
                i = parseInt(i);
                let checked = $.inArray(i, dic.form.fields) > -1 ? 'checked="checked"' : '';
                let value = dic.form !== !1 && typeof(dic.form.defaults[i]) !== "undefined" ? dic.form.defaults[i] : "";
                copy("#tpl_relation_fields_show", "#relation_fields_show", {0: i, 1: e.nome, 2: checked}, "append");
                copy("#tpl_relation_fields_default", "#relation_fields_default", {0: i, 1: e.nome, 2: value}, "append")
            })
        } else {
            $.each(dicRelation, function (i, e) {
                i = parseInt(i);
                copy("#tpl_relation_fields_show", "#relation_fields_show", {
                    0: i,
                    1: e.nome,
                    2: $.inArray(i, dic.form.fields) > -1 ? 'checked="checked"' : ''
                }, "append");
                copy("#tpl_relation_fields_default", "#relation_fields_default", {
                    0: i,
                    1: e.nome,
                    2: (dic.form !== !1 && typeof(dic.form.defaults[i]) !== "undefined" ? dic.form.defaults[i] : "")
                }, "append")
            })
        }
    } else {

        $.each(dicRelation, function (i, e) {
            i = parseInt(i);
            copy("#tpl_relation_fields_show", "#relation_fields_show", {
                0: i,
                1: e.nome,
                2: 'checked="checked"'
            }, "append");
            copy("#tpl_relation_fields_default", "#relation_fields_default", {0: i, 1: e.nome, 2: ""}, "append");
        });
    }
}

function checkEntityMultipleFields(values) {
    $("#requireListExtend").addClass("hide");
    $("#requireListExtendDiv").html("");
    $.each(dicionarios[$("#relation").val()], function (i, e) {
        if (e.key === "selecao_mult" || e.key === "list_mult" || e.key === "extend_mult" || e.key === "extend_folder" || e.key === "checkbox_mult") {
            var checked = typeof (values) !== "undefined" && $.inArray(e.column, values) > -1 ? '" checked="checked' : '';
            copy("#selectOneListOption", "#requireListExtendDiv", {0: e.column, 1: e.nome, 2: checked}, "append");
            $("#requireListExtend").removeClass("hide");
        }
    });
    checkAttrRelationToShow();
}

function addColumnFilter($this, entity, select) {
    $this.removeClass("m6").addClass("m3");
    var $column = $('<select class="filter_column col s12 m3"></select>').insertAfter($this);
    $.each(dicionarios[entity], function (id, data) {
        if (["extend", "extend_add", "extend_mult", "extend_folder", "list", "list_mult", "selecao", "selecao_mult", "checkbox_rel", "checkbox_mult"].indexOf(data.key) < 0)
            $column.append("<option value='" + data.column + "' " + (select === data.column ? "selected='selected'" : "") + ">" + data.nome + "</option>");
    });
}

function getDefaultsInfo() {
    var type = getType();

    if (entity.edit !== null)
        return assignObject(defaults.default, dicionarios[entity.name][entity.edit]);
    else if (type !== "")
        return assignObject(defaults.default, defaults[getType()]);
    else
        return assignObject(defaults.default, {});
}

function assignObject(ob1, ob2) {
    var t = typeof(ob1) === "object" ? JSON.parse(JSON.stringify(ob1)) : {};
    $.each(ob2, function (name, value) {
        if (typeof(value) === "object")
            t[name] = assignObject(t[name], value);
        else
            t[name] = value;
    });
    return t;
}

function getType() {
    var result = "";
    $(".selectInput").each(function () {
        if ($(this).val() !== null)
            result = $(this).val();
    });
    return result;
}

function showhideFormSup() {
    $("#form-sup").toggleClass("hide");
}
function showhideListSup() {
    $("#list-sup").toggleClass("hide");
}

$(function () {
    let headerHeight = $("#core-header").height() + parseInt($("#core-header").css("padding-top")) + parseInt($("#core-header").css("padding-bottom"));
    $("#entity-space").css("height", $(document).height() - headerHeight - 64);
    $("#space-attr-entity").css("height", $(document).height() - headerHeight - 16 - 76.28);
    $("#main").css("height", $(document).height() - headerHeight);

    readDefaults();
    readDicionarios();
    entityReset();

    $("#core-content").off("keyup change focus", "#entityName").on("keyup change focus", "#entityName", function () {
        if ($(this).val().length > 2)
            $(".requireNameEntity").removeClass("hide");
        else
            $(".requireNameEntity").addClass("hide");

    }).off("change", "#relation").on("change", "#relation", function () {
        checkFieldsOpenOrClose();
        checkEntityMultipleFields();
        checkFilterToApply();
        $("#nome").trigger("change").focus();

        let val = $("#funcaoRelation").val();
        if (["selecao", "selecao_mult", "checkbox_mult"].indexOf(val) === -1)
            $(".relation_creation_container").removeClass("hide");

    }).off("change", ".selectInput").on("change", ".selectInput", function () {
        setFormat($(this).val());
        applyAttr(assignObject(defaults.default, defaults[getType()]));
        checkFieldsOpenOrClose();
        $("#nome").trigger("change");

    }).off("change", "#haveAutor, #haveOwner").on("change", "#haveAutor, #haveOwner", function (e) {
        let alt = $(this).attr("id") === "haveAutor" ? "#haveOwner" : "#haveAutor";

        if(!$(this).prop("checked") || $(alt).prop("checked")) {
            if(confirm("Os dados com Referência a esta entidade serão perdidos.\n\nDeseja Formatar?"))
                $(alt).prop("checked", false);
            else
                $(this).prop("checked", !$(alt).prop("checked"));
        }

    }).off("keyup change", "#nome").on("keyup change", "#nome", function () {
        checkFieldsOpenOrClose($(this).val());

    }).off("change", "#default_custom").on("change", "#default_custom", function () {
        if ($(this).is(":checked") && ['extend_mult', 'extend', 'extend_folder'].indexOf(getType()) === -1) {
            $("#default_container").removeClass("hide");
            if ($("#unique").is(":checked"))
                $("#unique").trigger("click");
        } else {
            $("#default_container").addClass("hide");
        }

    }).off("change", "#size_custom").on("change", "#size_custom", function () {
        if ($(this).is(":checked")) {
            $("#size_container").removeClass("hide");
        } else {
            $("#size_container").addClass("hide");
        }

    }).off("change", "#unique").on("change", "#unique", function () {
        if ($(this).is(":checked") && $("#default_custom").is(":checked")) $("#default_custom").trigger("click");

    }).off("change", "#form").on("change", "#form", function () {
        if ($(this).is(":checked")) {
            $(".form_body").removeClass("hide");
            if (entity.name !== "" && entity.edit !== "" && typeof dicionarios[entity.name][entity.edit] !== "undefined" && typeof dicionarios[entity.name][entity.edit].form !== "undefined") {
                dicionarios[entity.name][entity.edit].form = Object.assign({}, defaults.default.form, defaults[getType()].form);
                $("#cols").val(12);
            }
        } else {
            $(".form_body").addClass("hide")
        }

    }).off("change", "#datagrid").on("change", "#datagrid", function () {
        if ($(this).is(":checked")) {
            $(".datagrid_body").removeClass("hide");
            if (entity.name !== "" && entity.edit !== "" && typeof dicionarios[entity.name][entity.edit] !== "undefined" && typeof dicionarios[entity.name][entity.edit].datagrid !== "undefined")
                dicionarios[entity.name][entity.edit].datagrid = Object.assign({}, defaults.default.form, defaults[getType()].form);
        } else {
            $(".datagrid_body").addClass("hide");
        }
    }).off("change", ".file-format").on("change", ".file-format", function () {
        if ($(this).is(":checked"))
            $("#formato-" + $(this).attr("id")).removeClass("hide");
        else
            $("#formato-" + $(this).attr("id")).addClass("hide");

    }).off("click", ".file-format").on("click", ".file-format", function () {
        var $this = $(this);
        setTimeout(function () {
            if ($this.prop("checked") && !$("#all-" + $this.attr("id")).prop("checked"))
                $("#all-" + $this.attr("id")).trigger("click");
        }, 50);

    }).off("change", ".allformat").on("change", ".allformat", function () {
        $("." + $(this).attr("rel") + "-format").prop("checked", $(this).is(":checked"));

    }).off("change", ".oneformat").on("change", ".oneformat", function () {
        if (!$(this).is(":checked")) {
            $("#all-" + $(this).attr("rel")).prop("checked", false);
        } else {
            var all = true;
            $.each($("." + $(this).attr("rel") + "-format"), function () {
                if (all && !$(this).is(":checked"))
                    all = false;
            });
            $("#all-" + $(this).attr("rel")).prop("checked", all);
        }

    }).off("change", "#colm").on("change", "#colm", function () {
        var $coll = $("#coll");
        var $cols = $("#cols");
        var value = parseInt($(this).val());
        if (parseInt($coll.val()) > value) {
            $coll.find("option").removeAttr("selected");
            $coll.find("option[value=" + $(this).val() + "]").attr("selected", "selected");
        }
        if (parseInt($cols.val()) < value) {
            $cols.find("option").removeAttr("selected");
            $cols.find("option[value=" + $(this).val() + "]").attr("selected", "selected");
        }

    }).off("change", ".filter").on("change", ".filter", function () {
        var $this = $(this);
        var column = $this.val();
        var entity = $("#relation").val();

        $this.removeClass("m3").addClass("m6").siblings(".filter_column").remove();
        $.each(dicionarios[entity], function (i, e) {
            if (e.column === column) {
                if (["extend", "extend_add", "extend_mult", "extend_folder", "list", "list_mult", "selecao", "selecao_mult", "checkbox_rel", "checkbox_mult"].indexOf(e.key) > -1)
                    addColumnFilter($this, e.relation, "");
                return false;
            }
        });
    }).off("change keyup", "#entityIcon").on("change keyup", "#entityIcon", function () {
        $("#entityIconDemo").text($(this).val());
    });
});