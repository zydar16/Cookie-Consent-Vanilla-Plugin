<?php
/**
 * Created by Timucin Besken
 */
$PluginInfo['CookieConsent'] = array(
    'Name' => 'Cookie Consent',
    'Description' => 'This plugin allows you to put a cookie consent banner on your Forum',
    'Version' => '0.5 Beta',
    'Author' => "Timucin Besken",
    'AuthorEmail' => 'beskent@gmail.com',
    'AuthorUrl' => 'http://gaming-italian-group.com',
    'MobileFriendly' => TRUE,
);

class CookieConsent extends Gdn_Plugin
{
    const SHORT_ROUTE = 'cookiepolicy';
    const LONG_ROUTE = 'vanilla/cookiepolicy';
    const PAGE_NAME = 'Cookie Policy';

    public function __construct() {

    }

    public function setup() {
        saveToConfig('Plugin.CookieConsent.Message', T('Plugins.CookieConsent.Message', 'This website uses cookies to ensure you get the best experience on our website'));
        saveToConfig('Plugin.CookieConsent.Dismiss', T('Plugins.CookieConsent.Dismiss', 'Got it!'));
        saveToConfig('Plugin.CookieConsent.LearnMore', T('Plugins.CookieConsent.LearnMore', 'Learn More!'));
        saveToConfig('Plugin.CookieConsent.Theme', 'dark-top');



        // get a reference to Vanillas routing class
        $router = Gdn::Router();

        // this is the ugly slug we want to change
        $pluginPage = self::LONG_ROUTE.'$1';

        // that's how the nice url should look like
        $newRoute = '^'.self::SHORT_ROUTE.'(/.*)?$';

        // "route 'yourforum.com/vanillacontroller/howtovanillapage' to
        // 'yourforum.com/fancyShortName'"
        if (!$router->matchRoute($newRoute)) {
            $router->setRoute($newRoute, $pluginPage, 'Internal');
        }
    }


    public function pluginController_CookieConsent_create($Sender) {

            $Sender->title('Cookie Consent');
            $Sender->addSideMenu('plugin/CookieConsent');
            $Sender->Form = new Gdn_Form();
            $this->dispatch($Sender, $Sender->RequestArgs);


    }


    public function vanillaController_CookiePolicy_create($Sender, $args) {

            // That one is critical! The template of your theme is called
            // default.master.tpl and calling this function sets the master view of
            // this controller to the default theme template.
            $Sender->masterView();

            // If you've changed the route, you should change that value, too. We
            // use it for highlighting the menu entry.
            $Sender->SelfUrl = self::SHORT_ROUTE;

            // If you need custom CSS or Javascript, create the files in the
            // subfolders "design" (for CSS) and "js" (for Javascript). The naming
            // of the files is completely up to you. You can load them by
            // uncommenting the respective line below.
            // $Sender->addCssFile('howtovanillapage.css', 'plugins/HowToVanillaPage');
            // $Sender->addJsFile('howtovanillapage.js', 'plugins/HowToVanillaPage');

            // There is a list of which modules to add to the panel for a standard
            // Vanilla page. We will add all of them, just to be sure our new page
            // looks familiar to the users.
            foreach (c('Modules.Vanilla.Panel') as $module) {
                // We have to exclude the MeModule here, because it is already added
                // by the template and it would appear twice otherwise.
                if ($module != 'MeModule') {
                    $Sender->addModule($module);
                }
            }

            // We can set a title for the page like that. But this is just a short
            // form for $sender->setData('Title', 'Vanilla Page');
            $Sender->title(t(self::PAGE_NAME));

            // This sets the breadcrumb to our current page.
            $Sender->setData('Breadcrumbs', array(array('Name' => t(self::PAGE_NAME), 'Url' => self::SHORT_ROUTE)));

            // If you would like to pass some other data to your view, you should do
            // it with setData. Let's do a "Hello World"...
            if ($args[0] != '') {
                // We will use this for a conditional output.
                $Sender->setData('hasArguments', true);
                // If we have a parameter use this.
                $name = $args[0];
            } else {
                // We will use this for a conditional output.
                $Sender->setData('hasArguments', false);

                $session = Gdn::session();
                if ($session->isValid()) {
                    // If user is logged in, get his name.
                    $name = $session->User->Name;
                } else {
                    // No parameter and no user name? We determine a name by ourselves
                    $name = t('Anonymous');
                }
            }

            // Let's pass this example to our view.
            $Sender->setData('name', $name);

            // We could have simply echoed to screen here, but Garden is a MVC
            // framework and that's why we should use a separate view if possible.
            $Sender->Render(parent::getView('cookiepolicy.php'));
    }


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

    public function base_getAppSettingsMenuItems_handler($Sender) {
        $Menu = &$Sender->EventArguments['SideMenu'];
        $Menu->addLink('Add-ons', 'Cookie Consent', 'plugin/CookieConsent', 'Garden.Settings.Manage');
    }


    public function Base_Render_Before($Sender)
    {

        $Message =  $TrimSize = c('Plugin.CookieConsent.Message');
        $Dismiss =  $TrimSize = c('Plugin.CookieConsent.Dismiss');
        $LearnMore = $TrimSize = c('Plugin.CookieConsent.LearnMore');
        $Theme = $TrimSize = c('Plugin.CookieConsent.Theme');

        $JavaScript = '<script type="text/javascript">
                            window.cookieconsent_options = {"message": "'.$Message.'",
                                                            "dismiss": "'.$Dismiss.'",
                                                            "learnMore": "'.$LearnMore.'",
                                                            "link": "'.self::SHORT_ROUTE.'",
                                                            "theme": "'.$Theme.'"};
                        </script>
                        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/1.0.9/cookieconsent.min.js"></script>
                        ';
        $Sender->Head->AddString($JavaScript);



        if ($Sender->Menu && $Sender->masterView != 'admin') {
            // If current page is our custom page, we want the menu entry
            // to be selected. This is only needed if you've changed the route,
            // otherwise it will happen automatically.
            if ($Sender->SelfUrl == self::SHORT_ROUTE) {
                $AnchorAttributes = array('class' => 'Selected');
            } else {
                $AnchorAttributes = '';
            }

            // We add our Link to a section (but you can pass an empty string
            // if there is no group you like to add your link to), pass a name,
            // the link target and our class to the function.
            $Sender->Menu->AddLink('', t(self::PAGE_NAME), self::SHORT_ROUTE, '', $AnchorAttributes);
        }
    }



    public function onDisable() {
        removeFromConfig('Plugin.CookieConsent.Message');
        removeFromConfig('Plugin.CookieConsent.Dismiss');
        removeFromConfig('Plugin.CookieConsent.LearnMore');
        removeFromConfig('Plugin.CookieConsent.Theme');


        Gdn::Router()-> DeleteRoute('^'.self::SHORT_ROUTE.'(/.*)?$');
        
    }



}