<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= esc($receita['titulo']) ?> - CookieHub
    </title>

    <link
        rel="stylesheet"
        href="<?= base_url('css/menu.css') ?>"
    >

    <link rel="stylesheet" href="<?= base_url('css/visualizarReceita.css') ?>">

</head>

<body>

    <?= $this->include('menu') ?>


    <div class="pagina-receita">

        <h1>
            <?= esc($receita['titulo']) ?>
        </h1>

        <p>
            Publicado por:
            <?= esc($receita['nomeUsuario']) ?>
        </p>


        <div class="tags">

            <?php foreach ($receita['nomesTags'] as $tag): ?>

                <span>
                    <?= esc($tag) ?>
                </span>

            <?php endforeach; ?>

        </div>


        <?php if (!empty($receita['imagem'])): ?>

            <img
                src="<?= base_url('uploads/' . $receita['imagem']) ?>"
                alt="<?= esc($receita['titulo']) ?>"
                class="imagem-receita"
            >

        <?php endif; ?>


        <section class="secao-ingredientes">

            <h2>Ingredientes</h2>

            <?php if (!empty($receita['ingredientes'])): ?>

                <div class="lista-ingredientes">

                    <?php foreach ($receita['ingredientes'] as $indice => $ingrediente): ?>

                        <div class="ingrediente">

                            <span class="nome-ingrediente">
                                <?= esc(ucfirst($ingrediente)) ?>
                            </span>

                            <span class="quantidade-ingrediente">

                                <?php if (isset($receita['quantidadeIngredientes'][$indice])): ?>

                                    <?= esc($receita['quantidadeIngredientes'][$indice]) ?> g

                                <?php endif; ?>

                            </span>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <p class="sem-informacoes">
                    Ingredientes não disponíveis para esta receita.
                </p>

            <?php endif; ?>

        </section>

        <section>

            <h2>Modo de preparo</h2>

            <p>
                <?= nl2br(esc($receita['legenda'])) ?>
            </p>

        </section>


       <section class="secao-nutricional">

            <h2>Informações nutricionais</h2>

            <?php if (!empty($receita['infosNutricionais'])): ?>

                <div class="nutrientes">

                    <div class="nutriente">
                        <strong>
                            <?= number_format($totalCalorias, 0, ',', '.') ?>
                        </strong>
                        <span>kcal</span>
                    </div>

                    <div class="nutriente">
                        <strong>
                            <?= number_format($totalProteinas, 1, ',', '.') ?> g
                        </strong>
                        <span>Proteínas</span>
                    </div>

                    <div class="nutriente">
                        <strong>
                            <?= number_format($totalCarboidratos, 1, ',', '.') ?> g
                        </strong>
                        <span>Carboidratos</span>
                    </div>

                    <div class="nutriente">
                        <strong>
                            <?= number_format($totalGorduras, 1, ',', '.') ?> g
                        </strong>
                        <span>Gorduras</span>
                    </div>

                </div>

            <?php else: ?>

                <p class="sem-informacoes">
                    Informações nutricionais não disponíveis para esta receita.
                </p>

            <?php endif; ?>

        </section>

    </div>

</body>

</html>