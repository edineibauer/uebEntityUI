/* ============================================
   Icon Picker - Material Icons + Emoji
   ============================================ */

var _iconPickerEl = null;
var _iconPickerTab = "material";
var _renderedIcons = [];

var _iconPickerData = {
    material: {
        "home": "casa inicio lar",
        "search": "busca pesquisa procurar",
        "settings": "configuracao ajustes opcoes engrenagem",
        "delete": "deletar excluir remover lixo",
        "done": "feito pronto concluido check",
        "info": "informacao informacoes dados",
        "help": "ajuda suporte duvida",
        "star": "estrela favorito destaque",
        "favorite": "favorito coracao curtir",
        "bookmark": "marcador salvar guardar",
        "visibility": "visibilidade olho ver mostrar",
        "visibility_off": "invisivel ocultar esconder",
        "lock": "cadeado trancado seguranca bloqueado",
        "lock_open": "cadeado aberto desbloqueado",
        "account_circle": "usuario conta perfil pessoa",
        "shopping_cart": "carrinho compras loja",
        "payment": "pagamento cartao pagar",
        "receipt": "recibo comprovante nota",
        "assessment": "avaliacao relatorio grafico estatistica",
        "build": "construir ferramenta chave",
        "code": "codigo programacao desenvolvimento",
        "dashboard": "painel controle",
        "event": "evento calendario data",
        "explore": "explorar bussola navegar",
        "extension": "extensao plugin complemento",
        "face": "rosto face pessoa",
        "grade": "nota avaliacao estrela",
        "history": "historico relogio tempo passado",
        "language": "idioma lingua mundo global",
        "launch": "abrir externo lancar",
        "list": "lista itens",
        "perm_identity": "identidade pessoa perfil",
        "power_settings_new": "energia ligar desligar",
        "print": "imprimir impressora",
        "schedule": "agenda horario relogio",
        "shopping_basket": "cesta compras",
        "store": "loja comercio estabelecimento",
        "thumb_up": "positivo curtir bom legal",
        "thumb_down": "negativo ruim",
        "timeline": "linha tempo cronologia",
        "today": "hoje data calendario",
        "touch_app": "toque interacao dedo",
        "trending_up": "tendencia subindo crescimento",
        "verified_user": "verificado usuario seguro",
        "work": "trabalho maleta emprego",
        "zoom_in": "ampliar zoom mais",
        "zoom_out": "reduzir zoom menos",
        "description": "descricao documento arquivo texto",
        "assignment": "tarefa trabalho documento",
        "bug_report": "bug erro problema",
        "cached": "cache atualizar",
        "check_circle": "check correto verificado ok",
        "date_range": "datas periodo intervalo",
        "dns": "servidor rede",
        "exit_to_app": "sair logout",
        "feedback": "feedback retorno opiniao",
        "get_app": "download baixar",
        "highlight_off": "remover cancelar x",
        "hourglass_empty": "ampulheta espera tempo",
        "label": "etiqueta rotulo tag",
        "note_add": "nota adicionar novo",
        "open_in_new": "abrir nova janela externo",
        "pan_tool": "mao parar",
        "redeem": "presente cupom voucher resgate",
        "reorder": "reordenar arrastar mover",
        "report_problem": "alerta problema aviso",
        "room": "local sala pin mapa",
        "stars": "estrelas premium qualidade",
        "subject": "assunto texto conteudo",
        "swap_horiz": "trocar horizontal transferir",
        "swap_vert": "trocar vertical",
        "track_changes": "rastrear mudancas alteracoes",
        "translate": "traduzir idioma",
        "update": "atualizar update",
        "watch_later": "assistir depois relogio",
        "add": "adicionar mais novo",
        "add_circle": "adicionar circulo mais",
        "create": "criar editar lapis",
        "filter_list": "filtrar lista ordenar",
        "flag": "bandeira sinalizar marcar",
        "link": "link url endereco",
        "mail": "email carta correio mensagem",
        "remove": "remover menos",
        "save": "salvar disquete guardar",
        "send": "enviar mensagem",
        "sort": "ordenar classificar",
        "undo": "desfazer voltar",
        "redo": "refazer avancar",
        "content_copy": "copiar duplicar",
        "content_paste": "colar",
        "content_cut": "cortar recortar",
        "clear": "limpar apagar",
        "block": "bloquear proibir",
        "reply": "responder voltar",
        "archive": "arquivar guardar",
        "drafts": "rascunhos",
        "inbox": "caixa entrada",
        "select_all": "selecionar tudo",
        "call": "ligar telefone chamada",
        "chat": "conversa bate papo",
        "comment": "comentario observacao",
        "contacts": "contatos agenda",
        "email": "email correio eletronico",
        "forum": "forum discussao grupo",
        "location_on": "localizacao mapa pin lugar",
        "message": "mensagem sms texto",
        "notifications": "notificacoes sino alerta",
        "phone": "telefone ligar celular",
        "sms": "mensagem texto celular",
        "vpn_key": "chave acesso senha",
        "chat_bubble": "balao conversa mensagem",
        "contact_mail": "contato email",
        "contact_phone": "contato telefone",
        "live_help": "ajuda ao vivo suporte chat",
        "group": "grupo equipe time pessoas",
        "group_add": "grupo adicionar convidar",
        "people": "pessoas usuarios grupo",
        "person": "pessoa usuario individual",
        "person_add": "pessoa adicionar convidar",
        "public": "publico mundo global",
        "share": "compartilhar enviar",
        "whatshot": "popular tendencia fogo quente",
        "mood": "humor emocao sorriso",
        "cake": "bolo aniversario festa",
        "domain": "dominio empresa predio",
        "school": "escola educacao universidade",
        "sentiment_satisfied": "satisfeito feliz contente",
        "sentiment_dissatisfied": "insatisfeito triste",
        "notifications_active": "notificacao ativa sino",
        "poll": "enquete pesquisa votacao",
        "local_atm": "caixa eletronico dinheiro banco",
        "local_cafe": "cafe cafeteria",
        "local_dining": "restaurante comida jantar",
        "local_fire_department": "bombeiro fogo emergencia",
        "local_gas_station": "posto gasolina combustivel",
        "local_grocery_store": "mercado supermercado compras",
        "local_hospital": "hospital saude medico",
        "local_library": "biblioteca livro",
        "local_mall": "shopping centro comercial",
        "local_offer": "oferta desconto tag",
        "local_parking": "estacionamento carro",
        "local_pharmacy": "farmacia remedio saude",
        "local_pizza": "pizza comida delivery",
        "local_shipping": "entrega frete envio caminhao",
        "local_taxi": "taxi transporte carro",
        "restaurant": "restaurante comida alimentacao",
        "spa": "spa relaxamento bem estar",
        "play_arrow": "play reproduzir iniciar",
        "pause": "pausar pausa",
        "stop": "parar stop",
        "volume_up": "volume som alto",
        "volume_off": "mudo sem som silencio",
        "mic": "microfone audio gravar",
        "mic_off": "microfone desligado mudo",
        "videocam": "camera video filmadora",
        "music_note": "musica nota melodia",
        "movie": "filme cinema video",
        "photo_camera": "camera foto fotografia",
        "camera_alt": "camera foto alternativa",
        "image": "imagem foto figura",
        "photo": "foto fotografia imagem",
        "palette": "paleta cores arte",
        "color_lens": "cor lente cores",
        "brush": "pincel arte pintar",
        "edit": "editar modificar lapis",
        "crop": "cortar recortar imagem",
        "filter": "filtro efeito",
        "computer": "computador desktop pc",
        "desktop_windows": "desktop monitor tela",
        "headset": "fone ouvido headset",
        "keyboard": "teclado digitar",
        "laptop": "notebook laptop portatil",
        "mouse": "mouse rato cursor",
        "phone_android": "celular android",
        "phone_iphone": "celular iphone apple",
        "security": "seguranca escudo protecao",
        "smartphone": "celular smartphone telefone",
        "tablet": "tablet ipad dispositivo",
        "tv": "televisao tv monitor",
        "watch": "relogio pulso smartwatch",
        "directions_car": "carro direcao automovel",
        "directions_bus": "onibus transporte",
        "directions_bike": "bicicleta ciclismo",
        "directions_walk": "andar caminhar pedestre",
        "flight": "voo aviao viagem",
        "hotel": "hotel hospedagem pousada",
        "local_airport": "aeroporto aviao viagem",
        "map": "mapa localizacao",
        "navigation": "navegacao direcao bussola",
        "place": "lugar local pin",
        "my_location": "minha localizacao gps",
        "terrain": "terreno montanha relevo",
        "traffic": "trafego transito semaforo",
        "train": "trem metro transporte",
        "cloud": "nuvem armazenamento",
        "cloud_download": "download baixar nuvem",
        "cloud_upload": "upload enviar nuvem",
        "folder": "pasta diretorio",
        "folder_open": "pasta aberta diretorio",
        "insert_drive_file": "arquivo documento",
        "attach_file": "anexar arquivo clip",
        "create_new_folder": "nova pasta criar",
        "battery_full": "bateria cheia energia",
        "bluetooth": "bluetooth conexao sem fio",
        "brightness_high": "brilho alto claro",
        "gps_fixed": "gps localizacao",
        "network_wifi": "wifi rede internet",
        "storage": "armazenamento disco memoria",
        "attach_money": "dinheiro cifrao valor moeda",
        "format_bold": "negrito bold texto",
        "insert_chart": "grafico inserir",
        "insert_emoticon": "emoji emoticon sorriso",
        "mode_edit": "editar modo lapis",
        "publish": "publicar enviar",
        "text_fields": "texto campos fonte",
        "title": "titulo cabecalho",
        "format_list_bulleted": "lista marcadores topicos",
        "format_list_numbered": "lista numerada ordem",
        "format_quote": "citacao aspas",
        "category": "categoria tipo grupo",
        "eco": "ecologia verde sustentavel natureza",
        "engineering": "engenharia tecnico",
        "fitness_center": "academia fitness exercicio",
        "flash_on": "flash luz camera",
        "pets": "animais pet bicho",
        "sports_esports": "jogos games esports",
        "sports_soccer": "futebol esporte bola",
        "wb_sunny": "sol ensolarado dia claro",
        "nights_stay": "noite lua escuro",
        "restaurant_menu": "cardapio menu restaurante",
        "science": "ciencia laboratorio quimica",
        "savings": "economia poupanca cofre porco",
        "medical_services": "saude medico medicina",
        "health_and_safety": "saude seguranca",
        "volunteer_activism": "voluntario doacao coracao mao",
        "workspace_premium": "premium qualidade destaque",
        "water_drop": "agua gota",
        "light_mode": "claro dia sol",
        "dark_mode": "escuro noite lua",
        "recycling": "reciclagem sustentavel",
        "handshake": "aperto mao acordo parceria",
        "psychology": "psicologia mente cerebro",
        "self_improvement": "meditacao yoga paz",
        "inventory": "inventario estoque",
        "qr_code": "qr code codigo",
        "qr_code_scanner": "scanner qr code leitor",
        "task_alt": "tarefa concluida check",
        "verified": "verificado selo",
        "currency_exchange": "cambio moeda troca",
        "price_check": "preco verificar valor",
        "sell": "vender venda etiqueta",
        "point_of_sale": "ponto venda caixa pdv",
        "storefront": "vitrine loja frente",
        "add_business": "adicionar empresa negocio",
        "pie_chart": "grafico pizza estatistica",
        "bar_chart": "grafico barras estatistica",
        "show_chart": "grafico linha tendencia",
        "leaderboard": "ranking classificacao lider",
        "speed": "velocidade rapido desempenho",
        "token": "token chave autenticacao",
        "key": "chave acesso",
        "admin_panel_settings": "admin painel config",
        "manage_accounts": "gerenciar contas usuarios",
        "supervisor_account": "supervisor gerente conta",
        "badge": "cracha identificacao",
        "diversity_1": "diversidade inclusao",
        "groups": "grupos pessoas comunidade",
        "campaign": "campanha megafone marketing",
        "tips_and_updates": "dicas atualizacoes lampada",
        "auto_awesome": "automatico incrivel estrela magica",
        "celebration": "celebracao festa comemoracao",
        "emoji_events": "eventos trofeu premiacao",
        "military_tech": "militar medalha conquista",
        "emoji_objects": "objetos lampada ideia",
        "local_activity": "atividade local ticket ingresso",
        "loyalty": "lealdade fidelidade cartao",
        "card_giftcard": "cartao presente vale",
        "card_membership": "cartao membro associacao",
        "credit_card": "cartao credito pagamento",
        "account_balance": "banco saldo conta",
        "account_balance_wallet": "carteira saldo dinheiro",
        "money": "dinheiro valor cedula",
        "paid": "pago pagamento concluido",
        "request_quote": "orcamento cotacao preco",
        "shopping_bag": "sacola compras loja",
        "add_shopping_cart": "adicionar carrinho compras"
    },
    emoji: [
        {e: "\u{1F600}", k: "sorriso feliz rosto alegre"},
        {e: "\u{1F603}", k: "sorriso feliz alegre dentes"},
        {e: "\u{1F60A}", k: "sorriso timido feliz corado"},
        {e: "\u{1F970}", k: "amor carinho coracao apaixonado"},
        {e: "\u{1F60E}", k: "oculos legal cool descolado"},
        {e: "\u{1F929}", k: "estrela impressionado wow"},
        {e: "\u{1F607}", k: "anjo inocente aurea"},
        {e: "\u{1F914}", k: "pensando duvida reflexao"},
        {e: "\u{1F62E}", k: "surpresa espanto boca aberta"},
        {e: "\u{1F622}", k: "triste chorando lagrima"},
        {e: "\u{1F621}", k: "raiva bravo irritado furioso"},
        {e: "\u{1F92F}", k: "explodindo mente chocado"},
        {e: "\u{1F973}", k: "festa celebracao comemorar chapeu"},
        {e: "\u{1F634}", k: "dormindo sono zzz"},
        {e: "\u{1F911}", k: "dinheiro rico cifrao"},
        {e: "\u{1F916}", k: "robo tecnologia bot"},
        {e: "\u{1F44D}", k: "positivo legal bom curtir joinha"},
        {e: "\u{1F44E}", k: "negativo ruim deslike"},
        {e: "\u{1F44F}", k: "palmas aplaudir parabens"},
        {e: "\u{1F64F}", k: "rezar obrigado por favor maos juntas"},
        {e: "\u{1F4AA}", k: "forca musculo forte braco"},
        {e: "\u{1F91D}", k: "aperto mao acordo negocio"},
        {e: "\u270C\uFE0F", k: "paz vitoria dois dedos"},
        {e: "\u{1F44B}", k: "tchau ola aceno mao"},
        {e: "\u{1F446}", k: "cima apontar dedo para cima"},
        {e: "\u270A", k: "punho forca luta"},
        {e: "\u2764\uFE0F", k: "coracao amor vermelho"},
        {e: "\u{1F499}", k: "coracao azul"},
        {e: "\u{1F49A}", k: "coracao verde"},
        {e: "\u{1F49B}", k: "coracao amarelo"},
        {e: "\u{1F9E1}", k: "coracao laranja"},
        {e: "\u{1F49C}", k: "coracao roxo"},
        {e: "\u{1F496}", k: "coracao brilho amor sparkle"},
        {e: "\u{1F49D}", k: "coracao presente laco"},
        {e: "\u{1F494}", k: "coracao partido quebrado triste"},
        {e: "\u{1F31F}", k: "estrela brilho destaque"},
        {e: "\u2B50", k: "estrela favorito amarela"},
        {e: "\u{1F525}", k: "fogo quente popular hot trend"},
        {e: "\u{1F4A7}", k: "agua gota"},
        {e: "\u{1F308}", k: "arco iris cores"},
        {e: "\u{1F31E}", k: "sol dia ensolarado"},
        {e: "\u{1F319}", k: "lua noite crescente"},
        {e: "\u26A1", k: "raio energia eletrico"},
        {e: "\u2744\uFE0F", k: "neve frio gelo floco"},
        {e: "\u{1F30A}", k: "onda mar agua oceano"},
        {e: "\u{1F343}", k: "folha natureza vento verde"},
        {e: "\u{1F33A}", k: "flor rosa hibisco"},
        {e: "\u{1F33B}", k: "girassol flor amarelo"},
        {e: "\u{1F334}", k: "palmeira praia tropical"},
        {e: "\u{1F30D}", k: "terra mundo planeta global"},
        {e: "\u{1F415}", k: "cachorro cao pet animal"},
        {e: "\u{1F408}", k: "gato cat pet felino"},
        {e: "\u{1F981}", k: "leao rei selva"},
        {e: "\u{1F43B}", k: "urso bear animal"},
        {e: "\u{1F98A}", k: "raposa fox animal"},
        {e: "\u{1F426}", k: "passaro ave bird"},
        {e: "\u{1F98B}", k: "borboleta butterfly inseto"},
        {e: "\u{1F41F}", k: "peixe fish agua"},
        {e: "\u{1F422}", k: "tartaruga lento calmo"},
        {e: "\u{1F985}", k: "aguia eagle ave"},
        {e: "\u{1F355}", k: "pizza comida fast food"},
        {e: "\u{1F354}", k: "hamburguer burger lanche"},
        {e: "\u{1F35F}", k: "batata frita"},
        {e: "\u{1F370}", k: "bolo doce fatia"},
        {e: "\u{1F382}", k: "bolo aniversario vela festa"},
        {e: "\u2615", k: "cafe coffee xicara quente"},
        {e: "\u{1F37A}", k: "cerveja beer caneca"},
        {e: "\u{1F377}", k: "vinho wine taca"},
        {e: "\u{1F34E}", k: "maca fruta vermelha"},
        {e: "\u{1F4B0}", k: "dinheiro saco money bolsa"},
        {e: "\u{1F4B3}", k: "cartao credito debito pagamento"},
        {e: "\u{1F48E}", k: "diamante joia precioso gema"},
        {e: "\u{1F381}", k: "presente gift caixa embrulho"},
        {e: "\u{1F3C6}", k: "trofeu premio campeao vitoria"},
        {e: "\u{1F3AF}", k: "alvo meta objetivo dardo"},
        {e: "\u{1F3A8}", k: "arte pintura paleta cores"},
        {e: "\u{1F4F1}", k: "celular telefone smartphone"},
        {e: "\u{1F4BB}", k: "computador laptop notebook"},
        {e: "\u{1F4E7}", k: "email carta envelope"},
        {e: "\u{1F4DE}", k: "telefone ligar receptor"},
        {e: "\u{1F511}", k: "chave key acesso"},
        {e: "\u{1F512}", k: "cadeado trancado seguro"},
        {e: "\u{1F513}", k: "cadeado aberto desbloqueado"},
        {e: "\u23F0", k: "relogio alarme tempo despertador"},
        {e: "\u{1F4C5}", k: "calendario data agenda"},
        {e: "\u{1F4CA}", k: "grafico estatistica barras"},
        {e: "\u{1F4C8}", k: "grafico subindo crescimento"},
        {e: "\u{1F4C9}", k: "grafico descendo queda"},
        {e: "\u{1F4CB}", k: "clipboard lista prancheta"},
        {e: "\u{1F4DD}", k: "nota escrever editar anotacao"},
        {e: "\u{1F4CC}", k: "pin fixar tachinha"},
        {e: "\u{1F514}", k: "sino notificacao alerta"},
        {e: "\u{1F4A1}", k: "lampada ideia luz inovacao"},
        {e: "\u{1F527}", k: "ferramenta chave inglesa"},
        {e: "\u{1F528}", k: "martelo ferramenta construir"},
        {e: "\u2699\uFE0F", k: "engrenagem configuracao mecanismo"},
        {e: "\u{1F6D2}", k: "carrinho compras supermercado"},
        {e: "\u{1F4E6}", k: "caixa pacote entrega encomenda"},
        {e: "\u{1F697}", k: "carro automovel veiculo"},
        {e: "\u{1F68C}", k: "onibus bus transporte"},
        {e: "\u2708\uFE0F", k: "aviao viagem voo"},
        {e: "\u{1F680}", k: "foguete rapido espacial lancamento"},
        {e: "\u{1F3E0}", k: "casa home lar moradia"},
        {e: "\u{1F3E2}", k: "predio escritorio empresa comercial"},
        {e: "\u{1F3EA}", k: "loja conveniencia comercio"},
        {e: "\u{1F3E5}", k: "hospital saude medico"},
        {e: "\u{1F3EB}", k: "escola educacao ensino"},
        {e: "\u{1F3ED}", k: "fabrica industria producao"},
        {e: "\u2705", k: "check ok correto sim confirmado verde"},
        {e: "\u274C", k: "x nao errado fechar cancelar"},
        {e: "\u26A0\uFE0F", k: "alerta aviso cuidado atencao"},
        {e: "\u2139\uFE0F", k: "informacao info"},
        {e: "\u2753", k: "interrogacao duvida pergunta"},
        {e: "\u2757", k: "exclamacao importante atencao"},
        {e: "\u{1F534}", k: "circulo vermelho"},
        {e: "\u{1F7E2}", k: "circulo verde"},
        {e: "\u{1F535}", k: "circulo azul"},
        {e: "\u{1F7E1}", k: "circulo amarelo"},
        {e: "\u{1F7E0}", k: "circulo laranja"},
        {e: "\u{1F7E3}", k: "circulo roxo"},
        {e: "\u26AB", k: "circulo preto"},
        {e: "\u26AA", k: "circulo branco"},
        {e: "\u267B\uFE0F", k: "reciclagem reciclar verde"},
        {e: "\u{1F6A9}", k: "bandeira vermelha flag alerta"}
    ]
};

function initIconPicker() {
    if (_iconPickerEl) return;

    var html =
        '<div id="iconPicker" class="icon-picker hide">' +
            '<div class="icon-picker-header">' +
                '<div style="display:flex;align-items:center;gap:6px">' +
                    '<input type="text" id="iconPickerSearch" placeholder="Buscar ícone..." autocomplete="off" style="flex:1">' +
                    '<button type="button" id="iconPickerRemove" class="icon-picker-clear-btn hide" onclick="clearIconFromPicker()" title="Remover ícone">' +
                        '<i class="material-icons">close</i>' +
                    '</button>' +
                '</div>' +
            '</div>' +
            '<div class="icon-picker-tabs">' +
                '<button type="button" class="icon-picker-tab active" data-tab="material">Material</button>' +
                '<button type="button" class="icon-picker-tab" data-tab="emoji">Emoji</button>' +
            '</div>' +
            '<div class="icon-picker-grid" id="iconPickerGrid"></div>' +
        '</div>';

    // Append inside #nav-menu to inherit theme CSS variables
    $("#nav-menu").append(html);
    _iconPickerEl = document.getElementById("iconPicker");

    $("#iconPickerSearch").on("input", function () {
        renderIconGrid($(this).val().toLowerCase().trim());
    });

    $(document).on("click", ".icon-picker-tab", function () {
        _iconPickerTab = $(this).data("tab");
        $(".icon-picker-tab").removeClass("active");
        $(this).addClass("active");
        renderIconGrid($("#iconPickerSearch").val().toLowerCase().trim());
    });

    $(document).on("click", ".icon-picker-item", function () {
        var idx = parseInt($(this).attr("data-idx"));
        if (!isNaN(idx) && _renderedIcons[idx] !== undefined) {
            selectIconFromPicker(_renderedIcons[idx]);
        }
    });

    $(document).on("mousedown touchstart", function (e) {
        if (_iconPickerEl && !$(_iconPickerEl).hasClass("hide")) {
            if (!$(e.target).closest("#iconPicker, .icon-picker-trigger").length) {
                closeIconPicker();
            }
        }
    });
}

function toggleIconPicker() {
    initIconPicker();
    if ($(_iconPickerEl).hasClass("hide")) {
        openIconPicker();
    } else {
        closeIconPicker();
    }
}

function openIconPicker() {
    initIconPicker();

    var $trigger = $(".icon-picker-trigger");
    if (!$trigger.length) return;

    var rect = $trigger[0].getBoundingClientRect();
    var pickerWidth = 300;

    if (window.innerWidth <= 768) {
        // Mobile: bottom sheet
        $(_iconPickerEl).css({top: "auto", left: "0", right: "0", bottom: "0"});
    } else {
        // Desktop: dropdown below trigger
        var pickerLeft = rect.left;
        if (pickerLeft + pickerWidth > window.innerWidth) {
            pickerLeft = window.innerWidth - pickerWidth - 8;
        }
        if (pickerLeft < 4) pickerLeft = 4;

        $(_iconPickerEl).css({
            top: (rect.bottom + 6) + "px",
            left: pickerLeft + "px",
            right: "auto",
            bottom: "auto"
        });
    }

    // Show/hide remove button based on current icon
    var currentIcon = $("#entityIcon").val();
    if (currentIcon) {
        $("#iconPickerRemove").removeClass("hide");
    } else {
        $("#iconPickerRemove").addClass("hide");
    }

    $(_iconPickerEl).removeClass("hide");
    renderIconGrid("");
    setTimeout(function() { $("#iconPickerSearch").val("").focus(); }, 50);
}

function closeIconPicker() {
    if (_iconPickerEl) {
        $(_iconPickerEl).addClass("hide");
    }
}

function renderIconGrid(query) {
    var $grid = $("#iconPickerGrid");
    $grid.html("");
    _renderedIcons = [];

    if (_iconPickerTab === "material") {
        var icons = _iconPickerData.material;
        for (var name in icons) {
            if (query && name.indexOf(query) === -1 && icons[name].indexOf(query) === -1) continue;
            var idx = _renderedIcons.length;
            _renderedIcons.push(name);
            $grid.append(
                '<button type="button" class="icon-picker-item" data-idx="' + idx + '" title="' + name + '">' +
                '<i class="material-icons">' + name + '</i>' +
                '</button>'
            );
        }
    } else {
        var emojis = _iconPickerData.emoji;
        for (var i = 0; i < emojis.length; i++) {
            var em = emojis[i];
            if (query && em.k.indexOf(query) === -1) continue;
            var idx = _renderedIcons.length;
            _renderedIcons.push(em.e);
            $grid.append(
                '<button type="button" class="icon-picker-item" data-idx="' + idx + '" title="' + em.k + '">' +
                '<span class="icon-picker-emoji">' + em.e + '</span>' +
                '</button>'
            );
        }
    }

    if (_renderedIcons.length === 0) {
        $grid.html('<div class="icon-picker-empty">Nenhum ícone encontrado</div>');
    }
}

function selectIconFromPicker(icon) {
    $("#entityIcon").val(icon);
    entity.icon = icon;
    renderIconPreview(icon);
    closeIconPicker();
}

function clearIconFromPicker() {
    $("#entityIcon").val("");
    entity.icon = "";
    renderIconPreview("");
    closeIconPicker();
}

function renderIconPreview(icon) {
    var $demo = $("#entityIconDemo");
    if (!icon) {
        $demo.addClass("material-icons").text("insert_emoticon").css("opacity", "0.3");
        return;
    }
    $demo.css("opacity", "");
    // Emoji: contains non-ASCII characters
    if (/[^\x00-\x7F]/.test(icon)) {
        $demo.removeClass("material-icons").addClass("icon-preview-emoji").text(icon);
    } else {
        $demo.removeClass("icon-preview-emoji").addClass("material-icons").text(icon);
    }
}