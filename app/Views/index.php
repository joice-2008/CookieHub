
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CookieHub</title>
    <link rel="stylesheet" href="<?= base_url('css/index.css') ?>">
</head>
<body>
    <div class="container">
        <div class="icone">
            <img src="<?= base_url('imagens/iconeIndex.png') ?>" alt="Logo">
        </div>
        <div class='logoMaior'>
            <p id="titulo1">Cookie</p><p id="titulo2">Hub</p>
        </div>
        <div class="apresentacao">
            <p class="textoApresentacao">O seu novo repositório de receitas</p>
            <p class="textoApresentacao">que une organização e praticidade em um só lugar!</p>
        </div>
        <div class="links">
            <div class="link">
                <a href="<?= base_url('login') ?>">Entrar</a>
            </div>
            <div class="link">
                <a href="<?= base_url('cadastro') ?>">Cadastrar-se</a>
            </div>
        </div>
    </div>
</body>
</html>