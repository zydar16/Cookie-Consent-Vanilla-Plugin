<?php if (!defined('APPLICATION')) exit(); ?>

<h1><?php echo T($this->Data['Title']); ?></h1>
<div class="Info">
    <?php echo t($this->Data['PluginDescription']); ?>
</div>
<h3><?php echo t('Settings'); ?></h3>
<?php
echo $this->Form->open();
echo $this->Form->errors();
?>
<ul>
    <li><?php
        echo $this->Form->label( T('Plugins.CookieConsent.MessageText', 'Message Text'), 'Plugin.CookieConsent.Message');
        echo $this->Form->textbox('Plugin.CookieConsent.Message');
        ?></li>
    <li><?php
        echo $this->Form->label(T('Plugins.CookieConsent.DismissText', 'Dismiss Text'), 'Plugin.CookieConsent.Dismiss');
        echo $this->Form->textbox('Plugin.CookieConsent.Dismiss');
        ?></li>
    <li><?php
        echo $this->Form->label(T('Plugins.CookieConsent.LearnMoreText', 'Learn More Text'), 'Plugin.CookieConsent.LearnMore');
        echo $this->Form->textbox('Plugin.CookieConsent.LearnMore');
        ?></li>
    <li><?php
        echo $this->Form->label(T('Plugins.CookieConsent.Theme', 'Theme of the banner'), 'Plugin.CookieConsent.Theme');
        echo $this->Form->dropDown('Plugin.CookieConsent.Theme', array(
            'dark-top' => 'Dark Top',
            'dark-bottom' => 'Dark Bottom',
            'dark-floating' => 'Dark Floating',
            'light-top' => 'Light Top',
            'light-bottom' => 'Light Bottom',
            'ligt-floating' => 'Light Floating'

        ));
        ?></li>
</ul>
<?php
echo $this->Form->close('Save');
?>
