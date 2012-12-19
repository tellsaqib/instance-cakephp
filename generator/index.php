<?php
error_reporting(E_ERROR);
include 'inflector.php';
$fields = array();
$fields[] = array('name' => 'Content','input_type' => 'BIGTEXT','output_type' => 'BIGTEXT','store_type' => 'BIGTEXT');
$entityName = 'Blog';

print_r($fields);
print('Controller<br />');
print(htmlentities(getController($entityName)));
print('<br />Model<br />');
print( htmlentities(getModel($entityName)));

function getController($controllerName,$actions = '', $helpers = null, $components = null, $uses = null) {
    $out = "<?php\n";
    $out .= "class $controllerName" . "Controller extends {$controllerName}AppController {\n\n";
    $out .= "\tvar \$name = '$controllerName';\n";

    if (count($uses)) {
        $out .= "\tvar \$uses = array('" . $this->_modelName($controllerName) . "', ";

        foreach ($uses as $use) {
            if ($use != $uses[count($uses) - 1]) {
                $out .= "'" . $this->_modelName($use) . "', ";
            } else {
                $out .= "'" . $this->_modelName($use) . "'";
            }
        }
        $out .= ");\n";
    }

    $out .= "\tvar \$helpers = array('Html', 'Form'";
    if (count($helpers)) {
        foreach ($helpers as $help) {
            $out .= ", '" . Inflector::camelize($help) . "'";
        }
    }
    $out .= ");\n";

    if (count($components)) {
        $out .= "\tvar \$components = array(";

        foreach ($components as $comp) {
            if ($comp != $components[count($components) - 1]) {
                $out .= "'" . Inflector::camelize($comp) . "', ";
            } else {
                $out .= "'" . Inflector::camelize($comp) . "'";
            }
        }
        $out .= ");\n";
    }
    $out .= $actions;

    $out .= "}\n";
    $out .= "?>";
    return $out;
}

function getModel($name, $associations = array(),  $validate = array(), $primaryKey = 'id', $useTable = null, $useDbConfig = 'default') {
    if (is_object($name)) {
        if (!is_array($associations)) {
            $associations = $this->doAssociations($name, $associations);
            $validate = $this->doValidation($name, $associations);
        }
        $primaryKey = $name->primaryKey;
        $useTable = $name->table;
        $useDbConfig = $name->useDbConfig;
        $name = $name->name;
    }

    $out = "<?php\n";
    $out .= "class {$name} extends {$name}AppModel {\n\n";
    $out .= "\tvar \$name = '{$name}';\n";

    if ($useDbConfig !== 'default') {
        $out .= "\tvar \$useDbConfig = '$useDbConfig';\n";
    }

    if (($useTable && $useTable !== Inflector::tableize($name)) || $useTable === false) {
        $table = "'$useTable'";
        if (!$useTable) {
            $table = 'false';
        }
        $out .= "\tvar \$useTable = $table;\n";
    }

    if ($primaryKey !== 'id') {
        $out .= "\tvar \$primaryKey = '$primaryKey';\n";
    }

    $validateCount = count($validate);
    if (is_array($validate) && $validateCount > 0) {
        $out .= "\tvar \$validate = array(\n";
        $keys = array_keys($validate);
        for ($i = 0; $i < $validateCount; $i++) {
            $val = "'" . $validate[$keys[$i]] . "'";
            $out .= "\t\t'" . $keys[$i] . "' => array({$val})";
            if ($i + 1 < $validateCount) {
                $out .= ",";
            }
            $out .= "\n";
        }
        $out .= "\t);\n";
    }
    $out .= "\n";

    if (!empty($associations)) {
        if (!empty($associations['belongsTo']) || !empty($associations['hasOne']) || !empty($associations['hasMany']) || !empty($associations['hasAndBelongsToMany'])) {
            $out.= "\t//The Associations below have been created with all possible keys, those that are not needed can be removed\n";
        }

        if (!empty($associations['belongsTo'])) {
            $out .= "\tvar \$belongsTo = array(\n";
            $belongsToCount = count($associations['belongsTo']);

            for ($i = 0; $i < $belongsToCount; $i++) {
                $out .= "\t\t'{$associations['belongsTo'][$i]['alias']}' => array(\n";
                $out .= "\t\t\t'className' => '{$associations['belongsTo'][$i]['className']}',\n";
                $out .= "\t\t\t'foreignKey' => '{$associations['belongsTo'][$i]['foreignKey']}',\n";
                $out .= "\t\t\t'conditions' => '',\n";
                $out .= "\t\t\t'fields' => '',\n";
                $out .= "\t\t\t'order' => ''\n";
                $out .= "\t\t)";
                if ($i + 1 < $belongsToCount) {
                    $out .= ",";
                }
                $out .= "\n";

            }
            $out .= "\t);\n\n";
        }

        if (!empty($associations['hasOne'])) {
            $out .= "\tvar \$hasOne = array(\n";
            $hasOneCount = count($associations['hasOne']);

            for ($i = 0; $i < $hasOneCount; $i++) {
                $out .= "\t\t'{$associations['hasOne'][$i]['alias']}' => array(\n";
                $out .= "\t\t\t'className' => '{$associations['hasOne'][$i]['className']}',\n";
                $out .= "\t\t\t'foreignKey' => '{$associations['hasOne'][$i]['foreignKey']}',\n";
                $out .= "\t\t\t'dependent' => false,\n";
                $out .= "\t\t\t'conditions' => '',\n";
                $out .= "\t\t\t'fields' => '',\n";
                $out .= "\t\t\t'order' => ''\n";
                $out .= "\t\t)";
                if ($i + 1 < $hasOneCount) {
                    $out .= ",";
                }
                $out .= "\n";

            }
            $out .= "\t);\n\n";
        }

        if (!empty($associations['hasMany'])) {
            $out .= "\tvar \$hasMany = array(\n";
            $hasManyCount = count($associations['hasMany']);

            for ($i = 0; $i < $hasManyCount; $i++) {
                $out .= "\t\t'{$associations['hasMany'][$i]['alias']}' => array(\n";
                $out .= "\t\t\t'className' => '{$associations['hasMany'][$i]['className']}',\n";
                $out .= "\t\t\t'foreignKey' => '{$associations['hasMany'][$i]['foreignKey']}',\n";
                $out .= "\t\t\t'dependent' => false,\n";
                $out .= "\t\t\t'conditions' => '',\n";
                $out .= "\t\t\t'fields' => '',\n";
                $out .= "\t\t\t'order' => '',\n";
                $out .= "\t\t\t'limit' => '',\n";
                $out .= "\t\t\t'offset' => '',\n";
                $out .= "\t\t\t'exclusive' => '',\n";
                $out .= "\t\t\t'finderQuery' => '',\n";
                $out .= "\t\t\t'counterQuery' => ''\n";
                $out .= "\t\t)";
                if ($i + 1 < $hasManyCount) {
                    $out .= ",";
                }
                $out .= "\n";
            }
            $out .= "\t);\n\n";
        }

        if (!empty($associations['hasAndBelongsToMany'])) {
            $out .= "\tvar \$hasAndBelongsToMany = array(\n";
            $hasAndBelongsToManyCount = count($associations['hasAndBelongsToMany']);

            for ($i = 0; $i < $hasAndBelongsToManyCount; $i++) {
                $out .= "\t\t'{$associations['hasAndBelongsToMany'][$i]['alias']}' => array(\n";
                $out .= "\t\t\t'className' => '{$associations['hasAndBelongsToMany'][$i]['className']}',\n";
                $out .= "\t\t\t'joinTable' => '{$associations['hasAndBelongsToMany'][$i]['joinTable']}',\n";
                $out .= "\t\t\t'foreignKey' => '{$associations['hasAndBelongsToMany'][$i]['foreignKey']}',\n";
                $out .= "\t\t\t'associationForeignKey' => '{$associations['hasAndBelongsToMany'][$i]['associationForeignKey']}',\n";
                $out .= "\t\t\t'unique' => true,\n";
                $out .= "\t\t\t'conditions' => '',\n";
                $out .= "\t\t\t'fields' => '',\n";
                $out .= "\t\t\t'order' => '',\n";
                $out .= "\t\t\t'limit' => '',\n";
                $out .= "\t\t\t'offset' => '',\n";
                $out .= "\t\t\t'finderQuery' => '',\n";
                $out .= "\t\t\t'deleteQuery' => '',\n";
                $out .= "\t\t\t'insertQuery' => ''\n";
                $out .= "\t\t)";
                if ($i + 1 < $hasAndBelongsToManyCount) {
                    $out .= ",";
                }
                $out .= "\n";
            }
            $out .= "\t);\n\n";
        }
    }
    $out .= "}\n";
    $out .= "?>";
    return $out;
}
?>