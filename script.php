<?php

require 'vendor/autoload.php';

use App\Http\Controllers\GuzzleHttp\Client;
use App\Models\Media;


// $http = new GuzzleHttp\Client;

// $response = $http->post('https://graphql.anilist.co', [
//     'json' => [
//         'query' => $query,
//         'variables' => $variables,
//     ]
// ]);

// $response = $http->post('https://graphql.anilist.co',
//     [
//         'query' => $query,
//         'variables' => $variables,
//     ]
// );

// echo $response->getBody();



// Here we define our query as a multi-line string
$query = '
query ($id: Int) { # Define which variables will be used in the query (id)
  Media (id: $id, type: ANIME) { # Insert our variables into the query arguments (id) (type: ANIME is hard-coded in the query)
    id
    title {
      romaji
      english
      native
    }
  }
}
';





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
  return json_decode($response->getBody(), true);
}

//var_dump(json_decode($response->getBody(), true));
//echo($response->getBody());

//--------------------------------------------------------------------------
function main()
{
  $query = '
query ($id: Int){
  Media(id:$id){
    title {
      romaji
      english
      native
    }
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
  }
}
';

  // Define our query variables and values that will be used in the query request
  $variables = [
    "id" => 1212
  ];


  $response = getData($query, $variables);
  Media::create($response);
  // $media = [
  //   'title' => $response['data']['Media']['title']['romaji']
  // ];
  //var_dump($media);
  // Media::create();
}
main();
