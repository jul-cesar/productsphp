<?php

header( 'Access-Control-Allow-Origin: *' );
header( 'Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS' );
header( 'Access-Control-Allow-Headers: Content-Type' );

ini_set( 'display_errors', 1 );
error_reporting( E_ALL );

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'products';

$conn = mysqli_connect( $servername, $username, $password, $dbname );

if ( !$conn ) {
    die( 'Connection failed: ' . mysqli_connect_error() );
}


/* l codigo encargado de hacer el loop para iterar por cada fila del csv y tomar
 los datos para la query, lo tengo comentado porque no es necesario usarlo siempre, si lo dejo sin comentar impidira renderizar los productos en la pagina


$file = fopen('Libro1.csv', 'r');

while (($data = fgetcsv($file)) !== FALSE) {
    // Get the values from the CSV file
    $name = mysqli_real_escape_string($conn, $data[0]);
    $img1 = mysqli_real_escape_string($conn, $data[1]);
    $precio_d1 = mysqli_real_escape_string($conn, $data[2]);
    $precio_olim = mysqli_real_escape_string($conn, $data[3]);
    $precio_exito = mysqli_real_escape_string($conn, $data[4]);
    $categorias_id = mysqli_real_escape_string($conn, $data[5]);

    // Check if the product already exists in the database
    $existingQuery = "SELECT * FROM products WHERE name = '$name'";
    $existingResult = mysqli_query($conn, $existingQuery);
    
    if (mysqli_num_rows($existingResult) > 0) {
        echo "Product already exists: $name<br>";
    } else {
        // Insert the data into the database
        $sql = "INSERT INTO products (name, img1, precio_d1, precio_olim, precio_exito, categorias_id) VALUES ('$name', '$img1', '$precio_d1', '$precio_olim', '$precio_exito', '$categorias_id')";
        
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully: $name<br>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

fclose($file); */ 

if ( $_SERVER[ 'REQUEST_METHOD' ] === 'DELETE' ) {
    $id = $_GET[ 'id' ];

    $sql = "DELETE FROM products WHERE id = $id";
    $result = mysqli_query( $conn, $sql );

    if ( $result ) {
        echo json_encode( array( 'success' => true ) );
    } else {
        echo json_encode( array( 'success' => false, 'error' => mysqli_error( $conn ) ) );
    }
}

if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {

    $name = mysqli_real_escape_string( $conn, $_POST[ 'name' ] );
    $img1 = mysqli_real_escape_string( $conn, $_POST[ 'img1' ] );
    $precio_d1 = mysqli_real_escape_string( $conn, $_POST[ 'precio_d1' ] );
    $precio_olim = mysqli_real_escape_string( $conn, $_POST[ 'precio_olim' ] );
    $precio_exito = mysqli_real_escape_string( $conn, $_POST[ 'precio_exito' ] );
    $category_id = mysqli_real_escape_string( $conn, $_POST[ 'category' ] );

    $sql2 = "INSERT INTO `products`(`name`, `img1`, `precio_d1`, `precio_olim`,`precio_exito`, `categorias_id`) VALUES ('$name','$img1','$precio_d1', '$precio_olim', '$precio_exito','$category_id')";
    $res = mysqli_query( $conn, $sql2 );

    if ( $res ) {
        echo json_encode( array( 'success' => true ) );
    } else {
        echo json_encode( array( 'success' => false, 'error' => mysqli_error( $conn ) ) );
    }
}

if ( isset( $conn ) && $conn->ping() ) {

    if ( $_SERVER[ 'REQUEST_METHOD' ] == 'GET' ) {
        if ( isset( $_GET[ 'category' ] ) && !empty( $_GET[ 'category' ] ) ) {
            $category = mysqli_real_escape_string( $conn, $_GET[ 'category' ] );
            $sql = "SELECT products.*, categorias.nombre
            FROM products
            JOIN categorias ON products.categorias_id = categorias.id
            WHERE categorias.nombre = '" . mysqli_real_escape_string( $conn, $category ) . "'";
            $result = mysqli_query( $conn, $sql );
            if ( mysqli_num_rows( $result ) > 0 ) {
                $products = mysqli_fetch_all( $result, MYSQLI_ASSOC );
                echo json_encode( $products );
            } else {
                echo json_encode( [] );
            }
        } else {
            $sql = "SELECT products.*, categorias.nombre
                    FROM products
                    JOIN categorias ON products.categorias_id = categorias.id";

            $result = mysqli_query( $conn, $sql );

            if ( $result ) {
                $data = array();
                while ( $row = mysqli_fetch_assoc( $result ) ) {
                    $data[] = $row;
                }
                echo json_encode( $data );
            } else {
                echo json_encode( array( 'success' => false, 'error' => mysqli_error( $conn ) ) );
            }
        }
    }
}
