/*
 * Executado em tempo real de funcionamento da tela
 */
window.onload = (function () {

    /**
     * Verifica se símbolos são aceitos nas expressões regulares
     * @returns retorna erros de especificação das expressões regulares
     */
    document.getElementById('defReg').addEventListener('keyup', analisaExpRegulares);

    /**
     * Responsável por chamar método para download da tabela do automato de análise léxica
     */
    document.getElementById('downloadTabelaAnaliseLexica').addEventListener('click', function (event) {
        downloadTabela(event, 'tabelaAnaliseLexica.csv');
    });

    /**
     * Responsável por chamar método para download da tabela do resultado da análise léxica
     */
    document.getElementById('downloadResultadoAnaliseLexica').addEventListener('click', function (event) {
        downloadTabela(event, 'resultadoAnaliseLexica.csv');
    });

    /**
     * Verifica se símbolos pertencem aos tokens válidos e separa 
     * @returns retorna erros de especificação das expressões regulares
     */
    //document.getElementById('defGram').addEventListener('keyup', analisaGramatica);

    /**
     * Abre a modal para excluir dados da tela
     */
    document.getElementById('idbtExcluirDados').addEventListener('click', openModalexcluirDadosTela);
    
    /**
     * Abre a modal para excluir dados do usuário e usuário
     */
    document.getElementById('idbtExcluirUsuario').addEventListener('click', openModalExcluirUsuario);

    /**
     * Abre a modal para sair do sistema
     */
    document.getElementById('btnSair').addEventListener('click', openModalSair);

    /**
     * Chama a função que realiza a exclusão dos dados da tela
     */
    document.getElementById('btnExcluirDados').addEventListener('click', excluirDados);

    /**
     * Fecha a modal de exclusão de dados da tela
     */
    document.getElementById('cancelarExcluirDados').addEventListener('click', closeExcluirDados);

    /**
     * Chama a função que realiza a exclusão dos dados do usuario
     */
    document.getElementById('btnExcluirUsuario').addEventListener('click', excluirUsuario);

    /**
     * Fecha a modal de exclusão de dados do usuário
     */
    document.getElementById('cancelarExcluirUsuario').addEventListener('click', closeExcluirUsuario);

    /**
     * Chama a função que realiza o logout do sistema
     */
    document.getElementById('btnSairLogout').addEventListener('click', logout);

    /**
     * Fecha a modal de logout caso não seja do interesse de sair do sistema
     */
    document.getElementById('cancelarLogout').addEventListener('click', closeModalSair);

    //****Inicio fechar modal*****//
    var modal = document.getElementById('myModal');

    window.onclick = function (event) {
        if (event.target == modal) {
            closeModal();
        }
    }

    var modal2 = document.getElementById('myModal2');

    window.onclick = function (event) {
        if (event.target == modal) {
            closeModal2();
        }
    }

    //****Fim fechar modais*****//

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    /*********************************Inicio da parte das sugestões********************************/
    // Array de sugestões
    var arquivo = new XMLHttpRequest();
    arquivo.open("GET", getBaseURL() + "data/sugestoes.txt", false);
    arquivo.send(null);

    // Processa as sugestões do arquivo
    var sugestoes = arquivo.responseText.split('\n');
    sugestoes = sugestoes.map(function (sugestao) {
        return sugestao.trim();
    });

    // Obtém o elemento textarea
    var campoTexto = document.getElementById('defReg');

    // Obtém o elemento do balão de sugestão
    var balaoSugestao = document.getElementById('balaoSugestao');

    // Função para mostrar as sugestões
    function mostrarSugestoes() {
        // Limpa as sugestões anteriores
        balaoSugestao.innerHTML = '';

        // Obtém o texto digitado
        var textoDigitado = campoTexto.value;
        var partes = textoDigitado.split(';');
        var ultimaParte = partes[partes.length - 1].trim().toLowerCase();

        // Verifica se há texto digitado na última parte
        if (ultimaParte.length > 0) {
            // Verifica se há sugestões para a última parte digitada
            var sugestoesFiltradas = sugestoes.filter(function (sugestao) {
                return sugestao.toLowerCase().startsWith(ultimaParte);
            });

            // Exibe as sugestões no balão de sugestão
            sugestoesFiltradas.forEach(function (sugestao) {
                var sugestaoElemento = document.createElement('div');
                sugestaoElemento.textContent = sugestao;
                sugestaoElemento.classList.add('sugestao');
                sugestaoElemento.addEventListener('click', function () {
                    completarTexto(sugestao);
                });
                balaoSugestao.appendChild(sugestaoElemento);
            });

            // Exibe o balão de sugestão se houver sugestões filtradas
            if (sugestoesFiltradas.length > 0) {
                balaoSugestao.style.display = 'block';
                balaoSugestao.style.top = campoTexto.offsetTop + campoTexto.offsetHeight + 'px';
                balaoSugestao.style.left = campoTexto.offsetLeft + 'px';
                return;
            }
        }

        // Se não houver sugestões ou texto incompleto, oculta o balão de sugestão
        balaoSugestao.style.display = 'none';
    }

// Evento input para monitorar a digitação no campo de texto
    campoTexto.addEventListener('input', function () {
        mostrarSugestoes();
        
        // Destativa os botões
        document.getElementById("btdesenhaautomato").disabled = true;
        document.getElementById("btexecutaanaliselex").disabled = true;
    });

    // Função para completar o texto do campo com a sugestão selecionada
    function completarTexto(sugestao) {
        var textoAtual = campoTexto.value; // Texto atual do campo
        var textoAntesCursor = textoAtual.substring(0, campoTexto.selectionStart); // Texto antes do cursor
        var textoDepoisCursor = textoAtual.substring(campoTexto.selectionEnd); // Texto depois do cursor

        // Encontrar a última parte digitada no texto atual
        var partes = textoAntesCursor.split(';');
        partes.pop(); // Remove a última parte incompleta
        partes.push(sugestao); // Adiciona a sugestão completa

        // Atualizar o texto do campo
        var novoTexto = partes.join('; ') + textoDepoisCursor;

        campoTexto.value = novoTexto;

        // Reposicionar o cursor
        campoTexto.selectionStart = campoTexto.selectionEnd = novoTexto.length - textoDepoisCursor.length;

        // Ocultar o balão de sugestão
        balaoSugestao.style.display = 'none';
    }

    // Fechar o balão de sugestão ao clicar em qualquer lugar fora dele
    document.addEventListener('click', function (event) {
        if (!balaoSugestao.contains(event.target) && event.target !== campoTexto) {
            balaoSugestao.style.display = 'none';
        }
    });
    /*********************************Fim da parte das sugestões********************************/



});


function analisaExpRegulares() {
    var defreg = $("#defReg").val();
    var dataToSend = JSON.stringify({
        "texto": defreg
    });
    $.getJSON(getBaseURL() + "index.php?classe=ControllerExpRegulares&metodo=analisaExpressoes" + "&dados=" + encodeURIComponent(dataToSend), function (result) {
        $("#saidaDefErros").val();
        $("#saidaDefErros").val(JSON.parse(result).texto);
    });
}

/**
 * Método que constroi o automato finito das expressões regulares 
 * preenchendo a tabela do automato salvando em csv para ser utilizada na análise léxica
 * @returns tela do automato finito
 */
function loadTabLexica() {
    // Exibe a mensagem de carregamento
    $("#mensagemCarregando").show();
    var defreg = $("#defReg").val();
    var dataToSend = JSON.stringify({
        "texto": defreg
    });
    $.getJSON(getBaseURL() + "index.php?classe=ControllerExpRegulares&metodo=geradorTabelaAutomatoFinito" + "&dados=" + encodeURIComponent(dataToSend), function (result) {
        $("#saidaDefErros").val(JSON.parse(result).texto);
    });

    // Ativa os botões
    document.getElementById("btdesenhaautomato").disabled = false;
    document.getElementById("btexecutaanaliselex").disabled = false;

    //Abre a modal
    openModalTabLex(dataToSend);
}

/*
 * Responsável por chamar a classe para abrir a tela modal e apresentar os resultados da tabela de análise léxica
 */
function openModalTabLex(dataToSend) {
    var div = document.getElementById('csvData');
    div.innerHTML = '';
    document.getElementById("myModal").style.display = "block";
    setTimeout(function () {
        $.getJSON(getBaseURL() + "index.php?classe=ControllerExpRegulares&metodo=mostraModalTabelaLexica" + "&dados=" + encodeURIComponent(dataToSend), function (result) {
            var div = document.getElementById('csvData');
            // Depois de obter o resultado, oculta a mensagem de carregamento
            $("#mensagemCarregando").hide();
            // Altera o conteúdo da div
            div.innerHTML = result;
        });
    }, 5000);
}

/*
 * Método responsável por fechar a modal da tabela de análise léxica
 */
function closeModal() {
    var div = document.getElementById('csvData');
    div.innerHTML = '';
    var modal = document.getElementById('myModal');
    modal.style.display = 'none';
}

/*
 * Método responsável por chamar a classe de análise léxica
 */
function analiseLexica() {
    // Exibe a mensagem de carregamento
    $("#mensagemCarregando").show();
    var defreg = $("#codTest").val();
    var dataToSend = JSON.stringify({
        "texto": defreg
    });
    $.getJSON(getBaseURL() + "index.php?classe=ControllerAnalisadorLexico&metodo=analiseLexica" + "&dados=" + encodeURIComponent(dataToSend), function (result) {
        $("#saidaAnalise").val(JSON.parse(result).texto);
    });
    //Abre a modal
    openModalResLex(dataToSend);
}

/*
 * Responsável por chamar a classe para abrir a tela modal e apresentar os resultados da tabela de análise léxica
 */
function openModalResLex(dataToSend) {
    var div = document.getElementById('csvData2');
    div.innerHTML = '';
    document.getElementById("myModal2").style.display = "block";
    setTimeout(function () {
        $.getJSON(getBaseURL() + "index.php?classe=ControllerAnalisadorLexico&metodo=mostraModalResultadoAnaliseLexica" + "&dados=" + encodeURIComponent(dataToSend), function (result) {
            var div = document.getElementById('csvData2');
            // Altera o conteúdo da div
            div.innerHTML = result;
            // Depois de obter o resultado, oculta a mensagem de carregamento
            $("#mensagemCarregando").hide();
        });
    }, 5000);
}

/*
 * Método responsável por fechar a modal da tabela de análise léxica
 */
function closeModal2() {
    var div = document.getElementById('csvData2');
    div.innerHTML = '';
    var modal = document.getElementById('myModal2');
    modal.style.display = 'none';
}

/**
 * Método que constroi o automato finito das expressões regulares gráficamente
 * com base na tabela de transição utilizada na análise léxica
 * @returns tela do automato finito
 */
function loadAutomato() {

    // Exibe a mensagem de carregamento
    $("#mensagemCarregando").show();

    var dataToSend = 'teste';

    $.getJSON(getBaseURL() + "index.php?classe=ControllerAutomato&metodo=gravaPaginaAutomato" + "&dados=" + encodeURIComponent(dataToSend), function (result) {
        //Abre a página com automato de análise léxica gráficamente conforme usuário
        window.open(getBaseURL() +  JSON.parse(result).texto + "/modalAutomato.html", "minhaJanela", "height=800,width=1000");
        // Depois de obter o resultado, oculta a mensagem de carregamento
        $("#mensagemCarregando").hide();
    });

}


function downloadTabela(event, nome) {
    // Cria um elemento de link temporário
    var link = document.createElement('a');

    // Define o atributo 'href' do link com o caminho para o script download.php e o nome do arquivo
    link.href = getBaseURL() + 'php/biblioteca/download.php?arquivo=' + encodeURIComponent(nome);

    // Define o atributo 'target' para '_blank' para abrir o link em uma nova janela/tab
    link.target = '_blank';

    // Simula um clique no link para iniciar o download
    link.click();
}

/**
 * Analisa a gramática digitada pelo usuário
 */
//function analisaGramatica() {
//    var defgram = $("#defGram").val();
//    var dataToSend = JSON.stringify({
//        "texto": defgram
//    });
//    $.getJSON(getBaseURL() + "index.php?classe=ControllerGramatica&metodo=analisaGramatica" + "&dados=" + encodeURIComponent(dataToSend), function (result) {
//        $("#saidaDefErros").val(JSON.parse(result).texto);
//    });
//}
//
//function loadFirstFollow() {
//    var defgram = $("#defGram").val();
//    var dataToSend = JSON.stringify({
//        "texto": defgram
//    });
//    $.getJSON(getBaseURL() + "index.php?classe=ControllerGramatica&metodo=geradorFirstFollow" + "&dados=" + encodeURIComponent(dataToSend), function (result) {
//        $("#saidaDefErros").val(JSON.parse(result).texto);
//    });
//    //Abre a modal
//    // openModalTabLex(dataToSend);
//}

/**
 * Abre a modal de logout do sistema
 */
function openModalSair() {
    document.getElementById("modalSair").style.display = "block";
}

/*
 * Fecha a modal de sair do sistema
 */
function closeModalSair() {
    document.getElementById("modalSair").style.display = "none";
}

/**
 * Realiza o Logout do sistema
 */
function logout() {
    closeModalSair();
    var dataToSend = JSON.stringify({
        "texto": "logout"
    });
    console.log(getBaseURL());
    $.getJSON(getBaseURL() + "index.php?classe=ControllerSistema&metodo=realizaLogout" + "&dados=" + encodeURIComponent(dataToSend), function (result) {
    });
    window.location.href = 'index.php';
}


/**
 * Abre a modal de exclusão dos dados da tela
 */
function openModalexcluirDadosTela() {
    document.getElementById("modalExcluirDados").style.display = "block";
}

/**
 * Fecha a modal de exclusão dos dados da tela
 */
function closeExcluirDados(){
    document.getElementById("modalExcluirDados").style.display = "none";
}

/**
 * Exclui os dados da tela
 */
function excluirDados(){
    document.getElementById("defReg").value = "";
    document.getElementById("codTest").value = "";
    document.getElementById("modalExcluirDados").style.display = "none";
}

/**
 * Abre a modal de exclusão de usuário
 */
function openModalExcluirUsuario(){
    document.getElementById("modalExcluirUsuario").style.display = "block";
}

/**
 * Fecha a modal de exclusão dos dados do usuario
 */
function closeExcluirUsuario(){
    document.getElementById("modalExcluirUsuario").style.display = "none";
}

/**
 * Exclui os dados do usuário
 */
function excluirUsuario(){
    closeExcluirUsuario();
    var dataToSend = JSON.stringify({
        "texto": "excluirdados"
    });
    console.log(getBaseURL());
    $.getJSON(getBaseURL() + "index.php?classe=ControllerSistema&metodo=excluirUsuario" + "&dados=" + encodeURIComponent(dataToSend), function (result) {
        console.log(JSON.parse(result));
        // Verifica o retorno do JSON
        if (result.resultado === true) {
            // Se for true, exibe uma mensagem de sucesso
            alert("Dados deletados com sucesso");  
        } else {
            // Se for false, exibe uma mensagem de falha
            alert("Dados não deletados");
        }
    });  
    window.location.href = 'index.php';
}

/**
 * Mensagem de carregando no sistema
 */
function mostrarCarregando() {
    document.getElementById('loading').style.display = 'block';
}

// Função para esconder mensagem de carregamento
function esconderCarregando() {
    document.getElementById('loading').style.display = 'none';
}

//Função importante para retornar a base da url para acesso em servidores
function getBaseURL() {
    return window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '/');
}