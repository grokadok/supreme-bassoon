<?php
$response->header("Content-Type", "text/html; charset=utf-8");

// If local environment, use dev files, else use minified files
$jsfiles = getenv('ISLOCAL') ? ['js/fetch.js', 'js/main.js'] : ['js/custom.min.js'];
$jsstring = '';
foreach ($jsfiles as $jsfile) {
    $jsstring .= '<script defer src="./assets/' . $jsfile . '"></script>';
}

// Handle card display (should have been done client side)
$content = !empty($card) ? ' card' : '';
$titleEditor = !empty($card) ? 'flipped-left' : '';
$titleCard = !empty($card) ? '' : 'flipped-right';
$form = !empty($card) ? 'flipped-left' : '';
$recto = !empty($card) ? '' : 'flipped-right';
$name = !empty($card) ? $card[0]['name'] : 'Clément';
$message = !empty($card) ? $card[0]['message'] : 'Tagazok.';
$city = !empty($card) ? $card[0]['city'] : 'Lyon';
$temp = !empty($card) ? round($this->weather->getWeather($card[0]['city'])['main']['temp']) : '11';

// HTML code
$main =
    '<!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Leneveu Romain, romain@bopalace.com">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="application-name" content="PopCarteTest">
        <meta name="apple-mobile-web-app-title" content="PopCarteTest">
        <meta name="msapplication-starturl" content="index.php">
        <title>PopCarteTest</title>
        <link rel="stylesheet" href="./assets/css/style.css">
        <link rel="icon" href="./favicon.svg" type="image/svg+xml">
        <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
        <link rel="icon" href="/favicon.png" type="image/png" sizes="32x32">
        ' . $jsstring . '
    </head>
    <body>
        <header>
            <div id="logo">
                <svg  viewBox="0 0 200 31">
                    <path d="M22.08,14.09C22.08,20.85 17.98,25.36 11.79,25.36C8.07,25.36 5.12,23.74 3.37,20.99L3.37,30.99L0,30.99L0,3.54L3.37,3.54L3.37,7.2C5.12,4.45 8.07,2.83 11.79,2.83C17.99,2.83 22.09,7.33 22.09,14.1L22.08,14.09ZM18.53,14.09C18.53,9.16 15.51,5.89 10.95,5.89C6.39,5.89 3.37,9.02 3.37,13.74L3.37,14.44C3.37,19.16 6.4,22.3 10.96,22.3C15.52,22.3 18.54,19.02 18.54,14.09L18.53,14.09ZM47.69,14.09C47.69,20.85 43.24,25.36 36.56,25.36C29.88,25.36 25.43,20.86 25.43,14.09C25.43,7.33 29.88,2.82 36.56,2.82C43.24,2.82 47.69,7.32 47.69,14.09ZM44.14,14.09C44.14,9.16 41.11,5.89 36.55,5.89C32,5.89 28.97,9.16 28.97,14.09C28.97,19.02 32,22.29 36.55,22.29C41.11,22.29 44.15,19.02 44.15,14.09L44.14,14.09ZM73.54,14.09C73.54,20.85 69.44,25.36 63.24,25.36C59.52,25.36 56.56,23.74 54.82,21L54.82,31L51.46,31L51.46,3.53L54.83,3.53L54.83,7.19C56.57,4.44 59.53,2.82 63.25,2.82C69.43,2.82 73.54,7.32 73.54,14.09ZM69.99,14.09C69.99,9.16 66.96,5.89 62.4,5.89C57.85,5.89 54.82,9.02 54.82,13.74L54.82,14.44C54.82,19.16 57.85,22.3 62.4,22.3C66.96,22.3 69.99,19.02 69.99,14.09ZM97.83,12.33L94.56,12.33C94.56,8.45 91.88,5.88 88.16,5.88C83.92,5.88 80.88,9.16 80.88,14.08C80.88,19.02 83.94,22.3 88.57,22.3C92.99,22.3 95.6,19.72 95.95,15.85L99.21,15.85C98.87,21.55 94.91,25.36 88.47,25.36C81.79,25.36 77.33,20.86 77.33,14.09C77.33,7.33 81.89,2.82 88.43,2.82C93.97,2.82 97.83,6.62 97.83,12.32L97.83,12.33ZM123.18,18.32C123.18,21.35 123.56,22.12 125.41,22.12L125.41,25.01C121.93,25.01 120.54,24.41 120.08,22.23C118.52,24.2 115.67,25.36 111.98,25.36C106.38,25.36 102.62,22.58 102.62,18.39C102.62,14.19 106.34,11.44 111.91,11.44C115.39,11.44 118.17,12.46 119.81,14.19L119.81,11.63C119.81,8.17 117.23,5.89 113.41,5.89C109.93,5.89 107.39,7.69 107.04,10.4L103.77,10.4C104.12,5.86 108.01,2.83 113.55,2.83C119.32,2.83 123.18,6.35 123.18,11.63L123.18,18.32ZM119.8,18.39C119.8,16.06 117.1,14.51 112.99,14.51C108.89,14.51 106.17,16.06 106.17,18.39C106.17,20.71 108.92,22.29 113.06,22.29C117.09,22.29 119.81,20.75 119.81,18.39L119.8,18.39ZM150,10.64L150,14.44L146.74,14.44L146.74,11.41C146.74,7.75 144.68,5.71 141.52,5.71C138.52,5.71 136.2,8 136.2,11.41L136.2,22.12L143.74,22.12L143.74,25.01L129.34,25.01L129.34,22.12L132.83,22.12L132.83,6.41L129.35,6.41L129.35,3.53L136.2,3.53L136.2,5.98C137.63,3.98 139.96,2.82 142.85,2.82C147.33,2.82 150.01,5.85 150.01,10.64L150,10.64ZM171.33,13.95L174.6,13.95L174.6,14.09C174.6,21.31 171.37,25.36 165.63,25.36C159.88,25.36 156.72,21.56 156.72,14.79L156.72,6.41L153.24,6.41L153.24,3.53L156.72,3.53L156.72,0L160.09,0L160.09,3.53L172.72,3.53L172.72,6.4L160.1,6.4L160.1,14.08C160.1,19.33 162.09,22.28 165.64,22.28C169.29,22.28 171.34,19.33 171.34,14.08L171.34,13.94L171.33,13.95ZM200,17.68C199.44,22.3 195.27,25.36 189.6,25.36C182.89,25.36 178.43,20.85 178.43,14.09C178.43,7.32 182.78,2.82 189.39,2.82C195.13,2.82 199.03,6.94 199.03,13.1L199.03,15.74L182.12,15.74C182.75,19.72 185.67,22.29 189.78,22.29C193.43,22.29 196.21,20.46 196.73,17.68L200,17.68ZM182.05,12.86L195.48,12.86C195.38,8.66 192.77,5.88 189.01,5.88C185.11,5.88 182.47,8.63 182.05,12.86Z" style="fill-rule:nonzero;"/>
                </svg>
            </div>
            <div id="hamburger">
                    <svg viewBox="0 0 200 178">
                    <path d="M0 11.13A11.13 11.13 0 0 1 11.11 0H188.9A11.1 11.1 0 0 1 200 11.13a11.14 11.14 0 0 1-11.11 11.12H11.1A11.1 11.1 0 0 1 0 11.12ZM0 89a11.13 11.13 0 0 1 11.11-11.13H188.9A11.1 11.1 0 0 1 200 89a11.14 11.14 0 0 1-11.11 11.13H11.1A11.1 11.1 0 0 1 0 89Zm0 77.88a11.13 11.13 0 0 1 11.11-11.13H188.9a11.1 11.1 0 0 1 11.1 11.13A11.14 11.14 0 0 1 188.89 178H11.1A11.1 11.1 0 0 1 0 166.87Z">
                    </path>
                </svg>
            </div>
        </header>
        <nav>
            <ul>
                <li><span>Anniversaire</span></li>
                <li><span>Mariage</span></li>
                <li><span>Naissance</span></li>
                <li><span>Baptême</span></li>
            </ul>
        </nav>
        <main>
            <div class="container">
                <div class="title">
                    <h1 id="title-editor" class="' . $titleEditor . '">Editeur de carte</h1>
                    <h1 id="title-card" class="' . $titleCard . '">Carte écrite par ' . $name . '</h1>
                </div>
                <div class="content' . $content . '">
                    <div id="form" class="' . $form . '">
                        <span>Prénom</span>
                        <input id="name" type="text" placeholder="Maxime">
                        <span>Message</span>
                        <textarea id="message" placeholder="Coucou ! Voici des nouvelles..."></textarea>
                        <span>Ville</span>
                        <div>
                            <input id="city" type="text" autocomplete="false" placeholder="Lyon">
                            <ul id="predictions"></ul>
                        </div>
                        <button id="submit">Créer</button>
                    </div>
                    <div id="recto" class="' . $recto . '">
                        <img src="./assets/img/photo.jpg" alt="La belle photo !">
                    </div>
                    <div id="verso" class="flipped-right">
                        <img src="./assets/img/carteverso.jpg" alt="Verso de la carte.">
                        <span id="verso-message">' . $message . '</span>
                        <span id="verso-end-message">Nous pensons à vous depuis <strong>' . $city . '</strong> ;)<br>Actuellement, il y fait <strong>' . $temp . '°C</strong>.</span>
                    </div>
                </div>
            </div>
        </main>
        <footer>
            <svg viewBox="0 0 200 200">
                <path d="M146.8 151.2c9.73-8.84 10.71-20.78 2.42-29.9-8.28-9.12-19.97-9.54-29.3-1.06l-1.39 1.28c-9.32 8.47-10.02 20.15-1.73 29.27 8.28 9.12 20.25 9.29 30 .44v-.02Zm6.41 7.13c-13.36 12.14-29.73 12.01-40.98-.37-6.77-7.45-8.94-16.27-6.68-24.68L85.8 151.25l-6.12-6.76 54.28-49.33 6.13 6.76-7.23 6.57c8.58-1.45 17.16 1.56 23.93 8.99 11.25 12.38 9.8 28.69-3.56 40.83M78.63 84.1c-5.9-11.76-17.14-15.9-28.15-10.38-11.01 5.52-14.56 16.68-8.9 27.93l.87 1.68c5.66 11.27 16.72 15.07 27.72 9.55 11.01-5.53 14.4-17.02 8.5-28.78h-.04Zm8.6-4.3c8.1 16.13 3.59 31.86-11.37 39.37-8.98 4.5-18.07 4.24-25.56-.2l11.98 23.86-8.15 4.1-32.95-65.55 8.15-4.1 4.4 8.75c.9-8.67 6.1-16.12 15.1-20.64 14.95-7.5 30.27-1.73 38.37 14.4M141 50.8c-4.98-12.2-15.86-17.2-27.26-12.54-11.4 4.66-15.67 15.85-10.7 28.03C108 78.48 118.9 83.48 130.3 78.83c11.4-4.66 15.67-15.84 10.7-28.04Zm8.87-3.63c6.82 16.68.23 32.4-16.48 39.23-16.7 6.83-32.4.23-39.23-16.48-6.82-16.71-.23-32.41 16.48-39.23 16.72-6.82 32.4-.24 39.23 16.48Z"></path><path d="M100 200C44.86 200 0 155.14 0 100S44.86 0 100 0s100 44.86 100 100-44.86 100-100 100Zm0-191.88C49.34 8.12 8.12 49.34 8.12 100s41.22 91.88 91.88 91.88 91.88-41.22 91.88-91.88c0-50.67-41.22-91.88-91.88-91.88Z">
                </path>
            </svg>
        </footer>
    </body>
    </html>';

$response->end($main);
