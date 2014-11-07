<?php
# include the PHP library (if not autoloaded)
require('./../lib/php/autoload.php');

################################################
# Optional:
# default is PNG but you may also use SVG
Emojione\Emojione::$imageType = 'svg';

# if you want to host the images somewhere else
# you can easily change the default paths
Emojione\Emojione::$imagePathPNG = './../assets/png/';
Emojione\Emojione::$imagePathSVG = './../assets/svg/';
################################################

?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>::toShort($str) - PHP - Emoji One Labs</title>

    <!-- Emoji One CSS: -->
    <link rel="stylesheet" href="./../assets/css/emojione.min.css" type="text/css" media="all" />

    <!-- jQuery: -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

    <!-- Demos Stylesheet: -->
    <link rel="stylesheet" href="styles/demos.css"/>

    <!-- Typekit: -->
    <script type="text/javascript" src="//use.typekit.net/ivu8ilu.js"></script>
    <script type="text/javascript">try{Typekit.load();}catch(e){}</script>

    <!-- Syntax Highlighting -->
    <script type="text/javascript" src="scripts/shCore.js"></script>
    <script type="text/javascript" src="scripts/shBrushJScript.js"></script>
    <script type="text/javascript" src="scripts/shBrushCss.js"></script>
    <script type="text/javascript" src="scripts/shBrushPhp.js"></script>
    <script type="text/javascript">SyntaxHighlighter.all();</script>
    <link rel="stylesheet" href="styles/shCoreRDark.css"/>

</head>
<body>

<!-- Masthead -->
<header class="masthead">
    <div class="container">
        <h1 class="masthead-title">Emoji One Labs</h1>
    </div>
</header>

<!-- Breadcrum Navigation -->
<nav class="breadcrumbs">
    <div class="container">
        <a class="breadcrumb-item top-level" href="index.html">All Demos</a> &rsaquo;
        <a href="index.html#php">PHP</a> &rsaquo;
        <a class="breadcrumb-item active" href="phptoshort.php">::toShort($str)</a>
    </div>
</nav>

<!-- Page: -->
<main>

    <div class="container" id="output">

        <h1>::toShort($str)</h1>
        <h2>convert native unicode emoji to shortnames</h2>

        <p>Our recommendation is to unify all user inputted text by converting native unicode emoji, such as those inputted by mobile devices, to their corresponding shortnames. This demo shows you how to use the <strong>::toShort($str)</strong> PHP function provided in our toolkit to do just that.</p>

        <p>Feel free to enter native unicode emoji in the input below to test the conversion process. For a complete list of emoji and their shortnames check out <a href="http://emoji.codes/" target="_blank">emoji.codes</a>.</p>

         <p class="notice"><strong>Note: </strong> Once you start dealing with native unicode characters server side, it's important to ensure that your web stack is set up to handle UTF-8 character encoding. That is outside of the scope of our demos, but a quick <a href="http://lmgtfy.com/?q=web+stack+utf-8" target="_blank">Google Search</a> will guide you in the right direction.</p>

        <div class="clearfix">
            <div class="column-1-2 input">
                <h3>Input:</h3>
                <form method="post" action="phptoshort.php#output">
                    <input type="text" id="inputText" name="inputText" value="<?php echo (isset($_POST['inputText'])) ? $_POST['inputText'] : 'Hello world! &#x1f604;'; ?>"/>
                    <input type="submit" value="Convert"/>
                </form>
            </div>
            <div class="column-1-2 output">
                <h3>Output:</h3>
                <p>
                    <?php
                    if(isset($_POST['inputText'])) {
                        echo Emojione\Emojione::toShort($_POST['inputText']);
                    }
                    ?>
                </p>
            </div>
        </div>



        <h3>PHP Snippet:</h3>
        <pre class="brush: php">
&lt;?php
    // include the PHP library (if not autoloaded)
    require('./../lib/php/autoload.php');

    if(isset($_POST['inputText'])) {
      echo Emojione\Emojione::toShort($_POST['inputText']);
    }
?&gt;
        </pre>

    </div>

</main>

<footer class="demo-footer">
    <div class="container">
        <small>&copy; Copyright 2014 Ranks.com.</small>
        <small>Emoji One artwork is licensed under the <a href="https://creativecommons.org/licenses/by/4.0/legalcode">CC-BY-SA-4.0</a> License</small>
        <small>Emoji One demos, documentation, scripts, stylesheets and all other non-artwork is licensed under the <a
                href="http://opensource.org/licenses/MIT">MIT</a> License</small>
    </div>
</footer>

</body>
</html>
