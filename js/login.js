//Função para mostrar a senha oculta ao clicar no olho
function mostrarSenha() {
    var senhaInput = document.getElementById("pass");
    var olhoIcon = document.getElementById("olho");

    if (senhaInput.type === "password") {
        senhaInput.type = "text";
        olhoIcon.src = "https://cdn0.iconfinder.com/data/icons/ui-icons-pack/100/ui-icon-pack-15-512.png"; // ícone de olho aberto
    } else {
        senhaInput.type = "password";
        olhoIcon.src = "https://cdn0.iconfinder.com/data/icons/ui-icons-pack/100/ui-icon-pack-14-512.png"; // ícone de olho fechado
    }
}

//Evento click no botão inicial convidado
document.getElementById('btnConvidado').addEventListener('click', function() {
    // Atualiza o valor do campo oculto "modo"
    document.querySelector('input[name="modo"]').value = "convidado";
    document.querySelector('.login-form').submit();
});

//Evento click no botão inicial entrar
document.getElementById('btnEntrar').addEventListener('click', function() {
    // Atualiza o valor do campo oculto "modo"
    document.querySelector('input[name="modo"]').value = "entrar";
    document.querySelector('.login-form').submit();
});

//Evento click no botão inicial cadastro
document.getElementById('btnCadastro').addEventListener('click', function() {
    // Atualiza o valor do campo oculto "modo"
    document.querySelector('input[name="modo"]').value = "cadastro";
    document.querySelector('.login-form').submit();
});