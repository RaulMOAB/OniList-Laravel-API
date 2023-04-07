<?php

require 'vendor/autoload.php';

use \PDO as PDO;

use App\Http\Controllers\GuzzleHttp\Client;

// Database connection: If $db_filename does not exist, it is created.
// ----------------------------------------------------------------------------
function connect_to_db(): PDO
{
  $servername = "localhost";
  $username = "root";
  $password = "123456789";

  try {
    $connection = new PDO("mysql:host=$servername;dbname=onilist", $username, $password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected succesfully";
    return $connection;
  } catch (PDOException $e) {
    echo "Connection failed " . $e->getMessage();
  }
}





function getData(string $query, array $variables): array
{
  // Make the HTTP Api request
  $http = new GuzzleHttp\Client;
  $response = $http->post('https://graphql.anilist.co', [
    'json' => [
      'query' => $query,
      'variables' => $variables,
    ]
  ]);
  $data = build_data_array(json_decode($response->getBody(), true));
  //var_dump(json_decode($response->getBody(), true));
  return $data;
}

function build_data_array(array $data): array
{
  $base = $data['data']['Page']['characters'][0]['name']['full'];
  var_dump($base);

  $media_data = [];

  return $media_data;
}


function insert_data(PDO $db, array $data): void
{
  $insert_sql_str = <<<END
    INSERT INTO characters (id, name, romaji, age, gender, description, image_large, image_medium)
    VALUES (:id, :name, :romaji, :age, :gender, :description, :image_large, :image_medium)
  END;

  $insert_statement = $db->prepare($insert_sql_str);


  //foreach ($data as $row) {
  $insert_statement->execute([
    ':id'                    => $data['id'],
    ':name'                  => $data['name']['native'],
    ':romaji'                => $data['name']['full'],
    ':age'                   => $data['age'],
    ':gender'                => $data['gender'],
    ':description'           => $data['description'],
    ':image_large'           => $data['image']['large'],
    ':image_medium'          => $data['image']['medium'],
  ]);
  //}
}

//--------------------------------------------------------------------------
function main()
{
  $db = connect_to_db();

  $media_gql = '
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
    "perPage" => 1 //"id" => 1212 si quieres buscar una media por id
  ];


  $media = getData($media_gql, $variables);
  //insert_data($db, $media);
}
//------------------------------------------------------------------------
main();
