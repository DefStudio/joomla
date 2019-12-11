# Clean Template Structure

## ./assets/js

JavaScript files (Is empty, its now loading from crypsis template). You can add your own js here.

## ./assets/less

Less files that will be compiled into CSS.

## ./language

Custom language files for the template.

## ./layouts/widgets

Template files for widgets. Widgets are simple standalone objects like JHtml.

## ./layouts

Template files for layouts. Layouts are similar to those in Joomla 3.0+.

## ./pages

Template files for pages. Pages are the traditional views.

## ./template.php

Main template class, which defines template overrides and custom method calls.

## ./config/config.xml

Template configuration options. Can be used both inside the template and less files.

##./config/kunena_tpml_clean.xml

Needed by Kunena template installer. Yes, the new templates will be installed by Joomla Extension Manager.

## ./config/template.xml

Legacy installer file for Kunena.


#How to start customizing the template

1: Open template.php
 - rename `class KunenaTemplateClean extends KunenaTemplate` to `class KunenaTemplateYournewname extends KunenaTemplate`
 - replace all `clean` words to `yournewname`

2: Open config/kunena_tpml_clean.xml
 - replace the words `clean` to `yournewname`

3: Enable your new template on the Kunena template page.

4: Start customizing your new template.
 - All Js files are loaded from the crypsis template. So you don't need to have the same files in your template. You have now always the latest version of the files.
 - All less files have been cleaned. Its now loading the basic bootstrap files from joomla.
 - If you want to remove bootstrap then you can remove the bootstrap files on the template.php file.
 - If you remove any file from your new template, then the view will be loaded from Crypsis template. So basically you can remove all files, and create only the file, for your custom work.

