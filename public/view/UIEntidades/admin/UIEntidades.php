<?php
DEV || die;
?>

<ul id="nav-entity" class="z-depth-4 hide space-header mode-background-colorBackground mode-text-colorText">
    <ul class="row" style="padding-top:10px">
        <li class="col s12" id="entity-space"></li>
    </ul>
</ul>

<div class="col s12 m4 space-header hide mode-background-colorBox mode-text-colorText" id="nav-menu">
    <header class="row">
        <div class="panel">
            <div class="col s12 padding-tiny">
                <button class="save-btn right" id="saveEntityBtn" onclick="saveEntity()">
                    <i class="material-icons">save</i>
                    Salvar Entidade
                </button>
                <button class="mobile-back-btn" onclick="mobileBackToList()" title="Voltar">
                    <i class="material-icons">arrow_back</i>
                </button>
                <button class="deleteEntityBtn hide" title="Excluir entidade"
                        onclick="removeEntity(entity.name)">
                    <i class="material-icons">delete</i>
                </button>
            </div>
        </div>
    </header>
    <div class="row"></div>
    <div class="panel" id="space-attr-entity" style="margin-top: 0!important;">
        <div class="row" id="entity-name">
            <input type="hidden" id="user" value="0"/>
            <input type="hidden" id="entityIcon" value="">
            <label class="col s12" style="display:flex;align-items:center;gap:8px">
                <button type="button" class="icon-picker-trigger" onclick="toggleIconPicker()" title="Escolher ícone" style="flex-shrink:0">
                    <span id="entityIconDemo" class="material-icons"></span>
                </button>
                <div style="flex:1">
                    <input id="entityName" type="text" placeholder="nome da entidade..." class="font-large col"
                           style="margin:0">
                </div>
                <span id="entityTypeLabel" class="entity-type-badge" style="flex-shrink:0"></span>
            </label>
            <div class="system-card" id="col-system">
                <div class="system-card-header">
                    <i class="material-icons system-card-icon">account_tree</i>
                    <span>Vínculo com Sistema</span>
                </div>
                <div class="system-card-body">
                    <div class="system-card-desc">Define a qual sistema os registros pertencem.</div>
                    <select id="system" class="system-select">
                        <option value=""><?=SITENAME?> (raiz)</option>
                    </select>
                </div>
                <div id="systemRequiredRow" class="system-required-row hide">
                    <label class="card-check-label">
                        <input type="checkbox" id="systemRequired" class="card-check-input"/>
                        <span class="card-check-box"></span>
                        <span>Exigir que registros sejam associados a este sistema</span>
                    </label>
                </div>
                <div id="systemRequiredBlock" class="system-required-block hide">
                    <i class="material-icons">info_outline</i>
                    <span id="systemRequiredBlockMsg"></span>
                </div>
            </div>

            <div class="system-card hide requireNameEntity" id="col-author">
                <div class="system-card-header">
                    <i class="material-icons system-card-icon">person</i>
                    <span>Vínculo com Usuário</span>
                </div>
                <div class="system-card-body">
                    <label class="card-check-label">
                        <input type="checkbox" id="haveAutor" class="card-check-input"/>
                        <span class="card-check-box"></span>
                        <span>Registrar o criador de cada registro</span>
                    </label>
                </div>
                <div id="authorTypeRow" class="author-type-row hide">
                    <div class="author-type-question">Visibilidade</div>
                    <div class="author-type-options">
                        <input type="radio" name="authorType" value="1" id="authorTypeInfo" class="author-radio-hidden"/>
                        <input type="radio" name="authorType" value="2" id="authorTypeOwner" class="author-radio-hidden"/>
                        <div class="author-type-btn active" data-value="1">
                            <div class="author-type-title">Público</div>
                            <div class="author-type-desc">Todos visualizam</div>
                        </div>
                        <div class="author-type-btn" data-value="2">
                            <div class="author-type-title">Privado</div>
                            <div class="author-type-desc">Apenas o criador</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row"></div>
        </div>

        <div class="hide" id="fieldStartChoice" style="padding:10px 4px">
            <div style="display:flex;gap:8px">
                <button class="field-choice-btn" onclick="chooseFromTemplate()">
                    <i class="material-icons">content_copy</i>
                    <span>Copiar entidade</span>
                </button>
                <button class="field-choice-btn" onclick="chooseFromScratch()">
                    <i class="material-icons">add</i>
                    <span>Começar do zero</span>
                </button>
            </div>
        </div>

        <div class="hide" id="templateEntitySelector" style="padding:8px 4px">
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px">
                <button class="field-choice-back" onclick="cancelTemplateSelection()">
                    <i class="material-icons">arrow_back</i>
                </button>
                <span style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.3px;opacity:0.5">Copiar campos de</span>
            </div>
            <select id="templateEntity" class="col s12 margin-0 mode-text-colorText" onchange="applyEntityTemplate()">
                <option value="">Selecione a entidade...</option>
            </select>
        </div>

        <div class="fields-header hide" id="fieldsHeader">
            <span>Campos</span>
            <button class="field-add-btn" onclick="addNewField()" title="Adicionar campo">
                <i class="material-icons">add</i>
                <span>Adicionar</span>
            </button>
        </div>
        <ul class="row" id="entityAttr"></ul>
    </div>
</div>

<div id="main" class="row mode-background-colorBox mode-text-colorText space-header">
    <div class="col s12 py-4" id="text-await">
        <h3 class="center color-text-gray text-await active">Carregando entidades...</h3>
    </div>
    <div class="col s12 hide requireNameEntity">
        <div class="col no-select margin-bottom padding-bottom" style="display:flex;align-items:center;gap:8px">
            <button class="mobile-back-btn" onclick="mobileBackToEntity()" title="Voltar">
                <i class="material-icons">arrow_back</i>
            </button>
            <button class="save-btn" title="Salvar Campo" onclick="editAttr()">
                <i class="material-icons">save</i>
                Salvar Campo
            </button>
            <button class="deleteFieldBtn hide" title="Excluir campo"
                    onclick="deleteCurrentField()" style="margin-left:auto">
                <i class="material-icons">delete</i>
            </button>
        </div>
        <div class="card padding-medium">
            <div class="row">
                <div class="col s12 l4 padding-small pad">
                    <label class="row" for="funcaoPrimary">Básico</label>
                    <select class="selectInput" id="funcaoPrimary">
                        <option value="" disabled selected>Inputs Básicas</option>
                    </select>
                </div>
                <div class="col s12 l4 padding-small">
                    <label class="row" for="funcaoRelation">Relação</label>
                    <select class="selectInput" id="funcaoRelation">
                        <option value="" disabled selected>Inputs Relacionados</option>
                    </select>
                </div>
                <div class="col s12 l4 padding-small">
                    <label class="row" for="funcaoIdentifier">Template</label>
                    <select class="selectInput" id="funcaoIdentifier">
                        <option value="" disabled selected>Inputs Pré-formatadas</option>
                    </select>
                </div>
            </div>

            <div class="col s12">
                <div class="col s12 m8 l8 padding-small hide" id="nomeAttr">
                    <label for="nome" class="color-text-gray">Nome do Campo</label>
                    <input id="nome" autocomplete="off" type="text" class="input">
                </div>

                <div class="col s12 m4 l4 hide relation_container" style="margin-top: 3px;">
                    <label class="color-text-gray">Entidade Relacionada</label>
                    <select class="input" id="relation"></select>
                </div>

                <div class="row requireName hide">
                    <div class="col s6 m3 l1" id="update_field">
                        <label class="color-text-gray row" for="update">Atualizar</label>
                        <label class="switch">
                            <input type="checkbox" class="input" id="update">
                            <div class="slider"></div>
                        </label>
                    </div>

                    <div class="col s6 m3 l1" id="unique_field">
                        <label class="color-text-gray row" for="unique">Único</label>
                        <label class="switch">
                            <input type="checkbox" class="input" id="unique">
                            <div class="slider"></div>
                        </label>
                    </div>

                    <div class="col s6 m3 l1" id="default_field">
                        <label class="color-text-gray row" for="default_custom">Nulo</label>
                        <label class="switch">
                            <input type="checkbox" id="default_custom">
                            <div class="slider"></div>
                        </label>
                    </div>

                    <div class="col s6 m3 l1" id="size_field" style="margin-bottom: 10px;">
                        <label class="color-text-gray row" for="size_custom">Tamanho</label>
                        <label class="switch">
                            <input type="checkbox" id="size_custom">
                            <div class="slider"></div>
                        </label>
                    </div>

                    <div class="col s12 m6 l3 relative hide" id="size_field_container">
                        <div class="col s12 relative hide" style="padding: 2px 10px 0px!important;"
                             id="size_container">
                            <div class="col s6" style="padding-right: 5px;">
                                <label for="minimo" class="color-text-gray">Min</label>
                                <input id="minimo" type="number" step="1" max="1000000" value="127" min="1"
                                       class="input">
                            </div>
                            <div class="col s6">
                                <label for="size" class="color-text-gray">Max</label>
                                <input id="size" type="number" step="1" max="1000000" value="127" min="1"
                                       class="input">
                            </div>
                        </div>
                    </div>

                    <div class="col s12 m8 l6 padding-tiny hide" id="default_container">
                        <label for="default" class="color-text-gray">Valor Inicial (Padrão)</label>
                        <input type="text" id="default" class="input">
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="hide card padding-medium" id="requireListFilter">
             <header class="row padding-small">
                 <span class="left padding-medium" style="padding-left: 0!important;">Filtrar Lista</span>
                 <button class="btn-floating theme opacity hover-opacity-off" onclick="addFilter()"><i
                             class="material-icons">add</i></button>
             </header>

             <div id="list-filter"></div>
         </div>
-->

        <div class="hide card padding-medium" id="requireListExtend">
            <header class="row padding-small">
                <span class="left padding-medium">Selecionar Opções de Campos Multiplos</span>
            </header>

            <p class="color-text-gray">esta entidade possúi campos com multiplos valores, marque para selecionar um
                em específico.</p>

            <div id="requireListExtendDiv"></div>
        </div>

        <div class="hide card padding-medium">
            <header class="row padding-8">
                <span class="left padding-12 padding-right upper font-bold">Configurar Campos do Formulário Relacional</span>
            </header>

            <div class="col padding-bottom">
                <div class="col s12 m5 padding-small">
                    <h4>Mostrar Campos</h4>
                    <div class="col" id="relation_fields_show"></div>
                </div>
                <div class="col s12 m7 padding-small">
                    <h4>Valor de Entrada Padrão</h4>
                    <div class="col" id="relation_fields_default" style="padding-top:4px"></div>
                </div>
                <div class="col padding-12"></div>
            </div>
        </div>

        <div class="hide card padding-medium">
            <header class="row padding-8">
                <span class="left padding-12 padding-right upper font-bold">Listagem de Dados da Entidade Relacional</span>
            </header>
            <div class="row padding-top">
                <div class="col s12 m4 l2 padding-small">
                    <label for="grid_relevant_relational" class="color-text-gray">Posição</label>
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
                    <label for="grid_class_relational" class="color-text-gray">Class</label>
                    <input id="grid_class_relational" type="text" class="input">
                </div>
                <div class="col s6 m4 padding-small">
                    <label for="grid_style_relational" class="color-text-gray">Style</label>
                    <input id="grid_style_relational" type="text" class="input">
                </div>
                <div class="col s6 m6 l3 padding-small">
                    <label for="grid_template_relational" class="color-text-gray">Template</label>
                    <input id="grid_template_relational" type="text" class="input">
                </div>
            </div>
            <div class="clearfix"></div>
        </div>


        <div class="requireName hide card padding-medium">
            <header class="row padding-8">
                <span class="left padding-12 padding-right upper font-bold">Mostrar este campo no formulário?</span>
                <label class="switch">
                    <input type="checkbox" class="input" id="form">
                    <div class="slider"></div>
                </label>
            </header>
            <input type="hidden" id="input" class="input"/>

            <div class="row hide form_body">

                <div class="col s12 l4 padding-small form_body">
                    <label class="color-text-gray">Largura do Campo</label>
                    <select class="input form_body" id="cols">
                        <option value="12" selected>100.% &nbsp;|&nbsp; 12/12</option>
                        <option value="11">91.6% &nbsp;|&nbsp; 11/12</option>
                        <option value="10">83.3% &nbsp;|&nbsp; 10/12</option>
                        <option value="9">75.0% &nbsp;|&nbsp; 9/12</option>
                        <option value="8">66.6% &nbsp;|&nbsp; 8/12</option>
                        <option value="7">58.3% &nbsp;|&nbsp; 7/12</option>
                        <option value="6">50.0% &nbsp;|&nbsp; 6/12</option>
                        <option value="5">41.6% &nbsp;|&nbsp; 5/12</option>
                        <option value="4">33.3% &nbsp;|&nbsp; 4/12</option>
                        <option value="3">25.0% &nbsp;|&nbsp; 3/12</option>
                        <option value="2">16.6% &nbsp;|&nbsp; 2/12</option>
                        <option value="1">08.3% &nbsp;|&nbsp; 1/12</option>
                    </select>
                </div>

                <div class="col s12 l4 padding-small form_body">
                    <label class="color-text-gray">Tablet</label>
                    <select class="input form_body" id="colm">
                        <option value="" selected disabled></option>
                        <option value="12">100.% &nbsp;|&nbsp; 12/12</option>
                        <option value="11">91.6% &nbsp;|&nbsp; 11/12</option>
                        <option value="10">83.3% &nbsp;|&nbsp; 10/12</option>
                        <option value="9">75.0% &nbsp;|&nbsp; 9/12</option>
                        <option value="8">66.6% &nbsp;|&nbsp; 8/12</option>
                        <option value="7">58.3% &nbsp;|&nbsp; 7/12</option>
                        <option value="6">50.0% &nbsp;|&nbsp; 6/12</option>
                        <option value="5">41.6% &nbsp;|&nbsp; 5/12</option>
                        <option value="4">33.3% &nbsp;|&nbsp; 4/12</option>
                        <option value="3">25.0% &nbsp;|&nbsp; 3/12</option>
                        <option value="2">16.6% &nbsp;|&nbsp; 2/12</option>
                        <option value="1">08.3% &nbsp;|&nbsp; 1/12</option>
                    </select>
                </div>

                <div class="col s12 l4 padding-small form_body">
                    <label class="color-text-gray">Desktop</label>
                    <select class="input form_body" id="coll">
                        <option value="" selected disabled></option>
                        <option value="12">100.% &nbsp;|&nbsp; 12/12</option>
                        <option value="11">91.6% &nbsp;|&nbsp; 11/12</option>
                        <option value="10">83.3% &nbsp;|&nbsp; 10/12</option>
                        <option value="9">75.0% &nbsp;|&nbsp; 9/12</option>
                        <option value="8">66.6% &nbsp;|&nbsp; 8/12</option>
                        <option value="7">58.3% &nbsp;|&nbsp; 7/12</option>
                        <option value="6">50.0% &nbsp;|&nbsp; 6/12</option>
                        <option value="5">41.6% &nbsp;|&nbsp; 5/12</option>
                        <option value="4">33.3% &nbsp;|&nbsp; 4/12</option>
                        <option value="3">25.0% &nbsp;|&nbsp; 3/12</option>
                        <option value="2">16.6% &nbsp;|&nbsp; 2/12</option>
                        <option value="1">08.3% &nbsp;|&nbsp; 1/12</option>
                    </select>
                </div>

                <div class="clearfix"></div>

                <div class="col">
                    <div class="col s12 padding-small">
                        <label class="color-text-gray">Descrição / Ajuda</label>
                        <input id="ajuda" type="text" class="input form_body">
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col hide" id="includebtnnew_field">
                    <div class="col s6 padding-small form_body">
                        <label class="color-text-gray row" for="autocompleteexists">Permitir utilizar valores existentes</label>
                        <label class="switch">
                            <input type="checkbox" class="input" id="autocompleteexists">
                            <div class="slider"></div>
                        </label>
                    </div>
                    <div class="col s6 padding-small form_body">
                        <label class="color-text-gray row" for="autocompletenovo">Adicionar botão para novos registros</label>
                        <label class="switch">
                            <input type="checkbox" class="input" id="autocompletenovo">
                            <div class="slider"></div>
                        </label>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col hide" id="orientation_field">
                    <div class="col s12 padding-small form_body">
                        <label class="color-text-gray">Orientação</label>
                        <select class="input form_body" id="orientation">
                            <option value="0">horizontal</option>
                            <option value="1">vertical</option>
                        </select>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div id="form-sup" class="col hide">
                    <div class="col s12 m6 padding-small form_body">
                        <label for="class" class="color-text-gray">Class</label>
                        <input id="class" type="text" class="input form_body">
                    </div>
                    <div class="col s12 m6 padding-small form_body">
                        <label for="style" class="color-text-gray">Style</label>
                        <input id="style" type="text" class="input form_body">
                    </div>
                    <div class="col s12 m6 padding-small form_body">
                        <label for="template" class="color-text-gray">Template</label>
                        <input id="template" type="text" class="input form_body">
                    </div>
                    <div class="col s12 m6 padding-small form_body">
                        <label for="atributos" class="color-text-gray">Atributos</label>
                        <input id="atributos" type="text" class="input form_body">
                    </div>
                </div>

                <button class="btn theme padding-small opacity hover-opacity-off hover-shadow right"
                        onclick="showhideFormSup()">
                    Avançado
                </button>

                <input type="hidden" class="input" id="type"/>

                <div class="clearfix"><br></div>
            </div>
        </div>

        <div class="requireName hide card padding-medium">
            <header class="row padding-8">
                <span class="left padding-12 padding-right upper font-bold">Mostrar este campo na tabela? </span>
                <label class="switch" style="margin: 0 !important">
                    <input type="checkbox" class="input" id="datagrid">
                    <div class="slider"></div>
                </label>
                <select class="input left datagrid_body" id="grid_relevant"
                        style="width: auto;margin:2px 0 0 25px;padding: 8px 0 5px;">
                    <option value="" selected>Ordem automática</option>
                    <option value="1">1° Primeiro item</option>
                    <option value="2">2° Segundo item</option>
                    <option value="3">3° Terceiro item</option>
                    <option value="4">4° Quarto item</option>
                    <option value="5">5° Quinto item</option>
                    <option value="6">6° Sexto item</option>
                </select>

                <button class="btn theme padding-small datagrid_body margin-small opacity hover-opacity-off hover-shadow right"
                        onclick="showhideListSup()">
                    Avançado
                </button>

            </header>

            <div class="row hide" id="list-sup">
                <div class="clearfix"></div>
                <div class="col s6 m4 padding-small">
                    <label for="grid_class" class="color-text-gray">Class</label>
                    <input id="grid_class" type="text" class="input">
                </div>
                <div class="col s6 m4 padding-small">
                    <label for="grid_style" class="color-text-gray">Style</label>
                    <input id="grid_style" type="text" class="input">
                </div>
                <div class="col s6 m4 padding-small">
                    <label for="grid_template" class="color-text-gray">Template</label>
                    <input id="grid_template" type="text" class="input">
                </div>

                <div class="clearfix"></div>
            </div>
        </div>

        <div class="requireName hide col">
            <div class="hide card padding-medium" id="regexp_field">
                <header class="row padding-8">
                    <i class="material-icons left">check</i>
                    <span class="left padding-left upper font-bold">Validação</span>
                </header>
                <div class="collapsible-body">
                    <div class="clearfix"></div>

                    <div class="col s12">
                        <label class="input-field col s12">
                            <span class="color-text-gray">Expressão Regular</span>
                            <input id="regexp" type="text" class="input font-medium">
                        </label>
                    </div>

                    <div class="clearfix"></div>
                </div>
            </div>
        </div>

        <div class="hide col" id="definirvalores">
            <div class="hide card padding-medium requireName">
                <header class="row padding-8 margin-bottom">
                        <span class="left padding-8 padding-right">
                            <i class="material-icons left">assignment</i>
                            <span class="left padding-left upper font-bold">Definir opções de seleção deste campo &nbsp;&nbsp;</span>
                        </span>
                    <span class="btn-floating left theme" id="allowBtnAdd" title="adicionar nova opção de seleção para este campo"
                          onclick="addValueAllow()">
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
                            <div class="padding-medium row round">
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
                            <div class="padding-medium row round">
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
                            <div class="padding-medium row round">
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
                            <div class="padding-medium row round">
                                <label class="col s6 m2 relative">
                                    <input type="checkbox" class="allformat" rel="document" id="all-document"/>
                                    <span>Todas</span>
                                </label>
                                <?php
                                $document = ["txt", "csv", "doc", "docx", "dot", "dotx", "dotm", "ppt", "pptx", "pps", "potm", "potx", "pdf", "xls", "xlsx", "xltx", "rtf"];
                                foreach ($document as $id) {
                                    echo "<label class='col s6 m2 relative'><input type='checkbox' class='document-format oneformat' rel='document' id='{$id}'/><span class='upper left padding-8'>{$id}</span></label>";
                                }

                                ?>
                            </div>
                        </div>
                        <div class="col s12 formato-div hide" id="formato-compact">
                            <div class="row padding-small"></div>
                            <div class="padding-medium row round">
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
                            <div class="padding-medium row round">
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

                <div class="clearfix col"></div>
            </div>
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

<div class="hide s-show" id="core-header-nav-bottom">
    <nav role="navigation">
        <ul class="core-class-container" style="padding:0">
            <div class="core-open-menu">
                <div class="core-menu-icon color-text-gray-dark"></div>
            </div>
            <div id="core-menu-custom-bottom" class="left"></div>
        </ul>
    </nav>
</div>