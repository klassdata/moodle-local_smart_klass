SmartKlass at a glance
======================

![The Learning Revolution has started](http://www.klassdata.com/wp-content/uploads/2014/10/klass-learning-analytics.png "The Learning Revolution has started")


SmartKlass is a dashboard that should be included as a part of the
*Moodle* virtual learning platform to empower teachers to manage the
learning journey of their students.

By analyzing student’s behavioral data we create a rich picture of the
evolution of the students in an online course:

-   We help teachers to identify the students lagging behind.

-   We help teachers to identify the students that content is not
    challenging enough for them.

-   We help teachers to compare participation and results to other
    courses.

-   We provide this information so the teacher can take action like
    contact directly with the students, assign special content,
    encourage participation or provide special tutoring.

-   We also help teachers by helping students: Students could learn
    about their performance, individually and compared with the group.

The SmartKlass local extension could be used with *Moodle* 2.4 version
and above.

This guide covers the installation and the steps that should be done
after the installation extension has completed.

Installing
==========

You have the following options to install SmartKlass plugin:

Moodle versions 2.5+ and above
------------------------------

### Installing directly from the Moodle plugins directory


1.  Login as an admin and go to *Administration \> Site administration \> Plugins \> Install plugins.

2.  Click the button 'Install plugins from Moodle plugins directory'.

3.  Search in moodle.org for the SmartKlass extension with the “Install”
    button (so you can be sure that it will be compatible with your
    *Moodle* version). Click the Install button then click Continue.

4.  Check that you obtain a 'Validation passed!' message, then click the
    button 'Install plugin'.

5.  Follow the instructions on your screen.

### Installing via uploaded ZIP file

1.  Go to the [*Moodle SmartKlass plugin*](https://moodle.org/plugins/edit.php?plugin=local_smart_klass)
2.  Select your current Moodle version (2.5+ or above), then choose the
    SmartKlass plugin, click Download and download the ZIP file
3.  Login to your Moodle site as an admin and go to Administration \> Site administration \> Plugins \> Install plugins.
4.  Upload the ZIP file and select the appropriate plugin type (local),
5.  Tick the acknowledgement checkbox, then click the button 'Install
    plugin from the ZIP file'.
6.  Check that you obtain a 'Validation passed!' message, then click the
    button 'Install plugin'.
7.  Follow the instructions on your screen.

Moodle versions before 2.5+
---------------------------

If you can't deploy the plugin code via the administration web interface
as explained above, you have to copy the plugin to the server file
system manually (e.g. if the web server process does not have write
access to the Moodle installation tree to do this for you or if your
*Moodle* version is older than 2.5+).

1.  Go to the [*Moodle SmartKlass plugin*](https://moodle.org/plugins/edit.php?plugin=local_smart_klass)
2.  Select your current Moodle version (2.4 or above), then choose the
    SmartKlass plugin, click Download and download the ZIP file.
3.  Upload or copy it to your Moodle server.
4.  Unzip it in the right place for the plugin:

> */path/to/moodle/**local**/*
> A new location will appear: /path/to/moodle/**local/SmartKlass**

5.  In your Moodle site (as admin) go to *Settings \> Site administration \> Notifications*

You should get a message saying the plugin is installed.

When installing manually:

-   Check the file permissions. The web server needs to be able to read
    the plugin files. If the the rest of Moodle works then try to make
    the plugin permissions and ownership match. 
    Configure the permissions for all the files and installed code trees
    with your preferred FTP client or your web host dashboard. (e.g. change
    */moodle/local/SmartKlass* to 755 and check if it works).

-   Did you definitely unzip or install the plugin in the correct place?
    SmartKlass plugin should be unzipped in: */path/to/moodle/local/SmartKlass*
    Where */path/to/moodle/* is the root code tree in your server.

-   Because Moodle scans plugin folders for new plugins you cannot have
    any other files or folders there. Make sure you deleted the zip file
    and don't try to rename (for example) an old version of the plugin
    to some other name - it will break.

-   Make sure the directory name for the plugin is correct. All the
    names have to match. If you change the name then it won't work.
    The name for the plugin directory should be called **SmartKlass** and
    should be included in *local*.

Configuring
===========

SmartKlass plugin uses the Cron process in *Moodle.* You should follow
the setting up instructions from Cron at
[*https://docs.moodle.org/28/en/Cron*](https://docs.moodle.org/28/en/Cron)

Note: Do not skip setting up the cron process on your server for your
Moodle. SmartKlass plugin will not work properly without it.

Updating
========

In *Moodle* 2.4 onwards, you can enable the automatic updates
notification in:

*Administration\> Site Administration \> Server \> Update notifications*

If the automatic check for available updates is enabled and there is a
new update available for SmartKlass, a notification will be sent to all
site admins via email and/or popup message (according to the admin's
messaging preferences in their profile settings).

*Install this update* button will be shown on the [Plugins
overview](https://docs.moodle.org/28/en/Plugins_overview) page:

*Settings \> Site Administration \> Plugins \>* [*Plugins
overview*](https://docs.moodle.org/28/en/Installing_plugins)*. *

An admin can also check for available updates using the 'Check for
available updates' button in this same [Plugins
overview](https://docs.moodle.org/28/en/Plugins_overview) page.

Any updates available are highlighted, with further information and a
download link in the notes column opposite the plugin.

Uninstalling
============

To uninstall the plugin:

1.  Go to *Administration\> Site Administration \> Plugins \> Plugins* *overview*
    and click the Uninstall link opposite the plugin you wish to remove.

2.  Use a file manager to remove/delete the actual plugin directory
    (/path/to/moodle/**local/SmartKlass**) as instructed, otherwise
    Moodle will reinstall it next time you access the site
    administration.