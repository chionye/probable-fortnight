<?php
header('Content-Type: application/json');

// Using NewsAPI - Free tier allows 100 requests per day
// For production, get your own API key from https://newsapi.org/
$apiKey = 'demo'; // Using demo key for testing - replace with real key for production

// Fallback to JSONPlaceholder if NewsAPI fails
$useNewsAPI = false; // Set to true when you have a valid API key

if ($useNewsAPI) {
    $url = "https://newsapi.org/v2/top-headlines?country=us&pageSize=8&apiKey=$apiKey";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        echo $response;
    } else {
        // Fallback to mock data
        echo json_encode(getMockNews());
    }
} else {
    // Use The Guardian API (no key required for limited usage)
    $url = "https://content.guardianapis.com/search?page-size=8&show-fields=thumbnail,trailText&api-key=test";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['response']['results'])) {
            // Convert Guardian format to NewsAPI format
            $articles = [];
            foreach ($data['response']['results'] as $item) {
                $articles[] = [
                    'title' => $item['webTitle'],
                    'description' => $item['fields']['trailText'] ?? 'Read the full story at The Guardian',
                    'url' => $item['webUrl'],
                    'urlToImage' => $item['fields']['thumbnail'] ?? null,
                    'source' => ['name' => 'The Guardian']
                ];
            }
            echo json_encode([
                'status' => 'ok',
                'articles' => $articles
            ]);
        } else {
            echo json_encode(getMockNews());
        }
    } else {
        echo json_encode(getMockNews());
    }
}

function getMockNews() {
    return [
        'status' => 'ok',
        'articles' => [
            [
                'title' => 'Breaking: Technology Advances in AI',
                'description' => 'Latest developments in artificial intelligence are changing the world.',
                'url' => 'https://example.com',
                'urlToImage' => 'https://via.placeholder.com/400x200/3B82F6/FFFFFF?text=Tech+News',
                'source' => ['name' => 'Tech News']
            ],
            [
                'title' => 'Global Economy Shows Positive Signs',
                'description' => 'Economic indicators suggest growth in major markets worldwide.',
                'url' => 'https://example.com',
                'urlToImage' => 'https://via.placeholder.com/400x200/10B981/FFFFFF?text=Business',
                'source' => ['name' => 'Business Today']
            ],
            [
                'title' => 'Climate Summit Reaches Agreement',
                'description' => 'World leaders commit to new environmental targets.',
                'url' => 'https://example.com',
                'urlToImage' => 'https://via.placeholder.com/400x200/059669/FFFFFF?text=Environment',
                'source' => ['name' => 'World News']
            ],
            [
                'title' => 'Sports: Championship Finals Preview',
                'description' => 'Teams prepare for the biggest game of the season.',
                'url' => 'https://example.com',
                'urlToImage' => 'https://via.placeholder.com/400x200/F59E0B/FFFFFF?text=Sports',
                'source' => ['name' => 'Sports Weekly']
            ],
            [
                'title' => 'Entertainment: New Movie Breaks Records',
                'description' => 'Latest blockbuster achieves historic box office success.',
                'url' => 'https://example.com',
                'urlToImage' => 'https://via.placeholder.com/400x200/8B5CF6/FFFFFF?text=Entertainment',
                'source' => ['name' => 'Entertainment News']
            ],
            [
                'title' => 'Health: New Study on Wellness',
                'description' => 'Research reveals important findings about healthy living.',
                'url' => 'https://example.com',
                'urlToImage' => 'https://via.placeholder.com/400x200/EC4899/FFFFFF?text=Health',
                'source' => ['name' => 'Health Magazine']
            ],
            [
                'title' => 'Science: Discovery in Space Exploration',
                'description' => 'Astronomers make groundbreaking observations of distant galaxies.',
                'url' => 'https://example.com',
                'urlToImage' => 'https://via.placeholder.com/400x200/6366F1/FFFFFF?text=Science',
                'source' => ['name' => 'Science Daily']
            ],
            [
                'title' => 'Travel: Top Destinations for 2024',
                'description' => 'Discover the most popular travel spots for your next adventure.',
                'url' => 'https://example.com',
                'urlToImage' => 'https://via.placeholder.com/400x200/14B8A6/FFFFFF?text=Travel',
                'source' => ['name' => 'Travel Guide']
            ]
        ]
    ];
}
