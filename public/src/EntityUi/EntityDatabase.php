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

        $string = "CREATE TABLE IF NOT EXISTS `" . PRE . $table . "` ("
            . "`{$this->entity}_id` INT(11) NOT NULL,"
            . "`{$dados['relation']}_id` INT(11) NOT NULL"
            . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";

        $this->exeSql($string);

        $this->createIndexFk($table, $this->entity . "_id", $this->entity, $dados['column'], !0);
        $this->createIndexFk($table, $dados['relation'] . "_id", $dados['relation'], $dados['column'], !0);
    }

    protected function createIndexFk($table, $column, $tableTarget, $col = null, $cascade = false)
    {
        $col = $col ?? $column;
        $constraint = substr("c_{$this->entity}_" . substr($col, 0, 5) . "_" . substr($tableTarget, 0, 5), 0, 64);
        $cascade = $cascade ? "CASCADE" : "SET NULL";

        $this->exeSql("ALTER TABLE `" . PRE . $table . "` ADD KEY `fk_" . $column . "` (`{$column}`)");
        $this->exeSql("ALTER TABLE `" . PRE . $table . "` ADD CONSTRAINT `{$constraint}` FOREIGN KEY (`{$column}`) REFERENCES `" . PRE . $tableTarget . "`(id) ON DELETE {$cascade} ON UPDATE NO ACTION");
    }

    protected function prepareSqlColumn($dados)
    {
        if ($dados['type'] === "json") {
            $dados['type'] = "longtext";
            $dados['size'] = "";
        } elseif ($dados['type'] === "datetime-local")
            $dados['type'] = "datetime";

        if ($dados['type'] === "text" && !empty($dados['size']) && $dados['size'] < 14000)
            $dados['type'] = "varchar";

        if ($dados['key'] === "percent") {
            $type = "varchar";
            $size = 5;
        } else {
            $type = (in_array($dados['type'], ["float", "real", "double"]) ? "double" : ($dados['type'] === "number" ? "int" : $dados['type']));
            $size = (in_array($dados['type'], ['smallint', 'tinyint', 'mediumint', 'int', 'bigint', 'float', 'real', 'double']) ? "" : ($dados['type'] === "decimal" ? "11," . ($dados['format'] === "valor" ? 2 : ($dados['format'] === "valor_decimal" ? 3 : ($dados['format'] === "valor_decimal_plus" ? 4 : ($dados['format'] === "valor_decimal_minus" ? 1 : 0)))) : $dados['size']));
        }

        return "`{$dados['column']}` {$type} "
            . (!empty($size) ? "({$size}) " : ($dados['type'] === "varchar" ? "(254) " : ($dados['type'] === "decimal" ? "(15,2) " : " ")))
//            . ($dados['default'] === false ? "NOT NULL " : "")
            . ($dados['default'] !== false && !empty($dados['default']) ? $this->prepareDefault($dados['default']) : ($dados['default'] !== false ? "DEFAULT NULL" : ""));
    }

    protected function exeSql($sql)
    {
        $exe = new SqlCommand();
        $exe->exeCommand($sql);
        if ($exe->getErro()) {
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
}
