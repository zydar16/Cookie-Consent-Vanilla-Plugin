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


    public function __construct() {

    }

    public function pluginController_CookieConsent_create($Sender) {

        $Sender->title('Cookie Consent');
        $Sender->addSideMenu('plugin/CookieConsent');
        $Sender->Form = new Gdn_Form();
        $this->dispatch($Sender, $Sender->RequestArgs);
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
        $Link =  $TrimSize = c('Plugin.CookieConsent.Link');
        $Theme =  $TrimSize = c('Plugin.CookieConsent.Theme');

        $ConfigurationModel->setField(array(
            'Plugin.CookieConsent.Message'     => $Message,
            'Plugin.CookieConsent.Dismiss'      => $Dismiss,
            'Plugin.CookieConsent.LearnMore'    => $LearnMore,
            'Plugin.CookieConsent.Link'    => $Link,
            'Plugin.CookieConsent.Theme'    => $Theme
        ));


        $Sender->Form->setModel($ConfigurationModel);


        if ($Sender->Form->authenticatedPostBack() === false) {
            $Sender->Form->setData($ConfigurationModel->Data);
        } else {
            $ConfigurationModel->Validation->applyRule('Plugin.CookieConsent.Message', 'Required');
            $ConfigurationModel->Validation->applyRule('Plugin.CookieConsent.Dismiss', 'Required');
            $ConfigurationModel->Validation->applyRule('Plugin.CookieConsent.LearnMore', 'Required');
            $ConfigurationModel->Validation->applyRule('Plugin.CookieConsent.Link', 'Required');
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
        $Link = $TrimSize = c('Plugin.CookieConsent.Link');
        $Theme = $TrimSize = c('Plugin.CookieConsent.Theme');

        $JavaScript = '<script type="text/javascript">
                            window.cookieconsent_options = {"message": "'.$Message.'",
                                                            "dismiss": "'.$Dismiss.'",
                                                            "learnMore": "'.$LearnMore.'",
                                                            "link": "'.$Link.'",
                                                            "theme": "'.$Theme.'"};
                        </script>
                        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/1.0.9/cookieconsent.min.js"></script>
                        ';
        $Sender->Head->AddString($JavaScript);
    }

    public function setup() {
        saveToConfig('Plugin.CookieConsent.Message', T('Plugins.CookieConsent.Message', 'This website uses cookies to ensure you get the best experience on our website'));
        saveToConfig('Plugin.CookieConsent.Dismiss', T('Plugins.CookieConsent.Dismiss', 'Got it!'));
        saveToConfig('Plugin.CookieConsent.LearnMore', T('Plugins.CookieConsent.LearnMore', 'Learn More!'));
        saveToConfig('Plugin.CookieConsent.Link', '#');
        saveToConfig('Plugin.CookieConsent.Theme', 'dark-top');
    }


    public function onDisable() {
        removeFromConfig('Plugin.CookieConsent.Message');
        removeFromConfig('Plugin.CookieConsent.Dismiss');
        removeFromConfig('Plugin.CookieConsent.LearnMore');
        removeFromConfig('Plugin.CookieConsent.Link');
        removeFromConfig('Plugin.CookieConsent.Theme');
        
    }



}