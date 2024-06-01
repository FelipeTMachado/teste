function mostrarSenhaCadastro() {
    var senha = document.getElementById("passCadastro");
    if (senha.type === "password") {
        senha.type = "text";
    } else {
        senha.type = "password";
    }
}

function mostrarConfSenhaCadastro() {
    var confSenha = document.getElementById("confPassCadastro");
    if (confSenha.type === "password") {
        confSenha.type = "text";
    } else {
        confSenha.type = "password";
    }
}

function validarSenhas() {
    var senha = document.getElementById("passCadastro").value;
    var confSenha = document.getElementById("confPassCadastro").value;
    var erroMensagem = document.getElementById("senhaErro");
    var senhaInput = document.getElementById("passCadastro");
    var confSenhaInput = document.getElementById("confPassCadastro");

    if (senha !== confSenha) {
        erroMensagem.style.display = "block";
        senhaInput.classList.add("error");
        confSenhaInput.classList.add("error");
        return false;
    } else {
        erroMensagem.style.display = "none";
        senhaInput.classList.remove("error");
        confSenhaInput.classList.remove("error");
        return true;
    }
}

document.getElementById("passCadastro").addEventListener("input", validarSenhas);
document.getElementById("confPassCadastro").addEventListener("input", validarSenhas);

function exibirToast(mensagem) {
    var toastContainer = document.getElementById("toast-container");
    var toast = document.createElement("div");
    toast.className = "toast";
    toast.innerText = mensagem;
    toastContainer.appendChild(toast);
    setTimeout(function() {
        toast.remove();
    }, 3000);
}

function mostrarLogin() {
    window.location.href = 'index.php';
}

function enviarDadosCadastro() {
    if (validarSenhas()) {
        var formData = {
            email: $("#emailCadastro").val(),
            senha: $("#passCadastro").val(),
            confSenha: $("#confPassCadastro").val()
        };

        $.ajax({
            url: "index.php?classe=ControllerSistema&metodo=cadastraUsuario&dados=cadastraUsuario",
            type: "POST",
            data: formData, 
            success: function(response) {
                //Retorna para a página de login 
                mostrarLogin();
                // Exibe mensagem de sucesso ou redireciona o usuário
                exibirToast("Cadastro realizado com sucesso!");
            },
            error: function(xhr, status, error) {
                // Exibe mensagem de erro
                exibirToast("Erro ao realizar cadastro.");
            }
        });
    }
}

// Evento click no botão inicial cadastro
document.getElementById('btnCadastrar').addEventListener('click', enviarDadosCadastro);
