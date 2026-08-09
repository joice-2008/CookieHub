<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CookieHub</title>
    <link rel="stylesheet" href="<?= base_url('css/menu.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/cadastroReceita.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/edicao.css') ?>">
</head>
<body>

    <?= $this->include('menu') ?>


    <?php if (session()->getFlashdata('erro')): ?>
                <div class="mensagem erro">
                    <?= session()->getFlashdata('erro') ?>
                </div>
            <?php endif; ?>
            
    <div class="cadastro-container">

        <div class="cabecalho-cadastro">
            

            <h1>Editar receita</h1>

            <p>
                Altere as informações da sua receita.
            </p>

        </div>


        <form
            action="<?= base_url('receita/atualizar/' . $receita['idReceita']) ?>"
            method="post"
            enctype="multipart/form-data"
            class="form-receita"
        >



            <section class="secao-form">

                <h2>Informações da receita</h2>

                <p class="descricao-secao">
                    Edite o título e a imagem da sua receita.
                </p>


                <div class="campo">

                    <label for="titulo">
                        Título
                    </label>

                    <input
                        type="text"
                        id="titulo"
                        name="titulo"
                        value="<?= esc($receita['titulo']) ?>"
                        required
                    >

                </div>


                <div class="campo">

                    <label>
                        Imagem
                    </label>

                    <?php if (!empty($receita['imagem'])): ?>

                        <div class="imagem-atual">

                            <img
                                src="<?= base_url('uploads/' . $receita['imagem']) ?>"
                                alt="<?= esc($receita['titulo']) ?>"
                            >

                        </div>

                    <?php endif; ?>


                    <div class="upload-imagem">

                        <div class="icone-upload">
                            📷
                        </div>

                        <div>

                            <strong>
                                Alterar imagem
                            </strong>

                            <small>
                                Clique para selecionar uma nova imagem
                            </small>

                        </div>

                        <input
                            type="file"
                            name="imagem"
                            accept="image/*"
                        >

                    </div>

                </div>

            </section>



            <section class="secao-form">

                <h2>Ingredientes</h2>

                <p class="descricao-secao">
                    Adicione, remova ou altere os ingredientes da receita.
                </p>



                <div class="pesquisa-ingrediente">

                    <div class="campo">

                        <label for="pesquisaIngrediente">
                            Pesquisar ingrediente
                        </label>

                        <input
                            type="text"
                            id="pesquisaIngrediente"
                            placeholder="Digite o nome do ingrediente"
                        >

                    </div>

                    <button
                        type="button"
                        class="btn-principal"
                        id="btnPesquisar"
                    >
                        Pesquisar
                    </button>

                </div>


                <!-- Resultados da pesquisa -->

                <div
                    id="resultadoPesquisa"
                    class="resultado-pesquisa"
                ></div>


                <!-- Ingrediente selecionado -->

                <div
                    id="ingredienteSelecionadoTexto"
                    class="ingrediente-selecionado"
                >
                    <strong>Nenhum ingrediente selecionado.</strong>
                </div>


                <!-- Quantidade -->

                <div class="adicionar-ingrediente">

                    <div class="campo">

                        <label for="quantidade">
                            Quantidade (g)
                        </label>

                        <input
                            type="number"
                            id="quantidade"
                            min="1"
                            placeholder="Ex.: 100"
                        >

                    </div>

                    <button
                        type="button"
                        class="btn-adicionar"
                        id="btnAdicionar"
                    >
                        Adicionar
                    </button>

                </div>


                <!-- Tabela -->

                <div class="tabela-container">

                    <table id="tabelaIngredientes">

                        <thead>

                            <tr>

                                <th>
                                    Ingrediente
                                </th>

                                <th>
                                    Quantidade (g)
                                </th>

                                <th>
                                    Ação
                                </th>

                            </tr>

                        </thead>

                        <tbody id="listaIngredientes">

                        </tbody>

                    </table>

                </div>


                <!-- Inputs enviados para o controller -->

                <div id="inputsOcultos"></div>

            </section>


            <!-- MODO DE PREPARO -->

            <section class="secao-form">

                <h2>Modo de preparo</h2>

                <p class="descricao-secao">
                    Edite o modo de preparo da receita.
                </p>


                <div class="campo">

                    <textarea
                        name="legenda"
                        rows="8"
                    ><?= esc($receita['legenda']) ?></textarea>

                </div>

            </section>


            <!-- TAGS -->

            <section class="secao-form">

                <h2>Tags</h2>

                <p class="descricao-secao">
                    Selecione as características da sua receita.
                </p>


                <div class="lista-tags">

                    <?php foreach ($tags as $tag): ?>

                        <label class="tag-checkbox">

                            <input
                                type="checkbox"
                                name="tags[]"
                                value="<?= $tag['idTag'] ?>"
                                <?= in_array($tag['idTag'], $idsTags) ? 'checked' : '' ?>
                            >

                            <span>
                                <?= esc($tag['nome']) ?>
                            </span>

                        </label>

                    <?php endforeach; ?>

                </div>

            </section>


            <!-- BOTÃO -->

            <div class="acoes-form">

                <button
                    type="submit"
                    class="btn-salvar"
                >
                    Salvar alterações
                </button>

            </div>


        </form>

    </div>

    <script>

    let ingredientes = <?= json_encode(
        $receita['ingredientes'] ?? [],
        JSON_UNESCAPED_UNICODE
    ) ?>;

    let quantidades = <?= json_encode(
        $receita['quantidadeIngredientes'] ?? [],
        JSON_UNESCAPED_UNICODE
    ) ?>;

    let infosNutricionais = <?= json_encode(
        !empty($receita['infosNutricionais'])
            ? json_decode($receita['infosNutricionais'], true)
            : [],
        JSON_UNESCAPED_UNICODE
    ) ?>;



    let ingredienteSelecionado = null;
    let resultadosPesquisa = [];

    ingredientes = ingredientes.map(function(nome, indice) {

        let info = infosNutricionais[indice] ?? {};

        return {

            idApi: info.idApi ?? null,

            nome: nome,

            quantidade: quantidades[indice] ?? 0,

            calorias: info.calorias ?? 0,

            proteinas: info.proteinas ?? 0,

            carboidratos: info.carboidratos ?? 0,

            gorduras: info.gorduras ?? 0

        };

    });

    const btnPesquisar = document.getElementById("btnPesquisar");
    const btnAdicionar = document.getElementById("btnAdicionar");

    const txtPesquisa = document.getElementById("pesquisaIngrediente");
    const txtQuantidade = document.getElementById("quantidade");

    const divResultados = document.getElementById("resultadoPesquisa");
    const textoSelecionado = document.getElementById(
        "ingredienteSelecionadoTexto"
    );

    const tabela = document.getElementById("listaIngredientes");
    const inputsOcultos = document.getElementById("inputsOcultos");


    btnPesquisar.addEventListener(
        "click",
        pesquisarIngrediente
    );

    btnAdicionar.addEventListener(
        "click",
        adicionarIngrediente
    );


    async function pesquisarIngrediente() {

        const pesquisa = txtPesquisa.value.trim();

        if (pesquisa === "") {

            alert("Digite um ingrediente.");

            return;
        }


        const resposta = await fetch(
            "<?= base_url('api/pesquisar/') ?>" +
            encodeURIComponent(pesquisa)
        );


        const resultados = await resposta.json();

        resultadosPesquisa = resultados;

        mostrarResultadosPesquisa(resultados);
    }

        function mostrarResultadosPesquisa(resultados) {

        divResultados.innerHTML = "";

        if (resultados.length === 0) {

            divResultados.innerHTML =
                "<p>Nenhum ingrediente encontrado.</p>";

            return;
        }


        resultados.forEach(function(item) {

            divResultados.innerHTML += `

                <div>

                    <span>
                        ${item.nome}
                    </span>

                    <button
                        type="button"
                        onclick="selecionarIngrediente(${item.id})"
                    >
                        Selecionar
                    </button>

                </div>

            `;

        });
    }

    async function selecionarIngrediente(id) {

        ingredienteSelecionado =
            resultadosPesquisa.find(function(item) {

                return item.id == id;

            });


        if (!ingredienteSelecionado) {

            return;
        }


        const resposta = await fetch(
            "<?= base_url('api/informacoes/') ?>" +
            ingredienteSelecionado.id
        );


        const dadosNutricionais =
            await resposta.json();


        ingredienteSelecionado.calorias =
            dadosNutricionais.calorias ?? 0;

        ingredienteSelecionado.proteinas =
            dadosNutricionais.proteinas ?? 0;

        ingredienteSelecionado.carboidratos =
            dadosNutricionais.carboidratos ?? 0;

        ingredienteSelecionado.gorduras =
            dadosNutricionais.gorduras ?? 0;


        textoSelecionado.innerHTML = `

            <strong>Ingrediente:</strong>
            ${ingredienteSelecionado.nome}

            <br>

            <small>
                ${ingredienteSelecionado.calorias}
                kcal por 100 g
            </small>

        `;


        divResultados.innerHTML = "";

    }

    function atualizarTela() {

        atualizarTabelaIngredientes();

        atualizarInputsOcultos();

    }

    function atualizarTabelaIngredientes() {

        tabela.innerHTML = "";


        ingredientes.forEach(function(item, indice) {

            tabela.innerHTML += `

                <tr>

                    <td>
                        ${item.nome.charAt(0).toUpperCase() + item.nome.slice(1)}
                    </td>

                    <td>
                        ${item.quantidade} g
                    </td>

                    <td>

                        <button
                            type="button"
                            onclick="removerIngrediente(${indice})"
                        >
                            Remover
                        </button>

                    </td>

                </tr>

            `;

        });

    }

    function atualizarInputsOcultos() {

        inputsOcultos.innerHTML = "";


        ingredientes.forEach(function(item, indice) {

            inputsOcultos.innerHTML += `

                <input
                    type="hidden"
                    name="ingredientes[${indice}][idApi]"
                    value="${item.idApi ?? ''}"
                >

                <input
                    type="hidden"
                    name="ingredientes[${indice}][nome]"
                    value="${item.nome}"
                >

                <input
                    type="hidden"
                    name="ingredientes[${indice}][quantidade]"
                    value="${item.quantidade}"
                >

                <input
                    type="hidden"
                    name="ingredientes[${indice}][calorias]"
                    value="${item.calorias ?? 0}"
                >

                <input
                    type="hidden"
                    name="ingredientes[${indice}][proteinas]"
                    value="${item.proteinas ?? 0}"
                >

                <input
                    type="hidden"
                    name="ingredientes[${indice}][carboidratos]"
                    value="${item.carboidratos ?? 0}"
                >

                <input
                    type="hidden"
                    name="ingredientes[${indice}][gorduras]"
                    value="${item.gorduras ?? 0}"
                >

            `;

        });

    }

    function adicionarIngrediente() {

        const quantidade = txtQuantidade.value;


        if (ingredienteSelecionado === null) {

            alert("Selecione um ingrediente.");

            return;
        }


        if (quantidade === "") {

            alert("Informe a quantidade.");

            return;
        }


        ingredientes.push({

            idApi: ingredienteSelecionado.id,

            nome: ingredienteSelecionado.nome,

            quantidade: quantidade,

            calorias: ingredienteSelecionado.calorias ?? 0,

            proteinas: ingredienteSelecionado.proteinas ?? 0,

            carboidratos: ingredienteSelecionado.carboidratos ?? 0,

            gorduras: ingredienteSelecionado.gorduras ?? 0

        });


        atualizarTela();


        txtQuantidade.value = "";

        txtPesquisa.value = "";

        ingredienteSelecionado = null;


        textoSelecionado.innerHTML =
            "<strong>Nenhum ingrediente selecionado.</strong>";

    }

    function removerIngrediente(indice) {

        ingredientes.splice(indice, 1);

        atualizarTela();

    }

    atualizarTela();

    </script>

</body>

</html>