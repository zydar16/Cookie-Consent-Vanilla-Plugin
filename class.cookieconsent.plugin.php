<?php
$PluginInfo['CookieConsent'] = array(
    'Name' => 'Cookie Consent',
    'Description' => 'This plugin allows you to put a cookie consent banner on your Forum',
    'Version' => '1.0',
    'Author' => "Timucin Besken",
    'AuthorEmail' => 'beskent@gmail.com',
    'AuthorUrl' => 'https://github.com/TimP4w/Cookie-Consent-Vanilla-Plugin',
    'MobileFriendly' => TRUE,
);



/**
 *This spawns a Cookie Banner for the WebMasters that needs to apply the Cookie EU law to their forums
 * and creates 2 pages: Privay Policy page and Cookie Policy page.
 *
 * A lot of work is made thanks to the Example plugin of Robin Jurinka: HowToVanillaPage under MIT License.
 *
 * @package CookieConsent
 * @author Timucin Besken
 * @license MIT
 */
class CookieConsent extends Gdn_Plugin
{

    public function __construct() {

    }

    /**
     * Setup is run whenever plugin is enabled.
     *
     *
     * @return void.
     * @package CookieConsent
     * @since 1.0
     */
    public function setup() {
        //Save default settings into Config file
        saveToConfig('Plugin.CookieConsent.Message', T('Plugins.CookieConsent.Message', 'This website uses cookies to ensure you get the best experience on our website'));
        saveToConfig('Plugin.CookieConsent.Dismiss', T('Plugins.CookieConsent.Dismiss', 'Got it!'));
        saveToConfig('Plugin.CookieConsent.LearnMore', T('Plugins.CookieConsent.LearnMore', 'Learn More!'));
        saveToConfig('Plugin.CookieConsent.Theme', 'dark-top');


        // get a reference to Vanillas routing class
        $router = Gdn::Router();

        // Old long slug
        $PrivacyPolicyLong = "vanilla/privacypolicy".'$1';
        $CookiePolicyLong = "vanilla/cookiepolicy".'$1';

        // New short slug
        $PrivacyNewRoute = '^privacypolicy(/.*)?$';
        $CookieNewRoute = '^cookiepolicy(/.*)?$';

        // Set routes to yourforum.com/privacypolicy and cookiepolicy
        if (!$router->matchRoute($PrivacyNewRoute)) {
            $router->setRoute($PrivacyNewRoute, $PrivacyPolicyLong, 'Internal');
        }

        if (!$router->matchRoute($CookieNewRoute)) {
            $router->setRoute($CookieNewRoute, $CookiePolicyLong, 'Internal');
        }
    }


    /**
     * OnDisable is run whenever plugin is disabled.
     *
     *
     * @return void.
     * @package CookieConsent
     * @since 1.0
     */
    public function onDisable() {
        // Removew from Config
        removeFromConfig('Plugin.CookieConsent.Message');
        removeFromConfig('Plugin.CookieConsent.Dismiss');
        removeFromConfig('Plugin.CookieConsent.LearnMore');
        removeFromConfig('Plugin.CookieConsent.Theme');

        //Delete routes
        Gdn::Router()-> DeleteRoute('^privacypolicy(/.*)?$');
        Gdn::Router()-> DeleteRoute('^cookiepolicy(/.*)?$');

    }



    /**
     * Create a new Privacy Policy page that uses the current theme.
     *
     *
     * @param object $sender VanillaController.
     * @return void.
     * @package CookieConsent
     * @since 1.0
     */
    public function vanillaController_privacypolicy_create($Sender) {


        //Set default Template
        $Sender->masterView();

        // If you've changed the route, you should change that value, too. We
        // use it for highlighting the menu entry.
        $Sender->SelfUrl = "privacypolicy";

        //Add Vanilla modules
        foreach (c('Modules.Vanilla.Panel') as $module) {
            // We have to exclude the MeModule here, because it is already added
            // by the template and it would appear twice otherwise.
            if ($module != 'MeModule') {
                $Sender->addModule($module);
            }
        }

        //Page Title
        $Sender->title(t("Privacy Policy"));

        // This sets the breadcrumb to our current page.
        $Sender->setData('Breadcrumbs', array(array('Name' => t("Privacy Policy"), 'Url' => "privacypolicy")));
        // Render the View
        $Sender->Render(parent::getView('privacypolicy.php'));
    }

    /**
     * Create a new Cookie Policy page that uses the current theme.
     *
     *
     * @param object $sender VanillaController.
     * @return void.
     * @package CookieConsent
     * @since 1.0
     */
    public function vanillaController_cookiepolicy_create($Sender) {

        //Set default Template
        $Sender->masterView();

        // If you've changed the route, you should change that value, too. We
        // use it for highlighting the menu entry.
        $Sender->SelfUrl = "cookiepolicy";

        //Add Vanilla modules
        foreach (c('Modules.Vanilla.Panel') as $module) {
            // We have to exclude the MeModule here, because it is already added
            // by the template and it would appear twice otherwise.
            if ($module != 'MeModule') {
                $Sender->addModule($module);
            }
        }

        //Page Title
        $Sender->title(t("Cookie Policy"));

        // This sets the breadcrumb to our current page.
        $Sender->setData('Breadcrumbs', array(array('Name' => t("Cookie Policy"), 'Url' => "cookiepolicy")));
        // Render the View
        $Sender->Render(parent::getView('cookiepolicy.php'));
    }

    /**
     * Create a Configuration Page for the plugin
     *
     *
     * @return void.
     * @package CookieConsent
     * @since 1.0
     */
    public function pluginController_CookieConsent_create($Sender) {
        $Sender->title('Cookie Consent');
        $Sender->addSideMenu('plugin/CookieConsent');
        $Sender->Form = new Gdn_Form();
        $this->dispatch($Sender, $Sender->RequestArgs);
    }


    /**
     * Controller for the plugin configuration page.
     *
     *
     * @param object $sender
     * @return void.
     * @package CookieConsent
     * @since 1.0
     */
    public function controller_index($Sender) {
        // Prevent non-admins from accessing this page
        $Sender->permission('Garden.Settings.Manage');
        $Sender->setData('PluginDescription',$this->getPluginKey('Description'));

        $Validation = new Gdn_Validation();
        $ConfigurationModel = new Gdn_ConfigurationModel($Validation);

        $Message =  $TrimSize = c('Plugin.CookieConsent.Message');
        $Dismiss =  $TrimSize = c('Plugin.CookieConsent.Dismiss');
        $LearnMore =  $TrimSize = c('Plugin.CookieConsent.LearnMore');
        $Theme =  $TrimSize = c('Plugin.CookieConsent.Theme');

        $ConfigurationModel->setField(array(
            'Plugin.CookieConsent.Message'     => $Message,
            'Plugin.CookieConsent.Dismiss'      => $Dismiss,
            'Plugin.CookieConsent.LearnMore'    => $LearnMore,
            'Plugin.CookieConsent.Theme'    => $Theme
        ));


        $Sender->Form->setModel($ConfigurationModel);


        if ($Sender->Form->authenticatedPostBack() === false) {
            $Sender->Form->setData($ConfigurationModel->Data);
        } else {
            $ConfigurationModel->Validation->applyRule('Plugin.CookieConsent.Message', 'Required');
            $ConfigurationModel->Validation->applyRule('Plugin.CookieConsent.Dismiss', 'Required');
            $ConfigurationModel->Validation->applyRule('Plugin.CookieConsent.LearnMore', 'Required');
            $Saved = $Sender->Form->save();
            if ($Saved) {
                $Sender->StatusMessage = T('Saved', 'Saved');
            }
        }

        $Sender->render($this->getView('cookieconsent.php'));
    }


    /**
     * Add a link to the Dashboard side menu
     *
     *
     * @param object $sender
     * @return void.
     * @package CookieConsent
     * @since 1.0
     */
    public function base_getAppSettingsMenuItems_handler($Sender) {
        $Menu = &$Sender->EventArguments['SideMenu'];
        $Menu->addLink('Add-ons', 'Cookie Consent', 'plugin/CookieConsent', 'Garden.Settings.Manage');
    }


    /**
     * Create a link to our page in the menu, on the footer and render the Javascript
     * to spawn the Cookie Consent bar.
     *
     * @param object $sender Garden Controller.
     * @return void.
     * @package CookieConsent
     * @since 1.0
     */
    public function Base_Render_Before($Sender)
    {

        $Message =  $TrimSize = c('Plugin.CookieConsent.Message');
        $Dismiss =  $TrimSize = c('Plugin.CookieConsent.Dismiss');
        $LearnMore = $TrimSize = c('Plugin.CookieConsent.LearnMore');
        $Theme = $TrimSize = c('Plugin.CookieConsent.Theme');
        $link = "cookiepolicy";

        $JavaScript = '<script type="text/javascript">
                            window.cookieconsent_options = {"message": "'.$Message.'",
                                                            "dismiss": "'.$Dismiss.'",
                                                            "learnMore": "'.$LearnMore.'",
                                                            "link": "'.$link.'",
                                                            "theme": "'.$Theme.'"};
                        </script>
                        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/1.0.9/cookieconsent.min.js"></script>
                        ';
        $Sender->Head->AddString($JavaScript);


        if ($Sender->Menu) {
            // Select Menu item
            if ($Sender->SelfUrl == "privacypolicy") {
                $AnchorAttributes = array('class' => 'Selected');
            } else {
                $AnchorAttributes = '';
            }

            // Add Link to the menu
            $Sender->Menu->AddLink('', t("Privacy Policy"), "privacypolicy", '', $AnchorAttributes);
        }

        // Add links to the footer
        $PPFooterLink = '<div align="right"><a href="privacypolicy">Privacy Policy</a></div>';
        $CPFooterLink = '<div align="right"><a href="cookiepolicy">Cookie Policy</a></div>';
        $Sender->addAsset('Foot', $PPFooterLink, 'privacypolicylink');
        $Sender->addAsset('Foot', $CPFooterLink, 'cookiepolicylink');
    }





}