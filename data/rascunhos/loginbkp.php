<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/toast.css">
    <title>Tela de Login</title>
</head>
<body>
    <div class="container">
        <form class="login-form" action="index.php" method="POST">
            <h1>SELSS</h1>
            <div class="input-container">
                <label for="email">Login Email</label>
                <input name="email" type="email" id="email" placeholder="Digite seu email" required>
            </div>
            <div class="input-container">
                <label for="pass">Senha</label>
                <input type="password" name="pass" id="pass" placeholder="Digite sua senha" required>
                <img src="https://cdn0.iconfinder.com/data/icons/ui-icons-pack/100/ui-icon-pack-14-512.png" id="olho" class="olho eye-icon" onclick="mostrarSenha()">
            </div>
            <input type="hidden" name="modo" value="convidado"> <!-- Adiciona um campo oculto para o modo "convidado" -->
            <button type="submit">Entrar</button>
            <br>
            <button type="button" id="btnConvidado">Convidado</button>
        </form>
    </div>
    <div id="toast-container"></div>
    <script src="js/toast.js"></script>
</body>
</html>
