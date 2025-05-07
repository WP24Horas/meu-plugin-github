<?php
/**
 * Plugin Name: Meu Plugin GitHub
 * Description: Plugin de exemplo para testar atualizações via GitHub.
 * Version: 1.0.2
 * Author: Asllan Maciel
 */

defined('ABSPATH') || exit;

// Define a constante necessária para updates
define('MPG_PLUGIN_FILE', __FILE__);
define('MPG_PLUGIN_TESTE', __FILE__);
define('MPG_PLUGIN_TESTE2', __FILE__);

// Autoload se quiser testar depois, mas por hora deixamos simples
// require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

add_action('init', function () {
    error_log('Meu Plugin GitHub ativo!');
});

require_once __DIR__ . '/includes/GitHubUpdater.php';
new GitHubUpdater();
