<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="forms.css">
    <title>Doação</title>
</head>
<body>
<div class="form-container"> 

    <h2>Doação</h2> 

    <form action="doacao.php" method="post"> 

        <label for="email">Email Educacional:</label> 

        <input type="email" id="email" name="email" required> 

 

        <label for="classificacao">Classificação do Objeto Doado:</label> 

        <input type="text" id="classificacao" name="classificacao" required> 

        <label for="quantidade">Quantidade:</label> 

        <input type="number" id="quantidade" name="quantidade" min="1" required> 

        <button type="submit">Doar</button> 

    </form> 

</div> 
</body>
</html>