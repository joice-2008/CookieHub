

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CookieHub</title>
    <link rel="stylesheet" href="<?= base_url('css/login.css') ?>">
</head>
<body>
    <div class="container">
        <div>
            <p class="titulo">Faça login no CookieHub</p>
        </div>
        <form action="<?= site_url('usuario/logar') ?>" method="post">
        <div>
            <label for="usuario" class="label">Usuário</label>
            <input type="text" class="campo" id="usuario" name="usuario">
        </div>
        <div>
            <label for="senha" class="label">Senha</label>
            <input type="password" class="campo" id="senha" name="senha">
        </div>
        <div>
            <input type="submit" value="Entrar" id="botao">
        </div>
        </form>
    </div>
    
</body>
</html>