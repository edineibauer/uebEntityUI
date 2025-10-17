<?php

namespace EntityUi;

use Config\Config;
use Conn\SqlCommand;
use Helpers\Check;
use Entity\Metadados;

abstract class EntityDatabase
{
    private $entity;
    private $indice = 10000;

    /**
     * @param string $entityName
     */
    public function __construct(string $entityName)
    {
        $this->entity = $entityName;
    }

    protected function createRelationalTable($dados)
    {
        $table = $this->entity . "_" . substr($dados['column'], 0, 5);

        $string = "CREATE TABLE IF NOT EXISTS `" . $table . "` ("
            . "`id` INT(11) NOT NULL AUTO_INCREMENT, `{$this->entity}_id` INT(11) NOT NULL,`{$dados['relation']}_id` INT(11) NOT NULL"
            . ", PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8";

        $this->exeSql($string);

        $this->createIndexFk($table, $this->entity . "_id", $this->entity, $dados['column'], !0);
        $this->createIndexFk($table, $dados['relation'] . "_id", $dados['relation'], $dados['column'], !0);
    }

    protected function createIndexFk($table, $column, $tableTarget, $col = null, $cascade = false)
    {
        $col = $col ?? $column;
        $constraint = substr("c_{$this->entity}_" . substr($col, 0, 5) . "_" . substr($tableTarget, 0, 5), 0, 64);
        $cascade = $cascade ? "CASCADE" : "SET NULL";

        // Verifica se a tabela de destino existe
        $sql = new SqlCommand();
        $sql->exeCommand("SHOW TABLES LIKE '{$tableTarget}'");
        if (!$sql->getRowCount()) {
            return; // Tabela de destino não existe, não criar foreign key
        }

        // Adiciona índice se não existir
        $sql->exeCommand("SHOW KEYS FROM `{$table}` WHERE Key_name = 'fk_{$column}'");
        if ($sql->getRowCount() === 0) {
            $this->exeSql("ALTER TABLE `" . $table . "` ADD KEY `fk_" . $column . "` (`{$column}`)", false);
        }

        // Verifica se a constraint já existe
        $sql->exeCommand("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table}' AND CONSTRAINT_NAME = '{$constraint}'");
        if ($sql->getRowCount() === 0) {
            $this->exeSql("ALTER TABLE `" . $table . "` ADD CONSTRAINT `{$constraint}` FOREIGN KEY (`{$column}`) REFERENCES `" . $tableTarget . "`(id) ON DELETE {$cascade} ON UPDATE NO ACTION", false);
        }
    }

    /**
     * @param array $dados
     * @param int $tipo
     * @return string
     */
    protected function prepareSqlColumn(array $dados, int $tipo = 1)
    {
        $allowDefault = true;
        if ($dados['type'] === "json") {
            $dados['type'] = "longtext";
            $dados['size'] = "";
            $allowDefault = false;
        }

        if ($dados['type'] === "datetime-local")
            $dados['type'] = "datetime";

        if ($dados['type'] === "text" && !empty($dados['size']) && $dados['size'] < 1000)
            $dados['type'] = "varchar";

        if ($dados['type'] === "varchar" && !empty($dados['size']) && $dados['size'] >= 1000) {
            $dados['type'] = "text";
            $allowDefault = false;
        }

        if(in_array($dados['type'], ["longtext", "text", "blob", "geometry", "json"]))
            $allowDefault = false;

        $type = (in_array($dados['type'], ["float", "real", "double"]) ? "double" : ($dados['type'] === "number" ? "int" : $dados['type']));
        $size = (in_array($dados['type'], ['smallint', 'tinyint', 'mediumint', 'int', 'bigint', 'float', 'real', 'double']) ? "" : ($dados['type'] === "decimal" ? "11," . ($dados['format'] === "valor" ? 2 : ($dados['format'] === "valor_decimal" ? 3 : ($dados['format'] === "valor_decimal_plus" ? 4 : ($dados['format'] === "valor_decimal_minus" ? 1 : 0)))) : $dados['size']));

        return "`{$dados['column']}` {$type} "
            . (!empty($size) ? "({$size}) " : ($dados['type'] === "varchar" ? "(254) " : ($dados['type'] === "decimal" ? "(15,2) " : " ")))
            . ($allowDefault && $dados['default'] !== false && !empty($dados['default']) && ($tipo === 0 || $dados['type'] !== "varchar") ? $this->prepareDefault($dados['default']) : ($dados['default'] !== false ? "DEFAULT NULL" : ""));
    }

    protected function exeSql($sql, $showError = true)
    {
        try {
            $exe = new SqlCommand();
            $exe->exeCommand($sql);
        } catch (\Exception $exception) {
            if($showError) {
                echo "<pre>";
                var_dump("Excessão: ", $exception);
                die;
            }
        }
        if ($showError && $exe->getErro()) {
            var_dump($sql);
            var_dump($exe->getErro());
        }
    }

    private function prepareDefault($default)
    {
        if ($default === 'datetime' || $default === 'date' || $default === 'time')
            return "";

        if (is_numeric($default))
            return "DEFAULT {$default}";

        return "DEFAULT '{$default}'";
    }

    /**
     * Obtém todas as foreign keys de uma coluna específica
     * @param string $table
     * @param string $column
     * @return array
     */
    protected function getColumnForeignKeys(string $table, string $column): array
    {
        $sql = new SqlCommand();
        $sql->exeCommand("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table}' AND COLUMN_NAME = '{$column}' AND REFERENCED_TABLE_NAME IS NOT NULL");
        return $sql->getResult() ?: [];
    }

    /**
     * Obtém todas as foreign keys de outras tabelas que referenciam esta coluna
     * @param string $table
     * @param string $column
     * @return array
     */
    protected function getReferencingForeignKeys(string $table, string $column = 'id'): array
    {
        $sql = new SqlCommand();
        $sql->exeCommand("SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME = '{$table}' AND REFERENCED_COLUMN_NAME = '{$column}'");
        return $sql->getResult() ?: [];
    }

    /**
     * Remove uma foreign key de forma segura
     * @param string $table
     * @param string $constraintName
     * @return bool
     */
    protected function dropForeignKey(string $table, string $constraintName): bool
    {
        try {
            $sql = new SqlCommand();
            $sql->exeCommand("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraintName}`");
            return !$sql->getErro();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove um índice de forma segura
     * @param string $table
     * @param string $indexName
     * @return bool
     */
    protected function dropIndex(string $table, string $indexName): bool
    {
        try {
            $sql = new SqlCommand();
            // Verifica se o índice existe
            $sql->exeCommand("SHOW KEYS FROM `{$table}` WHERE Key_name = '{$indexName}'");
            if ($sql->getRowCount() > 0) {
                $sql->exeCommand("ALTER TABLE `{$table}` DROP INDEX `{$indexName}`");
                return !$sql->getErro();
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Verifica se uma coluna existe em uma tabela
     * @param string $table
     * @param string $column
     * @return bool
     */
    protected function columnExists(string $table, string $column): bool
    {
        $sql = new SqlCommand();
        $sql->exeCommand("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
        return $sql->getRowCount() > 0;
    }

    /**
     * Verifica se uma tabela existe
     * @param string $table
     * @return bool
     */
    protected function tableExists(string $table): bool
    {
        $sql = new SqlCommand();
        $sql->exeCommand("SHOW TABLES LIKE '{$table}'");
        return $sql->getRowCount() > 0;
    }
}
