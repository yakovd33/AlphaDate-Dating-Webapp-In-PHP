<?php
    require_once(__DIR__ . '/Handlebars/Autoloader.php');
    Handlebars\Autoloader::register();

    use Handlebars\Handlebars;
    use Handlebars\Loader\FilesystemLoader;

    # Set the partials files
    
    $partialsDir = __DIR__ . '/templates';
    $partialsLoader = new FilesystemLoader($partialsDir, [
        "extension" => "hbs"
    ]);

    $handlebars = new Handlebars([
        "loader" => $partialsLoader,
        "partials_loader" => $partialsLoader
    ]);

    $handlebars->addHelper("safe", function ($template, $context, $args, $source) {
        return $context->get($args);
    });

    $handlebars->addHelper('random_emoji', function ($template, $context, $args, $source) {
        $emojis = [ 'smile-wink', 'smile-beam', 'smile', 'grin-beam-sweat', 'grin-squint-tears', 'grin-squint', 'grin-hearts', 'grin-beam', 'grin-alt', 'grin', 'grin-wink' ];
        return $emojis[rand(0, count($emojis) - 1)];
    });

    $handlebars->addHelper('random_emoji', function ($template, $context, $args, $source) {
        // return $args[0$value > $max;
        return true;
    });
?>