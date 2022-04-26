<?php
// RF1  Si he apretado submit
$disabled="disabled";
function valida_nombre($nombre) {
    $retorno = false;
    $expresion ="#^[a-zA-Z\ \'-]*$#";
    $ok = preg_match($expresion, $nombre);
    if ($nombre == "") {
        $retorno = "El nombre no puede estar vacío.";
    }
    elseif (!$ok) {
        $retorno = "El nombre solo puede contener caracteres válidos y no numéricos.";
    }
    return $retorno;
}
function valida_telefono(&$tel,$nombre,$agenda) {
    //cargamos el valor del campo teléfono 
        $tel = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
        if ($tel == "") {
            //si ya existe en la agenda
            if (isset($agenda[$nombre])) { 
                return false;
            } else {
                return "No se puede borrar un contacto inexistente";
            }
        } 
        $expresion ="#^[+0-9][0-9]*$#";
        $ok = preg_match($expresion, $tel); //sólo caracteres numéricos
        if ($ok) { 
            return false;
        } else { // 
            return "El campo teléfono pued contener solo  números y eventaulmente +";
        }
    }

if (isset($_POST['f1'])) {
// RF2 Leer valores del formulario (nombre, tel, agenda)
    $nombre = filter_input(INPUT_POST, "nombre", FILTER_SANITIZE_STRING);
    $agenda = $_POST['agenda'] ?? []; // array $agenda del formulario oculto
    var_dump($agenda);
    $disabled = "";
    if (count($agenda)==0) //si $agenda está vacía, desabilito el botón de borrar
        $disabled="disabled";
//RF3 Vamos a establecer una variable de error
    $error = false;
    if ($_POST['f1']<>'Borrar') { 
        $error = valida_nombre($nombre);   // valido 
        if (!$error)
            $error = valida_telefono($tel, $nombre, $agenda); //valido si el teléfono es numérico
    }
    if (!$error) {
        
        $opcion = $_POST['f1'];
        switch ($opcion) {
            case "Borrar":
                $contactos = sizeof($agenda);
                $agenda = [];
                $msj = "Se han borrado $contactos contactos de la agenda";
                $disabled="disabled";
                break;
            case "Añadir":
               
                  if ($tel == "") {
                     unset ($agenda[$nombre]); //Elimino un contacto
                    $msj = "Se ha elmininado el contacto de <span style='color:green'>$nombre</span>";
                } else {
                    if (isset($agenda[$nombre])) {
                        $msj = "Se ha modificado el contacto de <span style='color:green'>$nombre</span>";
                    } else {
                        $msj = "Se ha añadido el contacto de <span style='color:green'>$nombre</span>";
                    }
                      $agenda[$nombre] = $tel;
                    $disabled="";
                }
                break;
        }
    }

}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" type="text/css" rel="stylesheet">
    <title>Agenda</title>
    <?php
    if ($error)
        echo "<script>alert('$error');</script>";
    ?>
</head>
<body>
    <div class="container my-auto">
        <h1>Agenda Interactiva</h1>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-7">
                <form action="index.php" method="POST">
                    <fieldset>
                        <legend>Introducción de datos para Agenda</legend>
                        <div class="form-block">
                            <label class="form-label" for="nombre" >Nombre</label>
                            <input class="form-control" type="text" name="nombre" id="nombre">
                        </div>
                        <div class="form-block">
                            <label class="form-label" for="telefono" >Teléfono</label>
                            <input class="form-control" type="text" name="telefono" id="telefono">
                        </div>
                    </fieldset>
                    <div class="form-block">
                        <button type="submit" name="f1" value="Añadir" class="btn envia">Enviar</button>
                        <button type="submit" name="f1" value="Borrar" class="btn borra" <?=$disabled?>>Borrar</button>
                    </div>
                    <?php
                    foreach ($agenda as $nombre => $tel) {
                        echo "<input type='hidden' name='agenda[$nombre]' value ='$tel'>\n";
                    } ?>
                </form>
            </div>
            <div class="col-5">
                <h4>Contenido de la Agenda</h4>
                <table>
                    <tbody>
                        <thead>
                            <tr><th>Nombre</th><th>Teléfono</th></tr>
                        </thead>
                        <?php
                        foreach ($agenda as $nombre => $tel) {
                            echo "<tr><td>$nombre</td><td>$tel</td></tr>";
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <hr>
    <?php
    // $msj con la acción realizada
    if (isset($msj))
        echo "<h4>$msj</h4>";
    ?>
    <script src="/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
