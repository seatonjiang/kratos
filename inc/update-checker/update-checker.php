<?php
/**
 * Plugin Update Checker Library 4.5
 * http://w-shadow.com/
 * 
 * Copyright 2019 Janis Elsts
 * Released under the MIT license. See license.txt for details.
 */

require dirname(__FILE__) . '/Puc/v4p5/Factory.php';
require dirname(__FILE__) . '/Puc/v4/Factory.php';
require dirname(__FILE__) . '/Puc/v4p5/Autoloader.php';
new Puc_v4p5_Autoloader();

//Register classes defined in this file with the factory.
Puc_v4_Factory::addVersion('Plugin_UpdateChecker', 'Puc_v4p5_Plugin_UpdateChecker', '4.5');
Puc_v4_Factory::addVersion('Theme_UpdateChecker', 'Puc_v4p5_Theme_UpdateChecker', '4.5');

Puc_v4_Factory::addVersion('Vcs_PluginUpdateChecker', 'Puc_v4p5_Vcs_PluginUpdateChecker', '4.5');
Puc_v4_Factory::addVersion('Vcs_ThemeUpdateChecker', 'Puc_v4p5_Vcs_ThemeUpdateChecker', '4.5');

Puc_v4_Factory::addVersion('GitHubApi', 'Puc_v4p5_Vcs_GitHubApi', '4.5');
Puc_v4_Factory::addVersion('BitBucketApi', 'Puc_v4p5_Vcs_BitBucketApi', '4.5');
Puc_v4_Factory::addVersion('GitLabApi', 'Puc_v4p5_Vcs_GitLabApi', '4.5');