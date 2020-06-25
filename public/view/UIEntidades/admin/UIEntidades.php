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
                <button class="theme theme-text-aux opacity radius pointer right padding-tiny btn-flat"
                        style="height: 26px;" onclick="removeEntity('__$__')">
                    <i class="material-icons font-medium" style="margin-top: 4px;">delete</i>
                </button>
                <button class="theme theme-text-aux radius pointer right padding-tiny btn-flat"
                        style="margin-right: 5px;height: 26px;"
                        onclick="entityEdit('__$__')">
                    <i class="material-icons font-medium" style="margin-top: 4px;">edit</i>
                </button>
            </div>
        </div>
    </ul>
</ul>

<div class="col s12 m4 z-depth-2 space-header theme-l3" id="nav-menu">
    <header class="row">
        <div class="panel">
            <div class="col s12 padding-tiny">
                <button class="btn theme right radius" id="saveEntityBtn" onclick="saveEntity()">
                    Salvar Entidade
                    <i class="material-icons right padding-left">check</i>
                </button>
                <button class="btn theme left radius hide downloadEntity" title="baixar backup da entidade"
                        onclick="downloadEntity()">
                    <i class="material-icons left theme-text-aux">get_app</i>
                </button>
            </div>
        </div>
    </header>
    <div class="row"></div>
    <div class="panel" id="space-attr-entity" style="margin-top: 0!important;">
        <div class="row" id="entity-name">
            <label class="col s12">
                <label class="col right relative" style="width: 90px;padding: 3.5px 0 0 2px;">
                    <select id="user" class="col margin-0 theme-text-aux">
                        <option value="0" class="theme-l2 theme-text-aux">Entidade</option>
                        <option value="2" class="theme-l2 theme-text-aux">Sistema</option>
                        <option value="1" class="theme-l2 theme-text-aux">Usuário</option>
                    </select>
                </label>
                <div class="rest">
                    <input id="entityName" type="text" placeholder="nome da entidade..." class="font-large col theme-text-aux"
                           style="margin:0">
                </div>
            </label>
            <label class="col s12" id="col-system">
                <div class="col left relative theme-text-aux" style="width: 70px;padding-top: 14px">
                    Sistema:
                </div>
                <label class="col left" style="padding: 3.5px 0 0 0;width: 187px">
                    <select id="system" class="col margin-0 theme-text-aux">
                        <option value="" class="theme-l2 theme-text-aux"><?=SITENAME?></option>
                    </select>
                </label>
            </label>

            <div class="row font-small hide requireNameEntity">
                <div class="col left padding-tiny" style="width: 26px; height: 26px">
                    <a href="https://material.io/tools/icons/?style=baseline" target="_blank"
                       class="right btn-flat font-small theme-text-aux margin-0" style="width: 27px; height: 26px">
                        <i class="material-icons padding-tiny padding-4 theme-text-aux" id="entityIconDemo"></i>
                    </a>
                </div>
                <div class="left" style="width: 50px">
                    <input id="entityIcon" placeholder="ícone" type="text" class="theme-text-aux">
                </div>
                <label class="col relative" style="width: 70px">
                    <input type="checkbox" class="left" id="haveAutor"/>
                    <span class="left pointer theme-text-aux" style="padding:10px 0">Autor</span>
                </label>
                <label class="col relative" style="width: 110px">
                    <input type="checkbox" class="left" id="haveOwner"/>
                    <span class="left pointer theme-text-aux" style="padding:10px 0">Proprietário</span>
                </label>
            </div>
            <div class="row"></div>
        </div>

        <div class="col hide overflow-hidden relative padding-bottom" id="importForm">
            <br>
            <div class="row">
                <label for="import">Restaurar Entidade</label>
                <input type="file" name="import" id="import"/>
            </div>
            <button class="btn theme-d1 left" onclick="sendImport()">
                <i class="material-icons padding-right font-large left">send</i><span class="left">Enviar</span>
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
            <button class="btn-flat pointer right opacity theme theme-text-aux radius" style="padding: 9px 5px"
                    onclick="deleteAttr(__$0__)">
                <i class="material-icons right font-large">delete</i>
            </button>
            <button class="btn-flat pointer right theme theme-text-aux radius" style="margin-right: 5px;padding: 9px"
                    onclick="editAttr(__$0__)">
                <i class="material-icons right font-large">edit</i>
            </button>
        </li>
    </div>
</div>

<div id="main" class="row color-gray-light space-header">
    <div class="col s12 hide requireNameEntity">
        <div class="col no-select margin-bottom padding-bottom">
            <button class="theme left radius padding-left" title="Salvar Campo" onclick="editAttr()">
                Salvar Campo
                <i class="material-icons right" style="padding-left: 8px">check</i>
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

                    <div class="col s12 m8 l5 padding-tiny hide" id="default_container">
                        <label for="default" class="color-text-gray">Valor Inicial (Padrão)</label>
                        <input type="text" id="default" class="input">
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

        <label class="col s12 relative tpl hide" for="__$0__" id="selectOneListOption">
            <input type="checkbox" id="__$0__" class="left padding-right __$2__"/>
            <span class="left padding-medium font-medium pointer">__$1__ </span>
        </label>

        <div class="hide card padding-medium <?php //relation_container?>">
            <header class="row padding-8">
                <span class="left padding-12 padding-right upper font-bold">Configurar Campos do Formulário Relacional</span>
            </header>

            <div class="col padding-bottom">
                <div class="col s12 m5 padding-small">
                    <h4>Mostrar Campos</h4>
                    <div class="col" id="relation_fields_show"></div>

                    <label class="col s12 relativep pointer border-bottom hide" id="tpl_relation_fields_show">
                        <input type="checkbox" class="relation_fields_show" rel="__$0__" __$2__/>
                        <span class="left padding-8 font-medium">__$1__</span>
                    </label>
                </div>
                <div class="col s12 m7 padding-small">
                    <h4>Valor de Entrada Padrão</h4>
                    <div class="col" id="relation_fields_default" style="padding-top:4px"></div>

                    <div class="col hide" id="tpl_relation_fields_default">
                        <input type="text" class="col font-medium relation_fields_default" value="__$2__"
                               style="margin-bottom: 4px;" rel="__$0__">
                    </div>
                </div>
                <div class="col padding-12"></div>
            </div>
        </div>

        <div class="hide card padding-medium <?php //relation_container ?>">
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
                        <span class="color-text-gray">Opção de Seleção (Valor armazenado)</span>
                        <input class="values" type="text"/>
                    </label>

                    <label class="input-field col s12 m8 padding-small">
                        <span class="color-text-gray">Opção de Seleção (Label)</span>
                        <input class="names" type="text">
                    </label>
                </div>

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