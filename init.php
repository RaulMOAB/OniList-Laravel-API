<?php

declare(strict_types=1);

require 'vendor/autoload.php';


use App\Http\Controllers\GuzzleHttp\Client;
use \PDO as PDO;
use PDOException;

function connectToDB(string $servername, string $username, string $password, string $dbname): PDO
{
  // MySql Connection
  try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
  } catch (PDOException $e) {
    echo "Error al conectar con la base de datos: " . $e->getMessage();
    exit;
  }
}

function getJsonValues($data_json): string
{
  $value = array_values($data_json);
  return $value[0];
}

################################################
#         GET DATA FUNCTIONS
################################################
function getStaff(GuzzleHttp\Client $http): array
{
  $query = '
  query ($page: Int) { # Define each page
    Page(page:$page) {
      pageInfo{
        hasNextPage
      }
      staff {
        id
        name {
          full
          native
        }
        gender
        dateOfBirth{
          year
          month
          day
        }
        dateOfDeath{
          year
          month
          day
        }
        age
        yearsActive
        homeTown
        bloodType
        image {
          large
          medium
        }
        dateOfBirth {
          year
          month
          day
        }
        description
      }
    }
  }
  ';

  // Define our query variables and values that will be used in the query request


  $staffs = [];

  $hasNextPage = true;
  $currentPage = 1;
  $totalPages = 0;

  do {
    $variables = [
      "page" => $currentPage
    ];
    usleep(666666);
    try {

      $response = $http->post('https://graphql.anilist.co', [
        'json' => [
          'query' => $query,
          'variables' => $variables,
        ]
      ]);
    } catch (Exception $e) {
      echo 'exception...';
      sleep(61);
      $response = $http->post('https://graphql.anilist.co', [
        'json' => [
          'query' => $query,
          'variables' => $variables,
        ]
      ]);
    }

    $raw_data_staffs = json_decode((string) $response->getBody(), true);
    $totalPages++;
    echo $totalPages . '-';

    $raw_staffs = $raw_data_staffs['data']['Page']['staff'];
    foreach ($raw_staffs as $value) {

      //birthDate
      $birth_date = $value['dateOfBirth'];
      $birth_year_date = $birth_date['year'];
      $birth_month_date = $birth_date['month'];
      $birth_day_date = $birth_date['day'];
      $birth_time_date = strtotime($birth_year_date . '-' . $birth_month_date . '-' . $birth_day_date);
      //deathDate
      $death_date = $value['dateOfDeath'];
      $death_year_date = $death_date['year'];
      $death_month_date = $death_date['month'];
      $death_day_date = $death_date['day'];
      $death_time_date = strtotime($death_year_date . '-' . $death_month_date . '-' . $death_day_date);
      //yearsActive
      $active = null;
      $yearsArray = $value['yearsActive'];
      if (!empty($yearsArray)) {
        $active = count($yearsArray) == 2 ? $yearsArray[0] . '-' . $yearsArray[1] : $yearsArray[0] . '-' . 'Present';
      }


      $id           = $value['id'];
      $name         = $value['name']['native'];
      $romaji       = $value['name']['full'];
      $gender       = $value['gender'];
      $date_of_birth  = $birth_time_date ? date('Y-m-d', $birth_time_date) : null;
      $date_of_death  = $death_time_date ? date('Y-m-d', $death_time_date) : null;
      $age          = $value['age'];
      $years_active  = $active;
      $home_town     = $value['homeTown'];
      $blood_type    = $value['bloodType'];
      $description  = $value['description'];
      $image_large  = $value['image']['large'];
      $image_medium = $value['image']['medium'];

      $staff = [
        'id'            => $id,
        'name'          => $name,
        'romaji'        => $romaji,
        'gender'        => $gender,
        'date_of_birth'   => $date_of_birth,
        'date_of_death'   => $date_of_death,
        'age'           => $age,
        'years_active'   => $years_active,
        'home_town'      => $home_town,
        'blood_type'     => $blood_type,
        'description'   => $description,
        'image_large'   => $image_large,
        'image_medium'  => $image_medium,
      ];

      array_push($staffs, $staff);
    }

    $currentPage++;
    $hasNextPage = $raw_data_staffs['data']['Page']['pageInfo']['hasNextPage'];
  } while ($totalPages <= 2000);


  return $staffs;
}

function getMedias(GuzzleHttp\Client $http): array
{
  $query = '
    query ($page: Int){
      Page(page:$page){
      pageInfo{
        hasNextPage
      }
        media{
        id
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
    }
  ';
  $medias = [];

  $hasNextPage = true;
  $currentPage = 1;
  $totalPages = 0;

  do {
    $variables = [
      "page" => $currentPage
    ];
    usleep(666666);
    try {

      $response = $http->post('https://graphql.anilist.co', [
        'json' => [
          'query' => $query,
          'variables' => $variables,
        ]
      ]);
    } catch (Exception $e) {
      echo 'Exception ocurred...';
      sleep(61);
      $response = $http->post('https://graphql.anilist.co', [
        'json' => [
          'query' => $query,
          'variables' => $variables,
        ]
      ]);
    }
    $totalPages++;
    echo $totalPages . '-';

    $raw_data_media = json_decode((string) $response->getBody(), true);


    $raw_medias = $raw_data_media['data']['Page']['media'];


    foreach ($raw_medias as $value) {
      //startDate
      $startDate = $value['startDate'];
      $yearDate = $startDate['year'];
      $monthDate = $startDate['month'];
      $dayDate = $startDate['day'];
      //endDate
      $endtDate = $value['endDate'];
      $endYearDate = $endtDate['year'];
      $endMonthDate = $endtDate['month'];
      $endDayDate = $endtDate['day'];


      //Build data to insert
      $id = $value['id'];
      $title = $value['title']['romaji'];
      $description = $value['description'];
      $extra_large_banner_image = $value['coverImage']['extraLarge'];
      $large_banner_image = $value['coverImage']['large'];
      $medium_banner_image = $value['coverImage']['medium'];
      $format = $value['format'];
      $episodes = $value['episodes'];
      $chapters = $value['chapters'];
      $airing_status = $value['status'];
      $start_date_builded = $yearDate . '-' . $monthDate . '-' . $dayDate;
      $start_date = strtotime($start_date_builded);
      $end_date_builded = $endYearDate . '-' . $endMonthDate . '-' . $endDayDate;
      $end_date = strtotime($end_date_builded);
      $season = $value['season'];
      $season_year = $value['seasonYear'];
      $studios = array_map('getJsonValues', $value['studios']['nodes']);
      $source = $value['source'];
      $genres = $value['genres'];
      $romaji = $value['title']['romaji'];
      $native = $value['title']['native'];
      $trailer = $value['trailer'];
      $tags = array_map('getJsonValues', $value['tags']);
      $external_link = array_map('getJsonValues', $value['externalLinks']);
      $type = $value['type'];

      $media = [
        'id'                       => $id,
        'title'                    => $title,
        'description'              => $description,
        'extra_large_banner_image' => $extra_large_banner_image,
        'large_banner_image'       => $large_banner_image,
        'medium_banner_image'      => $medium_banner_image,
        'format'                   => $format,
        'episodes'                 => $episodes,
        'chapters'                 => $chapters,
        'airing_status'            => $airing_status,
        'start_date'               => $start_date ? date('Y-m-d', $start_date) : null, //string
        'end_date'                 => $end_date ? date('Y-m-d', $end_date) : null, //string
        'season'                   => $season,
        'season_year'              => $season_year,
        'studios'                  => json_encode($studios),
        'source'                   => $source,
        'genres'                   => json_encode($genres),
        'romaji'                   => $romaji,
        'native'                   => $native,
        'trailer'                  => json_encode($trailer),
        'tags'                     => json_encode($tags),
        'external_link'            => json_encode($external_link),
        'type'                     => $type
      ];

      array_push($medias, $media);
    }
    $currentPage++;
    $hasNextPage = $raw_data_media['data']['Page']['pageInfo']['hasNextPage'];
  } while ($hasNextPage);

  return $medias;
}

function getPersonDubCharacter(GuzzleHttp\Client $http): array
{
  $query = '
    query ($page: Int) { # Define each page
      Page(page:$page) {
        pageInfo{
          hasNextPage
        }
        staff{
          id
          characters{
            nodes{
              id
            }
          }
        }
      }
    }
  ';

  // Define our query variables and values that will be used in the query request
  $variables = [
    "page" => 1
  ];

  $person_dubs_character = [];

  $hasNextPage = true;
  $currentPage = 1;
  $totalPages = 0;
  do {
    $variables = [
      "page" => $currentPage
    ];
    usleep(666666);
    try {

      $response = $http->post('https://graphql.anilist.co', [
        'json' => [
          'query' => $query,
          'variables' => $variables,
        ]
      ]);
    } catch (Exception $e) {
      echo 'exception...';
      sleep(61);
      $response = $http->post('https://graphql.anilist.co', [
        'json' => [
          'query' => $query,
          'variables' => $variables,
        ]
      ]);
    }
    $totalPages++;
    echo $totalPages . '-';
    $raw_data_persons = json_decode((string) $response->getBody(), true);

    $raw_persons = $raw_data_persons['data']['Page']['staff'];
    foreach ($raw_persons as $value) {

      $person_id = $value['id'];
      $characters_ids = $value['characters']['nodes'];

      foreach ($characters_ids as $character_id) {
        $character = [
          'people_id' => $person_id,
          'character_id' => $character_id['id']
        ];
        array_push($person_dubs_character, $character);
      }
    }
    $currentPage++;
    $hasNextPage = $raw_data_persons['data']['Page']['pageInfo']['hasNextPage'];
  } while ($hasNextPage);

  return $person_dubs_character;
}
function getMediaRelation(GuzzleHttp\Client $http): array
{
  $query = '
  query($page: Int){
    Page(page: $page) {
      pageInfo{
        hasNextPage
      }
      media {
        id
        relations {
          nodes {
            id
            source
          }
        }
      }
    }
  }
  ';

  $medias_related_to = [];

  // Define our query variables and values that will be used in the query request
  $variables = [
    "page" => 1
  ];

  $hasNextPage = true;
  $currentPage = 1;
  $totalPages = 0;

  do {
    $variables = [
      "page" => $currentPage
    ];
    usleep(666666);
    try {

      $response = $http->post('https://graphql.anilist.co', [
        'json' => [
          'query' => $query,
          'variables' => $variables,
        ]
      ]);
    } catch (Exception $e) {
      echo 'exception...';
      sleep(61);
      $response = $http->post('https://graphql.anilist.co', [
        'json' => [
          'query' => $query,
          'variables' => $variables,
        ]
      ]);
    }
    $totalPages++;
    echo $totalPages . '-';
    $raw_media_relations = json_decode((string) $response->getBody(), true);

    $raw_relations = $raw_media_relations['data']['Page']['media'];

    foreach ($raw_relations as $value) {
      //Build data to insert
      $id = $value['id'];
      $relations = $value['relations']['nodes'];

      foreach ($relations as $value) {
        $relation_id = $value['id'];
        $source = $value['source'];
        $relation = [
          'media_id' => $id,
          'related_media_id' => $relation_id,
          'relationship_type' => $source
        ];
        array_push($medias_related_to, $relation);
        var_dump($relation);
      }
    }


    $currentPage++;
    $hasNextPage = $raw_media_relations['data']['Page']['pageInfo']['hasNextPage'];
  } while ($hasNextPage);

  return $medias_related_to;
}

function getCharacter(GuzzleHttp\Client $http): array
{
  $query = '
  query ($page: Int) { # Define each page
    Page(page:$page) {
      pageInfo{
        hasNextPage
      }
      characters{
        id
        name {
          full
          native
        }
        image {
          large
          medium
        }
        description
        gender
        dateOfBirth {
          year
          month
          day
        }
        age
        bloodType
        
      }
    }
  }
  ';

  // Define our query variables and values that will be used in the query request
  $variables = [
    "page" => 1
  ];

  $characters = [];

  $hasNextPage = true;
  $currentPage = 1;
  $totalPages = 0;

  do {
    $variables = [
      "page" => $currentPage
    ];
    usleep(666666);
    try {

      $response = $http->post('https://graphql.anilist.co', [
        'json' => [
          'query' => $query,
          'variables' => $variables,
        ]
      ]);
    } catch (Exception $e) {
      echo 'exception...';
      sleep(60);
      $response = $http->post('https://graphql.anilist.co', [
        'json' => [
          'query' => $query,
          'variables' => $variables,
        ]
      ]);
    }
    $totalPages++;
    echo $totalPages . '-';
    $raw_data_characters = json_decode((string) $response->getBody(), true);

    $raw_characters = $raw_data_characters['data']['Page']['characters'];
    foreach ($raw_characters as $value) {
      #Months
      $months = array(
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec"
      );
      #Birthday
      $birth_date = $value['dateOfBirth'];
      $birth_month_date = $birth_date['month'];
      $birth_day_date = $birth_date['day'];
      $birth = null;

      if ($birth_month_date) {
        $birth = $months[$birth_month_date - 1] . ' ' . $birth_day_date;
      }


      $id           = $value['id'];
      $name         = $value['name']['native'];
      $romaji       = $value['name']['full'];
      $age          = $value['age'];
      $gender       = $value['gender'];
      $blood_type   = $value['bloodType'];
      $birthday     = $birth;
      $description  = $value['description'];
      $image_large  = $value['image']['large'];
      $image_medium = $value['image']['medium'];

      $character = [
        'id'            => $id,
        'name'          => $name,
        'romaji'        => $romaji,
        'age'           => $age,
        'gender'        => $gender,
        'blood_type'    => $blood_type,
        'birthday'      => $birthday,
        'description'   => $description,
        'image_large'   => $image_large,
        'image_medium'  => $image_medium,
      ];

      array_push($characters, $character);
    }
    $currentPage++;
    $hasNextPage = $raw_data_characters['data']['Page']['pageInfo']['hasNextPage'];
  } while ($hasNextPage);


  return $characters;
}

function getPeopleWorksIn(GuzzleHttp\Client $http): array
{
  $query = '
  query ($page: Int) {
  Page(page: $page) {
    pageInfo{
      hasNextPage
    }
    staff {
      id
      staffMedia {
        nodes {
          id
        }
      }
    }
  }
  ';

  // Define our query variables and values that will be used in the query request
  $variables = [
    "page" => 1
  ];

  $people_works_in = [];

  $hasNextPage = true;
  $currentPage = 1;
  $totalPages = 0;

  do {
    $variables = [
      "page" => $currentPage
    ];
    usleep(666666);
    try {

      $response = $http->post('https://graphql.anilist.co', [
        'json' => [
          'query' => $query,
          'variables' => $variables,
        ]
      ]);
    } catch (Exception $e) {
      echo 'exception...';
      sleep(61);
      $response = $http->post('https://graphql.anilist.co', [
        'json' => [
          'query' => $query,
          'variables' => $variables,
        ]
      ]);
    }
    $totalPages++;
    echo $totalPages . '-';
    $raw_works_in = json_decode((string) $response->getBody(), true);
    $raw_people_works_in = $raw_works_in['data']['Page']['staff'];

    foreach ($raw_people_works_in as $value) {
      $id = $value['id'];
      $staff_media_id = $value['staffMedia']['nodes'];
      foreach ($staff_media_id as $value) {
        $media_id = $value['id'];
        $staff_media_relation = [
          'person_id' => $id,
          'media_id' => $media_id
        ];

        array_push($people_works_in, $staff_media_relation);
      }
    }
    $currentPage++;
    $hasNextPage = $raw_works_in['data']['Page']['pageInfo']['hasNextPage'];
  } while ($totalPages <= 2000);

  return $people_works_in;
}

function getCharacterAppearsIn(GuzzleHttp\Client $http): array
{
  $query = '
  query ($page: Int) { # Define each page
    Page(page:$page) {
      pageInfo{
        hasNextPage
      }
      characters{
        id
        media{
          edges{
            node{
              id
            }
            characterRole
          }
        }
      }
    }
  }
  ';

  // Define our query variables and values that will be used in the query request
  $variables = [
    "page" => 1
  ];

  $charactersAppearsIn = [];

  $hasNextPage = true;
  $currentPage = 1;
  $totalPages = 0;
  do {
    $variables = [
      "page" => $currentPage
    ];
    usleep(666666);
    try {

      $response = $http->post('https://graphql.anilist.co', [
        'json' => [
          'query' => $query,
          'variables' => $variables,
        ]
      ]);
    } catch (Exception $e) {
      echo 'exception...';
      sleep(61);
      $response = $http->post('https://graphql.anilist.co', [
        'json' => [
          'query' => $query,
          'variables' => $variables,
        ]
      ]);
    }
    $totalPages++;
    echo $totalPages . '-';
    $raw_data_charactersAppearsIn = json_decode((string) $response->getBody(), true);

    $raw_charactersAppearsIn = $raw_data_charactersAppearsIn['data']['Page']['characters'];
    foreach ($raw_charactersAppearsIn as $value) {

      $character_id    = $value['id'];
      $media_ids        = $value['media']['edges'];

      foreach ($media_ids as $media_id) {

        $characterAppearsIn = [
          'character_id'  => $character_id,
          'media_id'      => $media_id['node']['id'],
          'role'          => $media_id['characterRole'],
        ];

        array_push($charactersAppearsIn, $characterAppearsIn);
      }
    }
    $currentPage++;
    $hasNextPage = $raw_data_charactersAppearsIn['data']['Page']['pageInfo']['hasNextPage'];
  } while ($hasNextPage);


  return $charactersAppearsIn;
}


################################################
#         INSERTS DATA FUNCTIONS
################################################
function insertStaffs(PDO $db, array $staffs)
{
  $insert_sql_str = <<<END
    INSERT INTO people (id, name, romaji, gender, date_of_birth, date_of_death, age, years_active, home_town, blood_type, description, image_large, image_medium)
    VALUES (:id, :name, :romaji, :gender, :date_of_birth, :date_of_death, :age, :years_active, :home_town, :blood_type, :description, :image_large, :image_medium)
    ON DUPLICATE KEY UPDATE id=:id, name=:name, romaji=:romaji, gender=:gender, date_of_birth=:date_of_birth, date_of_death=:date_of_death, age=:age, years_active=:years_active, home_town=:home_town, blood_type=:blood_type, description=:description, image_large=:image_large, image_medium=:image_medium

  END;

  $insert_statement = $db->prepare($insert_sql_str);


  //foreach ($data as $row) {
  $index = 0;
  foreach ($staffs as $staff) {
    $insert_statement->execute([
      ':id'             => $staff['id'],
      ':name'           => $staff['name'],
      ':romaji'         => $staff['romaji'],
      ':gender'         => $staff['gender'],
      ':date_of_birth'  => $staff['date_of_birth'],
      ':date_of_death'  => $staff['date_of_death'],
      ':age'            => $staff['age'],
      ':years_active'   => $staff['years_active'],
      ':home_town'      => $staff['home_town'],
      ':blood_type'     => $staff['blood_type'],
      ':description'    => $staff['description'],
      ':image_large'    => $staff['image_large'],
      ':image_medium'   => $staff['image_medium'],
    ]);
    $index++;
    echo ' insert numero:' . $index . ' ';
  }
}

function insertMedias(PDO $db, array $medias): void
{
  $insert_sql_str = <<<END
    INSERT INTO medias (id, title, description, extra_large_banner_image, large_banner_image, medium_banner_image, format, episodes, chapters, airing_status, start_date, end_date, season, season_year, studios, source, genres, romaji, native, trailer, tags, external_link, type)
    VALUES (:id, :title, :description, :extra_large_banner_image, :large_banner_image, :medium_banner_image, :format, :episodes, :chapters, :airing_status, :start_date, :end_date, :season, :season_year, :studios, :source, :genres, :romaji, :native, :trailer, :tags, :external_link, :type) ON DUPLICATE KEY UPDATE id=:id, title=:title, description=:description, extra_large_banner_image=:extra_large_banner_image, large_banner_image=:large_banner_image, medium_banner_image=:medium_banner_image, format=:format, episodes=:episodes, chapters=:chapters, chapters=:chapters, airing_status=:airing_status, start_date=:start_date, end_date=:end_date, season=:season, season_year=:season_year, studios=:studios, source=:source, genres=:genres, romaji=:romaji, native=:native, trailer=:trailer, tags=:tags, external_link=:external_link, type=:type
  END;

  $insert_statement = $db->prepare($insert_sql_str);

  $index = 0;

  foreach ($medias as $media) {
    $insert_statement->execute([
      ':id' => $media['id'],
      ':title' => $media['title'],
      ':description' => $media['description'],
      ':extra_large_banner_image' => $media['extra_large_banner_image'],
      ':large_banner_image' => $media['large_banner_image'],
      ':medium_banner_image' => $media['medium_banner_image'],
      ':format' => $media['format'],
      ':episodes' => $media['episodes'],
      ':chapters' => $media['chapters'],
      ':airing_status' => $media['airing_status'],
      ':start_date' => $media['start_date'],
      ':end_date' => $media['end_date'],
      ':season' => $media['season'],
      ':season_year' => $media['season_year'],
      ':studios' => $media['studios'],
      ':source' => $media['source'],
      ':genres' => $media['genres'],
      ':romaji' => $media['romaji'],
      ':native' => $media['native'],
      ':trailer' => $media['trailer'],
      ':tags' => $media['tags'],
      ':external_link' => $media['external_link'],
      ':type' => $media['type']
    ]);
    $index++;
    echo ' insert numero:' . $index . ' ';
  }
}

function insertMediaRelations(PDO $db, array $medias_relations)
{
  $insert_sql_str = <<<END
    REPLACE INTO related_to (media_id, related_media_id, relationship_type)
    VALUES (:media_id, :related_media_id, :relationship_type)
  END;

  $insert_statement = $db->prepare($insert_sql_str);
  $index = 0;
  foreach ($medias_relations as $media) {
    $insert_statement->execute([
      ':media_id' => $media['media_id'],
      ':related_media_id' => $media['related_media_id'],
      ':relationship_type' => $media['relationship_type']
    ]);
    $index++;
    echo ' insert numero:' . $index . ' ';
  }
}

function insertCharacters(PDO $db, array $characters)
{
  $insert_sql_str = <<<END
    INSERT INTO characters (id, name, romaji, gender, birthday, age, blood_type, description, image_large, image_medium)
    VALUES (:id, :name, :romaji, :gender, :birthday, :age, :blood_type, :description, :image_large, :image_medium) ON DUPLICATE KEY UPDATE id=:id, name=:name, romaji=:romaji, gender=:gender, birthday=:birthday, age=:age, blood_type=:blood_type, description=:description, image_large=:image_large, image_medium=:image_medium
  END;

  $insert_statement = $db->prepare($insert_sql_str);


  $index = 0;
  foreach ($characters as $character) {
    $insert_statement->execute([
      ':id' => $character['id'],
      ':name' => $character['name'],
      ':romaji' => $character['romaji'],
      ':gender' => $character['gender'],
      ':birthday' => $character['birthday'],
      ':age' => $character['age'],
      ':blood_type' => $character['blood_type'],
      ':description' => $character['description'],
      ':image_large' => $character['image_large'],
      ':image_medium' => $character['image_medium'],
    ]);
    $index++;
    echo ' insert numero:' . $index . ' ';
  }
}

function insertPersonDubCharacter(PDO $db, array $persons_dub_array)
{
  $insert_sql_str = <<<END
    REPLACE INTO person_dubs_character (people_id, character_id)
    VALUES (:people_id, :character_id)
  END;

  $insert_statement = $db->prepare($insert_sql_str);


  $index = 0;
  foreach ($persons_dub_array as $person_dub) {
    try {
      $insert_statement->execute([
        ':people_id' => $person_dub['people_id'],
        ':character_id' => $person_dub['character_id'],
      ]);
      $index++;
      echo ' insert numero:' . $index . ' ';
    } catch (PDOException $e) {
      echo ' NO EXISTE ';
      continue;
    }
  }
}

function insertPeopleWorksIn(PDO $db, array $people_works_in)
{
  $insert_sql_str = <<<END
    REPLACE INTO works_in (person_id, media_id)
    VALUES (:person_id, :media_id)
  END;

  $insert_statement = $db->prepare($insert_sql_str);

  $index = 0;
  foreach ($people_works_in as $value) {
    try {
      $insert_statement->execute([
        ':person_id' => $value['person_id'],
        ':media_id' => $value['media_id']
      ]);
      $index++;
      echo ' insert numero:' . $index . ' ';
    } catch (PDOException $e) {
      echo ' NO EXISTE ';
      continue;
    }
  }
}

function insertCharacterAppearsIn(PDO $db, array $characters)
{
  $insert_sql_str = <<<END
    REPLACE INTO characters_appears_in (media_id, character_id, role)
    VALUES (:media_id, :character_id, :role)
  END;

  $insert_statement = $db->prepare($insert_sql_str);

  $index = 0;
  foreach ($characters as $character) {
    $insert_statement->execute([
      ':media_id' => $character['media_id'],
      ':character_id' => $character['character_id'],
      ':role' => $character['role'],
    ]);
    $index++;
    echo ' insert numero:' . $index . ' ';
  }
}


function main()
{
  #Credentials
  $servername = '127.0.0.1';
  $username = 'root';
  $password = 'myroot';
  $dbname = 'onilist';
  #Create conection
  $db = connectToDB($servername, $username, $password, $dbname);
  #Http Guzzle
  $http = new GuzzleHttp\Client;

  ###INSERTIONS###
  //echo 'media action';
  #Media insertion
  //$media_array_data = getMedias($http);
  //insertMedias($db, $media_array_data);
  //sleep(60);

  echo 'character action';
  #Character insertion
  $character_array_data = getCharacter($http);
  insertCharacters($db, $character_array_data);

  echo 'appears_in action';
  #character_appears_in insertion
  $characterAppearsIn_array_data = getCharacterAppearsIn($http);
  insertCharacterAppearsIn($db, $characterAppearsIn_array_data);

  echo 'related_to action';
  #related_to insertion
  $medias_relations = getMediaRelation($http);
  insertMediaRelations($db, $medias_relations);

  #Staff insertion
  echo 'staff action';
  $staff_array_data = getStaff($http);
  insertStaffs($db, $staff_array_data);

  echo 'dubs action';
  #person_dubs_character insertion
  $person_dub_character_array = getPersonDubCharacter($http);
  insertPersonDubCharacter($db, $person_dub_character_array);

  echo 'work_in action';
  #works_in insertion
  $people_works_in = getPeopleWorksIn($http);
  insertPeopleWorksIn($db, $people_works_in);
  //!USE REPLACE FOR RELATIONS TABLE FOR DO NOT DUPLICATE RECORDS WHEN CRON RUNS

  //?REPLACE INTO table(id,population)
  //?VALUES(2,2547);

  //  echo count($staff_array_data);
  echo "End of script :)";
}

main();
