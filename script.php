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






// Here we define our query as a multi-line string
// $query = '
// query ($id: Int) { # Define which variables will be used in the query (id)
//   Media (id: $id, type: ANIME) { # Insert our variables into the query arguments (id) (type: ANIME is hard-coded in the query)
//     id
//     title {
//       romaji
//       english
//       native
//     }
//   }
// }
// ';





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
  return $data;
  //var_dump(json_decode($response->getBody(), true));

}

function build_data_array(array $data): array
{
  $base = $data['data']['Media'];
  //startDate
  $startDate = $base['startDate'];
  $yearDate = $startDate['year'];
  $monthDate = $startDate['month'];
  $dayDate = $startDate['day'];
  //endDate
  $endtDate = $base['endDate'];
  $endYearDate = $endtDate['year'];
  $endMonthDate = $endtDate['month'];
  $endDayDate = $endtDate['day'];


  //Build data to insert
  $title = $base['title']['romaji'];
  $description = $base['description'];
  $extra_large_banner_image = $base['coverImage']['extraLarge'];
  $large_banner_image = $base['coverImage']['large'];
  $medium_banner_image = $base['coverImage']['medium'];
  $format = $base['format'];
  $episodes = $base['episodes'];
  $chapters = $base['chapters'];
  $airing_status = $base['status'];
  $start_date_builded = $yearDate . '-' . $monthDate . '-' . $dayDate;
  $start_date = strtotime($start_date_builded);
  $end_date_builded = $endYearDate . '-' . $endMonthDate . '-' . $endDayDate;
  $end_date = strtotime($end_date_builded);
  $season = $base['season'];
  $season_year = $base['seasonYear'];
  $studios = $base['studios']['nodes']; //array
  $source = $base['source'];
  $genres = $base['genres'];
  $romaji = $base['title']['romaji'];
  $native = $base['title']['native'];
  $trailer = $base['trailer'];
  $tags = $base['tags'];
  $external_link = $base['externalLinks'];
  $type = $base['type'];


  $media_data = [
    'title'                    => $title,
    'description'              => $description,
    'extra_large_banner_image' => $extra_large_banner_image,
    'large_banner_image'       => $large_banner_image,
    'medium_banner_image'      => $medium_banner_image,
    'format'                   => $format,
    'episodes'                 => $episodes,
    'chapters'                 => $chapters,
    'airing_status'            => $airing_status,
    'start_date'               => date('Y-m-d', $start_date), //string
    'end_date'                 => date('Y-m-d', $end_date), //string
    'season'                   => $season,
    'season_year'              => $season_year,
    'studios'                  => json_encode($studios),
    'source'                   => $source,
    'genres'                   => json_encode($genres),
    'romaji'                   => $romaji,
    'native'                   => $native,
    'trailer'                  => $trailer,
    'tags'                     => json_encode($tags),
    'external_link'            => json_encode($external_link),
    'type'                     => $type
  ];



  var_dump($media_data);
  // echo date('Y-m-d', $start_date) . PHP_EOL;
  // echo date('Y-m-d', $end_date);

  return $media_data;
}

//var_dump(json_decode($response->getBody(), true));
//echo($response->getBody());

function insert_data(PDO $db, array $data): void
{
  $insert_sql_str = <<<END
  INSERT INTO medias (title, description, extra_large_banner_image, large_banner_image, medium_banner_image, format, episodes, chapters, airing_status, start_date, end_date, season, season_year, studios, source, genres, romaji, native, trailer, tags, external_link, type)
  VALUES (:title, :description, :extra_large_banner_image, :large_banner_image, :medium_banner_image, :format, :episodes, :chapters, :airing_status, :start_date, :end_date, :season, :season_year, :studios, :source, :genres, :romaji, :native, :trailer, :tags, :external_link, :type)
END;

  $insert_statement = $db->prepare($insert_sql_str);


  //foreach ($data as $row) {
  $insert_statement->execute([
    ':title'                    => $data['title'],
    ':description'              => $data['description'],
    ':extra_large_banner_image' => $data['extra_large_banner_image'],
    ':large_banner_image'       => $data['large_banner_image'],
    ':medium_banner_image'      => $data['medium_banner_image'],
    ':format'                   => $data['format'],
    ':episodes'                 => $data['episodes'],
    ':chapters'                 => $data['chapters'],
    ':airing_status'            => $data['airing_status'],
    ':start_date'               => $data['start_date'],
    ':end_date'                 => $data['end_date'],
    ':season'                   => $data['season'],
    ':season_year'              => $data['season_year'],
    ':studios'                  => $data['studios'],
    ':source'                   => $data['source'],
    ':genres'                   => $data['genres'],
    ':romaji'                   => $data['romaji'],
    ':native'                   => $data['native'],
    ':trailer'                  => $data['trailer'],
    ':tags'                     => $data['tags'],
    ':external_link'            => $data['external_link'],
    ':type'                     => $data['type']
  ]);
  //}
}

//--------------------------------------------------------------------------
function main()
{
  $db = connect_to_db();

  $query = '
query ($id: Int){
  Media(id:$id){
    title {
      romaji
      english
      native
    }
    description
    coverImage {
      extraLarge
      large
      medium
    }
    format
    episodes
    chapters
  status
    startDate {
      year
      month
      day
    }
    endDate {
      year
      month
      day
    }
    season
    seasonYear
    studios {
      nodes {
        name
      }
    }
    source
    genres
    tags {
      name
    }
    externalLinks {
      url
    }
    type
    trailer {
      id
      site
      thumbnail
    }
  }
}
';

  // Define our query variables and values that will be used in the query request
  $variables = [
    "id" => 1212
  ];


  $media = getData($query, $variables);
  insert_data($db, $media);
}
//------------------------------------------------------------------------
main();
