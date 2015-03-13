<?php

# server setting
$host_name = gethostname();

define('SERVER_DOMAIN','phiresearchlab.org');
#define('SERVER','lvsiiuwp4.lab.local'.'.'.SERVER_DOMAIN);

define('SERVER','applab.phiresearchlab.org/');
// see which VM we are running so manifest links get generated properly

define('RELEASES_RELATIVE_PATH', 'releases/');
define('DOWNLOADS_RELATIVE_PATH','wp-content/plugins/app-release/releases/');

date_default_timezone_set('America/New_York');


abstract class BaseApp {
    public $version;
    public $release_date;
    public $size;
    public $github_link;
    public $mixpanel_id;
    public $app_is_archived;

    function __construct($ver, $rel, $size) {
        $this->version = $ver;
        $this->release_date = $rel;
        $this->size = $size;
        $this->app_is_archived = false;

    }

    public function set_github_link($link) {

        $this->github_link = $link;

    }

    public function set_mixpanel_id($link) {

        $this->mixpanel_id = $link;

    }

    public function archive_app() {
        $this->app_is_archived = true;

    }

}

class IosApp extends BaseApp {
    public $manifest_link;
    public $manifest_file_path;
    public $itunes_link;
    public $ipa_file;
    public $ios_dir;
    public $ipa_path;
    public $bundle_id;

    # common iOS settings
    const MANIFEST_PREFIX = 'itms-services://?action=download-manifest&url=https://';
    const MANIFEST_FILE = 'manifest.plist';

    function __construct($ver, $rel, $size, $ipa_file, $itunes_link) {
        parent::__construct($ver, $rel, $size);
        $this->ipa_file = $ipa_file;
        $this->itunes_link = $itunes_link;

    }

    public function print_info() {

        printf("\tIOS App Info\n");
        printf("\t\tIPA File = %s\n", $this->ipa_file);
        printf("\t\tIPA Path = %s\n", $this->ipa_path);
        printf("\t\tIOS dir = %s\n", $this->ios_dir);
        printf("\t\tManifest link = %s\n", $this->manifest_link);
        printf("\t\tManifest file path = %s\n", $this->manifest_file_path);
        printf("\t\tIOS dir = %s\n", $this->ios_dir);

        printf("\t\tBundle ID = %s\n", $this->bundle_id);

    }

    public function set_downloads($project) {

        $this->ios_dir = DOWNLOADS_RELATIVE_PATH.$project.'/ios/'.$this->version;
        $this->manifest_link = self::MANIFEST_PREFIX.SERVER.$this->ios_dir.'/'.self::MANIFEST_FILE;
        $this->manifest_file_path = RELEASES_RELATIVE_PATH.$project.'/ios/'.$this->version.'/'.self::MANIFEST_FILE;
        $this->ipa_path = $this->ios_dir.'/'.$this->ipa_file;

    }

    public function manifest_exists() {
        if (file_exists($this->ios_dir.self::MANIFEST_FILE) )
            return true;
        else
            return false;
    }

    public function write_manifest($app_title) {

        $manifest_file = fopen($this->manifest_file_path, "w") or die("Can't open file: ".$this->ios_dir.self::MANIFEST_FILE."\n");


        fwrite($manifest_file,  '<?xml version="1.0" encoding="UTF-8"?>'."\n");
        fwrite($manifest_file,  '<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">'."\n");
        fwrite($manifest_file,  '<plist version="1.0">'."\n");
        fwrite($manifest_file,  '  <dict>'."\n");
        fwrite($manifest_file,  '    <key>items</key>'."\n");
        fwrite($manifest_file,  '      <array>'."\n");
        fwrite($manifest_file,  '        <dict>'."\n");
        fwrite($manifest_file,  '          <key>assets</key>'."\n");
        fwrite($manifest_file,  '          <array>'."\n");
        fwrite($manifest_file,  '            <dict>'."\n");
        fwrite($manifest_file,  '              <key>kind</key>'."\n");
        fwrite($manifest_file,  '              <string>software-package</string>'."\n");
        fwrite($manifest_file,  '              <key>url</key>'."\n");
        fwrite($manifest_file,  '              <string>http://'.SERVER.$this->ios_dir.$this->ipa_file."</string>\n");
        fwrite($manifest_file,  '            </dict>'."\n");
        fwrite($manifest_file,  '          </array>'."\n");
        fwrite($manifest_file,  '          <key>metadata</key>'."\n");
        fwrite($manifest_file,  '          <dict>'."\n");
        fwrite($manifest_file,  '            <key>bundle-identifier</key>'."\n");
        fwrite($manifest_file,  '            <string>'.$this->bundle_id."</string>\n");
        fwrite($manifest_file,  '            <key>bundle-version</key>'."\n");
        fwrite($manifest_file,  '            <string>'.$this->version."</string>\n");
        fwrite($manifest_file,  '            <key>kind</key>'."\n");
        fwrite($manifest_file,  '            <string>software</string>'."\n");
        fwrite($manifest_file,  '            <key>title</key>'."\n");
        fwrite($manifest_file,  '            <string>'.$app_title."</string>\n");
        fwrite($manifest_file,  '          </dict>'."\n");
        fwrite($manifest_file,  '        </dict>'."\n");
        fwrite($manifest_file,  '      </array>'."\n");
        fwrite($manifest_file,  '    </dict>'."\n");
        fwrite($manifest_file,  '</plist>'."\n");

        fclose($manifest_file);

    }


    public function set_bundle_id($bundle_id) {
        $this->bundle_id = $bundle_id;
    }

    public function write_download_buttons($app_name) {


        // do not display app metadata if app is archived
        if ($this->app_is_archived == false) {

            echo "iOS Version: $this->version<br />";
            echo "Released: $this->release_date<br />";
            echo "Size: $this->size<br />";
        }


        echo '<div class="btn-toolbar">';

        // do not display any download app buttons if app is archived
        if ($this->app_is_archived == false) {

            $anchor_start = '<a id="'.$this->mixpanel_id.'" href="';

            if($this->itunes_link) {
                echo $anchor_start;
                echo $this->itunes_link;
                echo '" class="btn btn-sm btn-info">iOS Download</a>';
            } else {
                // detect iOS devices
                $iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
                $iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
                $iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");

                if ($iPhone | $iPad | $iPod)
                    $ios_device = true;

                else
                    $ios_device = false;

                // manifest links only work for iOS devices and IPA can only be open on desktop
                if ($ios_device) {
                    if($this->manifest_link) {
                        echo $anchor_start;

                        // set manifest link
                        echo $this->manifest_link;
                        echo '" class="btn btn-sm btn-info">iOS Download</a>';
                    }
                } else if ($this->ipa_path) {
                    echo $anchor_start;
                    echo $this->ipa_path;
                    echo '" class="btn btn-sm btn-info">iOS Download</a>';
                }

            }
        }

        // GitHub links are displayed for all projects, even archived ones
        if ($this->github_link != null) {
            echo '<a href="';
            echo $this->github_link;
            echo '" class="btn btn-sm btn-warning">View Code on GitHub</a>';
        }


        echo '</div>';

    }

    public function write_platform_label() {
        echo '<span class="label label-info" style="margin-left:2px; margin-top:5px; display: inline-block">iOS</span>';
    }
}

class AndroidApp extends BaseApp {
    public $apk_file;
    public $apk_path;
    public $google_play_link;
    public $downloads_path;

    function __construct($ver, $rel, $size, $apk_file, $google_play_link) {
        parent::__construct($ver, $rel, $size);
        $this->apk_file = $apk_file;
        $this->google_play_link = $google_play_link;

    }

    public function set_downloads($project) {

        // if no APK file then archived project and no app downloads
        if ($this->apk_file != null) {

            $this->apk_path = DOWNLOADS_RELATIVE_PATH.'/'.$project.'/android/.'.$this->version.'/'.$this->apk_file;
        }
    }

    public function write_download_buttons() {

        // do not display app metadata if app is archived
        if ($this->app_is_archived == false) {


            //echo '<!-- start write_download_buttons() for AndroidApp object  -->';
            echo "Android Version: $this->version<br />";
            echo "Released: $this->release_date<br />";
            echo "Size: $this->size<br />";

        }
        echo '<div class="btn-toolbar">';

        // do not display any download app buttons if app is archived
        if ($this->app_is_archived == false) {


            $anchor_start = '<a id="'.$this->mixpanel_id.'" href="';


            if($this->google_play_link) {
                echo $anchor_start;
                echo $this->google_play_link;
                echo '" class="btn btn-sm btn-success">Android Download</a>';
            } else if($this->apk_path) {
                echo $anchor_start;
                echo $this->apk_path;
                echo '" class="btn btn-sm btn-success">Android Download</a>';
            }

        }
        // GitHub links are displayed for all projects, even archived ones
        if ($this->github_link != null) {
            echo '<a href="';
            echo $this->github_link;
            echo '" class="btn btn-sm btn-warning">View Code on GitHub</a>';
        }

        echo '</div>';

    }

    public function write_platform_label() {
        echo '<span class="label label-success" style="margin-left:2px; display: inline-block">Android</span>';
    }


}

class Project {
    public $name;
    public $app_title;
    public $icon;
    public $ios_app;
    public $android_app;
    public $download_path;
    public $has_ios_app;
    public $has_android_app;


    function __construct($name, $title, $icon) {
        $this->name = $name;
        $this->app_title = $title;
        $this->icon = $icon;
        $this->download_path = DOWNLOADS_RELATIVE_PATH.$name;
        $this->has_android_app = false;
        $this->has_ios_app = false;


    }

    public function write_ios_manifest_file() {
        // if it does not exist then create it
        $this->ios_app->write_manifest($this->app_title);
    }


    public function write_download_buttons() {

        //echo '<!-- start output from php project->write_download_buttons() function -->';

        if ($this->ios_app) {
            $this->ios_app->write_download_buttons($this->name);
        }
        if ($this->android_app) {
            $this->android_app->write_download_buttons();
        }

        //echo '<!-- end output from php project->write_download_buttons() function -->';

    }

    public function write_platform_labels() {

        echo '<div class="platform-labels">';

        echo '<span>Supported Platforms:</span>';
        if ($this->ios_app) {
            $this->ios_app->write_platform_label();
        }
        if ($this->android_app) {
            $this->android_app->write_platform_label();
        }

        // echo '<!-- end output from php project->write_download_buttons() function -->';
        echo '</div>';

    }

    public function write_panel_heading() {
        //echo '<!-- start output from php project->write_panel_heading() function -->';
        echo '<div class="panel-heading"><h3 class="panel-title right-block">';
        echo $this->app_title;
        echo '</h3></div>';
        //echo '<!-- end output from php project->write_panel_heading() function -->';

    }

    public function write_panel_body() {

        $title = $this->title;

        //echo '<!-- start output from php project->write_panel_body() function -->';
        echo '<div class="panel-body"><div class="media"><a class="pull-left" href="#">';
        echo '<img class="pull-left" src="';
        echo $this->icon;
        echo '" title="'; echo $title; echo '" alt="'; echo $title; echo '" /></a>';
        echo '<div class="media-body">';
        echo '<p>';
        $this->write_platform_labels();

        echo '</div><br />';

        $this->write_inner_panels();

        echo '</div></div>';
        //echo '<!-- end output from php project->write_panel_body() function -->';



    }

    public function write_panel_footer() {
        //echo '<!-- start output from php project->write_panel_footer() function -->';
        echo '<div class="panel-footer">';
        //$this->write_download_buttons();
        //$this->write_platform_labels();

        echo '</div>';
        //echo '<!-- end output from php project->write_panel_footer() function -->';

    }

    public function write_panel() {
        $this->write_panel_heading();
        $this->write_panel_body();
        $this->write_panel_footer();

    }

    public function add_android_app($droid_app) {
        $droid_app->set_downloads($this->download_path);
        $this->android_app = $droid_app;
        $this->has_android_app = true;

    }

    public function add_ios_app($ios_app) {
        $ios_app->set_downloads($this->download_path);
        $this->ios_app = $ios_app;
        $this->has_ios_app = true;

    }

    public function write_inner_panels() {

        $detailPanelId = $this->name.'detailPanel';
        $downloadPanelId = $this->name.'downloadPanel';

        echo '<div class="panel-group" id="accordion">';


        // Downloads  panel
        echo '<div class="panel panel-default">';
        echo '<div class="panel-heading">';

        echo '<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#';
        echo $downloadPanelId;

        echo '">Downloads</a></h4></div><div id="';
        echo $downloadPanelId;
        echo'" class="panel-collapse collapse">';
        echo '<div class="panel-body">';

        $this->write_download_buttons();

        echo '</div></div></div>';


        echo'</div>';

    }


}

class ProjectTemplate
{
    public $name;
    public $platform;
    public $app_name;
    public $app;
    public $icon;
    public $github_link;
    public $app_store_link;
    public $bundle_id;

    function __construct($project_name, $platform, $app_name, $app, $bundle_id, $icon, $github, $app_store_link)
    {
        $this->name = $project_name;
        $this->platform = $platform;
        $this->app_name = $app_name;
        $this->app = $app;
        $this->icon = $icon;
        $this->github_link = $github;
        $this->app_store_link = $app_store_link;
        $this->bundle_id = $bundle_id;

    }

}

class Release
{
    // copy metadata from project template
    public $project;
    public $platform;
    public $app_name;
    public $app;
    public $icon;
    public $date;
    public $manifest_link;
    public $github_link;
    public $app_store_link;
    public $download_link;
    public $ios_app;
    public $android_app;

    // get other data from plug-in UI
    public $version;


    function __construct($project, $version)
    {
        $this->project = $project;
        $this->version = $version;
        // error_log('Release constructor using project '.$project.' for release version '.$version ,0);

    }

    public function print_info() {

        printf("Release Info\n");
        printf("\tApp name = %s\n", $this->app_name);
        printf("\tProject = %s\n", $this->project);
        printf("\tPlatform = %s\n", $this->platform);

    }


    public function init($project_template)
    {
        // error_log('Release init() ',0);

        //$project_template = $this->find_project_template($this->project);

        //if ($project_template == null)
          //  error_log('Release init() could not find project template.', 0);

        $this->project = $project_template->name;
        $this->platform = $project_template->platform;
        $this->app_name = $project_template->app_name;
        $this->app = $project_template->app;
        $this->icon = $project_template->icon;
        $this->github_link = $project_template->github_link;
        $this->app_store_link = $project_template->app_store_link;

        $this->download_link = 'Not available';
        $this->date = date('m/d/Y');

        if ($this->platform === ReleaseManager::$ios_platform_id)
            $this->configure_ios_release($project_template->bundle_id);
        elseif ($this->platform === ReleaseManager::$android_platform_id)
            $this->configure_android_release();

    }


    public function configure_ios_release($bundle_id)
    {

        //error_log('Configuring iOS Release object with version '.$this->version,0);

        $this->ios_app = new IosApp($this->version, $this->date, '', $this->app, $this->app_store_link);
        $this->ios_app->set_downloads($this->project);
        $this->ios_app->set_bundle_id($bundle_id);
        $this->download_link = $this->ios_app->ipa_path;
        //error_log('Download path set to '.$this->download_link,0);
        $this->manifest_link = $this->ios_app->manifest_link;

    }

    public function configure_android_release()
    {
        $this->android_app = new AndroidApp($this->version, $this->date, '', $this->app, $this->app_store_link);
        $this->android_app->set_downloads($this->project);
        $this->download_link = $this->android_app->apk_path;

    }

}

class ReleaseManager
{

    // constants
    public static $ios_platform_id = 'ios';
    public static $android_platform_id = 'android';

    public static $lydia_android = 'lydia-android';
    public static $lydia_ios = 'lydia-ios';
    public static $photon = 'photon';
    public static $ptt = 'ptt';
    public static $bluebird = 'bluebird';
    public static $epi = 'epi';
    public static $everydose = 'everydose';
    public static $retro = 'retro';
    public static $tempmon = 'tempmon';
    public static $trainers_guide = 'trainers-guide';
    public static $wisqars = 'wisqars';
    public static $mmwrnav = 'mmwr-navigator';
    public static $mmwrmap = 'mapapp';
    public static $minesim = 'minesim';


    public static $download_root = 'http://172.16.100.214/wp-content/plugins/app-release/releases/';

    private $project_templates;

    function __construct()
    {
        $this->project_templates = [
            self::$photon => new ProjectTemplate(self::$photon, self::$ios_platform_id,'MMWR Express', 'photon.ipa', null, 'images/mmwr_express_icon.png', 'https://github.com/informaticslab/photon', 'https://itunes.apple.com/us/app/mmwr-express/id868245971?mt=8'),
            self::$lydia_ios => new ProjectTemplate(self::$lydia_ios, self::$ios_platform_id,'STD Tx Guide 2015', 'StdTxGuide.ipa', 'gov.cdc.StdTxGuide', 'images/std1_icon.png', 'https://github.com/informaticslab/lydia-ios', null),
            self::$lydia_android => new ProjectTemplate(self::$lydia_android,  self::$android_platform_id,'STD Tx Guide 2015', 'lydia-release.apk', null, 'images/std1_icon.png', 'https://github.com/informaticslab/lydia-droid', null),
            self::$ptt => new ProjectTemplate(self::$ptt, self::$ios_platform_id,'PTT Advisor', 'PTTAdvisor.ipa', null, 'images/ptt_icon.png', 'https://github.com/informaticslab/ptt-advisor', 'https://itunes.apple.com/us/app/ptt-advisor/id537989131?mt=8&ls=1'),
            self::$bluebird => new ProjectTemplate(self::$bluebird, self::$ios_platform_id,'Bluebird', 'bluebird.ipa', 'gov.cdc.bluebird', 'images/std1_icon.png', 'https://github.com/informaticslab/bluebird', null),
            self::$epi => new ProjectTemplate(self::$epi, self::$ios_platform_id,'Epi Info', 'EpiInfo.ipa', 'gov.cdc.csels.EpiInfo', 'images/epi_icon.png', null, null),
            self::$everydose => new ProjectTemplate(self::$everydose, self::$android_platform_id,'EveryDose', 'EveryDose.apk', null, 'images/tempmon_icon.png', null, null),
            self::$retro => new ProjectTemplate(self::$retro, self::$ios_platform_id,'ARCH-Couples', 'retro.ipa', 'gov.cdc.retro', 'images/retro_icon.png', 'https://github.com/informaticslab/retro', null),
            self::$tempmon => new ProjectTemplate(self::$tempmon, self::$ios_platform_id,'Temp Monitor', 'TempMonitor.ipa', 'gov.cdc.iiu.TempMonitor', 'images/tempmon_icon.png', 'https://github.com/informaticslab/ebolocatemp-ios', null),
            self::$trainers_guide => new ProjectTemplate(self::$trainers_guide, self::$ios_platform_id,'NIOSH Trainers Guide', 'TrainersGuide.ipa', 'gov.cdc.TrainersGuide', 'images/HomecareApp-icon.png', null,null),
            self::$wisqars => new ProjectTemplate(self::$wisqars, self::$ios_platform_id,'WISQARS', 'WisqarsMobile.ipa', 'WisqarsMobileN26', 'images/WISQARSMobileApp72.png', null, null),
            self::$mmwrnav => new ProjectTemplate(self::$mmwrnav, self::$ios_platform_id,'MMWR Navigator', 'mmwr-navigator.ipa', 'gov.cdc.mmwr-navigator', 'images/mmwr_nav_icon.png', 'https://github.com/informaticslab/mmwr-nav', null),
            self::$mmwrmap => new ProjectTemplate(self::$mmwrmap, self::$ios_platform_id,'MMWR Map Navigator', 'MapApp.ipa', 'gov.cdc.MmwrMapApp', 'images/mmwr_map_icon.png', 'https://github.com/informaticslab/mmwr-nav', null),
            self::$minesim => new ProjectTemplate(self::$minesim, self::$ios_platform_id,'NIOSH Mine Safety Training', 'mine_sim.ipa', 'gov.cdc.MineSim', 'images/mine_safety_icon.png', 'https://github.com/informaticslab/vrminesim', null)

        ];


    }

    public function configure_release($project, $version)
    {

        //error_log('Configuring release for project'.$project.', release '.$version, 0);

        $template = $this->project_templates[$project];
        $release = new Release($project, $version);
        $release->init($template);

        return $release;

    }


    public function write_plugin_project_buttons($project)
    {

        foreach ($this->project_templates as $key => $value) {

            echo '<input type="radio" name="project_name_input" value="', $key, '"';
            if($project === $key)
                echo ' checked="checked"';
            echo ' />', $key, '</br>';

        }

    }

    public function write_ios_manifest_files()
    {
        foreach ($this->project_templates as $key => $project) {

            /// TBD manifest are manual right now
            ///$project->write_ios_manifest_file();

        }

    }

}


// code below should eventually be modified or removed
# PTT Advisor App
$ptt_project = new Project('ptt-advisor', 'PTT Advisor', 'images/ptt_icon.png');
$ptt_itunes_link = "https://itunes.apple.com/us/app/ptt-advisor/id537989131?mt=8&ls=1";
$ptt_ios_app = new IosApp('1.0.3.001', '7/6/12', '1.3MB', 'PTTAdvisor.ipa', $ptt_itunes_link);
$ptt_ios_app->set_github_link('https://github.com/informaticslab/ptt-advisor');
$ptt_ios_app->set_mixpanel_id('ptt-applab-download');
$ptt_project->add_ios_app($ptt_ios_app);

# Photon (MMWR Express) App  settings
$photon_project = new Project('photon', 'MMWR Express', 'images/mmwr_express_icon.png');
$photon_itunes_link = "https://itunes.apple.com/us/app/mmwr-express/id868245971?mt=8";
$photon_ios_app = new IosApp('1.0.0','5/6/14', '3.2MB', 'photon.ipa', $photon_itunes_link);
$photon_ios_app->set_github_link('https://github.com/informaticslab/photon');
$photon_ios_app->set_mixpanel_id('mmwrexpress-applab-download');
$photon_project->add_ios_app($photon_ios_app);

# Lydia settings
$lydia_ios_project = new Project('lydia-ios', 'STD Tx Guide 2015', 'images/std1_icon.png');
$lydia_ios_app = new IosApp('0.3.5.1', '2/27/15', '5.4MB', 'StdTxGuide.ipa', null);
$lydia_ios_app->set_github_link('https://github.com/informaticslab/lydia-ios');
$lydia_ios_app->set_bundle_id('gov.cdc.StdTxGuide');
$lydia_ios_app->set_mixpanel_id('lydia-ios-applab-download');
$lydia_ios_project->add_ios_app($lydia_ios_app);

$lydia_android_project = new Project('lydia-android ', 'STD Tx Guide 2015', 'images/std1_icon.png');
$lydia_android_app = new AndroidApp('0.3.9','3/2/14', '1.4MB', 'lydia-release.apk', null);
$lydia_android_app->set_github_link('https://github.com/informaticslab/lydia-droid');
$lydia_android_app->set_mixpanel_id('lydia-android-applab-download');
$lydia_android_project->add_android_app($lydia_android_app);

# Bluebird settings
$bluebird_project = new Project('bluebird', 'Bluebird', 'images/std1_icon.png');
$bluebird_ios_app = new IosApp('0.1.6.1', '10/15/14', '1.1MB', 'bluebird.ipa', null);
$bluebird_ios_app->set_github_link('https://github.com/informaticslab/bluebird');
$bluebird_ios_app->set_bundle_id('gov.cdc.bluebird');
$bluebird_ios_app->set_mixpanel_id('bluebird-ios-applab-download');
$bluebird_project->add_ios_app($bluebird_ios_app);

# CLIP settings
$clip_project = new Project('clip', 'NHSN CLIP', 'images/clip_icon.png');
$clip_ios_app = new IosApp('0.5.12.001', '6/1/2012', '1.9MB', 'clipam.ipa', null);
$clip_ios_app->set_github_link('https://github.com/informaticslab/clip');
$clip_ios_app->set_bundle_id('gov.cdc.clipam');
$clip_ios_app->set_mixpanel_id('clip-applab-download');
$clip_ios_app->archive_app();
$clip_project->add_ios_app($clip_ios_app);


# Epi Info (Stat Calc) iPad App
$epi_project = new Project('epi', 'Epi Info', 'images/epi_icon.png');
$epi_ios_app = new IosApp('2.0', '9/5/14', '15.7MB', 'EpiInfo.ipa', null);
$epi_ios_app->set_bundle_id('gov.cdc.csels.EpiInfo');
$epi_ios_app->set_mixpanel_id('epi-applab-download');
$epi_project->add_ios_app($epi_ios_app);

# EveryDose settings
$everydose_project = new Project('everydose', 'EveryDose', 'images/tempmon_icon.png');
$everydose_android_app = new AndroidApp('1.1.0','12/03/14', '7MB', 'EveryDose.apk', null);
$everydose_android_app->set_mixpanel_id('everydose-android-applab-download');
$everydose_project->add_android_app($everydose_android_app);


# NIOSH Mine Safety Sim App
$minesim_project = new Project('minesim', 'NIOSH Mine Safety Training', 'images/mine_safety_icon.png');
$minesim_ios_app = new IosApp('0.7301.276', '6/19/2012', '38.8MB', 'mine_sim.ipa', null);
$minesim_ios_app->set_github_link('https://github.com/informaticslab/vrminesim');
$minesim_ios_app->set_bundle_id('gov.cdc.MineSim');
$minesim_ios_app->set_mixpanel_id('niosh-mine-applab-download');
$minesim_project->add_ios_app($minesim_ios_app);

# MMWR Map App
$mmwr_map_project = new Project('mapapp', 'MMWR Map Navigator', 'images/mmwr_map_icon.png');
$mmwr_map_ios_app = new IosApp('1.3.6.1', '10/16/14', '328KB', 'MapApp.ipa', null);
$mmwr_map_ios_app->set_github_link('https://github.com/informaticslab/mmwr-map');
$mmwr_map_ios_app->set_bundle_id('gov.cdc.MmwrMapApp');
$mmwr_map_ios_app->set_mixpanel_id('mmwr-map-applab-download');
$mmwr_map_project->add_ios_app($mmwr_map_ios_app);

# MMWR Navigator App
$mmwr_nav_project = new Project('mmwr-navigator', 'MMWR Navigator', 'images/mmwr_nav_icon.png');
$mmwr_nav_ios_app = new IosApp('0.8.12.1', '10/15/14', '10.9MB', 'mmwr-navigator.ipa', null);
$mmwr_nav_ios_app->set_github_link('https://github.com/informaticslab/mmwr-nav');
$mmwr_nav_ios_app->set_bundle_id('gov.cdc.mmwr-navigator');
$mmwr_nav_ios_app->set_mixpanel_id('mmwr-nav-applab-download');
$mmwr_nav_project->add_ios_app($mmwr_nav_ios_app);


# Pedigree (Family History )iPhone App settings
$pedigree_project = new Project('pedigree', 'Family Heath History', 'images/family_hx_icon.png');
$pedigree_ios_app = new IosApp('0.4.10.1', '4/15/14', '925KB', 'FamilyHistory.ipa', null);
$pedigree_ios_app->set_github_link('https://github.com/informaticslab/pedigree');
$pedigree_ios_app->set_bundle_id('gov.cdc.FamilyHistory');
$pedigree_ios_app->set_mixpanel_id('pedigree-ios-applab-download');
$pedigree_ios_app->archive_app();
$pedigree_project->add_ios_app($pedigree_ios_app);

# NIOSH Respirator App
$respguide_project = new Project('respguide', 'NIOSH Facepiece Respirator Guide', 'images/niosh_face_icon.png');
$respguide_ios_app = new IosApp('1.2.8.001', '6/4/2012', '321KB', 'Respirator%20Guide.ipa', null);
$respguide_ios_app->set_github_link('https://github.com/informaticslab/respguide');
$respguide_ios_app->set_bundle_id('gov.CDC.Respirator-Guide');
$respguide_ios_app->set_mixpanel_id('niosh-face-applab-download');
$respguide_ios_app->archive_app();
$respguide_project->add_ios_app($respguide_ios_app);

# Retro iPad App
$retro_project = new Project('retro', 'ARCH-Couples', 'images/retro_icon.png');
$retro_ios_app = new IosApp('0.2.2.1', '10/15/14', '957KB', 'retro.ipa', null);
$retro_ios_app->set_github_link('https://github.com/informaticslab/retro');
$retro_ios_app->set_bundle_id('gov.cdc.retro');
$retro_ios_app->set_mixpanel_id('retro-applab-download');
$retro_project->add_ios_app($retro_ios_app);

# STD 1 settings
$std1_project = new Project('stdguide', 'STD Guide, Version 1', 'images/std1_icon.png');
$std1_ios_app = new IosApp('0.4.4.001', '6/4/2012', '1.73MB', 'Std-Guide.ipa', null);
$std1_ios_app->set_github_link('https://github.com/informaticslab/std1');
$std1_ios_app->set_bundle_id('gov.cdc.Std-Guide');
$std1_ios_app->set_mixpanel_id('std1-applab-download');
$std1_ios_app->archive_app();
$std1_project->add_ios_app($std1_ios_app);

# STD 2 settings
$std2_project = new Project('std2', 'STD Guide, Version 2', 'images/std2_icon.png');
$std2_ios_app = new IosApp('0.9.3.001', '6/4/2012', '2.36MB', 'STD%20Guide%202.ipa', null);
$std2_ios_app->set_github_link('https://github.com/informaticslab/std2');
$std2_ios_app->set_bundle_id('gov.CDC.STD-Guide-2');
$std2_ios_app->set_mixpanel_id('std2-applab-download');
$std2_ios_app->archive_app();
$std2_project->add_ios_app($std2_ios_app);


# STD 3 settings
$std3_project = new Project('std3', 'STD Guide, Version 3', 'images/std3_icon.png');
$std3_ios_app = new IosApp('1.0.9', '6/5/2013', '8.1MB', 'StdGuide3.ipa', 'https://itunes.apple.com/us/app/std-tx-guide/id655206856?mt=8');
$std3_ios_app->set_github_link('https://github.com/informaticslab/shirly');
$std3_ios_app->set_mixpanel_id('std3-applab-download');
$std3_project->add_ios_app($std3_ios_app);
$std3_android_app = new AndroidApp('0.3.1','8/26/14', '732KB', 'StdGuide.apk', 'https://play.google.com/store/apps/details?id=gov.cdc.oid.nchhstp.stdguide');
$std3_android_app->set_mixpanel_id('std3-android-applab-download');
$std3_project->add_android_app($std3_android_app);

# Temp Monitor settings
$tempmon_project = new Project('tempmon', 'Temp Monitor', 'images/tempmon_icon.png');
$tempmon_ios_app = new IosApp('0.2.3.1', '10/10/2014', '370KB', 'TempMonitor.ipa', null);
$tempmon_ios_app->set_bundle_id('gov.cdc.iiu.TempMonitor');
$tempmon_ios_app->set_github_link('https://github.com/informaticslab/ebolocatemp-ios');
$tempmon_ios_app->set_mixpanel_id('tempmon-applab-download');
$tempmon_project->add_ios_app($tempmon_ios_app);
$tempmon_android_app = new AndroidApp('2.0','10/15/14', '76KB', 'TempMonitor.apk', null);
$tempmon_android_app->set_mixpanel_id('tempmon-android-applab-download');
$tempmon_project->add_android_app($tempmon_android_app);

# Tox Guide iPhone App
$tox_guide_project = new Project('toxguide', 'ATSDR ToxGuide', 'images/tox_icon.png');
$tox_guide_ios_app = new IosApp('0.6.2.001', '6/1/2012', '254KB', 'mToxGuide.ipa', null);
$tox_guide_ios_app->set_github_link('https://github.com/informaticslab/toxguide');
$tox_guide_ios_app->set_bundle_id('gov.cdc.mToxGuide');
$tox_guide_ios_app->set_mixpanel_id('tox-applab-download');
$tox_guide_ios_app->archive_app();
$tox_guide_project->add_ios_app($tox_guide_ios_app);

# Wisqars App
$wisqars_project = new Project('wisqars', 'WISQARS Mobile', 'images/WISQARSMobileApp72.png');
$wisqars_ios_app = new IosApp('0.2.7', '9/13/13', '18.5MB', 'WisqarsMobile.ipa', null);
$wisqars_ios_app->set_bundle_id('');
$wisqars_ios_app->set_mixpanel_id('wisqars-applab-download');
$wisqars_project->add_ios_app($wisqars_ios_app);






