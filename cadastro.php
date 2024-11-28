<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="forms.css">
    <title>Cadastrar Usuário</title>
</head>

<body>
    <div class="form-container">

        <h2>Cadastro</h2>

        <form action="cadastro.php" method="post">
            <select>
                <option selected disabled value="">Selecionar Usuário</option>
                <option value="colaborador">Colaboradores</option>
                <option value="adm">Adm</option>
                <option value="doador">Doador</option>
            </select>
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>
            <label for="email">Email Educacional:</label>
            <input type="email" id="email" name="email" required>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
            <button type="submit">Cadastrar</button>

        </form>

    </div>
</body>

</html>