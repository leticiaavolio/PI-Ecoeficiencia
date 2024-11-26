<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div class="form-container"> 

    <h2>Troca</h2> 

    <form action="troca.php" method="post"> 

        <label for="email">Email Educacional:</label> 
        <input type="email" id="email" name="email" required> 
        <label for="classificacao">Classificação do Objeto Escolhido:</label> 
        <input type="text" id="classificacao" name="classificacao" required> 
        <label for="quantidade">Quantidade:</label> 
        <input type="number" id="quantidade" name="quantidade" min="1" required> 

        <button type="submit">Trocar</button> 

    </form> 

</div> 
</body>
</html>