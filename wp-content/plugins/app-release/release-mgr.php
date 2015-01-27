<?php

# server setting
$host_name = gethostname();

define('SERVER_DOMAIN','phiresearchlab.org');

// see if we are running on edemo, if so use it in manifest, otherwise use live domain name
if ($host_name == 'plvsirduedemo2.lab.local')
    define('SERVER','edemo'.'.'.SERVER_DOMAIN);  # edemo
else
    define('SERVER','www'.'.'.SERVER_DOMAIN);  # live

define('APP_ROOT','');
define('DOWNLOADS_RELATIVE_PATH','releases/');


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

    public function set_downloads($downloads_rel_path) {

        $this->ios_dir = $downloads_rel_path.'/ios/'.$this->version.'/';
        $this->manifest_link = self::MANIFEST_PREFIX.SERVER.APP_ROOT.$this->ios_dir.self::MANIFEST_FILE;
        $this->ipa_path = APP_ROOT.$this->ios_dir.$this->ipa_file;

    }

    public function manifest_exists() {
        if (file_exists($this->ios_dir.self::MANIFEST_FILE) )
            return true;
        else
            return false;
    }

    public function write_manifest($app_title) {

        $manifest_file = fopen($this->ios_dir.self::MANIFEST_FILE, "w") or die("Can't open file: ".$this->ios_dir.self::MANIFEST_FILE);


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
        fwrite($manifest_file,  '              <string>https://'.SERVER.$this->ipa_path."</string>\n");
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
                echo '" class="btn btn-sm btn-info">iOS Release Download</a>';
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
                        echo '" class="btn btn-sm btn-info">iOS Beta Download</a>';
                    }
                } else if ($this->ipa_path) {
                    echo $anchor_start;
                    echo $this->ipa_path;
                    echo '" class="btn btn-sm btn-info">iOS Beta Download</a>';
                }

            }
        }

        // GitHub links are displayed for all projects, even archived ones
        if ($this->github_link != null) {
            echo '<a href="';
            echo $this->github_link;
            echo '" class="btn btn-sm btn-warning">Code on GitHub</a>';
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

    public function set_downloads($downloads_path) {

        // if no APK file then archived project and no app downloads
        if ($this->apk_file != null) {

            $this->apk_path = "$downloads_path/android/$this->version/$this->apk_file";
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
                echo '" class="btn btn-sm btn-success">Android Release Download</a>';
            } else if($this->apk_path) {
                echo $anchor_start;
                echo $this->apk_path;
                echo '" class="btn btn-sm btn-success">Android Beta Download</a>';
            }

        }
        // GitHub links are displayed for all projects, even archived ones
        if ($this->github_link != null) {
            echo '<a href="';
            echo $this->github_link;
            echo '" class="btn btn-sm btn-warning">Code on GitHub</a>';
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
    public $short_description;
    public $icon;
    public $ios_app;
    public $android_app;
    public $download_path;
    public $has_ios_app;
    public $has_android_app;


    function __construct($name, $title, $short_desc, $icon) {
        $this->name = $name;
        $this->app_title = $title;
        $this->short_description = $short_desc;
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
        echo '<p>';echo $this->short_description;echo '</p>';
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

        // Detailed Information panel
//        echo '<div class="panel panel-default">';
//        echo '<div class="panel-heading">';
//
//        echo '<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#';
//        echo $detailPanelId;
//        echo '">Detailed Information</a></h4></div><div id="';
//        echo $detailPanelId;
//        echo '" class="panel-collapse collapse">';
//        echo '<div class="panel-body">Detailed Information goes here.</div></div></div>';

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

    function __construct($project_name, $platform, $app_name, $app, $icon, $github, $app_store_link)
    {
        $this->name = $project_name;
        $this->platform = $platform;
        $this->app_name = $app_name;
        $this->app = $app;
        $this->icon = $icon;
        $this->github_link = $github;
        $this->app_store_link = $app_store_link;

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
            $this->configure_ios_release();
        elseif ($this->platform === ReleaseManager::$android_platform_id)
            $this->configure_android_release();

    }


    public function configure_ios_release()
    {

        //error_log('Configuring iOS Release object with version '.$this->version,0);

        $this->ios_app = new IosApp($this->version, $this->date, '', $this->app, $this->app_store_link);
        $this->ios_app->set_downloads(ReleaseManager::$download_root.$this->project);
        $this->download_link = $this->ios_app->ipa_path;
        //error_log('Download path set to '.$this->download_link,0);
        $this->manifest_link = $this->ios_app->manifest_link;

    }

    public function configure_android_release()
    {
        $this->android_app = new AndroidApp($this->version, $this->date, '', $this->app, $this->app_store_link);
        $this->android_app->set_downloads(ReleaseManager::$download_root.$this->project);
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


    public static $download_root = 'http://172.16.100.213/wordpress/wp-content/plugins/app-release/releases/';

    private $project_templates;

    function __construct()
    {
        $this->project_templates = [
            self::$photon => new ProjectTemplate(self::$photon, self::$ios_platform_id,'MMWR Express', 'photon.ipa', 'images/mmwr_express_icon.png', 'https://github.com/informaticslab/photon', 'https://itunes.apple.com/us/app/mmwr-express/id868245971?mt=8'),
            self::$lydia_ios => new ProjectTemplate(self::$lydia_ios, self::$ios_platform_id,'STD Tx Guide 2015', 'StdTxGuide.ipa', 'images/std1_icon.png', 'https://github.com/informaticslab/lydia-ios', null),
            self::$lydia_android => new ProjectTemplate(self::$lydia_android,  self::$android_platform_id,'STD Tx Guide 2015', 'lydia-release.apk', 'images/std1_icon.png', 'https://github.com/informaticslab/lydia-droid', null),
            self::$ptt => new ProjectTemplate(self::$ptt, self::$ios_platform_id,'PTT Advisor', 'PTTAdvisor.ipa', 'images/ptt_icon.png', 'https://github.com/informaticslab/ptt-advisor', 'https://itunes.apple.com/us/app/ptt-advisor/id537989131?mt=8&ls=1'),
            self::$bluebird => new ProjectTemplate(self::$bluebird, self::$ios_platform_id,'Bluebird', 'bluebird.ipa', 'images/std1_icon.png', 'https://github.com/informaticslab/bluebird', null),

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

}






