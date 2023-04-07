<?php

// VERSION 1

declare(strict_types=1);

require 'vendor/autoload.php';


use App\Http\Controllers\GuzzleHttp\Client;
use \PDO as PDO;
use PDOException;

function connect_to_db(string $servername, string $username, string $password, string $dbname): PDO
{
  // Conexión a MySql
  try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
  } catch (PDOException $e) {
    echo "Error al conectar con la base de datos: " . $e->getMessage();
    exit;
  }
}

function getCharacter(GuzzleHttp\Client $http): array
{
  $query = '
  query ($perPage: Int) { # Define each page
    Page(perPage:$perPage) {
      pageInfo{
        hasNextPage
      }
      characters {
        id
        name {
          full
          native
        }
        gender
        age
        image{
          large
          medium
        }
        description
      }
    }
  }
  ';

  // Define our query variables and values that will be used in the query request
  $variables = [
    "page" => 1
  ];




  $response = $http->post('https://graphql.anilist.co', [
    'json' => [
      'query' => $query,
      'variables' => $variables,
    ]
  ]);

  $characters = [];
  
  $hasNextPage = true;
  $currentPage = $variables['page'];
  
  $index_request = 0 ;
  do {
    
    $response = $http->post('https://graphql.anilist.co', [
      'json' => [
        'query' => $query,
        'variables' => $variables,
        ]
      ]);

    $raw_data_characters = json_decode((string) $response->getBody(), true);
    
    $raw_characters = $raw_data_characters['data']['Page']['characters'];
    foreach ($raw_characters as $value) {
      $id           = $value['id'];
      $name         = $value['name']['native'];
      $romaji       = $value['name']['full'];
      $description  = $value['description'];
      $image_large  = $value['image']['large'];
      $image_medium = $value['image']['medium'];
      $age          = $value['age'];
      $gender       = $value['gender'];

      $character = [
        'id'            => $id,
        'name'          => $name,
        'romaji'        => $romaji,
        'description'   => $description,
        'image_large'   => $image_large,
        'image_medium'  => $image_medium,
        'age'           => $age,
        'gender'        => $gender,
      ];

      array_push($characters, $character);

    }
    $variables['page'] = $currentPage++;
    //$hasNextPage = $raw_data_characters['data']['Page']['pageInfo']['hasNextPage']; de momento no
    
    if($index_request == 90){//Si el numero de peticion es el 90
      $index_request = 0;//reset de la variable
      sleep(60); //Se parara durante 60 segundos
    }
    $index_request++;

  } while ($currentPage<=50);


  return $characters;
}


function main()
{
  #Credentials
  $servername = '127.0.0.1';
  $username = 'root';
  $password = 'myroot';
  $dbname = 'onilist';

  #Create conection
  $db = connect_to_db($servername, $username, $password, $dbname);

  #Http Guzzle
  $http = new GuzzleHttp\Client;

  #character insertion
  $character_array_data = getCharacter($http);
  //TODO INSERT $character_array_data to onilist database
  //Sleep de 60 segundos por cada tabla
  

  echo count($character_array_data);

}

main();