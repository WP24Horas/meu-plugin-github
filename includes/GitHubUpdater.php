<?php

class GitHubUpdater
{
    private $repo_url = 'https://api.github.com/repos/WP24Horas/meu-plugin-github';
    private $version;

    private $plugin_file;

    public function __construct()
{
    if ( ! function_exists( 'get_plugin_data' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugin_data = get_plugin_data(MPG_PLUGIN_FILE);
    $this->version = $plugin_data['Version'];

    $this->plugin_file = plugin_basename(MPG_PLUGIN_FILE);

    add_filter('pre_set_site_transient_update_plugins', [$this, 'check_update']);
    add_filter('plugins_api', [$this, 'plugin_info'], 10, 3);
}


    public function check_update($transient)
{
    if (empty($transient->checked)) {
        return $transient;
    }

    $response = wp_remote_get("{$this->repo_url}/releases/latest", [
        'headers' => [
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url(),
        ],
    ]);

    if (is_wp_error($response)) {
        return $transient;
    }

    $release = json_decode(wp_remote_retrieve_body($response));

    $latest = ltrim($release->tag_name, 'v');

    if (version_compare($this->version, $latest, '<')) {
        $transient->response[$this->plugin_file] = (object)[
            'slug'        => dirname($this->plugin_file),
            'plugin'      => $this->plugin_file,
            'new_version' => $latest,
            'package'     => "https://github.com/WP24Horas/meu-plugin-github/releases/download/{$release->tag_name}/meu-plugin-github.zip",
            'url'         => $release->html_url,
        ];
    }

    return $transient;
}


    public function plugin_info($res, $action, $args)
    {
        if ($action !== 'plugin_information' || $args->slug !== dirname($this->plugin_file)) {
            return false;
        }

        $response = wp_remote_get("{$this->repo_url}/releases/latest", [
            'headers' => ['Accept' => 'application/vnd.github.v3+json'],
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $release = json_decode(wp_remote_retrieve_body($response));

        return (object)[
            'name'        => 'Meu Plugin GitHub',
            'slug'        => dirname($this->plugin_file),
            'version'     => $release->tag_name,
            'author'      => '<a href="https://asllanmaciel.com.br">Asllan Maciel</a>',
            'homepage'    => $release->html_url,
            'download_link' => $this->zip_url,
            'sections'    => ['description' => $release->body],
        ];
    }
}
