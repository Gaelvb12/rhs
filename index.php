<?php

include 'conexion.php';

$mensaje = "";
$tabla_solicitudes = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $trabajador_id = intval($_POST['trabajador_id']);
    $tipo_solicitud = htmlspecialchars($_POST['tipo_solicitud']);  // Sanitización de entrada

    // Validar entrada
    if (empty($trabajador_id) || empty($tipo_solicitud)) {
        $mensaje = "Todos los campos son obligatorios.";
    } else {
        // Consulta para obtener la fecha de ingreso y los nuevos campos
        $query_fecha = $conn->prepare("SELECT fecha_ingreso, apellidoP, apellidoM, email, contrasena FROM trabajadores WHERE id = ?");
        $query_fecha->bind_param("i", $trabajador_id);
        $query_fecha->execute();
        $result_fecha = $query_fecha->get_result();

        if ($result_fecha && $result_fecha->num_rows > 0) {
            $row = $result_fecha->fetch_assoc();
            $fecha_ingreso = $row['fecha_ingreso'];
            $apellidoP = $row['apellidoP'];
            $apellidoM = $row['apellidoM'];
            $email = $row['email'];
            $contrasena = $row['contrasena'];
            $meses_trabajados = (strtotime(date("Y-m-d")) - strtotime($fecha_ingreso)) / (30 * 24 * 60 * 60);

            if ($meses_trabajados < 6) {
                $mensaje = "El trabajador no cumple con los 6 meses de antigüedad necesarios.";
            } else {
                // Verificar si ya se solicitó la prestación
                $query_verificacion = $conn->prepare("SELECT * FROM prestacion_lentes 
                                                      WHERE trabajador_id = ? 
                                                      AND tipo_solicitud = ? 
                                                      AND YEAR(fecha_solicitud) = YEAR(CURDATE())");
                $query_verificacion->bind_param("is", $trabajador_id, $tipo_solicitud);
                $query_verificacion->execute();
                $result_verificacion = $query_verificacion->get_result();

                if ($result_verificacion && $result_verificacion->num_rows > 0) {
                    $mensaje = "El trabajador ya ha solicitado la prestación para $tipo_solicitud este año.";

                    $tabla_solicitudes = "<h2>Información de la Solicitud Existente</h2>
                                          <table>
                                            <thead>
                                                <tr>
                                                    <th>Tipo de Solicitud</th>
                                                    <th>Fecha de Solicitud</th>
                                                    <th>Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>";
                    while ($row_solicitud = $result_verificacion->fetch_assoc()) {
                        $tabla_solicitudes .= "<tr>
                                                <td>{$row_solicitud['tipo_solicitud']}</td>
                                                <td>{$row_solicitud['fecha_solicitud']}</td>
                                                <td>{$row_solicitud['monto']}</td>
                                               </tr>";
                    }
                    $tabla_solicitudes .= "</tbody></table>";
                } else {
                    // Registrar la prestación
                    $query_insert = $conn->prepare("INSERT INTO prestacion_lentes (trabajador_id, tipo_solicitud, fecha_solicitud, monto)
                                                    VALUES (?, ?, CURDATE(), 2500)");
                    $query_insert->bind_param("is", $trabajador_id, $tipo_solicitud);
                    if ($query_insert->execute()) {
                        $mensaje = "Prestación registrada exitosamente.";
                    } else {
                        $mensaje = "Error al registrar la prestación: " . $conn->error;
                    }
                }
            }
        } else {
            $mensaje = "El trabajador con ID $trabajador_id no fue encontrado.";
        }
    }
}

// Obtener la lista de trabajadores con fecha de ingreso
$query_trabajadores = "SELECT id, CONCAT(nombre, ' ', apellidoP, ' ', apellidoM) AS nombre_completo, email, fecha_ingreso FROM trabajadores";
$result_trabajadores = $conn->query($query_trabajadores);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Prestación de Lentes</title>
    <style>
        table {
            width: 80%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .search-bar {
            margin-bottom: 15px;
        }

        .mensaje {
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        .form-container {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Solicitud de Prestación de Lentes</h1>

    <!-- Mensaje -->
    <p class="<?php echo empty($tabla_solicitudes) ? 'mensaje' : 'error'; ?>">
        <?php echo $mensaje; ?>
    </p>

    <!-- Filtro de búsqueda -->
    <div class="search-bar">
        <label for="buscar_trabajador">Buscar Trabajador:</label>
        <input type="text" id="buscar_trabajador" placeholder="Escribe el nombre o ID" onkeyup="filtrarTrabajadores()">
    </div>

    <!-- Tabla de trabajadores -->
    <h2>Trabajadores Disponibles</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Email</th>
                <th>Fecha de Ingreso</th>
            </tr>
        </thead>
        <tbody id="tabla_trabajadores">
            <?php
            if ($result_trabajadores && $result_trabajadores->num_rows > 0) {
                while ($row = $result_trabajadores->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['nombre_completo']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['fecha_ingreso']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No hay trabajadores registrados.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Formulario para solicitar prestación -->
    <div class="form-container">
        <form method="POST" action="">
            <label for="trabajador_id">ID del Trabajador:</label>
            <input type="number" id="trabajador_id" name="trabajador_id" required>

            <label for="tipo_solicitud">Tipo de Solicitud:</label>
            <select id="tipo_solicitud" name="tipo_solicitud" required>
                <option value="trabajador">Para el Trabajador</option>
                <option value="hijo">Para el Hijo</option>
            </select>

            <button type="submit">Solicitar Prestación</button>
        </form>
    </div>

    <!-- Tabla de información de la solicitud -->
    <?php
    if (!empty($tabla_solicitudes)) {
        echo $tabla_solicitudes;
    }
    ?>

    <script>
        function filtrarTrabajadores() {
            const input = document.getElementById("buscar_trabajador").value.toLowerCase();
            const filas = document.querySelectorAll("#tabla_trabajadores tr");

            filas.forEach(fila => {
                const texto = fila.textContent.toLowerCase();
                fila.style.display = texto.includes(input) ? "" : "none";
            });
        }
    </script>
</body>
</html>
