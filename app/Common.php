<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (!function_exists('auth_user')) {

    function auth_user()
    {
        return [
            'id' => session()->get('user_id'),
            'name' => session()->get('user_name'),
            'email' => session()->get('user_email'),
        ];
    }
}

if (!function_exists('vite_assets')) {

    function vite_assets()
    {
        $manifestPath = FCPATH . 'assets/.vite/manifest.json';

        if (!file_exists($manifestPath)) {
            return ['css' => null, 'js' => null];
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);

        $app = $manifest['resources/js/app.js'];

        return [
            'css' => isset($app['css'][0]) ? base_url('assets/' . $app['css'][0]) : null,
            'js' => isset($app['file']) ? base_url('assets/' . $app['file']) : null,
        ];
    }
}

if (!function_exists('is_active')) {

    function is_active($path)
    {
        $uri = service('uri');

        return $uri->getSegment(1) === $path ? 'active' : '';
    }
}

if (!function_exists('is_admin')) {
    function is_admin()
    {
        return auth_user()['email'] === 'admin@mail.com';
    }
}
