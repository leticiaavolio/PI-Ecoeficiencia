<?php 
include 'conexao.php';
session_start();

    if(!isset($_SESSION['usuario_id']) || !isset($_SESSION['perfil'])) {
   
        header("Location: index.php?page=verSaldo ");
        exit;
    }

    if ($_SESSION['perfil'] != "colaborador" || $_SESSION['perfil'] != "adm"){
        header("Location: index.php?page=verSaldo ");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") 
    {
        $coin = mysqli_real_escape_string($conn, $_POST["coin"]);
        $categorias = mysqli_real_escape_string($conn, $_POST["categorias"]);
        $quantidade = mysqli_real_escape_string($conn, $_POST["quantidade"]);


        $stmt = $conn->prepare("INSERT INTO produtos (coin, categorias, quantidade) VALUES (?, ?, ?)");

        $stmt->bind_param("ssi", $coin, $categorias, $quantidade);

        if ($stmt->execute()) {
            echo "Novo registro criado com sucesso";
        } else {
            echo "Erro: " . $stmt->error;
        }
    
        $stmt->close();
        
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doação</title>
    <link rel="stylesheet" href="forms.css">
</head>
<body>
<div class="form-container"> 

    <h2>Doação</h2> 

    <form action="doacao.php" method="post"> 

        <label for="number">senacoins:</label> 

        <input type="coin" id="coin" name="coin" required> 

 

        <label for="classificacao">Classificação do Objeto Doado:</label> 

        <form action="index.php">
            <select name="categorias" id="categorias">
                <option selected disable value = ""> Selecionar categoria</option>
                <option value="Acessorios">Acessorios -- 3 senacoins</option>
                <option value="Livros/DVD/CD/Disco">Livros/ DVDs/ CD/ Disco -- 3 senacoins</option>
                <option value="Utensilios de cozinha">Utensílios de cozinha -- 3 senacoins</option>                
                <option value="Artigos de decoracao">Artigos de decoração -- 3 senacoins</option>
                <option value="Vestuario e calçados">Vestuário e Calçados -- 5 senacoins</option>
                <option value="Brinquedos e jogos">Brinquedos e jogos -- 5 senacoins</option>
                <option value="Artigos automotivos">Artigos automotivos -- 6 senacoins</option>
                <option value="Eletronicos/ Eletrodomesticos">Eletrônicos/ Eletrodomésticos -- 10 senacoins</option>
                <option value="Mochilas">Mochilas -- 10 senacoins</option>
            </select>
        </form>

        <label for="quantidade">Quantidade:</label> 

        <input type="number" id="quantidade" name="quantidade" min="1" required> 

        <button type="submit">Doar</button> 

    </form> 

</div> 
</body>
</html>