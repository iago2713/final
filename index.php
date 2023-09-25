<!DOCTYPE html>
<html>
<head>
    <title>Agenda de Contatos</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Agenda de Contatos</h1>

    <?php
    $conexao = new mysqli("localhost", "root", "", "agendecontatos");

    if ($conexao->connect_error) {
        die("Conexão falhou: " . $conexao->connect_error);
    }

    function listarContatos($conexao) {
        $sql = "SELECT * FROM contatos";
        $result = $conexao->query($sql);

        if ($result->num_rows > 0) {
            echo "<h2>Listagem de Contatos</h2>";
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                $codigo = $row['código'];
                $nome = $row['nome'];
                $telefone = $row['telefone'];
                $email = $row['e-mail'];

                echo "<li>{$nome} - {$telefone} - {$email} [<a href='?acao=editar&codigo={$codigo}'>Editar</a>] [<a href='?acao=excluir&codigo={$codigo}'>Excluir</a>]</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Nenhum contato encontrado.</p>";
        }
    }

    listarContatos($conexao);

    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["acao"])) {
        $acao = $_GET["acao"];

        if ($acao == "editar" && isset($_GET["codigo"])) {
            $codigo = $_GET["codigo"];
            $sql = "SELECT * FROM contatos WHERE código = $codigo";
            $result = $conexao->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo "<h2>Editar Contato</h2>";
                echo "<form method='post'>";
                echo "<input type='hidden' name='codigo' value='{$row['código']}'>";
                echo "Nome: <input type='text' name='nome' value='{$row['nome']}'><br>";
                echo "Telefone: <input type='text' name='telefone' value='{$row['telefone']}'><br>";
                echo "E-mail: <input type='text' name='email' value='{$row['e-mail']}'><br>";
                echo "<input type='submit' value='Salvar Edição'>";
                echo "</form>";
            } else {
                echo "<p>Contato não encontrado.</p>";
            }
        } elseif ($acao == "excluir" && isset($_GET["codigo"])) {
            $codigo = $_GET["codigo"];
            $sql = "DELETE FROM contatos WHERE código = $codigo";
            if ($conexao->query($sql) === TRUE) {
                header("Location: index.php");
                exit;
            } else {
                echo "<p>Erro ao excluir contato: " . $conexao->error . "</p>";
            }
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nome"]) && isset($_POST["email"])) {
        $nome = $_POST["nome"];
        $telefone = $_POST["telefone"];
        $email = $_POST["email"];

        // Verifica se nome e email estão preenchidos
        if (empty($nome) || empty($email)) {
            echo "<p>Nome e E-mail são campos obrigatórios.</p>";
        } else {
            // Verifica se o telefone está em branco
            if (empty($telefone)) {
                echo "<p>O telefone é um campo obrigatório.</p>";
            } else {
                if (isset($_POST["codigo"])) {
                    $codigo = $_POST["codigo"];
                    $sql = "UPDATE contatos SET nome = '$nome', telefone = '$telefone', `e-mail` = '$email' WHERE código = $codigo";
                } else {
                    $sql = "INSERT INTO contatos (nome, telefone, `e-mail`) VALUES ('$nome', '$telefone', '$email')";
                }

                if ($conexao->query($sql) === TRUE) {
                    if (isset($_POST["codigo"])) {
                        echo "<p>Contato atualizado com sucesso.</p>";
                    } else {
                        echo "<p>Contato inserido com sucesso.</p>";
                    }
                } else {
                    echo "<p>Erro ao salvar contato: " . $conexao->error . "</p>";
                }

                header("Location: index.php");
                exit;
            }
        }
    }

    $conexao->close();
    ?>

    <h2>Incluir Contato</h2>
    <form method="post">
        Nome: <input type="text" name="nome"><br>
        Telefone: <input type="text" name="telefone"><br>
        E-mail: <input type="text" name="email"><br>
        <input type="submit" value="Incluir">
    </form>
</body>
</html>