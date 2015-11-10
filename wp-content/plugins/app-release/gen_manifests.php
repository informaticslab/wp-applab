<?php

require("release-mgr.php");

$release_mgr = new ReleaseManager();

$ios_releases = array (
    $release_mgr->configure_release(ReleaseManager::$bluebird,'0.1.6.1'),
    $release_mgr->configure_release(ReleaseManager::$epi, '2.0'),
    $release_mgr->configure_release(ReleaseManager::$lydia_ios, '2.2.2.1'),
    $release_mgr->configure_release(ReleaseManager::$minesim, '0.7301.276'),
    $release_mgr->configure_release(ReleaseManager::$mmwrmap, '1.3.6.1'),
    $release_mgr->configure_release(ReleaseManager::$mmwrnav, '0.8.12.1'),
    $release_mgr->configure_release(ReleaseManager::$retro, '0.2.2.1'),
    $release_mgr->configure_release(ReleaseManager::$tempmon, '0.2.3.1'),
    $release_mgr->configure_release(ReleaseManager::$trainers_guide, '0.9.4'),
    $release_mgr->configure_release(ReleaseManager::$wisqars, '0.2.7'),
    $release_mgr->configure_release(ReleaseManager::$lifeguard, '0.3.1.2'),
    $release_mgr->configure_release(ReleaseManager::$photon, '1.3.4.4'),
    $release_mgr->configure_release(ReleaseManager::$ladder_safety_ios, '1.7.1.2')

);

foreach ($ios_releases as $release) {

    $release->ios_app->write_manifest($release->app_name);

}
