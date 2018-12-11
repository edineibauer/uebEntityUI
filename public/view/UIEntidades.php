<?php
if (empty($_SESSION['userlogin']) || $_SESSION['userlogin']['setor'] !== "1" || $_SESSION['userlogin']['nivel'] !== "1") {
    $data['response'] = 3;
    $data['data'] = HOME . "login";
} else {
    ob_start();
    ?>
    <ul id="nav-entity" class="theme-text-aux z-depth-4 space-header theme-l1">
        <div class="row">
            <div class="padding-12 col">
                <div class="left upper padding-medium">
                    Entidades
                </div>
                <div class="right align-right">
                    <button class="btn theme btn-floating right" id="newEntityBtn" onclick="entityEdit()">
                        <i class="material-icons">add</i>
                    </button>
                    <button class="btn theme btn-floating right" onclick="uploadEntity()">
                        <i class="material-icons">backup</i>
                    </button>
                </div>
            </div>
        </div>
        <ul class="row">
            <li class="col s12" id="entity-space">
            </li>
            <div class="col s12 hide" id="tpl-entity">
                <div class="col s7 padding-small">__$__</div>
                <div class="padding-small col s5 align-right">
                    <button class="theme-text-aux opacity radius pointer right padding-tiny btn-flat"
                            style="height: 26px;" onclick="removeEntity('__$__')">
                        <i class="material-icons white-text font-medium" style="margin-top: 4px;">delete</i>
                    </button>
                    <button class="theme-text-aux radius pointer right padding-tiny btn-flat"
                            style="margin-right: 5px;height: 26px;"
                            onclick="entityEdit('__$__')">
                        <i class="material-icons white-text font-medium" style="margin-top: 4px;">edit</i>
                    </button>
                </div>
            </div>
        </ul>
    </ul>

    <div class="col s12 m4 z-depth-2 space-header theme-l4 color-text-grey-dark-medium" id="nav-menu">
        <header class="row">
            <div class="panel">
                <div class="col s12 padding-tiny">
                    <button class="btn theme left" id="saveEntityBtn" onclick="saveEntity()">
                        salvar
                        <i class="material-icons right padding-left">check</i>
                    </button>
                    <button class="theme right btn-floating" title="Novo Atributo" id="saveAttrBtn"
                            onclick="editAttr()">
                        <i class="material-icons right">add</i>
                    </button>
                </div>
            </div>
        </header>
        <div class="row"></div>
        <div class="panel" id="space-attr-entity">
            <div class="row">
                <label class="col s12">
                    <span>Nome da Entidade</span>
                    <input id="entityName" type="text" placeholder="entidade..." class="font-large" style="margin:0">
                </label>

                <div class="row font-small">
                    <div class="col left padding-tiny" style="width: 33px; height: 33px">
                        <a href="https://material.io/tools/icons/?style=baseline" target="_blank"
                           class="right btn-flat font-small theme-text margin-0" style="width: 33px; height: 33px">
                            <i class="material-icons padding-tiny padding-4 theme-text-d" id="entityIconDemo"></i>
                        </a>
                    </div>
                    <div class="rest">
                        <input id="entityIcon" placeholder="ícone" type="text" class="rest">
                    </div>
                </div>
            </div>

            <div class="col hide overflow-hidden relative" id="importForm">
                <hr>
                <br>
                <span class="row">
                    <label for="import">Importar Entidade</label>
                    <input type="file" name="import" id="import"/>
                </span>
                <button class="col s12 btn-large theme-d1" onclick="sendImport()">
                    <i class="material-icons padding-right left">send</i><span class="left">Enviar</span>
                </button>
            </div>

            <ul class="row" id="entityAttr"></ul>

            <li class="col s12 list-att-__$0__ hide" id="tpl-attrEntity"
                style="border-bottom: solid 1px #EEE;padding-left:0">
                <div class="left" style="width: 27px">
                    <button class="btn-flat z-depth-0 pointer left theme theme-text-aux radius padding-0"
                            style="margin-bottom: 1px"
                            onclick="indiceChange(__$0__,-1)">
                        <i class="material-icons right font-large">keyboard_arrow_up</i>
                    </button>
                    <button class="btn-flat z-depth-0 pointer left theme theme-text-aux radius padding-0"
                            onclick="indiceChange(__$0__,1)">
                        <i class="material-icons right font-large">keyboard_arrow_down</i>
                    </button>
                </div>
                <span class="left overflow-hidden padding-4" style="width: 150px">__$1__</span>
                <button class="btn-flat pointer right opacity theme-text-aux radius" style="padding: 9px 5px"
                        onclick="deleteAttr(__$0__)">
                    <i class="material-icons right font-large">delete</i>
                </button>
                <button class="btn-flat pointer right theme-text-aux radius" style="margin-right: 5px;padding: 9px"
                        onclick="editAttr(__$0__)">
                    <i class="material-icons right font-large">edit</i>
                </button>
            </li>
        </div>
    </div>

    <div id="main" class="row color-gray-light space-header">
        <div class="col s12 hide" id="requireNameEntity">
            <div class="card padding-medium">
                <div class="row">
                    <div class="col s12 m4 padding-small pad">
                        <label class="row" for="funcaoPrimary">Genérico</label>
                        <select class="selectInput" id="funcaoPrimary">
                            <option value="" disabled selected>Input Genérica</option>
                            <option value="text">Texto</option>
                            <option value="textarea">Área de Texto</option>
                            <option value="html">Área de HTML</option>
                            <option value="int">Inteiro</option>
                            <option value="float">Float</option>
                            <option value="boolean">Boleano</option>
                            <option value="select">Select</option>
                            <option value="radio">Radio</option>
                            <option value="checkbox">CheckBox</option>
                            <!--                        <option value="range">Range</option>-->
                            <option value="color">Cor</option>
                            <option value="source">Arquivo</option>
                            <option value="sources">Arquivos Multiplos</option>
                            <option value="information">Informação</option>
                        </select>
                    </div>
                    <div class="col s12 m4 padding-small">
                        <label class="row" for="funcaoIdentifier">Semântico</label>
                        <select class="selectInput" id="funcaoIdentifier">
                            <option value="" disabled selected>Input de Identidade</option>
                            <option value="title">Título</option>
                            <option value="link">Link</option>
                            <option value="status">Status</option>
                            <option value="valor">R$ Valor</option>
                            <option value="percent">Porcentagem %</option>
                            <option value="url">Url</option>
                            <option value="email">Email</option>
                            <option value="password">Password</option>
                            <option value="tel">Telefone</option>
                            <option value="cpf">Cpf</option>
                            <option value="cnpj">Cnpj</option>
                            <option value="ie">Inscrição Estadual</option>
                            <option value="rg">RG</option>
                            <option value="card_number">Número de Cartão</option>
                            <option value="cep">Cep</option>
                            <option value="date">Data</option>
                            <option value="datetime">Data & Hora</option>
                            <option value="time">Hora</option>
                            <option value="week">Semana</option>
                            <option value="month">Mês</option>
                            <option value="year">Ano</option>
                        </select>
                    </div>
                    <div class="col s12 m4 padding-small">
                        <label class="row" for="funcaoRelation">Relacional</label>
                        <select class="selectInput" id="funcaoRelation">
                            <option value="" disabled selected>Input Relacional</option>
                            <option value="extend">Extensão</option>
                            <option value="extend_add">Extensão Add</option>
                            <option value="extend_mult">Extensão Multipla</option>
                            <option value="list">Lista</option>
                            <option value="list_mult">Lista Multipla</option>
                            <option value="selecao">Seleção</option>
                            <option value="selecao_mult">Seleção Multipla</option>
                            <option value="checkbox_rel">CheckBox</option>
                            <option value="checkbox_mult">CheckBox Multiplo</option>
                            <option value="publisher">Autor</option>
                            <option value="owner">Dono</option>
                            <option value="passwordRequired">Password Check</option>
                        </select>
                    </div>
                </div>

                <div class="col s12">
                    <div class="col s12 m8 l8 padding-small hide" id="nomeAttr">
                        <label for="nome">Nome do Atributo</label>
                        <input id="nome" autocomplete="off" type="text" class="input">
                    </div>

                    <div class="col s12 m4 l4 hide relation_container">
                        <label>Entidade Relacionada</label>
                        <select class="input" id="relation"></select>
                    </div>

                    <div class="row requireName hide">

                        <div class="col s6 m3 l1">
                            <label class="row" for="update">Update</label>
                            <label class="switch">
                                <input type="checkbox" class="input" id="update">
                                <div class="slider"></div>
                            </label>
                        </div>

                        <div class="col s6 m3 l1">
                            <label class="row" for="unique">Único</label>
                            <label class="switch">
                                <input type="checkbox" class="input" id="unique">
                                <div class="slider"></div>
                            </label>
                        </div>

                        <div class="col s6 m3 l1">
                            <label class="row" for="default_custom">Nulo</label>
                            <label class="switch">
                                <input type="checkbox" id="default_custom">
                                <div class="slider"></div>
                            </label>
                        </div>

                        <div class="col s6 m3 l1" style="margin-bottom: 10px;">
                            <label class="row" for="size_custom">Tamanho</label>
                            <label class="switch">
                                <input type="checkbox" id="size_custom">
                                <div class="slider"></div>
                            </label>
                        </div>

                        <div class="col s12 m6 l2 relative hide" style="padding: 25px 0 0px 5px!important;"
                             id="size_container">
                            <input id="size" type="number" step="1" max="1000000" value="127" min="1" class="input">
                        </div>

                        <div class="col s12 m8 l6 padding-tiny hide" id="default_container">
                            <label for="default">Valor Padrão</label>
                            <input id="default" type="text" class="input">
                        </div>
                    </div>
                </div>
            </div>

            <div id="tpl-list-filter" class="hide col s12 filterTpl">
                <select class="filter col s12 m6"></select>
                <select class="filter_operator col s12 m2">
                    <option value="__$0__" selected>__$0__</option>
                    <option value="=">=</option>
                    <option value="!=">!=</option>
                    <option value="<="><=</option>
                    <option value=">=">>=</option>
                    <option value=">">></option>
                    <option value="<"><</option>
                    <option value="%%">%%</option>
                    <option value="%=">%=</option>
                    <option value="=%">=%</option>
                    <option value="!%%">!%%</option>
                    <option value="!%=">!%=</option>
                    <option value="!=%">!=%</option>
                    <option value='in'>in "1,2"</option>
                    <option value='!in'>! in "1,2"</option>
                </select>
                <input type="text" class="filter_value col s12 m4" style="padding-top: 13px;" value="__$1__">
            </div>
            <option id="optionTpl" class="hide" value="__$0____$2__">__$1__</option>

            <div class="hide card padding-medium" id="requireListFilter">
                <header class="row padding-small">
                    <span class="left padding-medium" style="padding-left: 0!important;">Filtrar Lista</span>
                    <button class="btn-floating theme opacity hover-opacity-off" onclick="addFilter()"><i
                                class="material-icons">add</i></button>
                </header>

                <div id="list-filter"></div>
            </div>


            <div class="hide card padding-medium" id="requireListExtend">
                <header class="row padding-small">
                    <span class="left padding-medium">Selecionar Opções de Campos Multiplos</span>
                </header>

                <p class="color-text-gray">esta entidade possúi campos com multiplos valores, marque para selecionar um
                    em específico.</p>

                <div id="requireListExtendDiv"></div>
            </div>

            <label class="col s12 relative tpl hide" for="__$0__" id="selectOneListOption">
                <input type="checkbox" id="__$0__" class="left padding-right __$2__"/>
                <span class="left padding-medium font-medium pointer">__$1__ </span>
            </label>

            <div class="requireName hide card padding-medium">
                <header class="row padding-small">
                    <span class="left padding-medium">Formulário</span>
                    <label class="switch">
                        <input type="checkbox" class="input" id="form">
                        <div class="slider"></div>
                    </label>
                </header>
                <input type="hidden" id="input" class="input"/>

                <div class="row hide form_body">

                    <div class="col hide relation_creation_container padding-bottom">
                        <div class="col s12 m5 padding-small">
                            <h4>Mostrar Campo</h4>
                            <div class="col" id="relation_fields_show"></div>

                            <label class="col s12 relativep pointer border-bottom hide" id="tpl_relation_fields_show">
                                <input type="checkbox" class="relation_fields_show" rel="__$0__" __$2__/>
                                <span class="left padding-8 font-medium">__$1__</span>
                            </label>
                        </div>
                        <div class="col s12 m7 padding-small">
                            <h4>Definir Valor Padrão</h4>
                            <div class="col" id="relation_fields_default" style="padding-top:4px"></div>

                            <div class="col hide" id="tpl_relation_fields_default">
                                <input type="text" class="col font-medium relation_fields_default" value="__$2__"
                                       style="margin-bottom: 4px;" rel="__$0__">
                            </div>
                        </div>
                        <div class="col padding-12"></div>
                    </div>

                    <div class="col s4 padding-small form_body">
                        <label>Colunas Smartphone</label>
                        <select class="input form_body" id="cols">
                            <option value="12" selected>12/12</option>
                            <option value="11">11/12</option>
                            <option value="10">10/12</option>
                            <option value="9">9/12</option>
                            <option value="8">8/12</option>
                            <option value="7">7/12</option>
                            <option value="6">6/12</option>
                            <option value="5">5/12</option>
                            <option value="4">4/12</option>
                            <option value="3">3/12</option>
                            <option value="2">2/12</option>
                            <option value="1">1/12</option>
                        </select>
                    </div>

                    <div class="col s4 padding-small form_body">
                        <label>Colunas Tablet</label>
                        <select class="input form_body" id="colm">
                            <option value="" selected disabled></option>
                            <option value="12">12/12</option>
                            <option value="11">11/12</option>
                            <option value="10">10/12</option>
                            <option value="9">9/12</option>
                            <option value="8">8/12</option>
                            <option value="7">7/12</option>
                            <option value="6">6/12</option>
                            <option value="5">5/12</option>
                            <option value="4">4/12</option>
                            <option value="3">3/12</option>
                            <option value="2">2/12</option>
                            <option value="1">1/12</option>
                        </select>
                    </div>

                    <div class="col s4 padding-small form_body">
                        <label>Colunas Desktop</label>
                        <select class="input form_body" id="coll">
                            <option value="" selected disabled></option>
                            <option value="12">12/12</option>
                            <option value="11">11/12</option>
                            <option value="10">10/12</option>
                            <option value="9">9/12</option>
                            <option value="8">8/12</option>
                            <option value="7">7/12</option>
                            <option value="6">6/12</option>
                            <option value="5">5/12</option>
                            <option value="4">4/12</option>
                            <option value="3">3/12</option>
                            <option value="2">2/12</option>
                            <option value="1">1/12</option>
                        </select>
                    </div>
                    <div class="clearfix"></div>

                    <div class="col s12 m6 padding-small form_body">
                        <label for="class">Class</label>
                        <input id="class" type="text" class="input form_body">
                    </div>
                    <div class="col s12 m6 padding-small form_body">
                        <label for="style">Style</label>
                        <input id="style" type="text" class="input form_body">
                    </div>
                    <div class="col s12 m6 padding-small form_body">
                        <label for="template">Template</label>
                        <input id="template" type="text" class="input form_body">
                    </div>
                    <div class="col s12 m6 padding-small form_body">
                        <label for="atributos">Atributos</label>
                        <input id="atributos" type="text" class="input form_body">
                    </div>
                    <div class="clearfix"><br></div>
                </div>
            </div>

            <div class="requireName hide card padding-medium">
                <header class="row padding-small">
                    <span class="left padding-medium">Tabela</span>
                    <label class="switch">
                        <input type="checkbox" class="input" id="datagrid">
                        <div class="slider"></div>
                    </label>
                </header>

                <div class="row hide datagrid_body">
                    <div class="clearfix"></div>

                    <div class="col s12 m6 l2 padding-small">
                        <label for="grid_relevant">Posição</label>
                        <select class="input" id="grid_relevant" style="padding: 8px 0 5px;">
                            <option value="" selected>auto</option>
                            <option value="1">1°</option>
                            <option value="2">2°</option>
                            <option value="3">3°</option>
                            <option value="4">4°</option>
                            <option value="5">5°</option>
                            <option value="6">6°</option>
                        </select>
                    </div>
                    <div class="col s6 m6 l3 padding-small">
                        <label for="grid_class">Class</label>
                        <input id="grid_class" type="text" class="input">
                    </div>
                    <div class="col s6 m4 padding-small">
                        <label for="grid_style">Style</label>
                        <input id="grid_style" type="text" class="input">
                    </div>
                    <div class="col s6 m6 l3 padding-small">
                        <label for="grid_template">Template</label>
                        <input id="grid_template" type="text" class="input">
                    </div>

                    <div class="clearfix"></div>

                    <div class="row padding-top hide relation_container">

                        <div class="col padding-top margin-top margin-bottom theme-text upper">
                            <div class="col border-bottom margin-small" style="width: 400px">
                                Tabela Relacional
                            </div>
                        </div>
                        <div class="col s12 m4 l2 padding-small">
                            <label for="grid_relevant_relational">Posição</label>
                            <select class="input" id="grid_relevant_relational" style="padding: 8px 0 5px;">
                                <option value="" selected>não</option>
                                <option value="1">1°</option>
                                <option value="2">2°</option>
                                <option value="3">3°</option>
                                <option value="4">4°</option>
                                <option value="5">5°</option>
                                <option value="6">6°</option>
                            </select>
                        </div>
                        <div class="col s6 m6 l3 padding-small">
                            <label for="grid_class_relational">Class</label>
                            <input id="grid_class_relational" type="text" class="input">
                        </div>
                        <div class="col s6 m4 padding-small">
                            <label for="grid_style_relational">Style</label>
                            <input id="grid_style_relational" type="text" class="input">
                        </div>
                        <div class="col s6 m6 l3 padding-small">
                            <label for="grid_template_relational">Template</label>
                            <input id="grid_template_relational" type="text" class="input">
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>

            <div class="requireName hide card padding-medium">
                <header class="row padding-large">
                    <span class="left">Validação</span>
                    <i class="material-icons padding-left">check</i>
                </header>
                <div class="collapsible-body">
                    <div class="clearfix"></div>

                    <div class="col s12">
                        <label class="input-field col s12">
                            <span>Expressão Regular para Validação</span>
                            <input id="regex" type="text" class="input font-medium">
                        </label>
                    </div>

                    <div class="clearfix"></div>
                </div>
            </div>

            <div class="requireName hide card padding-medium">
                <header class="row padding-medium">
                <span class="left padding-medium">
                    <i class="material-icons left">assignment</i>
                    <span class="left padding-left">Valores Permitidos &nbsp;&nbsp;</span>
                </span>
                    <span class="btn-floating left theme" id="allowBtnAdd"
                          onclick="copy('#tplValueAllow', '#spaceValueAllow');$('#spaceValueAllow').find('.allow:first-child').find('.values').focus()">
                    <i class="material-icons">add</i>
                </span>
                </header>

                <div class="col s12 hide" id="format-source">
                    <div class="clearfix"></div>

                    <div class="col s12">
                        <label class="col s6 m2 relative">
                            <input type="checkbox" class="file-format" id="image"/>
                            <span class="left padding-8">Imagens</span>
                        </label>
                        <label class="col s6 m2 relative">
                            <input type="checkbox" class="file-format" id="video"/>
                            <span class="left padding-8">Vídeos</span>
                        </label>
                        <label class="col s6 m2 relative">
                            <input type="checkbox" class="file-format" id="audio"/>
                            <span class="left padding-8">Audios</span>
                        </label>
                        <label class="col s6 m2 relative">
                            <input type="checkbox" class="file-format" id="document"/>
                            <span class="left padding-8">Doc.</span>
                        </label>
                        <label class="col s6 m2 relative">
                            <input type="checkbox" class="file-format" id="compact"/>
                            <span class="left padding-8">Compact.</span>
                        </label>
                        <label class="col s6 m2 relative">
                            <input type="checkbox" class="file-format" id="denveloper"/>
                            <span class="left padding-8">Dev.</span>
                        </label>
                    </div>

                    <div class="panel">
                        <div class="col s12 formato-div hide" id="formato-image">
                            <div class="row padding-small"></div>
                            <div class="padding-medium row color-grey-light round">
                                <label class="col s6 m2 relative">
                                    <input type="checkbox" class="allformat" rel="image" id="all-image"/>
                                    <span>Todas</span>
                                </label>
                                <?php
                                $document = ["png", "jpg", "jpeg", "gif", "bmp", "tif", "tiff", "psd", "svg"];
                                foreach ($document as $id) {
                                    echo "<label class='col s6 m2 relative'><input type='checkbox' class='image-format oneformat' rel='image' id='{$id}'/><span class='upper left padding-8'>{$id}</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col s12 formato-div hide" id="formato-video">
                            <div class="row padding-small"></div>
                            <div class="padding-medium row color-grey-light round">
                                <label class="col s6 m2 relative">
                                    <input type="checkbox" class="allformat" rel="video" id="all-video"/>
                                    <span>Todos</span>
                                </label>
                                <?php
                                $document = ["mp4", "avi", "mkv", "mpeg", "flv", "wmv", "mov", "rmvb", "vob", "3gp", "mpg"];
                                foreach ($document as $id) {
                                    echo "<label class='col s6 m2 relative'><input type='checkbox' class='video-format oneformat' rel='video' id='{$id}'/><span class='upper left padding-8'>{$id}</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col s12 formato-div hide" id="formato-audio">
                            <div class="row padding-small"></div>
                            <div class="padding-medium row color-grey-light round">
                                <label class="col s6 m2 relative">
                                    <input type="checkbox" class="allformat" rel="audio" id="all-audio"/>
                                    <span>Todos</span>
                                </label>
                                <?php
                                $document = ["mp3", "aac", "ogg", "wma", "mid", "alac", "flac", "wav", "pcm", "aiff", "ac3"];
                                foreach ($document as $id) {
                                    echo "<label class='col s6 m2 relative'><input type='checkbox' class='audio-format oneformat' rel='audio' id='{$id}'/><span class='upper left padding-8'>{$id}</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col s12 formato-div hide" id="formato-document">
                            <div class="row padding-small"></div>
                            <div class="padding-medium row color-grey-light round">
                                <label class="col s6 m2 relative">
                                    <input type="checkbox" class="allformat" rel="document" id="all-document"/>
                                    <span>Todas</span>
                                </label>
                                <?php
                                $document = ["txt", "doc", "docx", "dot", "dotx", "dotm", "ppt", "pptx", "pps", "potm", "potx", "pdf", "xls", "xlsx", "xltx", "rtf"];
                                foreach ($document as $id) {
                                    echo "<label class='col s6 m2 relative'><input type='checkbox' class='document-format oneformat' rel='document' id='{$id}'/><span class='upper left padding-8'>{$id}</span></label>";
                                }

                                ?>
                            </div>
                        </div>
                        <div class="col s12 formato-div hide" id="formato-compact">
                            <div class="row padding-small"></div>
                            <div class="padding-medium row color-grey-light round">
                                <label class="col s6 m2 relative">
                                    <input type="checkbox" class="allformat" rel="compact" id="all-compact"/>
                                    <span>Todas</span>
                                </label>
                                <?php
                                $document = ["rar", "zip", "tar", "7z"];
                                foreach ($document as $id) {
                                    echo "<label class='col s6 m2 relative'><input type='checkbox' class='compact-format oneformat' rel='compact' id='{$id}'/><span class='upper left padding-8'>{$id}</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col s12 formato-div hide" id="formato-denveloper">
                            <div class="row padding-small"></div>
                            <div class="padding-medium row color-grey-light round">
                                <label class="col s6 m2 relative">
                                    <input type="checkbox" class="allformat" rel="denveloper" id="all-denveloper"/>
                                    <span>Todas</span>
                                </label>
                                <?php
                                $document = ["html", "css", "scss", "js", "tpl", "mst", "json", "xml", "md", "sql", "dll", "eot", "woff", "woff2", "ttf"];
                                foreach ($document as $id) {
                                    echo "<label class='col s6 m2 relative'><input type='checkbox' class='denveloper-format oneformat' rel='denveloper' id='{$id}'/><span class='upper left padding-8'>{$id}</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col s12" id="spaceValueAllow"></div>

                <div class="col s12 font-medium hide allow" id="tplValueAllow">
                    <label class="input-field col s12 m4 padding-small">
                        <span>Valor</span>
                        <input class="values" type="text"
                               onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 49 && event.charCode <= 57">
                    </label>

                    <label class="input-field col s12 m8 padding-small">
                        <span>Nome</span>
                        <input class="names" type="text">
                    </label>
                </div>

                <div class="clearfix col"></div>
            </div>

            <div class="clearfix"><br></div>

            <li style="display: none">
                <div class="collapsible-header"><i class="material-icons">whatshot</i>Metadados
                </div>
                <div class="collapsible-body">
                    <div class="clearfix"></div>

                    <div class="col s12 m6">
                        <div class="input-field col s12">
                            <input id="pref" placeholder="separe com vírgula" type="text"
                                   class="validate" ng-model="attr.prefixo">
                            <label for="pref">Prefixo</label>
                        </div>
                    </div>
                    <div class="col s12 m6">
                        <div class="input-field col s12">
                            <input id="sulf" placeholder="separe com vírgula" type="text"
                                   class="validate" ng-model="attr.sulfixo">
                            <label for="sulf">Sulfixo</label>
                        </div>
                    </div>

                    <div class="clearfix col"></div>
                </div>
            </li>
        </div>
        <div class="clearfix col"><br><br><br></div>
    </div>
    <?php

    $data['data'] = ob_get_contents();
    ob_end_clean();
}