<?php
$servername = "localhost";
$username = "root";
$password = "Senai@118";
$dbname = "teste_formulario";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) exit("Conexão falhou: " . $conn->connect_error);

$mensagem = "";
// Excluir Pokémon
if (isset($_POST['excluir']) && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("DELETE FROM Pokemon WHERE id = ?");
    $stmt->bind_param("i", $id);
    $mensagem = $stmt->execute()
        ? "<div class='mensagem-sucesso'>Pokémon liberado com sucesso!</div>"
        : "<div class='mensagem-erro'>Erro ao liberar Pokémon!</div>";
    $stmt->close();
}

// Cadastrar Pokémon
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['excluir'])) {
    $nome = trim($_POST['nome']);
    $tipo = trim($_POST['tipo']);

    if (empty($nome) || empty($tipo)) {
        $mensagem = "<div class='mensagem-erro'>Por favor, preencha todos os campos!</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO Pokemon (nome, tipo) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome, $tipo);
        $mensagem = $stmt->execute() 
            ? "<div class='mensagem-sucesso'>✨ Pokémon capturado com sucesso! ✨</div>"
            : "<div class='mensagem-erro'>Erro ao cadastrar: " . $stmt->error . "</div>";
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Cadastro de Pokémon</title>
<style>
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        margin: 0; padding: 0;
        background: url('pikapi.jpg') no-repeat center/cover;
        min-height: 100vh;
        display: flex; justify-content: center; align-items: center;
    }

    .grid-container {
        display: grid;
        grid-template-columns: 350px 550px;
        background-color: rgba(255,255,255,0.93);
        border-radius: 20px;
        box-shadow: 0 0 25px rgba(0,0,0,0.3);
        overflow: hidden;
        width: 90%; max-width: 950px;
        margin: 20px;
    }

    /* Painéis */
    .left-panel, .right-panel { padding: 25px; box-sizing: border-box; }
    .left-panel {
        background-color: #FFE4E1;
        display: flex; flex-direction: column; align-items: center;
    }
    .right-panel { background-color: white; display: flex; flex-direction: column; }

    /* Formulário */
    h2 {
        color: #FF0000;
        text-align: center;
        margin-bottom: 20px;
        font-weight: bold;
        font-size: 1.5rem;
    }
    form {
        background-color: #fff5f5;
        padding: 20px;
        border-radius: 10px;
        border: 2px solid #FF0000;
        width: 100%;
    }
    input[type="text"], select {
        width: 80%;
        padding: 10px;
        margin: 8px 0 15px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 15px;
    }
    input[type="text"]:focus, select:focus {
        border-color: #FF0000;
        outline: none;
        box-shadow: 0 0 5px rgba(255,0,0,0.3);
    }
    input[type="submit"] {
        background-color: #FF0000;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 25px;
        width: 100%;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }
    input[type="submit"]:hover { background-color: #cc0000; }

    /* Tabela */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 15px;
    }
    th {
        background-color: #FF0000;
        color: white;
        padding: 12px;
        text-align: left;
    }
    td {
        border-bottom: 2px solid #ddd;
        padding: 10px;
    }
    tr:hover { background-color: #f9f9f9; }

    /* Botões */
    .btn-container {
        display: flex;
        gap: 10px;
        justify-content: center;
    }
    .delete-btn, .edit-btn {
        border: none;
        background: none;
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    .delete-btn:hover, .edit-btn:hover {
        transform: scale(1.2);
    }
    .pokeball-icon, .edit-icon {
        width: 30px;
        height: 30px;
    }
    .edit-icon { filter: hue-rotate(150deg); }

    /* Tipos */
    .tipo-badge {
        padding: 5px 12px;
        border-radius: 15px;
        color: white;
        font-weight: bold;
        font-size: 13px;
        text-transform: uppercase;
    }
    .tipo-fogo { background-color: #F08030; }
    .tipo-agua { background-color: #6890F0; }
    .tipo-grama { background-color: #78C850; }
    .tipo-eletrico { background-color: #F8D030; }
    .tipo-normal { background-color: #A8A878; }
    .tipo-veneno { background-color: #A040A0; }
    .tipo-lutador { background-color: #C03028; }
    .tipo-fantasma { background-color: #705898; }
    .tipo-psiquico { background-color: #F85888; }
    .tipo-gelo { background-color: #98D8D8; }
    .tipo-pedra { background-color: #B8A038; }
    .tipo-aco { background-color: #B8B8D0; }
    .tipo-dragao { background-color: #7038F8; }
    .tipo-sombrio { background-color: #705848; }
    .tipo-fada { background-color: #EE99AC; }

    /* Mensagens */
    .mensagem-sucesso, .mensagem-erro {
        text-align: center;
        padding: 12px;
        border-radius: 8px;
        font-weight: bold;
        margin-top: 10px;
    }
    .mensagem-sucesso { background-color: #4CAF50; color: white; }
    .mensagem-erro { background-color: #f44336; color: white; }
</style>
</head>
<body>

<div class="grid-container">
    <!-- Painel Esquerdo -->
    <div class="left-panel">
        <img src="https://www.freepnglogos.com/uploads/pokemon-logo-png-0.png" width="180" style="margin-bottom:15px;">
        <h2>Cadastrar Pokémon</h2>

        <form method="POST">
            <label for="nome">Nome do Pokémon:</label><br>
            <input type="text" id="nome" name="nome" required placeholder="Ex: Pikachu">

            <label for="tipo">Tipo:</label><br>
            <select id="tipo" name="tipo" required>
                <option value="">Selecione o tipo...</option>
                <option value="Fogo">Fogo</option>
                <option value="Água">Água</option>
                <option value="Grama">Grama</option>
                <option value="Elétrico">Elétrico</option>
                <option value="Normal">Normal</option>
                <option value="Veneno">Veneno</option>
                <option value="Lutador">Lutador</option>
                <option value="Fantasma">Fantasma</option>
                <option value="Psíquico">Psíquico</option>
                <option value="Gelo">Gelo</option>
                <option value="Pedra">Pedra</option>
                <option value="Aço">Aço</option>
                <option value="Dragão">Dragão</option>
                <option value="Sombrio">Sombrio</option>
                <option value="Fada">Fada</option>
            </select>

            <input type="submit" value="Capturar Pokémon!">
        </form>
    </div>

    <!-- Painel Direito -->
    <div class="right-panel">
        <h2>Pokédex</h2>

        <?php
        $result = $conn->query("SELECT * FROM Pokemon ORDER BY id");

        if ($result->num_rows > 0) {
            echo "<table>
                    <tr><th>ID</th><th>Nome</th><th>Tipo</th><th>Ações</th></tr>";

            while ($row = $result->fetch_assoc()) {
                $tipoClass = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $row["tipo"]));
                $tipoClass = preg_replace('/[^a-z]/', '', $tipoClass);

                echo "<tr>
                        <td>#".str_pad($row["id"],3,'0',STR_PAD_LEFT)."</td>
                        <td>".htmlspecialchars($row["nome"])."</td>
                        <td><span class='tipo-badge tipo-".$tipoClass."'>".htmlspecialchars($row["tipo"])."</span></td>
                        <td class='btn-container'>

                            <form method='POST' onsubmit='return confirm(\"Tem certeza que deseja liberar este Pokémon?\");'>
                                <input type='hidden' name='id' value='".$row["id"]."'>
                                <button type='submit' name='excluir' class='delete-btn'>
                                    <img src='https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png' class='pokeball-icon' title='Excluir'>
                                </button>
                            </form>

                            <form method='GET' action='editar.php'>
                                <input type='hidden' name='id' value='".$row["id"]."'>
                                <button type='submit' class='edit-btn'>
                                    <img src='https://cdn-icons-png.flaticon.com/512/1159/1159633.png' class='edit-icon' title='Editar'>
                                </button>
                            </form>

                        </td>
                      </tr>";
            }

            echo "</table>";
        } else {
            echo "<p>Nenhum Pokémon cadastrado ainda.</p>";
        }
        ?>

        <?php if (!empty($mensagem)) echo $mensagem; ?>
    </div>
</div>

<?php $conn->close(); ?>
</body>
</html>
