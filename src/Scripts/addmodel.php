<?php

use Dotenv\Dotenv;

require 'vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$host = $_ENV['DB_HOST'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_DATABASE'];

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro de conexão: ' . $e->getMessage();
    die();
}


$tableName = $argv[1];
$modelName = $argv[2];

$query = $conn->query("DESCRIBE $tableName");

$columns = [];

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $columns[] = $row;
}

$classCode = "<?php\n\nnamespace Models;\n\nrequire_once 'Model.php';\n\nclass $modelName extends Model\n{\n";

foreach ($columns as $column) {
    $propertyName = $column['Field'];
    $propertyType = null;

    if (str_contains($column['Type'], 'int')) {
        $propertyType = 'int';
    } elseif (str_contains($column['Type'], 'varchar') || str_contains($column['Type'], 'text') || str_contains($column['Type'], 'date') || str_contains($column['Type'], 'time')) {
        $propertyType = 'string';
    } elseif (str_contains($column['Type'], 'enum')) {
        // Extract enum values
        preg_match_all("/'([^']+)'/", $column['Type'], $matches);
        $enumValues = $matches[1];
        $propertyType = 'string';
        $enumString = "'" . implode("', '", $enumValues) . "'";
        $classCode .= "    const $propertyName = [$enumString];\n";
    }

    $nullable = $column['Null'] === 'YES' ? '|null' : '';

    $classCode .= "    public $propertyType$nullable \$$propertyName";

    if ($nullable !== '') {
        $classCode .= " = null";
    }

    $classCode .= ";\n";
}

$classCode .= "    protected string \$table = '$tableName';\n}";

$filePath = __DIR__ . '/../Models/' . $modelName . '.php';

file_put_contents($filePath, $classCode);

echo "Classe gerada com sucesso!";

?>
