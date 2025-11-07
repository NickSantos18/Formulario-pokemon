<?php
$servername = "localhost";
$username = "root";
$password = "Senai@118";
$dbname = "teste_formulario";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Conexão falhou: " . $conn->connect_error);

$mensagem = "";
$id = $_GET['id'] ?? null;

if (!$id) die("Pokémon não encontrado.");

// 🔹 Contar total de Pokémons cadastrados
$result = $conn->query("SELECT COUNT(*) AS total FROM Pokemon");
$total = $result->fetch_assoc()['total'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST['nome']);
    $tipo = trim($_POST['tipo']);
    $stmt = $conn->prepare("UPDATE Pokemon SET nome=?, tipo=? WHERE id=?");
    $stmt->bind_param("ssi", $nome, $tipo, $id);
    if ($stmt->execute()) {
        header("Location: pokemon.php");
        exit;
    } else {
        $mensagem = "<p style='color:red;'>Erro ao atualizar Pokémon!</p>";
    }
}

$stmt = $conn->prepare("SELECT * FROM Pokemon WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$pokemon = $result->fetch_assoc();
if (!$pokemon) die("Pokémon não encontrado!");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Pokémon</title>
<style>
body {
    font-family: Arial;
    background-image: url('pikapi.jpg');
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
form {
    background: white;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    width: 320px;
}
input, select {
    display: block;
    margin-bottom: 15px;
    padding: 8px;
    width: 100%;
}
button {
    background: #FF0000;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 10px;
    cursor: pointer;
    width: 100%;
}
button:hover { background: #c00000; }
a {
    text-decoration: none;
    color: #333;
    display: block;
    text-align: center;
    margin-top: 10px;
}
.total {
    text-align: center;
    margin-bottom: 15px;
    font-weight: bold;
    color: #333;
}
</style>
</head>
<body>
<form method="POST">
    <h2>Editar Pokémon</h2>

    <!-- 🔹 Exibir quantidade total -->
    <div class="total">Total de Pokémons cadastrados: <?php echo $total; ?></div>

    <label>Nome:</label>
    <input type="text" name="nome" value="<?php echo htmlspecialchars($pokemon['nome']); ?>" required>

    <label>Tipo:</label>
    <select name="tipo" required>
        <?php
        $tipos = ["Fogo","Água","Grama","Elétrico","Normal","Veneno","Lutador","Fantasma","Psíquico","Gelo","Pedra","Aço","Dragão","Sombrio","Fada"];
        foreach ($tipos as $t) {
            $sel = ($pokemon['tipo'] == $t) ? "selected" : "";
            echo "<option value='$t' $sel>$t</option>";
        }
        ?>
    </select>

    <button type="submit">Salvar Alterações</button>
    <a href="pokemon.php">⬅ Voltar</a>
</form>
<?php echo $mensagem; ?>
</body>
</html>
