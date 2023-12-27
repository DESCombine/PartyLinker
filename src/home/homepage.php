<!DOCTYPE html>
    <html lang="it">
        <head>
            <title><?php echo $templateParams["title"]; ?></title>
            <meta charset="UTF-8"/>
            <link rel="stylesheet" type="text/css" href="static/css/homepage.css"/>
            <!-- Font Renner import -->
            <link rel="stylesheet" href="https://indestructibletype-fonthosting.github.io/renner.css" type="text/css" charset="utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <!-- Bootstrap import -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        </head>
        <body>
            <div class="container-fluid p-0 overflow-hidden">
                <header class="text-center">
                    <h1>PartyLinker</h1>
                </header>
                <main class="container-fluid mt-3">
                    <div class="row justify-content-start" id="online-users">
                        <h2>&#128101Online Users</h2>
                        <div class="me-1">
                            <ul class="list-group list-group-flush list-group-horizontal">
                                <li class="list-group-item">
                                    <img src="static/img/default-profile.png" alt="" class="img-fluid img-thumbnail">
                                </li>
                            </ul>
                        </div>
                    </div>
                    <ol class="list-group list-group-flush my-3">
                        <li class="container-fluid list-group-item">
                            <div class="row justify-content-start" id="us-post">
                                <div class="col-1">
                                    <img src="static/img/default-profile.png" alt="" class="img-fluid float-start img-thumbnail">
                                </div>
                                <div class="col-1">
                                    <h2>Username</h2>
                                </div>
                            </div>
                            <div class="container-fluid mb-3" id="img-post">
                                <div class="justify-content-center">
                                    <img src="static/img/default-image.png" alt="" class="img-fluid mx-auto d-block">
                                </div>
                            </div>
                        </li>
                    </ol>
                </main>
                <aside class="container-fluid text-center">
                    <ol class="list-group list-group-flush list-group-horizontal">
                        <li class="list-group-item">
                            <form action="#">
                                <input type="submit" class="form-control" name="sumbit-like" value="&#129293">
                            </form>
                        </li>
                        <li class="list-group-item">
                            <a href="comments.html">&#128172</a>
                        </li>
                        <li class="list-group-item">
                            <a href="partecipants.html">&#128111</a>
                        </li>
                    </ol>
                    <p>Post description</p>
                </aside>
                <nav class="navbar navbar-expand bg-light justify-content-center fixed-bottom">
                    <ol class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="homepage.php">&#127968</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../search/searchpage.html">&#128269</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../map/mappage.html">&#128506</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../profile/profilepage.html">&#128100</a>
                        </li>
                    </ol>
                </nav>
            </div>
            <!-- Bootstrap import -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        </body>
    </html>