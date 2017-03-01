<?php

// Use ClearDB
$db = parse_url(getenv('CLEARDB_DATABASE_URL'));
$container->setParameter('database_driver', 'pdo_mysql');
$container->setParameter('database_host', $db['host']);
$container->setParameter('database_port', '~');
$container->setParameter('database_name', trim($db['path'], '/'));
$container->setParameter('database_user', $db['user']);
$container->setParameter('database_password', $db['pass']);

$container->setParameter('discord_client_id', getenv('DISCORD_CLIENT_ID'));
$container->setParameter('discord_client_secret', getenv('DISCORD_CLIENT_SECRET'));
