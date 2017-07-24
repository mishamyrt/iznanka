<!DOCTYPE html>
<html>
<head>
    <title><?php echo self::get('title') ?></title> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo self::anticache('/img/favicon.png') ?>" /> 
</head>
<body>
    <?php echo self::display(self::get('template')) ?> 
</body>
</html>