
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?= $this->include('menu') ?>


    <h1>Cadastrar Receita</h1>

    <form action="<?= base_url('receita/salvar') ?>" method="post" enctype="multipart/form-data">

        <div>
            <label>Título</label><br>
            <input type="text" name="titulo" required>
        </div>

        <br>

        <div>
            <label>Imagem</label><br>
            <input type="file" name="imagem" accept="image/*">
        </div>

        <br>

        <div>
            <label>Pesquisar ingrediente</label><br>
            <input type="text" id="pesquisaIngrediente">
            <button type="button"   id="btnPesquisar">>Pesquisar</button>
        </div>

        <br>

        <div id="resultadoPesquisa">
            <!-- Resultados da API aparecerão aqui -->
        </div>

        <hr>

        <h3>Ingredientes adicionados</h3>

        <label>Ingrediente</label>

    <label>Pesquisar ingrediente</label>

    <input
        type="text"
        id="pesquisaIngrediente">

    <button
        type="button"
        id="btnPesquisar">

    Pesquisar

    </button>

    <br><br>

    <div id="resultadoPesquisa"></div>

    <hr>

    <p id="ingredienteSelecionadoTexto">
    Nenhum ingrediente selecionado.
    </p>

    <label>Quantidade (g)</label>

    <input
        type="number"
        id="quantidade">

    <button
        type="button"
        id="btnAdicionar">

    Adicionar

    </button>

    <br><br>



    </button>

        <table border="1" width="100%" id="tabelaIngredientes">
            <thead>
                <tr>
                    <th>Ingrediente</th>
                    <th>Quantidade (g)</th>
                    <th>Ação</th>
                </tr>
            </thead>

            <tbody id="listaIngredientes">

            </tbody>
        </table>

        <div id="inputsOcultos"></div>

        <br>

        <label>Modo de preparo</label><br>

        <textarea
            name="legenda"
            rows="8"
            cols="70"
            placeholder="Descreva o modo de preparo..."></textarea>

        <br><br>
    <h3>Tags</h3>

    <?php foreach ($tags as $tag): ?>

        <label>
            <input
                type="checkbox"
                name="tags[]"
                value="<?= $tag['idTag']; ?>">

            <?= esc($tag['nome']); ?>
        </label>

        <br>

    <?php endforeach; ?>

    <br>

    <button type="submit">
        Salvar Receita
    </button>

    </form>

    <script>

    const btnAdicionar = document.getElementById("btnAdicionar");

    const btnPesquisar = document.getElementById("btnPesquisar");

    btnPesquisar.addEventListener(
        "click",
        pesquisarIngrediente
    );

    btnAdicionar.addEventListener("click", adicionarIngrediente);
    let ingredientes = [];

    async function pesquisarIngrediente(){
        const pesquisa = document
        .getElementById("pesquisaIngrediente")
        .value;

    }
    function adicionarIngrediente(){

        const ingrediente =
            document.getElementById("ingrediente").value;

        const quantidade =
            document.getElementById("quantidade").value;

        if(ingrediente == "" || quantidade == ""){

            alert("Preencha os dois campos.");

            return;

        }

    
        const inputs = document.getElementById("inputsOcultos");

    ingredientes.push({

        idApi: ingredienteSelecionado.id,

        nome: ingredienteSelecionado.nome,

        imagem: ingredienteSelecionado.imagem,

        quantidade: quantidade,

        calorias: ingredienteSelecionado.calorias,

        carboidratos: ingredienteSelecionado.carboidratos,

        proteinas: ingredienteSelecionado.proteinas,

        gorduras: ingredienteSelecionado.gorduras

    });

    atualizarTela();

    document.getElementById("ingrediente").value = "";

    document.getElementById("quantidade").value = "";

    }

    function atualizarTela(){

        const tabela = document.getElementById("listaIngredientes");
        const inputs = document.getElementById("inputsOcultos");

        tabela.innerHTML = "";
        inputs.innerHTML = "";

        ingredientes.forEach(function(item, indice){

            tabela.innerHTML += `

                <tr>

                    <td>${item.nome}</td>

                    <td>${item.quantidade} g</td>

                    <td>

                        <button
                            type="button"
                            onclick="removerIngrediente(${indice})">

                            Remover

                        </button>

                    </td>

                </tr>

            `;

            inputs.innerHTML += `

                <input
                    type="hidden"
                    name="ingredientes[]"
                    value="${item.nome}">

                <input
                    type="hidden"
                    name="quantidades[]"
                    value="${item.quantidade}">

            `;

        });

    }


    function removerIngrediente(indice){

        ingredientes.splice(indice,1);

        atualizarTela();

    }
    </script>
</body>
</html>


